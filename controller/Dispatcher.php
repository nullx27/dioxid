<?php

/**
 * Dispatcher.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package Controller
 */

namespace dioxid\controller;

use dioxid\error\exception\NotFoundException;

use dioxid\controller\Controller;
use dioxid\config\Config;

/**
 * dioxid\controller$Dispatcher
 * Crawls the URL for requestet Controller and Actions and calls them.
 * At the moment the only supported
 * URL Schema is http://example.com/dispatcher_limit/controller/arction/key/value/
 *
 * If no Controller is provided the dispatcher tries to call an Index Controller,
 * if no action is provided the index action is called.
 *
 * It lackes error handling at the moment, so beware!
 *
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @date 22.03.2011 14:34:20
 *
 */
class Dispatcher {

	/**
	 * Is a coustom index set? If so this is an array.
	 * @var bool | array
	 * @staticvar
	 */
	static protected $customIndex = false;

	/**
	 * Array containing the custom set static routs
	 * @var array
	 * @staticvar
	 */
	static protected $staticRoutes = array();

	/**
	 * Contains the Called URL
	 * @var string
	 * @staticvar
	 */
	static protected $calledUrl = "";

	/**
	 * Method: dispatch
	 * Crawls the URL and calls the requested Controller and Action
	 */
	public static function dispatch() {


		// Build the reuqest URL
		// taken and modified from http://stackoverflow.com/questions/5216172/getting-current-url
		$req_url = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
		$req_url .= $_SERVER["HTTP_HOST"];
		$req_url .= $_SERVER["REQUEST_URI"];
		$req_url = static::$calledUrl =  parse_url($req_url);

		// If theres no dispatcher limit dont replace anything
	    if(Config::getVal('misc', 'dispatcher_limit') != "" ||
	    	Config::getVal('misc', 'dispatcher_limit') != 0) {
		    $request = str_replace(Config::getVal('misc', 'dispatcher_limit', true), '',
		    	$req_url['path']);
	    } else {
	    	$request = $req_url['path'];
	    }

        $request = trim($request, '/');

		$chunks = explode('/',$request);

		// Parse the GET Params
		$GET = array();
		$param = array();

		if(count($chunks) >= 2) $GET = array_merge($GET, array_slice($chunks, 2));

		if(@$req_url['query'] != "") {
			$pairs = explode('&', $req_url['query']);
			foreach($pairs as $pair){
				$parts = explode('=', $pair);
				$GET = array_merge($GET, $parts);
			}
		}

		for($i=0; $i<=count($GET)-1; $i+=2){
			$param[ $GET[$i] ] = $GET[$i+1];
			if($GET[$i] == "" && $GET[$i+1] == "") break;
		}

		// Match static routes before standard dispatching
		foreach (static::$staticRoutes as $route){
			$pattern = '/(' . str_replace('*', '.*', str_replace('/','\/',
				$route['route'])) . '(?:\?.*)?)/';
			if(preg_match($pattern, $request)){
				static::load(Config::getVal('misc', 'controller_namespace',true) .
					$route['class'], $route['method'], $param);
				return;
			}
		}

		// If no controller is provided
		if($chunks[0] == ""){
			if(static::$customIndex){
				static::load(Config::getVal('misc', 'controller_namespace',true) .
					static::$customIndex['class'], static::$customIndex['method']);
			} else {
				static::load(Config::getVal('misc', 'controller_namespace',true) .
					'Index', '_index', $param);
			}

		    return;
		}

		 $class = Config::getVal('misc', 'controller_namespace', true) . ucfirst($chunks[0]);

		// This fixes a weired bug where it tries to load a namespace without controller
		if(substr($class, -1) == "\\") return;

		// If a Controller but no Action is provided
		if(count($chunks) == 1 && $chunks[0] != ""){
			static::load($class, 'index', $param);
		}

		// If a Controller and an Action is provided
		if(count($chunks) >= 2) {
			$method = $chunks[1];
			static::load($class, $method, $param);
		}
	}

	/**
	 * Method: load
	 * Calls the Controller and the action and sets the params
	 * This lacks also error handling.
	 *
	 * @param string $class fully quallified class with namespace
	 * @param string $method the action which was requested
	 * @param mixed $params
	 */
	public static function load($class, $method, $params=Null) {
		if(class_exists($class)){

		    if(method_exists($class, $method)){

			    // Public methods which are not intent to get called over the
			    // dispatcher have a leading underscore
			    if(substr($method, 0,1) == "_") return false;

                // If the controller extends dioxid\controller\Controller call it
                // over an instance of the class
			    if(is_subclass_of($class, 'dioxid\controller\Controller')){

			        $instance = $class::getInstance();
				    ($params) ? $instance::_setParamArray($params): "";
				    $instance::_setCalledUrl(static::$calledUrl);

				    call_user_func_array(array($instance,$method), array());
				    return true;
			    } else {

			        call_user_func_array(array($class,$method), $params ?: array());
			        return true;
			    }

			} else {
				throw new NotFoundException('Action "'.$method.'" not Found');
				return false;
			}
		} else {
			throw new NotFoundException("Controller \"$class\" does not exists");
			return false;
		}
	}

	/**
	 * Method: registerIndex
	 * Sets an Controller and an Action which is called when no Controller
	 * and Action is provided in the url
	 *
	 * @param string $controller
	 * @param string $action
	 */
	public static function registerIndex($controller, $action){
		static::$customIndex = array('class' => $controller, 'method' => $action);
	}

	/**
	 * Method: addRoute
	 * Lets you set a Controller and an Action which gets called when your
	 * rout was requested. You can use * as a wildcard for variables.
	 *
	 * @param string $route
	 * @param string $controller
	 * @param string $action
	 */
	public static function addRoute($route, $controller, $action){
		static::$staticRoutes[] = array('route'  => $route,
										'class'  => $controller,
										'method' => $action);
	}
}


?>
