<?php
/**
 * file_name  - project_name
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package package_name
 */

namespace dixoid\model\engine;

use PDO;
use Exception;

use dioxid\lib\Base;
use dioxid\config\Config;
use dioxid\model\InterfaceEngine;

abstract class PDOEngine extends Base implements InterfaceEngine {

	protected static $connection;

	abstract public static function _init($dsn) {

		try {
            static::$pdo = new PDO($dsn, Config::getVal('database', 'user'), Config::getVal('database', 'password'), array());
        } catch (Exception $e) {
            print $e->getMessage();
            return false;
        }
	}

	abstract public function __callstatic($name, $args){
		$callback = array ( static::$connection, $name ) ;
        return call_user_func_array ( $callback , $args ) ;
	}
}

?>