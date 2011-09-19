<?php
/**
 * SingleResult.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package Model
 * @subpackage Query
 */

namespace dioxid\model\query;

class SingleResult extends ResultSet{

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

		foreach ($result[0] as $key => $value)
			$this->_result[$key] = $value;
	}

	public function valid() {
    	return $this->cursor < sizeof($this->_result);
  	}

	public function current() {
		$val =  array_values($this->_result);
		return $val[$this->cursor];
  	}
}

?>