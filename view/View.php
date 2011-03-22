<?php

/**
 * View.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package view
 */

namespace dioxid\view;

use dioxid\config\Config;
use dioxid\lib\Base;

require_once __DIR__ . '/../lib/h2o/h2o.php';

class View extends Base {

    protected static $h2o;

    public function __construct($tmpl, $class){
        if(!$tmpl){

        }
    }

    public static function getView($tmpl=NUll){
		$class = get_called_class();
		if (!isset(static::$instances[$class]))  static::$instances[$class] = new $class($tmpl, $class);
		return static::$instances[$class];
	}
}

?>