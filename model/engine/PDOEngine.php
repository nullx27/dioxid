<?php
/**
 * file_name  - project_name
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package package_name
 */

namespace dioxid\model\engine;

use PDO;
use Exception;

use dioxid\lib\Base;
use dioxid\config\Config;
use dioxid\model\InterfaceEngine;


abstract class PDOEngine extends Base implements InterfaceEngine {

	protected static $pdo;

	public static function _init($dsn, $options=array()) {

		try {
            static::$pdo = new PDO($dsn, Config::getVal('database', 'user'), Config::getVal('database', 'password'), $options);
        } catch (Exception $e) {
            print $e->getMessage();
            return false;
        }
	}

	public function __call($name, $args){
		$callback = array ( static::$pdo, $name ) ;
        return call_user_func_array ( $callback , $args ) ;
	}

}

?>