<?php

/**
 * View.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package View
 */

namespace dioxid\view;

use dioxid\common\Loader;
use dioxid\common\Base;

use dioxid\config\Config;
use dioxid\view\exception\EngineNotFoundException;
use dioxid\view\exception\ClassNotFoundException;
use dioxid\view\exception\MethodNotFoundException;
use dioxid\view\exception\TemplateNotFoundException;

class View extends Base {

	public static $engine;

	public static $helper = array();

	public static function _init(){

		try{
			static::setEngine(Config::getVal('view', 'engine'));
		}
		catch (EngineNotFoundException $e){

			static::$engine = false;
		}

		static::loadDefaultHelpers();
	}

	public static function setEngine($engine){
		$engine_class = __NAMESPACE__ . '\\engine\\' . ucfirst($engine) . 'Engine';

		if(class_exists($engine_class)) {
			static::$engine = $engine_class::getInstance();

		} else {
			throw new EngineNotFoundException();
		}
	}

	public static function __callstatic($name, $args = array()){
		if(!static::$engine)
			throw new EngineNotFoundException();

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

	public function __call($name, $args= array()){
		if(!static::$engine)
			throw new EngineNotFoundException();

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

	public function __set($k,$v){

		return call_user_func_array(array(static::$engine, '__set'), array($k,$v));
	}

	public function __get($key){
		return call_user_func_array(array(static::$engine, '__get'), array($key));
	}


	public static function useHelper($name, $class=false) {
		if (($pos = strripos($name, 'Helper')) !== false) {
            $name = substr($name, 0, $pos);
        }

        if(!$class)
			$class = __NAMESPACE__ . '\\helper\\' . ucfirst($name) . 'Helper';


		if(!class_exists($class))
			throw new ClassNotFoundException("Helper $name not found");

		static::$engine->handleHelper($name, new $class);
	}

	public static function loadDefaultHelpers(){
		static::useHelper('baseUrl');
	}

	public function __destruct() {
		if(static::$engine)
			call_user_func(array(static::$engine, 'finally'));
	}
}

?>