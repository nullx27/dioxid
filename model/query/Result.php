<?php
/**
 * Result.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package package_name
 */

namespace dioxid\model\query;

use Iterator;

/**
 * dioxid\model\query$Result
 * The Result Object
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @date 20.05.2011 17:46:29
 *
 */
class Result implements Iterator{

	/**
	 * Time the query needed to finish
	 * @var float | null
	 */
	private $_querytime = null;

	/**
	 * The query which was used
	 * @var string | null
	 */
	private $_query = null;

	/**
	 * The Results
	 * @var array
	 */
	protected $_result = array();

	protected $cursor = 0;

	/**
	 * Method: __construct
	 *
	 * @param string $query the Query
	 * @param float $time the Time
	 * @param array $result assoc array from fetchAll(PDO::FETCH_ASSOC)
	 */
	public function __construct($query, $time, $result){
		$this->_query = $query;
		$this->_querytime = $time;

		foreach ($result as $item ){
			foreach ($item as $key => $value){
				$this->_result[$key][] = $value;
			}
		}
	}

	public function __get($key){
		return $this->_result[$key];
	}

	/**
	 * Method: _toArray
	 * @return array The result as Assoc array
	 */
	public function _toArray(){
		return $this->_result;
	}

	/**
	 * Method: getQuery
	 * @return string The query which was used
	 */
	public function getQuery(){
		return $this->_query;
	}

	/**
	 * Method: getTime
	 * @return the time needed
	 */
	public function getTime(){
		return $this->_querytime;
	}

 	public function rewind() {
    	$this->cursor = 0;
  	}

  	public function valid() {
  		$c = 0;
  		//find the biggest array in the results, so nothing gets cut out
  		foreach($this->_result as $res){
			$c = (count($res) > $c)? count($res) : $c;
  		}

    	return $this->cursor < $c;
  	}

  	public function key() {
    	return $this->cursor;
  	}

  	public function current() {
		$out = array();
  		foreach ($this->_result as $key => $val){
			$out[@$key] = $this->_result[@$key][$this->cursor];
		}
		return $out;
  	}

  	public function next() {
    	$this->cursor++;
  	}
}

?>