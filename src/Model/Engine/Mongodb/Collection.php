<?php
/**
 * Collection.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package Model
 * @subpackage Database
 */

namespace dioxid\model\engine\mongodb;

class Collection {
	protected $_name;
	protected $_document;
	protected $_buffer;

	public function __construct($name, $document){
		$this->_name = $name;
		$this->_document = &$document;
	}

	public function insert($value){
		$this->_document->$this->_name->insert($value);
		$ret =  $this->_document->$this->_name->findOne();
		$this->_buffer = $ret["id"];
	}

	public function __get($key){
		if(function_exists(array($this, $key))){
			call_user_func(array($this, $key));
			return;
		}
		$this->_buffer = ($this->_document->$this->_name->findeOne(array($key)));
	}

	public function query($query){
		$this->_buffer = $this->_document->$this->_name->find($query);
	}

	public function all(){
		return $this->query(array(""));
	}

	public function create_index($name, $start=1, $direction='asc'){
		if(strtolower($direction) == "acs") {
			if($start < 0)
				$start = $start * (-1);
		}
		else {
			if($start > 0){
				$start = $start * (-1);
			}
		}

		$ar = array($name => $start);
		$this->_document->$this->_name->ensureIndex($ar);
	}

	public function __toString(){
		ob_start();
		print ($this->_buffer);
		$cont = ob_get_contents();
		ob_end_clean();
		return $cont;

	}
}

?>