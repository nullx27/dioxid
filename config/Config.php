<?php
namespace dioxid\config;

class Config {

	private static $config;

	public function __construct() { }

	public static function loadConfig($path){
		$config = file_get_contents($path);
		static::$config = parse_ini_string($config, true);
	}

	public static function getVal($section, $val){
		if(key_exists($section, static::$config) && key_exists($val, static::$config[$section])){
			return static::$config[$section][$val];
		}
		return false;


	}


}

?>