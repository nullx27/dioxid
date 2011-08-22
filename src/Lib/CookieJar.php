<?php
/**
 * CookieJar.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package Lib
 * @subpackage Cookiejar
 */

namespace dioxid\lib;

use Exception;
use Serializable;
use dioxid\lib\cookiejar\exception\CouldNotWriteToCacheException;
use dioxid\lib\cookiejar\exception\CookieNonInRegistryException;

use dioxid\lib\cookiejar\Cookie;
use dioxid\config\Config;

class CookieJar {

	protected static $registry = array();

	private static function checkCache(){
		$file = Config::getVal('path', 'app_path', true) . DIRECTORY_SEPARATOR .
			Config::getVal('path', 'cache', true) . DIRECTORY_SEPARATOR . "cookie.dat";
		if(!file_exists($file)){
			try{
				$h = fopen($file, 'x+');
				fclose($h);
			}
			catch (Exception $e){
				throw new CouldNotWriteToCacheException($e->getMessage);
			}
		}
	}

	private static function save(){
		$file = Config::getVal('path', 'app_path', true) . DIRECTORY_SEPARATOR .
			Config::getVal('path', 'cache', true) . DIRECTORY_SEPARATOR . "cookie.dat";

		static::cleanUp();

		$data = serialize(static::$registry);

		try {
			file_put_contents($file, $data);
		}
		catch(Exception $e){
			throw new CouldNotWriteToCacheException($e->getMessage());
		}
	}

	public static function load(){
		$file = Config::getVal('path', 'app_path', true) . DIRECTORY_SEPARATOR .
			Config::getVal('path', 'cache', true) . DIRECTORY_SEPARATOR . "cookie.dat";

		try {
			$data = file_get_contents($file);
		}
		catch(Exception $e){
			throw new CouldNotWriteToCacheException($e->getMessage());
		}


		static::$registry = unserialize($data);
		static::cleanUp();
	}

	private static function cleanUp(){
		foreach(static::$registry as $domain => $cookie){
			if($cookie->isExpiered()){
				unset($cookie);
				unset(static::$registry[$domain]);
			}
		}
		static::$registry = array_values(static::$registry);
	}

	public static function setCookie($domain, $data, $expiere=350){
		if(!key_exists($domain, static::$registry)){
			static::$registry[$domain] = new Cookie($expiere);
		}

		static::$registry[$domain]->addData($data);
		print_r(static::$registry[$domain]->data);
		static::save();
	}

	public static function setCookieString($domain, $str, $expiere=350){
		$months = array(
			"Jan" => 1,
			"Feb" => 2,
			"Mar" => 3,
			"Apr" => 4,
			"May" => 5,
			"Jun" => 6,
			"Jul" => 7,
			"Aug" => 8,
			"Sep" => 9,
			"Oct" => 10,
			"Nov" => 11,
			"Dec" => 12
		);

		$chunks = explode(";", $str);
		$data = array();
		foreach ($chunks as $chunk){
			$d = explode("=",$chunk);
			$data[$d[0]] = $d[1];
		}

		if(key_exists('expires', $data)){
			$datec = explode(",", $data['expires']);
			$ch = explode(" ", $datec);
			$da = explode("-", $ch[1]);
			$time = explode(":", $ch[1]);

			$exp = mktime($time[0],$time[1],$time[3], $months[$da[1]], $da[0], $da[2]);
		}

		static::setCookie($domain, $data, $exp);
	}

	public static function setClientCookie($domain){
		if(!key_exists($domain, static::$registry))
			throw new CookieNonInRegistryException();
		$cookie = &static::$registry[$domain];

		foreach($cookie->data as $k => $v){
			setcookie($k, $v, $cookie->expiere);
		}
	}

	public static function getCookie($domain){
		static::load();
		if(key_exists($domain, static::$registry))
			return static::$registry[$domain]->data;
		return false;
	}

	public static function getCookieValue($domain, $key){
		static::load();
		if(key_exists($domain, static::$registry))
			if(key_exists($key, static::$registry[$domain]->data)){
				return static::$registry[$domain]->data[$key];
			}
		return false;
	}

	public static function getCookieString($domain){
		if(!key_exists($domain, static::$registry))
			return false;

		$cookie = &static::$registry[$domain];
		$out = "";
		foreach ($cookie->data as $key => $val){
			$out .= $key . "=" . $val . ";";
		}
		return $out;
	}

	public static function getClientCookie(){
		$out = array();
		foreach ($_COOKIE as $key => $val){
			$out[$key] = $val;
		}

		return $out;
	}

	public static function debug (){
		print_r(static::$registry);
	}

	public static function getCookieFromWebsite($url){

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		$out = curl_exec($ch);

		preg_match_all('/^Set-Cookie: (.*?);/m',$out , $m);
		$o = array();
		foreach($m[1] as $k => $v) {
			$a = explode("=", urldecode($v));
			$o[$a[0]] = $a[1];
		}

		return $o;

	}

	public static function domainExists($domain){
		static::load();
		return key_exists($domain, static::$registry);
	}
}

?>