<?php
namespace dioxid\controller;
use dioxid\lib\Base;

abstract class Controller extends Base {
    protected static $params = array();

	public function __construct() { }

	public static function index() { }


	public static function _setParamArray($params){
	    static::$params = $params;
	}

	public static function _addParamArray($params){
	    array_merge(static::$params, $params);
	}

	protected static function setParam($key, $value){
	    static::$params[$key] = $value;
	}

	protected static function getParamArray(){
	    return static::$params;
	}

	protected static function getParam($key) {
        if(key_exists($key, static::$params)){
            return static::$params[$key];
        }
        return false;
	}

}

?>