<?php
/**
 * Query.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package package_name
 */

namespace dioxid\model\query;

use dioxid\error\exception\CouldNotWriteToCacheException;

use PDO;
use PDOException;
use Exception;

class Query {

	private $_tname;

	private $_adapter = false;

	const DISTINCT = 'distinct';
    const COLUMNS = 'columns';
    const FROM = 'from';
    const JOIN = 'join';
    const INTO = 'into';
    const UNION = 'union';
    const WHERE = 'where';
    const GROUP = 'group';
    const HAVING = 'having';
    const ORDER = 'order';
    const LIMIT_COUNT = 'limitcount';
    const LIMIT_OFFSET = 'limitoffset';


    const SET = 'set';
    const VALUES = 'values';


    const TYPE = 'type';
    const BIND = 'bind';
    const TABLES = 'tables';

    const SELECT = 'select';
    const UPDATE = 'update';
    const INSERT = 'insert';
    const DELETE = 'delete';


	const SQL_SELECT = 'SELECT';
	const SQL_INSERT = 'INSERT INTO';
	const SQL_UPDATE = 'UPDATE';
	const SQL_DELETE = 'DELETE';
	const SQL_UNION = 'UNION';

	const SQL_DISINCT = 'DISTINCT';

	const SQL_WILDCARD = '*';

	const SQL_FROM = 'FROM';

	const SQL_INNER_JOIN = 'INNER JOIN';
	const SQL_LEFT_JOIN = 'LEFT JOIN';
	const SQL_RIGHT_JOIN = 'RIGHT JOIN';
	const SQL_FULL_JOIN = 'FULL JOIN';
	const SQL_CROSS_JOIN = 'CROSS JOIN';
	const SQL_NATRUAL_JOIN = 'NATURAL JOIN';

	const SQL_ON = 'ON';


	const SQL_WHERE = 'WHERE';
	const SQL_AND = 'AND';
	const SQL_OR = 'OR';


	const SQL_GROUP = 'GROUP BY';
	const SQL_HAVING = 'HAVING';
	const SQL_ORDER = 'ORDER BY';

	const SQL_LIMIT = 'LIMIT';

	const SQL_ASC = 'ASC';
	const SQL_DESC = 'DESC';
	const SQL_AS = 'AS';
	const SQL_SET = 'SET';
	const SQL_VALUES = 'VALUES';



	private $_querystack = false;

	private $_querystack_select_init = array(
			self::TYPE => self::SELECT,
			self::DISTINCT => false,
			self::COLUMNS => array(),
			self::UNION => array(),
			self::FROM => array(),
			self::JOIN => array(),
			self::WHERE => array(),
			self::GROUP => array(),
			self::HAVING => array(),
			self::ORDER => array(),
			self::LIMIT_COUNT => null,
			self::LIMIT_OFFSET => null,
			self::BIND => array()
		);

	private $_querystack_update_init = array(
			self::TYPE => self::UPDATE,
			self::TABLES => array(),
			self::JOIN => array(),
			self::SET => array(),
			self::WHERE => array(),
			self::BIND => array()
		);

	private $_querystack_insert_init = array(
			self::TYPE => self::INSERT,
			self::INTO => array(),
			self::COLUMNS => array(),
			self::VALUES => array(),
			self::BIND => array()
		);

	private $_querystack_delete_init = array(
			self::TYPE => self::DELETE,
			self::FROM => array(),
			self::JOIN => array(),
			self::WHERE => array(),
			self::BIND => array()
		);


	private $_WHERE_allowed = array(
			self::SELECT,
			self::DELETE,
			self::UPDATE
	);

	private $_querytime = null;
	private $_querytime_start = null;
	private $_querytime_end = null;


// ************************************************************************** //

	public function __construct($tablename, $adapter){
		$this->_tname = $tablename;
		$this->_adapter = $adapter;
	}


	/**
	 * Method: select
	 *
	 * @param string|array $what
	 */
	public function select($what=null){
		if($this->_querystack)
			throw new Exception();
		$this->_querystack = $this->_querystack_select_init;

		$this->from($this->_tname);

		if($what){
			if(!is_array($what))
				$what = array($what);

			$this->_querystack[self::COLUMNS] = array_merge(
				$this->_querystack[self::COLUMNS], $what);
		}

		return $this;
	}


