<?php

/*
 * Author: Necro
 * File: RestfulController.php
 * Desc:
 */

namespace dioxid\controller;

use dioxid\controller\Controller;

class RestfulController extends Controller {


    protected static $allowed_methods = array(
                                            'create' => array('PUT'),
                                            'read' => array('GET'),
                                            'update' => array('POST'),
                                            'delete' => array('DELETE'),
                                            'info' => array('OPTIONS'));

    protected static function useREST($callbacks = array(), $class=Null){

        if(!is_array($callbacks)) return false;


        $method = $_SERVER['REQUEST_METHOD'];

        if(!$class) $class = get_called_class();

        if(in_array($method, static::$allowed_methods['create'])) {

            print_r(call_user_func_array(array($class, $callbacks['create']), array($put_handler)));
            return;
        }

       elseif(in_array($method, static::$allowed_methods['read'])){

            call_user_func(array($class, $callbacks['read']));
            return;
        }

        elseif(in_array($method, tatic::$allowed_methods['update'])){
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