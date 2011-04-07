<?php

/**
 * View.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package view
 */

namespace dioxid\view;

use dioxid\lib\Base;

use dioxid\exception\TemplateNotFoundException;

use dioxid\config\Config;
use dioxid\error\exception\EngineNotFoundException;
use dioxid\error\exception\MethodNotFoundException;

class View extends Base {
	public static $engine;

	public static function _init(){
		$engine_class = __NAMESPACE__ . '\\engine\\' . ucfirst(Config::getVal('view', 'engine')) . 'Engine';


		if(class_exists($engine_class)) {
			static::$engine = $engine_class::getInstance();
		} else {
			throw new EngineNotFoundException;
		}
	}

	public static function __callstatic($name, $args = array()){

		if(method_exists(__CLASS__, $name)){
			call_user_func_array(array(__CLASS__, $name), $args);
		}
		elseif(method_exists(static::$engine, $name)){
			call_user_func_array(array(static::$engine, $name), $args);
		}
		else {
			throw new MethodNotFoundException();
		}
	}

	public function __destruct() {
		call_user_func(array(static::$engine, 'finally'));
	}
}

?>