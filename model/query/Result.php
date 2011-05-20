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