	/**
	 * Method: update
	 * format: array('table' => 'new value')
	 * @param array $set
	 * @throws Exception
	 */
	public function update(array $set) {
		if($this->_querystack)
			throw new Exception();

		$this->_querystack = $this->_querystack_update_init;
		$this->_querystack[self::TABLES][] = $this->_tname;

		$this->_querystack[self::SET] = array_merge(
				$this->_querystack[self::SET], $set);


		return $this;
	}

	public function set(array $set){
		foreach ($set as $col => $v) {
			$this->_querystack[self::SET][][$col] = $v;
		}


		return $this;
	}

	public function columns(array $columns){
		if(!array_key_exists(self::COLUMNS, $this->_querystack))
			throw new Exception();
		$this->_querystack[self::COLUMNS] =
			array_merge($this->_querystack[self::COLUMNS], $columns);

		return $this;
	}

	public function addTable($table){
		if(!array_key_exists(self::TABLES, $this->_querystack))
			throw new Exception('Cant use addTable here!');

		if(preg_match('/^(.+)\s+[A|a][S|s]\s+(.+)$/i', $table, $m)) {
            $this->_querystack[self::TABLES][] = array($m[1]=>$m[2]);
		} else {
			$this->_querystack[self::TABLES][] = $table;
		}

		return $this;
	}

	public function insert($what){
		if($this->_querystack)
			throw new Exception();

		$this->_querystack = $this->_querystack_insert_init;

		$this->_querystack[self::INTO][] = $this->_tname;

		foreach ($what as $col => $val){
			$this->_querystack[self::COLUMNS][] = $col;
			$this->_querystack[self::VALUES][] = $val;
		}

		return $this;
	}

	public function delete(){
		if($this->_querystack)
			throw new Exception();

		$this->_querystack[self::FROM] = $this->_tname;

		return $this;
	}

	public function where($where){
		if(!in_array($this->_querystack[self::TYPE], $this->_WHERE_allowed))
			throw new Exception();

		$this->_querystack[self::WHERE][] = array(self::SQL_AND => $where);

		return $this;
	}

	public function orWhere($where){
		if(!in_array($this->_querystack[self::TYPE], $this->_WHERE_allowed))
			throw new Exception();

		$this->_querystack[self::WHERE][] = array(self::SQL_OR => $where);

		return $this;
	}

	public function from($col){
		if(!array_key_exists(self::FROM, $this->_querystack))
			throw new Exception('Cant use FROM here!');

		if(preg_match('/^(.+)\s+[A|a][S|s]\s+(.+)$/i', $col, $m)) {
            $this->_querystack[self::FROM][] = array($m[1]=>$m[2]);
		} else {
			$this->_querystack[self::FROM][] = $col;
		}

		return $this;
	}

	public function into($where){

	}

	/**
	 * Method: join
	 * assoc array: array("what" => "on")
	 * ex: array("foo as f" => "f.b = bar.foo")
	 *
	 * @param array $join
	 * @param string $cond condition for joining
	 */
	public function join(array $join){
		$this->_join(self::SQL_INNER_JOIN, $join);
		return $this;
	}

	public function leftjoin(array $join){
		$this->_join(self::SQL_LEFT_JOIN, $join);
		return $this;
	}

	public function rightjoin(array $join){
		$this->_join(self::SQL_RIGHT_JOIN, $join);
		return $this;
	}

	public function crossjoin(array $join){
		$this->_join(self::SQL_CROSS_JOIN, $join);
		return $this;
	}

	public function fulljoin(array $join){
		$this->_join(self::SQL_FULL_JOIN, $join);
		return $this;
	}

	public function naturaljoin(array $join){
		$this->_join(self::SQL_NATRUAL_JOIN, $join);
		return $this;
	}

	public function distinct(){
		if(!array_key_exists(self::DISTINCT, $this->_querystack))
			throw new Exception('Cant use DISTINCT HERE!');

		$this->_querystack[self::DISTINCT] = true;
		return $this;
	}

	public function having($cond){
		if(!array_key_exists(self::HAVING, $this->_querystack))
			throw new Exception("cant use having here");

		if(count($this->_querystack[self::GROUP]) == 0)
			throw new Exception('Cant use having without GROUP BY!');

		if(!is_array($cond))
			$cond = array($cond);

		$this->_querystack[self::HAVING][] = $cond;
		return $this;
	}

