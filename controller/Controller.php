<?php

/**
 * Controller.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package controller
 */

namespace dioxid\controller;
use dioxid\lib\Base;

/**
 * dioxid\controller$Controller
 * Baseclass for Normal Controllers
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @date 22.03.2011 14:22:47
 * @abstract
 */
abstract class Controller extends Base {

	/**
	 * GET Parameter
	 * @var array
	 * @staticvar
	 */
	protected static $params = array();

	public function __construct() { }

	/**
	 * Method: index
	 * Dummy index Action
	 */
	public static function index() { }


	/**
	 * Method: _setParamArray
	 * Set {@link $params}, mostly used by the dispatcher
	 * @param array $params
	 */
	public static function _setParamArray($params){
	    static::$params = $params;
	}

	/**
	 * Method: _addParamArray
	 * Adds an array to {@link $params}
	 * @param array $params
	 */
	public static function _addParamArray($params){
	    array_merge(static::$params, $params);
	}

	/**
	 * Method: setParam
	 * Sets an key-value pair in {@link $params}
	 * @param string $key
	 * @param string $value
	 */
	protected static function setParam($key, $value){
	    static::$params[$key] = $value;
	}

	/**
	 * Method: getParamArray
	 * Returns the complete {@link $params} array
	 * @return array
	 */
	protected static function getParamArray(){
	    return static::$params;
	}

	/**
	 * Method: getParam
	 * Gets an Paaram
	 * @param string $key
	 * @return string|bool
	 */
	protected static function getParam($key) {
        if(key_exists($key, static::$params)){
            return static::$params[$key];
        }
        return false;
	}

}

?>