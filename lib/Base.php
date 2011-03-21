<?php

/*
 * Author: Necro
 * File: base.php
 */

namespace dioxid\lib;

abstract class Base {

    protected static $instances = array();

    public function __construct(){}

    public static function getInstance(){
		$class = get_called_class();
		if (!isset(static::$instances[$class]))  static::$instances[$class] = new $class();
		return static::$instances[$class];
	}
}

?>