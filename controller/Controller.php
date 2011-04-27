<?php

/**
 * Controller.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package controller
 */

namespace dioxid\controller;
use dioxid\lib\Base;

use dioxid\view\View;

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

	protected static $baseUrl = array();

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


	public static function _setCalledUrl($url_arr){
		static::$baseUrl = $url_arr;
	}

	public static function _getBaseUrl(){
		return static::$baseUrl['scheme'] . '://' . static::$baseUrl['host'];
	}

	public static function _getBaseUrlPath(){
		return static::$baseUrl['path'];
	}

	public static function _getFullUrl(){
		return static::$baseUrl['scheme'] . '://' .
				static::$baseUrl['host'] .
				((@static::$baseUrl['port']) ? ':' .@static::$baseUrl['port'] : "") . '/' .
				static::$baseUrl['path'] .
				((@static::$baseUrl['query']) ? '?' .@static::$baseUrl['query'] : "");
	}

	public static function __getFullRequest(){
		return static::$baseUrl;
	}

	protected static function getView(){
		return View::getInstance();
	}
}

?>