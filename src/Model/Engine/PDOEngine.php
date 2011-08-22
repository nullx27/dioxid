<?php
/**
 * PDOEngine.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package Model
 * @subpackage Database
 */

namespace dioxid\model\engine;

use PDO;
use Exception;

use dioxid\common\Base;
use dioxid\config\Config;
use dioxid\model\InterfaceEngine;

/**
 * dioxid\model\engine$PDOEngine
 * Abstract Baseclass for PDO related models.
 *
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @date 20.05.2011 16:52:12
 *
 */
abstract class PDOEngine extends Base implements InterfaceEngine {

	/**
	 * Holds the PDO Instance
	 * @var PDO
	 */
	protected static $pdo;

	/**
	 * Method: _init
	 * Constructor.
	 * Prepares the DSN and conntects to the database.
	 *
	 * @param string $dsn
	 * @param array $options holds the options for a new PDO instance
	 */
	public static function _init($dsn, $options=array()) {

		try {
            static::$pdo = new PDO($dsn, Config::getVal('database', 'user'), Config::getVal('database', 'password'), $options);
        } catch (Exception $e) {
            print $e->getMessage(); //TODO: Throw right exception!!!!
            return false;
        }
	}

	/**
	 * Method: __call
	 * Redirects all calls to this class to the PDO class
	 *
	 * @param string $name
	 * @param array $args
	 */
	public function __call($name, $args){
		$callback = array ( static::$pdo, $name ) ;
        return call_user_func_array ( $callback , $args ) ;
	}

}

?>