	public function group($by){
		if(!array_key_exists(self::GROUP, $this->_querystack))
			throw new Exception('Cant use GROUP BY here!');

		if(!is_array($by))
			$by = array($by);
		$this->_querystack[self::GROUP][] = $by;

		return $this;
	}

	public function order($by){
		if(!array_key_exists(self::ORDER, $this->_querystack))
			throw new Exception('Cant use ORDER BY here!');

		if(!is_array($by))
			$by = array($by);

		$this->_querystack[self::ORDER][] = $by;

		return $this;
	}

	public function limit($count=NULL, $offset=NULL){
		if(!array_key_exists(self::LIMIT_COUNT, $this->_querystack) ||
			!array_key_exists(self::LIMIT_OFFSET, $this->_querystack))
				throw new Exception("Cant use LIMIT here");

		$this->_querystack[self::LIMIT_COUNT] = $count;
		$this->_querystack[self::LIMIT_OFFSET] = $offset;

		return $this;
	}


	public function union($select = array()){
		if(!array_key_exists(self::UNION, $this->_querystack))
			throw new Exception("Cant use UNION here");

		if($select instanceof Query){
			if($select->_type() != self::SELECT)
				throw new Exception("Queryobject is not a SELECT Type");
			$this->_querystack[self::UNION][] = $select->_toString();
		}
		elseif(is_array($select)){
			foreach ($select as $stmt){
				if($stmt instanceof Query){
					if($select->_type() != self::SELECT)
						throw new Exception("Queryobject is not a SELECT Type");
					$this->_querystack[self::UNION][] = $select->_toString();
				} else {
					$this->_querystack[self::UNION][] = $stmt;
				}
			}
		}
		else {
			$this->_querystack[self::UNION][] = $select;
		}

		return $this;
	}

	/**
	 * Method: bind
	 * Needs an assoc array to work,
	 * e.g.: array(':foo', 'bar)
	 * @param array $vars
	 */
	public function bind(array $vars){
		$this->_querystack[self::BIND][] = $vars;
		return $this;
	}

// ************************************************************************** //

	public function fetch(){
		$query = $this->_assemble();
		$stmt = $this->_adapter->prepare($query);

		if(count($this->_querystack[self::BIND]) > 0){
			foreach($this->_querystack[self::BIND] as $items){
				foreach($items as $bind => $param){
					$stmt->bindValue($bind, $param);
				}
			}
		}

		$this->_startQuery();
			$stmt->execute();
		$this->_endQuery();

		$ret = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if($ret === NULL){
			return FALSE;
		}

		return new Result($query, $this->querytime(), $ret);
	}

	public function exec(){
		$query = $this->_assemble();

		$stmt = $this->_adapter->prepare($query);

		if(count($this->_querystack[self::BIND]) > 0){
			foreach($this->_querystack[self::BIND] as $items){
				foreach($items as $bind => $param){
					$stmt->bindValue($bind, $param);
				}
			}
		}

		$this->_startQuery();
		$stmt->execute();
		$this->_endQuery();

		if($this->_querystack[self::TYPE] == self::INSERT)
			return $this->_adapter->lastInsertId();
	}

	public function querytime(){
		if(!$this->_querytime)
			$this->_querytime = $this->_querytime_end - $this->_querytime_start;
		return $this->_querytime;
	}

	public function _type(){
		if(!$this->_querystack)
			return $this->_querystack;
		return $this->_querystack[self::TYPE];
	}

	public function _toString(){
		return $this->_assemble();
	}

	public function escpae($var){
		if(is_int($var))
			return (int) $var;

		if(is_float($var))
			return (float) $var;

		if( is_null($var) )
      		return "NULL";

		return (string) $this->_adapter->quote($var);
	}

// ************************************************************************** //

	private function _assemble(){
		switch ($this->_querystack[self::TYPE]) {
			case self::SELECT:
				return $this->_assembleSelect();
				break;
			case self::UPDATE:
				return $this->_assembleUpdate();
				break;
			case self::INSERT:
				return $this->_assembleInsert();
				break;
			case self::DELETE:
				return $this->_assembleDelete();
				break;
			default:
				throw new Exception("Unkown Type");
				break;
		}
	}

