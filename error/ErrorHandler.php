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

/**
 * dioxid\error$ErrorHandler
 * Application Exception and Errorhandler
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @date 20.04.2011 16:24:23
 *
 */
class ErrorHandler {

	/**
	 * Debug information?
	 * @var bool
	 */
	private static $debug = false;

	/**
	 * Selected Errorlevel
	 * @var int
	 */
	private static $error_level;

	/**
	 * Method: _init
	 * Sets the Errorlevel based on the Configfile
	 */
	public static function _init(){
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

	/**
	 * Method: register
	 * Register both Error- and ExceptionHandler
	 */
	public static function register(){
		static::_init();
		static::registerErrorHandler();
		static::registerExceptionHandler();
	}

	/**
	 * Method: registerErrorHandler
	 * Registers the Errorhandler
	 */
	public static function registerErrorHandler(){
		restore_error_handler();
		set_error_handler(__NAMESPACE__ . '\ErrorHandler::handleError', static::$error_level);
	}

	/**
	 * Method: registerExceptionHandler
	 * Registers the Exceptionhandler
	 */
	public static function registerExceptionHandler(){
		set_exception_handler(__NAMESPACE__ . '\ErrorHandler::exceptionHandler');
	}

	/**
	 * Method: exceptionHandler
	 * Handles Exception, prepares them and passes them to ErrorHandler
	 * @param unknown_type $exception
	 */
	public static function exceptionHandler($exception){
		static::handleError($exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine(), $exception->getTrace());
	}

	/**
	 * Method: errorHeader
	 * Sends an HTTP Header baased on the Exception/Error Code
	 * @param unknown_type $code
	 */
	private static function errorHeader($code){
		header("HTTP/1.1 " . $code);
	}

	/**
	 * Method: handleError
	 * Sends the error http code, outputs debuginfo or errorpage
	 *
	 * @param int $errno
	 * @param string $msg
	 * @param string $file
	 * @param int $line
	 * @param array $trace
	 */
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
            	$i++, @$frame["file"], @$frame["line"],
            	$frame["function"],
            	implode(", ", array_map(
                	function ($e) { return var_export($e, true); }, $frame["args"])));
   	 	}
		return $out;
	}

}

//TODO: Custom error/404 Pages for each application

?>