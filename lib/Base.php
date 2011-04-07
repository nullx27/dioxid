<?php

/**
 * Base.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package lib
 */

namespace dioxid\lib;

/**
 * dioxid\lib$Base
 * Baseclass which main purpose it is to garantee the use of singletons
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @date 22.03.2011 14:54:19
 * @abstract
 */
abstract class Base {

	/**
	 * Collection of all instances and the called origin
	 * @var array
	 */
    protected static $instances = array();

    final private function __construct(){
		static::_init();
    }

	final private function  __clone() { }

	public static function _init(){
		return;
	}

    /**
     * Method: getInstance
     * Checks if an Instance for an class was allready created return the same instance. If not create an new one and
     * return it.
     */
    public static function getInstance(){
		$class = get_called_class();
		if (!isset(static::$instances[$class]))  static::$instances[$class] = new $class();
		return static::$instances[$class];
	}
}

?>