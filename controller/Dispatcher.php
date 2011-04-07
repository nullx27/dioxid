<?php

/**
 * Dispatcher.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package controller
 */

namespace dioxid\controller;

use dioxid\error\exception\NotFoundException;

use dioxid\controller\Controller;
use dioxid\config\Config;

/**
 * dioxid\controller$Dispatcher
 * Crawls the URL for requestet Controller and Actions and calls them. At the moment the only supported
 * URL Schema is http://example.com/dispatcher_limit/controller/arction/key/value/
 *
 * If no Controller is provided the dispatcher tries to call an Index Controller, if no action is provided
 * the index action is called.
 *
 * It lackes error handling at the moment, so beware!
 *
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @date 22.03.2011 14:34:20
 *
 */
class Dispatcher {

	/**
	 * Method: dispatch
	 * Crawls the URL and calls the requested Controller and Action
	 */
	public static function dispatch() {

		// If theres no dispatcher limit dont replace anything
	    if(Config::getVal('misc', 'dispatcher_limit') != "")
		    $request = str_replace(Config::getVal('misc', 'dispatcher_limit'), '', $_SERVER['REQUEST_URI']);

		//if the first thign in the $request is a / remove it, so it cant cause later any problems
		if(substr($request, 0,1) == "/") {
		    $request = substr($request, 1);
		}

		$chunks = explode('/',$request);

		// if no controller is provided
		if($chunks[0] == ""){
			static::load(Config::getVal('misc', 'controller_namespace') . 'Index', 'index');
		    return;
		}

		$class = Config::getVal('misc', 'controller_namespace') . $chunks[0];

		//This fixes a weired bug where it tries to load a namespace without controller
		if(substr($class, -1) == "\\") return;

		//If a Controller but no Action is provided
		if(count($chunks) == 1 && $chunks[0] != ""){
			static::load($class, 'index');
		}

		//If a Controller and an Action is provided
		if(count($chunks) >= 2) {

			$method = $chunks[1];

			//Build the GET params
			$get = array_slice($chunks, 2);
			$param = array();

			for($i=0; $i<=count($get)-1; $i+=2){
				$param[ $get[$i] ] = $get[$i+1];
				if($get[$i] == "" && $get[$i+1] == "") break;
			}

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

			    //Public methods which are not intent to get called over the dispatcher have a leading underscore
			    if(substr($method, 0,1) == "_") return false;

                //if the controller extends dioxid\controller\Controller call it over an instance of the class
			    if(is_subclass_of($class, 'dioxid\controller\Controller')){

			        $instance = $class::getInstance();
				    ($params) ? $instance::_setParamArray($params): "";

				    call_user_func_array(array($instance,$method), array());
				    return true;
			    } else {

			        call_user_func_array(array($class,$method), $params ?: array());
			        return true;
			    }

			} else {
				throw new NotFoundException('Method not Found');
				return false;
			}
		} else {
			print "Class $class does not exists";
			return false;
		}
	}
}


?>