<?php

/**
 * Session.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package lib
 */

namespace dioxid\lib;
use dioxid\error\exception\NamespaceAllreadyExistsException;

use dioxid\lib\base;

class Session {

	protected $namespace;

	public function __construct($namespace){
		$this->namespace = $namespace;
		session_start();
		if(in_array($_SESSION, $namespace)) throw new NamespaceAllreadyExistsException("$namespace allready used in session");
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