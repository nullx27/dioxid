<?php

/**
 * Model.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package model
 */

namespace  dioxid\model;

use dioxid\error\exception\EngineNotFoundException;

use dioxid\config\Config;

class Model {

	private static $_engine;
	protected static $db;

	protected static $_name=false;
	protected static $_driver=false;


	final public function __construct() {

		if(!static::$_driver){
			$class = 'dioxid\\model\\engine\\'. Config::getVal('database', 'driver', true) . 'Engine';
		} else {
			$class = 'dioxid\\model\\engine\\'. static::$_driver . 'Engine';
		}

		if(class_exists($class)){
			static::$db = call_user_func_array(array($class, 'getInstance'), array(static::$_name));
		}
		else {
			throw new EngineNotFoundException($class . ' not found');
		}

		static::_init();
	}

	public static function _init(){}

}

?>