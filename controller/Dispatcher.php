<?php
namespace dioxid\controller;
use dioxid\config\Config;

class Dispatcher {

	public static function dispatch() {

		$request = str_replace(Config::getVal('misc', 'dispatcher_limit'), '', $_SERVER['REQUEST_URI']);

		if(substr($request, 0,1) == "/") {
		    $request = substr($request, 1);
		}

		$chunks = explode('/',$request);

		if($chunks[0] == ""){
			static::load(Config::getVal('misc', 'controller_namespace') . 'Index', 'index');
		    return;
		}

		$class = Config::getVal('misc', 'controller_namespace') . $chunks[0];

		if(substr($class, -1) == "\\") return;

		if(count($chunks) == 1 && $chunks[0] != ""){
			static::load($class, 'index');
		}

		if(count($chunks) >= 2) {

			$method = $chunks[1];

			//Build the GET params
			$us_param = array_slice($chunks, 2);

			for($i=0; $i<=count($us_param)-1; $i+=2){
				$param[ $us_param[$i] ] = $us_param[$i+1];
				if($us_param[$i] == "" || $us_param[$i+1] == "") break;
			}

			static::load($class, $method, $param);
		}
	}

	public static function load($class, $method, $params=Null) {
		if(class_exists($class)){

		    if(method_exists($class, $method)){

			    //Public methods which are not intent to get called over the dispatcher have a leading underscore
			    if(substr($method, 0,1) == "_") return false;

                //if the controller extends dioxid\controller\Controller call it over an instance of the class
			    if(method_exists($class, 'getInstance')){

			        $instance = $class::getInstance();
				    ($params) ? $instance::_setParamArray($params): "";

				    call_user_func_array(array($instance,$method), array());
				    return true;
			    } else {

			        call_user_func_array(array($class,$method), $params ?: array());
			        return true;
			    }

			} else {
				print "Method $method does not exists";
				return false;
			}
		} else {
			print "Class $class does not exists";
			return false;
		}
	}
}


?>