<?php

/**
 * Base.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package Common
 */

namespace dioxid\common;

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
    	$class = get_called_class();
    	if(method_exists($class, '_init')){
    		call_user_func_array(array($class, '_init'),
    			array(implode(',', func_get_args())));
    	}
    }

	final private function  __clone() { }


    /**
     * Method: getInstance
     * Checks if an Instance for an class was allready created return the same instance. If not create an new one and
     * return it.
     */
    public static function getInstance(){
		$class = get_called_class();
		$args = implode(',', func_get_args());
		if (!isset(static::$instances[$class]))  static::$instances[$class] = new $class($args);
		return static::$instances[$class];
	}
}

?>