<?php

/**
 * Model.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package model
 */

namespace  dioxid\model;

use dioxid\config\Config;
use dioxid\lib\Base;
use Exception;
use PDO;

abstract class Model extends Base {

    protected static $pdo = false;

    public function __construct() {
        static::createPdo();
    }

    protected static function createPdo(){
         $dsn     = Config::getVal('database', 'driver') .
                   ':host=' . Config::getVal('database', 'host') .
                   ';port=' . Config::getVal('database', 'port') .
                   ';dbname=' . Config::getVal('database', 'database');

        try {
            static::$pdo = new PDO($dsn, Config::getVal('database', 'user'), Config::getVal('database', 'password'), array());
        } catch (Exception $e) {
            print $e->getMessage();
            return false;
        }
    }

    public static function __callStatic ( $name, $args ) {
        $callback = array ( static::$pdo, $name ) ;
        return call_user_func_array ( $callback , $args ) ;
    }

    public static function factory($backend=Null){
		if(!$backend){
			$backend = Config::getVal('database', 'driver');
		}
    }
}


?>