	private function _assembleSelect(){
		$sql = "";

		//SELECT
		$this->_add($sql, self::SQL_SELECT);

		//Distinct?
		if($this->_querystack[self::DISTINCT])
			$this->_add($sql, self::SQL_DISINCT);

		//Columns
		if(count($this->_querystack[self::COLUMNS]) > 0 ){
			$this->_add($sql, $this->_querystack[self::COLUMNS], '', ',');
		} else {
			$this->_add($sql, self::SQL_WILDCARD);
		}


		//FROM
		$this->_add($sql, self::SQL_FROM);
		foreach ($this->_querystack[self::FROM] as $column){
			$from = array();

			if(is_array($column)){
				foreach ($column as $col => $alias)
					$from[] =  $col . ' ' . self::SQL_AS . ' ' . $alias;
			} else {
				$from[] = $column;
			}
		};
		$this->_add($sql, $from);

		//JOIN
		if(count($this->_querystack[self::JOIN]) > 0) {
			foreach ($this->_querystack[self::JOIN] as $counter => $join){
				$this->_add($sql, $join['type']);
				$this->_add($sql, $join['table']);

				if($join['alias']) {
					$this->_add($sql, self::SQL_AS);
					$this->_add($sql, $join['alias']);
				}

				$this->_add($sql, self::SQL_ON);
				$this->_add($sql, $join['condition']);
			}
		}

		//WHERE
		if(count($this->_querystack[self::WHERE]) > 0){
			$w = "";
			$this->_add($sql, self::SQL_WHERE);
			foreach ($this->_querystack[self::WHERE] as $counter => $where){
				foreach ($where as $type => $cond){
					if($counter != 0)
						$this->_add($sql, $type);
					$this->_add($sql, "(".$cond.")");
				}
			}
		}

		//GROUP BY
		if(count($this->_querystack[self::GROUP]) > 0) {
			$this->_add($sql, self::SQL_GROUP);
			foreach ($this->_querystack[self::GROUP] as $counter => $by){
				$this->_add($sql, $by, '', ', ');
			}
		}

		//HAVING
		if(count($this->_querystack[self::HAVING]) > 0) {
			$this->_add($sql, self::SQL_HAVING);
			foreach ($this->_querystack[self::HAVING] as $counter => $having){
				$this->_add($sql, $having, '', ', ');
			}
		}

		//ORDER BY
		if(count($this->_querystack[self::ORDER]) > 0) {
			$this->_add($sql, self::SQL_ORDER);
			foreach ($this->_querystack[self::ORDER] as $counter => $order){
				$this->_add($sql, $order, '', ', ');
			}
		}

		//LIMIT
		if($this->_querystack[self::LIMIT_COUNT] ||
			$this->_querystack[self::LIMIT_OFFSET]){
				$this->_add($sql, self::SQL_LIMIT);
				$limit = array(
					($this->_querystack[self::LIMIT_COUNT]) ?
						$this->_querystack[self::LIMIT_COUNT] :
						0,
					($this->_querystack[self::LIMIT_OFFSET]) ?
						$this->_querystack[self::LIMIT_OFFSET] :
						''
				);
				$this->_add($sql, $limit, '', ', ');
			}

		//UNION
		if(count($this->_querystack[self::UNION]) > 0){
			foreach ($this->_querystack[self::UNION] as $union){
				$this->_add($sql, self::UNION);
				$this->_add($sql, $union);
			}
		}
		return $sql;

	}

	private function _assembleUpdate(){
		$sql = "";

		//UPDATE
		$this->_add($sql, self::SQL_UPDATE);

		//Tables
		foreach ($this->_querystack[self::TABLES] as $tables){
			$table = array();

			if(is_array($tables)){
				foreach ($tables as $col => $alias)
					$table[] =  $col . ' ' . self::SQL_AS . ' ' . $alias;
			} else {
				$table[] = $tables;
			}
		};
		$this->_add($sql, $table);

		//COLUMNS
		$this->_add($sql, $this->_querystack[self::COLUMNS], '', ',');

		//SET
		$this->_add($sql, self::SQL_SET);
		$set = array();

		foreach ($this->_querystack[self::SET] as $col => $val){
			$set[] = $col. '=' . $this->escpae($val);
		}

		$this->_add($sql, $set, '', ' ,');



		//WHERE
		if(count($this->_querystack[self::WHERE]) > 0){
			$w = "";
			$this->_add($sql, self::SQL_WHERE);
			foreach ($this->_querystack[self::WHERE] as $counter => $where){
				foreach ($where as $type => $cond){
					if($counter != 0)
						$this->_add($sql, $type);
					$this->_add($sql, "(".$cond.")");
				}
			}
		}
		return $sql;
	}

