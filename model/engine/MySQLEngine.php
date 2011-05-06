<?php
/**
 * file_name  - project_name
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package package_name
 */

namespace dioxid\model\engine;

use dioxid\model\engine\PDOEngine;
use dioxid\config\Config;
use PDO;

class MySQLEngine extends PDOEngine {

	public static function _init(){
		$dsn = 'mysql:dbname='.Config::getVal('database', 'database', true) .
				';host='.Config::getVal('database', 'host', true);

		$options = array(
			PDO::MYSQL_ATTR_READ_DEFAULT_FILE => '/etc/my.cnf',
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
			PDO::MYSQL_ATTR_COMPRESS => true);

		parent::_init($dsn, $options);
	}
}

?>