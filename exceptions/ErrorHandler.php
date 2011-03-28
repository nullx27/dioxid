<?php
/**
 * ErrorHandler.php  - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package Exception
 */

namespace dioxid\exception;

use dioxid\lib\Base;

class ErrorHandler extends Base{

	//Shamelessly taken from Tom Frosts Hydrogen ;)
	// 2xx Successful
	const HTTP_OK = "200 OK";
	const HTTP_CREATED = "201 Created";
	const HTTP_ACCEPTED = "202 Accepted";
	const HTTP_NONAUTHORITATIVE_INFORMATION = "203 Non-Authoritative Information";
	const HTTP_NO_CONTENT = "204 No Content";
	const HTTP_RESET_CONTENT = "205 Reset Content";
	const HTTP_PARTIAL_CONTENT = "206 Partial Content";

	// 3xx Redirection
	const HTTP_MULTIPLE_CHOICES = "300 Multiple Choices";
	const HTTP_MOVED_PERMANENTLY = "301 Moved Permanently";
	const HTTP_FOUND = "302 Found";
	const HTTP_SEE_OTHER = "303 See Other";
	const HTTP_NOT_MODIFIED = "304 Not Modified";
	const HTTP_USE_PROXY = "305 Use Proxy";
	const HTTP_TEMPORARY_REDIRECT = "307 Temporary Redirect";

	// 4xx Client Error
	const HTTP_BAD_REQUEST = "400 Bad Request";
	const HTTP_UNAUTHORIZED = "401 Unauthorized";
	const HTTP_PAYMENT_REQUIRED = "402 Payment Required";
	const HTTP_FORBIDDEN = "403 Forbidden";
	const HTTP_NOT_FOUND = "404 Not Found";
	const HTTP_METHOD_NOT_ALLOWED = "405 Method Not Allowed";
	const HTTP_NOT_ACCEPTABLE = "406 Not Acceptable";
	const HTTP_PROXY_AUTHENTICATION_REQUIRED = "407 Proxy Authentication Required";
	const HTTP_REQUEST_TIMEOUT = "408 Request Timeout";
	const HTTP_CONFLICT = "409 Conflict";
	const HTTP_GONE = "410 Gone";
	const HTTP_LENGTH_REQUIRED = "411 Length Required";
	const HTTP_PRECONDITION_FAILED = "412 Precondition Failed";
	const HTTP_REQUEST_ENTITY_TOO_LARGE = "413 Request Entity Too Large";
	const HTTP_REQUEST_URI_TOO_LONG = "414 Request-URI Too Long";
	const HTTP_UNSUPPORTED_MEDIA_TYPE = "415 Unsupported Media Type";
	const HTTP_REQUESTED_RANGE_NOT_SATIFSIABLE = "416 Requested Range Not Satisfiable";
	const HTTP_EXPECTATION_FAILED = "417 Expectation Failed";

	// 5xx Server Error
	const HTTP_INTERNAL_SERVER_ERROR = "500 Internal Server Error";
	const HTTP_NOT_IMPLEMENTED = "501 Not Implemented";
	const HTTP_BAD_GATEWAY = "502 Bad Gateway";
	const HTTP_SERVICE_UNAVAILABLE = "503 Service Unavailable";
	const HTTP_GATEWAY_TIMEOUT = "504 Gateway Timeout";
	const HTTP_HTTP_VERSION_NOT_SUPPORTED = "505 HTTP Version Not Supported";

	public static $debug = false;

	public static function register(){
		static::registerErrorHandler();
		static::registerExceptionHandler();
	}

	public static function debug(){
		static::$debug = true;
	}

	public static function registerErrorHandler(){
		set_error_handler(__NAMESPACE__ . 'ErrorHandler::errorHandler', E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
	}

	public static function registerExceptionHandler(){
		set_exception_handler(__NAMESPACE__ . 'ErrorHandler::exceptionHandler');
	}

	public static function errorHandler($errno, $msg, $file, $line){
		if($errno == E_ERROR) {

		} else {

		}
	}

	public static function exceptionHandler($exception){
		static::errorHandler($exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine());
	}

	public static function errorHeader($code) {
		header("HTTP/1.1 " . $code);
	}

	public static function errorPage(){
		$out .= "<html>";
		$out .= "<head>";
		$out .= "</head>";
		$out .= "<body>";

		if(static::$debug){
			$out .= "<div>";
			$out .= static::prettyBacktrace(debug_backtrace());
			$out .= "</div>";
		}

		$out .= "</body>";
		$out .= "</html>";
	}

	public static function prettyBacktrace($backtrace){

	}


}

?>