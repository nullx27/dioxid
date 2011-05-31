<?php

/**
 * RestfulController.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package Controller
 */

namespace dioxid\controller;

use dioxid\controller\Controller;

/**
 * dioxid\controller$RestfulController
 * This is a Basecontroller for the use of RESTFUL requests in form of a CRUD interface.
 * To use this, just extend it and call static::useREST in your action and provide it with a assoc array in
 * the schema of 'method' => 'callback' where method is create, read, update or delete (CRUD)
 *
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @date 22.03.2011 14:45:23
 *
 */
class RestfulController extends Controller {

	/**
	 * Allowd Methods for the CRUD interface
	 * @var array
	 */
    protected static $allowed_methods = array(
                                            'create' => array('PUT'),
                                            'read' => array('GET'),
                                            'update' => array('POST'),
                                            'delete' => array('DELETE'),
                                            'info' => array('OPTIONS'));

    /**
     * Method: useREST
     * Checks the REQUEST_METHOD and calls a callback for each provided method.
     * Additonally you can provided a fully quallified class with namespace in which the callback should be called.
     * By default the class which registerd the callbacks will be used.
     *
     * @param array $callbacks
     * @param mixed $class default null
     *
     */
    protected static function useREST($callbacks = array(), $class=Null){

        if(!is_array($callbacks)) return false;

        $method = $_SERVER['REQUEST_METHOD'];

        if(!$class) $class = get_called_class();

        if(in_array($method, static::$allowed_methods['create'])) {
			//If Create were requested the callback gets called with an filediscriptor to the putdata
			$putdata = fopen("php://input", "r");
            call_user_func_array(array($class, $callbacks['create']), array(&$putdata));
            return;
        }

       elseif(in_array($method, static::$allowed_methods['read'])){

            call_user_func(array($class, $callbacks['read']));
            return;
        }

        elseif(in_array($method, static::$allowed_methods['update'])){
            call_user_func(array($class, $callbacks['update']));
            return;
        }

        elseif(in_array($method, static::$allowed_methods['delete'])){
            call_user_func(array($class, $callbacks['delete']));
            return;
        }

        elseif(in_array($method, static::$allowed_methods['info'])){
            return json_encode(array_keys($callbacks));
            return;
        }

        else {
            return false;
        }


    }
}

?>