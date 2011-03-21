<?php

/*
 * Author: Necro
 * File: model.php
 */

namespace  dioxid\model;
use dioxid\lib\Base;
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
}


?>