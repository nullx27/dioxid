<?php
/**
 * ErrorHandler.php  - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package Exception
 */

namespace dioxid\error;

use dioxid\lib\Base;
use dioxid\config\Config;

class ErrorHandler extends Base{
	private static $debug = false;
	private static $error_level;

	public static function init(){
		if(Config::getVal('error', 'debug') != 1) static::$debug = true;

		switch (Config::getVal('error', 'level')){
			case 'notice':
				static::$error_level = E_NOTICE;
				break;
			case 'warning':
				static::$error_level = E_WARNING & ~E_NOTICE;
				break;
			case 'error':
				static::$error_level = E_ERROR;
				break;
			case 'all':
				static::$error_level = E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING;
				break;
			default:
				static::$error_level = E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING;
				break;
		}
	}

	public static function register(){

		static::registerErrorHandler();
		static::registerExceptionHandler();
	}

	public static function registerErrorHandler(){
		restore_error_handler();
		set_error_handler(__NAMESPACE__ . '\ErrorHandler::handleError', static::$error_level);
	}

	public static function registerExceptionHandler(){
		set_exception_handler(__NAMESPACE__ . '\ErrorHandler::exceptionHandler');
	}

	public static function exceptionHandler($exception){
		static::handleError($exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine(), $exception->getTrace());
	}

	private static function errorHeader($code){
		header("HTTP/1.1 " . $code);
	}

	public static function handleError($errno, $msg, $file, $line, $trace=null){
		if($errno == 0) $errno = 500;
		static::errorHeader($errno);

		if(static::$debug){
			ob_start();
			@include 'static/error.html';
			$out = ob_get_contents();
			ob_end_clean();
			die($out);
		} else {
			if(!$trace) $trace = debug_backtrace();

			$stacktrace = static::prettyStacktrace($trace);
			ob_start();
			@include 'static/error_debug.html';
			$out = ob_get_contents();
			ob_end_clean();
			die(strtr($out, array('$msg' => $msg, '$file' => $file, '$line' => $line, '$stactrace' => $stacktrace )));
		}
	}

	/**
	 *
	 * Method: prettyStacktrace
	 * Shamelessly taken from http://stackoverflow.com/questions/3481419/how-to-disable-php-cutting-off-parts-of-long-arguments-in-exception-stack-trace
	 * and slightly modified
	 * @param unknown_type $trace
	 */
	private static function prettyStacktrace($trace){
		$out = "";
		$i = 0;
		foreach ($trace as $frame) {
        	$out .= sprintf("#%d %s(%d): %s(%s)<br />\n",
            	$i++, $frame["file"], $frame["line"],
            	$frame["function"],
            	implode(", ", array_map(
                	function ($e) { return var_export($e, true); }, $frame["args"])));
   	 	}
		return $out;
	}

}

?>