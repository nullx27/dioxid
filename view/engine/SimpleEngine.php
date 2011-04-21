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

	/**
	 * Fullpath to the template
	 * @var mixed
	 */
    protected static $_file = false;

    /**
     * The process template
     * @var string
     */
    protected static $_output = false;

    /**
     * The template Variables
     * @var array
     */
    protected static $_vars = array();


    protected static $_disallow_output = false;

    /**
     * Class Constructor
     * Tries to load the template from the template folder
     * If in the templatefolder an folder named like the controller exists
     * and in this folder is an template named after the action, which was
     * called, it will be loaded as a template
     */
    public static function _init() {

    	$trace=debug_backtrace();
		$caller = $trace[6];
		$folder = end(explode('\\', $caller['class']));
		$template = $caller['function'];

		try {
			static::load($folder, $template, true);
		}
		catch (TemplateNotFoundException $e) {}
    }

    /**
     * Method: load
     * Loads the Template
     * @param string $folder the template folder
     * @param string $template the template name
     * @param bool $without_ext extent the templateextension to the templatename automaticly
     */
    public static function load($folder, $template, $without_ext=false){
        if(!$template || !$folder){
          throw new TemplateNotFoundException('No template provided');
        }

        //fully quallified template path
        if($without_ext) {
        	$fqtp = Config::getVal('path', 'app_path') .
        		Config::getVal('path', 'template_path') . DIRECTORY_SEPARATOR .
        		$folder . DIRECTORY_SEPARATOR . $template .
        		Config::getVal('view', 'extension');
        } else {
        	$fqtp = Config::getVal('path', 'app_path') .
        	Config::getVal('path', 'template_path') . "/" .  $template;
        }

        if(file_exists($fqtp)){
        	static::$_file = $fqtp;
        } else {
        	throw new TemplateNotFoundException("$template not found");
        }
    }



    /**
     * Method: process
     * Processes the Template
     * @throws TemplateNotFoundException
     */
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


    /**
     * Method: disableOutput
     * Disables automatic output on destruction of the object
     */
    public static function disableOutput(){
		static::$_disallow_output = true;
    }

    /**
     * Method: getOutput
     * Procresses the template if its not procressed and returns the output
     * @return string static::$_output
     */
    public static function getOutput(){
    	if(!static::$_output)
    		static::process();
        return static::$_output;
    }

    /**
     * Method: assign
     * Assigns an Element to an key, so it kann be called in the template
     * @param string $key
     * @param mixed $val
     */
    public static function assign($key, $val){
    	static::$_vars[$key] = $val;
    }

    /**
     * Method: assignArray
     * Assings an Array of key/value pairs to the template
     * @param unknown_type $arr
     */
    public static function assignArray($arr){
		array_merge(static::$_vars, $arr);
    }

    /**
     * Method: show
     * Prints the Processed template
     * @throws TemplateNotFoundException
     */
    public static function show(){
    	if(!static::$_output) throw new TemplateNotFoundException("Template not processed!");
        print static::$_output;
    }

    public static function loadLayout(){
		//TODO:: loadLayout
    }

    /**
     * Method: finally
     * Gets called in the destructor of the View Object
     * Calls the process and show function of this class
     */
    public static function finally(){
    	if(!static::$_disallow_output){
    		static::process();
    		static::show();
    	}
    }
}

?>