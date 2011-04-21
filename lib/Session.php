<?php

/**
 * Session.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package lib
 */

namespace dioxid\lib;

use dioxid\error\exception\NamespaceAllreadyExistsException;

class Session {

	/**
	 * Variable Namespace for the Session
	 * @var string
	 */
	protected $namespace;

	/**
	 * Method: __construct
	 * @param unknown_type $namespace
	 * @throws NamespaceAllreadyExistsException
	 */
	public function __construct($namespace){
		$this->namespace = $namespace;
		session_start();
	}

	public function __set($key, $val) {
		if($key == 'id') {
			session_id($val);
			return true;
		}

		$_SESSION[$this->namespace][$key] = $val;
		return true;
	}

	public function __get($key){
		if($key == "id") return session_id();

		if(in_array($_SESSION[$this->namespace], $key)){
			return $_SESSION[$this->namespace][$key];
		return false;
		}
	}

	public function destroy(){
		session_destroy();
	}

}
?>