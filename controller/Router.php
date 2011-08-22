<?php
/**
 * Router.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package package_name
 */

namespace dioxid\controller;

class Router {

	protected static $routes = array();

	/**
	 * Method: register
	 * Registers a new Static Route
	 * Regex can be used here.
	 * Example: Router:registerStaticRoutes(':lang:/index/:bar:', array('index','index);
	 *	You have access to your defined variables like normal variables.
	 *	Eg. in your controller: static::getParam('lang') would return the :lang:
	 *	variable.
	 *
	 * @param string $route The Static route
	 * @param array $caller Assoc Array for Controller => Action pair
	 */
	public static function registerStaticRoutes($route, Array $callback) {
		static::$routes[] = array($route => $callback);
	}

	/**
	 * Method: registerStaticRoutesArray
	 *
	 * Registers an Array of Static Routes
	 *
	 * @param Array $routes has to have the format: array(route, array(class, method), ...)
	 */
	public static function registerStaticRoutesArray(Array $routes){
		foreach($routes as $route){
			foreach($route as $r => $callback){
				static::registerStaticRoutes($r, $callback);
			}
		}
	}

	/**
	 * Method: matchRoutes
	 *
	 * @param String $request
	 */
	public static function matchRoutes($request){
		foreach (static::$routes as $counter){
			foreach($counter as $route => $callback){

				if(preg_match('/'.preg_replace('/(\:.*\:)/', '.*', str_replace('/','\/', $route)).'/', $request)){

					$var = explode('/', $route);
					$val = explode('/', $request);
					$param = array();

					for($i=0;$i<count($var);$i++){
						if(preg_match('/:.*:/', $var[$i])){
							$param[str_replace(':', '', $var[$i])] = $val[$i];
						}
					}

				$return = array(
							'class' => ucfirst(key($callback)),
							'method' => current($callback),
							'param' => $param
						);
				return $return;

				}
			} //end foreach
		} //end foreach
		return false;
	}

}

?>