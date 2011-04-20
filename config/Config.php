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

	public function __construct() { }

	/**
	 * Loads a configfile
	 * @param string $path
	 * @param bool $cahced should the config file get cached?
	 */
	public static function loadConfig($path, $cached=false){
		$cached ? static::loadCached($path) : static::loadIni($path);
	}

	/**
	 * Method: loadCached
	 * Loads the cached config file or if the config is not cached,
	 * it gets cached
	 * @param string $path path to the config file
	 */
	public static function loadCached($path){
		$tmp = explode('/', $path);
		$conf = end($tmp);
		$folder = substr($path, 0, (strlen($path) - strlen($conf)));

		$cachefile = $folder . ".". $conf . ".cache";
		if(file_exists($cachefile) && filectime($cachefile) > filectime($path)){
			static::$config = unserialize(file_get_contents($cachefile));
		} else {
			$conf = static::loadIni($path);
			file_put_contents($cachefile, serialize(static::$config));
		}
	}

	/**
	 * Method: loadIni
	 * Loads the Configuration
	 * @param string $path path to the config file
	 */
	public static function loadIni($path){
		//TODO: Try/catch that fucker if it fails
		$time = microtime(true);
		static::$config =  parse_ini_string(file_get_contents($path), true);
	}

	/**
	 * Method: getVal
	 * Gets a Value form the Config
	 * @param string $section Specify the Section
	 * @param string $val the Key
	 */
	public static function getVal($section, $val, $required = false){
		if(key_exists($section, static::$config) && key_exists($val, static::$config[$section])){
			return static::$config[$section][$val];
		}

		if($required) throw new RequiredValNotFoundException("Required Val \"$val\" not found in $section");

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

	//TODO: add new values permanent or temp

}

?>