	private function _assembleInsert(){
		$sql = "";

		//INSERT
		$this->_add($sql, self::SQL_INSERT);

		//INTO
		foreach ($this->_querystack[self::INTO] as $tables){
			$table = array();

			if(is_array($tables)){
				foreach ($tables as $col => $alias)
					$table[] =  $col . ' ' . self::SQL_AS . ' ' . $alias;
			} else {
				$table[] = $tables;
			}
		};
		$this->_add($sql, $table);

		//Columns
		$this->_add($sql, '(');
		$this->_add($sql, $this->_querystack[self::COLUMNS], '', ',');
		$this->_add($sql, ')');

		//VALUES
		$this->_add($sql, self::SQL_VALUES);
		$this->_add($sql, '(');
		$this->_add($sql, $this->_querystack[self::VALUES], '', ',');
		$this->_add($sql, ')');

		return $sql;
	}

	private function _assembleDelete(){
		$sql = "";

		//DELETE
		$this->_add($sql, self::SQL_DELETE);

		//FROM
		$this->_add($sql, self::SQL_FROM);
		foreach ($this->_querystack[self::FROM] as $column){
			$from = array();

			if(is_array($column)){
				foreach ($column as $col => $alias)
					$from[] =  $col . ' ' . self::SQL_AS . ' ' . $alias;
			} else {
				$from[] = $column;
			}
		};
		$this->_add($sql, $from);


		//JOIN
		if(count($this->_querystack[self::JOIN]) > 0) {
			foreach ($this->_querystack[self::JOIN] as $counter => $join){
				$this->_add($sql, $join['type']);
				$this->_add($sql, $join['table']);

				if($join['alias']) {
					$this->_add($sql, self::SQL_AS);
					$this->_add($sql, $join['alias']);
				}

				$this->_add($sql, self::SQL_ON);
				$this->_add($sql, $join['condition']);
			}
		}

		//WHERE
		if(count($this->_querystack[self::WHERE]) > 0){
			$w = "";
			$this->_add($sql, self::SQL_WHERE);
			foreach ($this->_querystack[self::WHERE] as $counter => $where){
				foreach ($where as $type => $cond){
					if($counter != 0)
						$this->_add($sql, $type);
					$this->_add($sql, "(".$cond.")");
				}
			}
		}

		//todo:USING

		return $sql;
	}

	private function _add(&$sql, $what, $prefix='', $postfix='', $trim=true){
		if(is_array($what)){
			$what = implode($postfix, $what);
		}

		$sql .= " " . $prefix . $what . $postfix;


		if($trim) $sql = trim(trim(trim($sql, $postfix), $prefix));
	}


// ************************************************************************** //

	private function _join($type, array $join){

		if(!array_key_exists(self::JOIN, $this->_querystack))
			throw new Exception("Cant use a join here!");

		if(@count($this->_querystack[self::UNION]) > 0)
			throw new Exception("Cant use a join with a union!");


		foreach ($join as $colum => $cond){

			if(is_array($colum)){
				foreach ($colum as $tname => $talias){
					$name = $tname;
					$alias = $talias;
				}
			}

			elseif(preg_match('/^(.+)\s+[A|a][S|s]\s+(.+)$/i', $colum, $m)) {
            	$name = $m[1];
            	$alias = $m[2];
			}

			else {
				$name = $colum;
				$alias = null;
			}
		}

		$join = array(
			"type" => $type,
			"table" => $name,
			"alias" => $alias,
			"condition" => $cond
		);

		$this->_querystack[self::JOIN][] = $join;
	}

	private function _startQuery(){
		$this->_querytime_start = microtime(true);
	}

	private function _endQuery(){
		$this->_querytime_end = microtime(true);
	}


}

?>