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

	private $_adapter = false;

	const DISTINCT = 'distinct';
    const COLUMNS = 'columns';
    const FROM = 'from';
    const INTO = 'into';
    const UNION = 'union';
    const WHERE = 'where';
    const GROUP = 'group';
    const HAVING = 'having';
    const ORDER = 'order';
    const USING = 'using';
    const LIMIT_COUNT = 'limitcount';
    const LIMIT_OFFSET = 'limitoffset';


    const SET = 'set';
    const VALUES = 'values';


    const TYPE = 'type';
    const SELECT = 'select';
    const UPDATE = 'update';
    const INSERT = 'insert';
    const DELETE = 'delete';


	const SQL_SELECT = 'SELECT';
	const SQL_INSERT = 'INSERT INTO';
	const SQL_UPDATE = 'UPDATE';
	const SQL_DELETE = 'DELETE';
	const SQL_UNION = 'UNION';

	const SQL_DISINCT = 'DISINCT';

	const SQL_FROM = 'FROM';

	const SQL_INNER_JOIN = 'INNER JOIN';
	const SQL_LEFT_JOIN = 'LEFT JOIN';
	const SQL_RIGHT_JOIN = 'RIGHT JOIN';
	const SQL_FULL_JOIN = 'FULL JOIN';
	const SQL_CROSS_JOIN = 'CROSS JOIN';
	const SQL_NATRUAL_JOIN = 'NATURAL JOIN';

	const SQL_ON = 'ON';

	const SQL_USING = 'USING';

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
			self::WHERE => array(),
			self::GROUP => array(),
			self::HAVING => array(),
			self::ORDER => array(),
			self::LIMIT_COUNT => null,
			self::LIMIT_OFFSET => null
		);

	private $_querystack_update_init = array(
			self::TYPE => self::UPDATE,
			self::COLUMNS => array(),
			self::SET => array(),
			self::WHERE => array(),
		);

	private $_querystack_insert_init = array(
			self::TYPE => self::INSERT,
			self::INTO => array(),
			self::COLUMNS => array(),
			self::VALUES => array()
		);

	private $_querystack_delete_init = array(
			self::TYPE => self::DELETE',
			self::FROM => array(),
			self::WHERE => array(),
			self::USING => array()
		);


	private $_WHERE_allowed = array(
			self::SELECT,
			self::DELETE,
			self::UPDATE
	);




	public function __construct($tablename, $adapter){
		$this->_tname = $tablename;
		$this->_adapter = $adapter;
	}



	private function _unpack($array){

	}

	/**
	 * Method: select
	 *
	 * @param string|array $what
	 */
	public function select($what){
		if($this->_querystack)
			throw new Exception();
		$this->_querystack = $this->_querystack_select_init;
		$this->_querystack[self::FROM] = $this->_tname;
	}

	public function update($what) {
		if($this->_querystack)
			throw new Exception();

		$this->_querystack = $this->_querystack_update_init;
	}

	public function insert($what){
		if($this->_querystack)
			throw new Exception();

		$this->_querystack[self::INTO] = $this->_tname;
	}

	public function delete($what){
		if($this->_querystack)
			throw new Exception();

		$this->_querystack[self::FROM] = $this->_tname;
	}

	public function where($where){
		if(!in_array($this->_querystack[self::TYPE], $this->_WHERE_allowed))
			throw new Exception();
	}

	public function orWhere($where){
		if(!in_array($this->_querystack[self::TYPE], $this->_WHERE_allowed))
			throw new Exception();
	}


	public function from($where){

	}

	/**
	 * Method: join
	 * assoc array: array("what" => "on")
	 * ex: array("foo as f" => "f.b = bar.foo")
	 *
	 * @param array $join
	 */
	public function join(array $join){

	}

	public function leftjoin(array $join){

	}

	public function rightjoin(array $join){

	}

	public function crossjoin(array $join){

	}

	public function fulljoin(array $join){

	}

	public function fulljoin(array $join){

	}

	public function distinct(){
		if($this->_querystack[self::TYPE] != self::SELECT)
			throw new Exception();
	}

}

?> 8