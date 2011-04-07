<?php

/**
 * SimpleEngine.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package view/engine
 */

namespace dioxid\view\engine;

use dioxid\view\View;

use Exception;
use dioxid\lib\Base;
use dioxid\config\Config;
use dioxid\view\InterfaceEngine;

use dioxid\error\exception\TemplateNotFoundException;

class SimpleEngine extends Base implements InterfaceEngine {

    protected static $_file = false;
    protected static $_output = false;
    protected static $_vars = array();

    public static function _init() {

    	$trace=debug_backtrace();
		$caller = $trace[6];
		$folder = end(explode('\\', $caller['class']));
		$template = $caller['function'];

		try {
			static::load($folder, $template, true);
		}
		catch (TemplateNotFoundException $e) {

		}
    }

    public static function load($folder, $template, $without_ext=false){
        if(!$template || !$folder){
          throw new TemplateNotFoundException('No template provided');
        }

        //fully quallified template path
        if($without_ext) {
        	$fqtp = Config::getVal('path', 'app_path') . Config::getVal('path', 'template_path') . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $template . Config::getVal('view', 'extension');
        } else {
        	$fqtp = Config::getVal('path', 'app_path') . Config::getVal('path', 'template_path') . "/" .  $template;
        }

        if(file_exists($fqtp)){
        	static::$_file = $fqtp;
        } else {
        	throw new TemplateNotFoundException("$template not found");
        }
    }

    public static function process(){
		if(!static::$_file) throw new TemplateNotFoundException('No template loaded!');

    	extract(static::$_vars);
		try {
			ob_start();
			@include(static::$_file);
			$__content = ob_get_contents();
			ob_end_clean();
		} catch (Exception $e) {
			throw new TemplateNotFoundException($e->getMessage());
		}

		static::$_output = $__content;
    }

    public static function assign($key, $val){
    	static::$_vars[$key] = $val;
    }

    public static function assignArray($arr){
		foreach($arr as $key => $val)
			static::assign($key, $val);
    }

    public static function show(){
    	if(!static::$_output) throw new TemplateNotFoundException("Template not processed!");
        print static::$_output;
    }

    public static function finally(){
    	static::process();
    	static::show();
    }
}

?>