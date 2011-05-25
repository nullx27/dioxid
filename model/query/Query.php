<?php
/**
 * Query.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package package_name
 */

namespace dioxid\model\query;

use PDO;
use PDOException;
use Exception;


use dioxid\model\query\Result;
use dioxid\model\query\Query;
use dioxid\error\exception\CouldNotWriteToCacheException;


/**
 * dioxid\model\query$Query
 *
 * Gernerall porpose SQL Abstraction Class.
 *
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @date 20.05.2011 17:01:29
 *
 */
class Query {


	/**
	 * The Databasetable name
	 * @var string
	 */
	private $_tname;

	/**
	 * Holds the PDO connector.
	 * @var boolÊ|ÊPDO
	 */
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


	/**
	 * Internal used Array which holds all options and commands
	 * requested form the SQL-Query
	 * @var bool | array
	 */
	private $_querystack = false;

	/**
	 * Bare initializing array for SELECT querys;
	 * @var array
	 */
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

	/**
	 * Bare initializing array for UPDATE querys;
	 * @var array
	 */
	private $_querystack_update_init = array(
			self::TYPE => self::UPDATE,
			self::TABLES => array(),
			self::JOIN => array(),
			self::SET => array(),
			self::WHERE => array(),
			self::BIND => array()
		);

	/**
	 * Bare initializing array for INSERT querys;
	 * @var array
	 */
	private $_querystack_insert_init = array(
			self::TYPE => self::INSERT,
			self::INTO => array(),
			self::COLUMNS => array(),
			self::VALUES => array(),
			self::BIND => array()
		);

	/**
	 * Bare initializing array for DELETE querys;
	 * @var array
	 */
	private $_querystack_delete_init = array(
			self::TYPE => self::DELETE,
			self::FROM => array(),
			self::JOIN => array(),
			self::WHERE => array(),
			self::BIND => array()
		);


	/**
	 * Definition where a "WHERE" statement is allowed
	 * @var array
	 */
	private $_WHERE_allowed = array(
			self::SELECT,
			self::DELETE,
			self::UPDATE
	);

	/**
	 * Time which a query needed to execute
	 * @var null | float
	 */
	private $_querytime = null;

	/**
	 * Time the query was send
	 * @var null | float
	 */
	private $_querytime_start = null;

	/**
	 * Time the query finished
	 * @var null | float
	 */
	private $_querytime_end = null;


// ************************************************************************** //

	/**
	 * Method: __construct
	 * Sets the Adapter and the table name.
	 *
	 * @param string $tablename
	 * @param PDO $adapter
	 */
	public function __construct($tablename, &$adapter){
		$this->_tname = $tablename;
		$this->_adapter = $adapter;

	}


	/**
	 * Method: select
	 * Initalizes a Select query.
	 *
	 * @param string|array $what Column names.
	 * @returns Query
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
	 * Initializes a UPDATE Query.
	 *
	 * @param array $set format: array('table' => 'new value')
	 * @throws Exception
	 * @return Query
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

	/**
	 * Method: columns
	 * Sets the columns which will be used for the query
	 * @param array $columns
	 * @throws Exception
	 * @return Query
	 */
	public function columns(array $columns){
		if(!array_key_exists(self::COLUMNS, $this->_querystack))
			throw new Exception();
		$this->_querystack[self::COLUMNS] =
			array_merge($this->_querystack[self::COLUMNS], $columns);

		return $this;
	}

	/**
	 * Method: addTable
	 * Adds a Table to the Query.
	 *
	 * @param array | string $table Can be a string like "table" or "table as t"
	 * 	or an array with the foramt of ('alias' => 'table')
	 *
	 * @throws Exception
	 * @return Query
	 */
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

