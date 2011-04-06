<?php

/**
 * config.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package config
 */

namespace dioxid\config;

/**
 * Interface to work globally with the Configfile
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @date 22.03.2011 14:16:21
 *
 */
use dioxid\error\exception\RequiredValNotFoundException;

class Config {

	/**
	 * The Config
	 * @var array
	 */
	private static $config;

	private static $tmpConfig = array();

	public function __construct() { }

	/**
	 * Loads a configfile
	 * @param string $path
	 */
	public static function loadConfig($path){
		$config = file_get_contents($path);
		static::$config = parse_ini_string($config, true);
	}



	/**t
	 * Method: getVal
	 * Gets a Value form the Config
	 * @param string $section Specify the Section
	 * @param string $val the Key
	 */
	public static function getVal($section, $val, $required = false){
		if(key_exists($section, static::$config) && key_exists($val, static::$config[$section])){
			return static::$config[$section][$val];
		}

		if($required) throw new RequiredValNotFoundException("Required Val, $val not found in $section");

		return false;

	}

	/**
	 * Method: getSection
	 * Reutrns a complete section as an Array
	 * @param string $section
	 * @retrun array
	 */
	public static function getSection($section){
		if(key_exists($section, static::$config)){
			return static::$config[$section];
		}
	}


	//TODO: YAML Configfile

	//TODO: cache file

	//TODO: add new values permanent or temp

}

?>