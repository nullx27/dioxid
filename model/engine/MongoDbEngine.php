<?php
/**
 * MongoDbEngine.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package package_name
 */

namespace dioxid\model\engine;

use Mongo;

use dioxid\config\Config;
use dioxid\model\engine\NoSQLEngine;

use dioxid\model\engine\mongodb\Collection;

use dioxid\error\exception\NoDatabaseTableNameException;
use dioxid\error\CantEtablishDatabaseConnectionException;


class MongoDbEngine extends NoSQLEngine {
	protected static $mongo;
	protected static $document;

	public static function _init($document){
		if(!$document) throw new NoDatabaseTableNameException();

		$con = Config::getVal('database', 'host') . ":" . Config::getVal('database', 'port');
		static::$mongo = new Mongo($con);

		if(Config::getVal('database', 'user') != "" && Config::getVal('database', 'password') != ""){
			$ret = static::$mongo->authenticate(Config::getVal('database', 'user'),
				Config::getVal('database', 'password'));

			if($ret['ok'] === 0)
				throw new CantEtablishDatabaseConnectionException($ret['errmsg']);
		}

		static::$document = static::$mongo->$document;
	}

	 public function __get($key){
		return new Collection($key, &static::$document);
	}

	final public function __destruct(){
		#static::$mongo->command(array("logout" => 1));
	}


}

?>