	/**
	 * Method: insert
	 * Initilizes a INSERT query
	 * @param array $what format: ('column' => 'value')
	 * @throws Exception
	 * @returns Query
	 */
	public function insert(array $what){
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

	/**
	 * Method: delete
	 * Initilizes a DELETE query
	 *
	 * @throws Exception
	 * @return Query
	 */
	public function delete(){
		if($this->_querystack)
			throw new Exception();

		$this->_querystack[self::FROM] = $this->_tname;

		return $this;
	}

	/**
	 * Method: where
	 * Adds a Where clause to the query
	 *
	 * @param string $where
	 * @throws Exception
	 * @return Query
	 */
	public function where($where){
		if(!in_array($this->_querystack[self::TYPE], $this->_WHERE_allowed))
			throw new Exception();

		$args = func_get_args();
		if(count($args) > 1){
			$args = array_slice($args, 1);
			foreach ($args as $arg){
				$where = preg_replace('/\?/', $this->escpae($arg), $where, 1);
			}
		}

		$this->_querystack[self::WHERE][] = array(self::SQL_AND => $where);

		return $this;
	}

	/**
	 * Method: orWhere
	 * Adds an Where clause to the query
	 *
	 * @param string $where
	 * @throws Exception
	 * @return Query
	 */
	public function orWhere($where){
		if(!in_array($this->_querystack[self::TYPE], $this->_WHERE_allowed))
			throw new Exception();

		$args = func_get_args();
		if(count($args) > 1){
			$args = array_slice($args, 1);
			foreach ($args as $arg){
				$where = preg_replace('/\?/', $this->escpae($arg), $where, 1);
			}
		}

		$this->_querystack[self::WHERE][] = array(self::SQL_OR => $where);

		return $this;
	}

	/**
	 * Method: from
	 * Adds the FROM clause to the query
	 *
	 * @param string $col format: 'column' or 'column as c'
	 * @throws Exception
	 * Ïreturn Query
	 */
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

	/**
	 * Method: distinct
	 * Adds the Distinct clause to the SELECT query
	 *
	 * @throws Exception
	 */
	public function distinct(){
		if(!array_key_exists(self::DISTINCT, $this->_querystack))
			throw new Exception('Cant use DISTINCT HERE!');

		$this->_querystack[self::DISTINCT] = true;
		return $this;
	}

	/**
	 * Method: having
	 * Adds the having clause to the array.
	 * @param array|string $cond conditions for having
	 */
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

	/**
	 * Method: group
	 * Adds the GROUP BY Clause to the query
	 *
	 * @param string | array $by column(s) for the GROUP BY
	 * @throws Exception
	 */
	public function group($by){
		if(!array_key_exists(self::GROUP, $this->_querystack))
			throw new Exception('Cant use GROUP BY here!');

		if(!is_array($by))
			$by = array($by);
		$this->_querystack[self::GROUP][] = $by;

		return $this;
	}

	/**
	 * Method: order
	 * Adds the ORDER BY Clause to the query
	 *
	 * @param string | array $by column(s) for the ORDER BY
	 * @throws Exception
	 */
	public function order($by){
		if(!array_key_exists(self::ORDER, $this->_querystack))
			throw new Exception('Cant use ORDER BY here!');

		if(!is_array($by))
			$by = array($by);

		$this->_querystack[self::ORDER][] = $by;

		return $this;
	}

	/**
	 * Method: limit
	 * Adds the LIMIT clause to the Query
	 *
	 * @param int $count
	 * @param int $offset
	 * @throws Exception
	 */
	public function limit($count=NULL, $offset=NULL){
		if(!array_key_exists(self::LIMIT_COUNT, $this->_querystack) ||
			!array_key_exists(self::LIMIT_OFFSET, $this->_querystack))
				throw new Exception("Cant use LIMIT here");

		$this->_querystack[self::LIMIT_COUNT] = $count;
		$this->_querystack[self::LIMIT_OFFSET] = $offset;

		return $this;
	}


	/**
	 * Method: union
	 * Adds an UNION to the query
	 *
	 * @param Query | string $select can be an Query object or string containing
	 * a query
	 * @throws Exception
	 */
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

	/**
	 * Method: fetch
	 * Executes the query and returns a Resultobject containg all results
	 *
	 * @return Result | null
	 */
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

	/**
	 * Method: exec
	 * Executes the Query.
	 *
	 */
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

	/**
	 * Method: querytime
	 * Calculates the time the query needed
	 * @return float
	 */
	public function querytime(){
		if(!$this->_querytime)
			$this->_querytime = $this->_querytime_end - $this->_querytime_start;
		return $this->_querytime;
	}

	/**
	 * Method: _type
	 * Returns the Querytype
	 *
	 * @return string
	 */
	public function _type(){
		if(!$this->_querystack)
			return $this->_querystack;
		return $this->_querystack[self::TYPE];
	}

	/**
	 * Method: _toString
	 * Returns the Query
	 *
	 * @return string
	 */
	public function _toString(){
		return $this->_assemble();
	}

	/**
	 * Method: escpae
	 * Escapes a Value for use in the query
	 *
	 * @param mixed $var
	 */
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

	/**
	 * Method: _assemble
	 * Assables the Query
	 * @throws Exception
	 * @return string
	 */
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

	/**
	 * Method: _assembleSelect
	 * Assables a SELECT query
	 * @return string
	 */
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

	/**
	 * Method: _assembleUpdate
	 * Assembales a UPDATE query
	 * @return string
	 */
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

	/**
	 * Method: _assembleInsert
	 * Assembles a INSERT Query
	 * @return string
	 */
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

	/**
	 * Method: _assembleDelete
	 * Assebles a DELETE query
	 *
	 * @return string
	 */
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

	/**
	 * Method: _add
	 *
	 * Adds singleparts to the Query
	 *
	 * @param string $sql
	 * @param sring | array $what
	 * @param string $prefix
	 * @param string $postfix
	 * @param bool $trim
	 */
	private function _add(&$sql, $what, $prefix='', $postfix='', $trim=true){
		if(is_array($what)){
			$what = implode($postfix, $what);
		}

		$sql .= " " . $prefix . $what . $postfix;


		if($trim) $sql = trim(trim(trim($sql, $postfix), $prefix));
	}


// ************************************************************************** //

	/**
	 * Method: _join
	 * Prepares Joins
	 *
	 * @param string $type
	 * @param array $join
	 * @throws Exception
	 */
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

	/**
	 * Method: _startQuery
	 * Records the time when a query was started
	 */
	private function _startQuery(){
		$this->_querytime_start = microtime(true);
	}

	/**
	 * Method: _endQuery
	 * Records the time when a query has ended
	 */
	private function _endQuery(){
		$this->_querytime_end = microtime(true);
	}


}

?>