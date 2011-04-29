<?php

/**
 * config.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package config
 */

namespace dioxid\config;

use Exception;
use dioxid\error\exception\RequiredValNotFoundException;
use dioxid\error\exception\ConfigValueAlreadyExistsException;
use dioxid\error\exception\CantAccessConfigFileException;

/**
 * Interface to work globally with the Configfile
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @date 22.03.2011 14:16:21
 *
 */

class Config {

	/**
	 * The Config
	 * @var array
	 */
	private static $config;
	private static $path;

	/**
	 * Loads a configfile
	 * @param string $path
	 * @param bool $cahced should the config file get cached?
	 */
	public static function loadConfig($path, $cached=false){
		static::$path = $path;
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
			try{
				static::$config = unserialize(file_get_contents($cachefile));
			}
			catch (Exception $e) {
				throw new CantAccessConfigFileException($e->getMessage());
			}

		} else {
			$conf = static::loadIni($path);
			try{
				file_put_contents($cachefile, serialize(static::$config));
			}
			catch (Exception $e){
				throw new CantAccessConfigFileException('Cant create cache file');
			}
		}
	}

	/**
	 * Method: loadIni
	 * Loads the Configuration
	 * @param string $path path to the config file
	 */
	public static function loadIni($path){
		try{
			static::$config =  parse_ini_string(file_get_contents($path), true);
		}
		catch (Exception $e){
			throw new CantAccessConfigFileException();
		}
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


	public static function setVal($section, $key, $value, $override=false){
		if(!$override && @key_exists($key, static::$config[$section]))
			throw new ConfigValueAlreadyExistsException();

		static::$config[$section][$key] = $value;
	}

	public static function setValPermanent($section, $key, $value, $override=false) {
		static::setVal($section, $key, $value, $override);
		static::write_config(static::$config);
	}


	//taken and modified from: http://stackoverflow.com/questions/1268378/create-ini-file-write-values-in-php
	protected static function write_config($assoc_arr){
	    $content = "";

        foreach ($assoc_arr as $key=>$elem) {
			$content .= "[".$key."]\n";

			foreach ($elem as $key2=>$elem2) {
				$content .= "    ";
            	if(is_array($elem2)) {
					for($i=0;$i<count($elem2);$i++) {
	                	$content .= $key2."[] = ".$elem2[$i]."\n";
    	            }
				}

				else if($elem2=="") $content .= $key2." = \n";
                else $content .= $key2." = \"".$elem2."\"\n";
			}
			$content .= "\n";
        }

        try {
        	$handle = fopen(static::$path, 'w');
        	fwrite($handle, $content);
        	fclose($handle);
        }
        catch (Exception $e) {
			throw new CantAccessConfigFileException();
        }
	}
}

?>