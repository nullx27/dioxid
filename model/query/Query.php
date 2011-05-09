<?php
/**
 * Query.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package package_name
 */

namespace dioxid\model\query;

use Exception;

class Query {

	private $_tname;
	private $_type = false;

	private $_adapter = false;

	private $_what = '*';
	private $_where = '';

	private $_joinstack = array();

	private $_modifier = '';
	private $_aggregatorstack = array();
	private $_limit = array();


	public function __construct($tablename, $adapter){
		$this->_tname = $tablename;

		$this->_adapter = $adapter;
	}

	public function select ($what){
		if($this->_type != "")
			throw new Exception('Wrong Type, and replace me with my own exception please!');
		$this->_type = 'SELECT';
		if(!is_array($what))
		{
			$this->_what = $what;
		} else {
			$this->_what = implode(', ', $what);
		}
		return $this;
	}

	public function where($stmt){
		if($this->_where != '')
			$this->_where .= ' AND ';

		$this->_where .= trim($stmt);
		return $this;
	}


	public function orWhere($stmt){
		if($this->_where == '')
			throw new Exception('No where statement where set before, also i need a own exception!');

		$this->_where .= ' OR ' . trim($stmt);
		return $this;
	}

	public function join($what, $on){
		$this->_joinstack['JOIN'][] = array($what => $on);
		return $this;
	}

	public function rightjoin($what, $on){
		$this->_joinstack['RIGHTJOIN'][] = array($what => $on);
		return $this;
	}

	public function outerjoin($what, $on){
		$this->_joinstack['OUTERJOIN'][] = array($what => $on);
		return $this;
	}

	public function crossjoin($what, $on){
		$this->_joinstack['CROSSJOIN'][] = array($what => $on);
		return $this;
	}

	public function distinct(){
		if($this->_modifier != '')
			throw new Exception();
		$this->_modifier = 'DISTINCT';
		return $this;
	}

	public function limit($start, $end) {
		$this->_limit = array($start, $end);
		return $this;
	}

	public function groupby(array $what){
		$this->_aggregatorstack[] = 'GROUP BY ' . implode(', ', $what);
		return $this;
	}

	public function having($what){
		$this->_aggregatorstack[] = 'HAVING (' . trim($what) . ')';
		return $this;
	}

	public function orderby(array $what, $how = 'ASC'){
		$this->_aggregatorstack[] = 'ORDER BY ' . implode(', ', $what) . " " . strtoupper(trim($how));
		return $this;
	}

	public function insert(array $what){
		if($this->_type != "")
			throw new Exception('Wrong Type, and replace me with my own exception please!');
		$this->_type = 'INSERT';

		$cols = '';
		$vals = '';
		foreach($what as $col => $val){
			$cols .= $col .', ';
			$vals .= "'" . $val . "'" .', ';
		}
		$this->_what = "(" . trim($cols,', ') . ") VALUES (". trim($vals, ', ') . ")";
		return $this;
	}

	public function update(array $what){
		if($this->_type != "")
			throw new Exception('Wrong Type, and replace me with my own exception please!');
		$this->_type = 'UPDATE';
		$out = '';

		foreach($what as $col => $val){
			$out .= "$col=$val, ";
		}
		$this->_what = trim($out, ', ');
		return $this;
	}

	public function delete(){
		if($this->_type != "")
			throw new Exception('Wrong Type, and replace me with my own exception please!');
		$this->_type = 'DELETE';
		return $this;
	}

	private function _assemble(){
		$query = '';
		switch($this->_type){
			case 'SELECT':
				$query .= $this->_type;
				if($this->_modifier != '') $query .= " " . $this->_modifier;
				$query .= " " . $this->_what;
				$query .= " FROM `" . $this->_tname . '`';

				if(count($this->_joinstack) > 0) {
					foreach ($this->_joinstack as $type => $counter){
						foreach ($counter as $c => $con){
							foreach($con as $what => $where) {
								$query .= " ";
								$query .= $type;
								$query .= " " . $what ;
								$query .= " ON " .$where;
							}
						}
					}
				}

				if($this->_where != ''){
					$query .= " WHERE " . $this->_where;
				}

				if(count($this->_aggregatorstack) > 0) {
					foreach ($this->_aggregatorstack as $agg) {
					$query .= " ";
					$query .= $agg;
				}

				if(count($this->_limit) > 0){
					$query .= " ";
					$query .= "LIMIT " . $this->_limit[0] . ", " . $this->_limit[1];
				}
		}
			break;
		case 'INSERT':
			$query .= 'INSERT INTO `' . $this->_tname . '`';
			$query .= ' ' . $this->_what;
			break;
		case 'UPDATE':
			$query .= "UPDATE";
			$query .= ' `' . $this->_tname . '`';
			$query .= ' SET ' . $this->_what;
			$query .= ' WHERE ' . $this->_where;
			break;
		case 'DELETE':
			$query .= "DELETE";
			$query .= ' FROM `' . $this->_tname . '`';
			$query .= ' WHERE ' . $this->_where;
			break;
	}

	return trim($query);
	}

	public function _toString(){
		return $this->_assemble();
	}

	public function exec(){
		$query = $this->_assemble();
		$this->_adapter->exec($query);
		return $this->_adapter->lastInsertId();
	}

	public function fetch(){
		$query = $this->_assemble();
		$res = $this->_adapter->query($query);
		return $res->fetchObject();
	}
}

?>