<?php
/**
 * file_name  - project_name
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package Lib
 * @subpackage CookieJar
 */

namespace dioxid\lib\cookiejar;

class Cookie {
	public $set_on;
	public $expires;

	public $data = array();

	public function __construct($expires){
		$this->set_on = time();
		$this->expires = $this->set_on + $expires;
	}

	public function isExpiered(){
		return ($this->expires > time()) ? true : false;
	}

	public function addData($data){
		array_merge($this->data, $data);
	}
}

?>