<?php
/**
 * Result.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package package_name
 */

namespace dioxid\model\query;

class Result {

	private $_querytime = null;

	private $_query = null;

	protected $_result;

	public function __construct($query, $time){
		$this->_query = $query;
		$this->_querytime = $time;
	}

	public function __set($col, $value){
		$this->_result[$col] = $value;
	}

	public function __get($key){
		return $this->_result[$key];
	}

	public function _toArray(){
		return $this->_result;
	}

	public function getQuery(){
		return $this->_query;
	}

	public function getTime(){
		return $this->_querytime;
	}
}

?>