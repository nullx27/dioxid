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
use dioxid\view\engine\simpleEngine\ContextSandbox;

use dioxid\error\exception\TemplateNotFoundException;

class SimpleEngine extends Base implements InterfaceEngine {

	/**
	 * Fullpath to the template
	 * @var mixed
	 */
    protected $_template = false;

    /**
     * The process template
     * @var string
     */
    protected $_output = false;

    /**
     * The template Variables
     * @var array
     */
    protected $_vars = array();


    protected $_disallow_output = false;

    protected $_layout = false;

    protected $_helper = array();

    /**
     * Class Constructor
     * Tries to load the template from the template folder
     * If in the templatefolder an folder named like the controller exists
     * and in this folder is an template named after the action, which was
     * called, it will be loaded as a template
     */
    public function _init() {

    	$trace=debug_backtrace();

    	//TODO: This is hardcoded but shoulnd't be!
		$caller = $trace[10];

		$folder = end(explode('\\', $caller['class']));

		$template = $caller['function'];

		try {
			$this->load($folder, $template, true);
		}
		catch (TemplateNotFoundException $e) {}

		try {
			$folder = (Config::getVal('view', 'layout_folder')) ?
				(Config::getVal('view', 'layout_folder')) : false;

			$template = (Config::getVal('view', 'layout_name')) ?
				(Config::getVal('view', 'layout_name')) : false;

			$this->loadLayout($folder, $template,true);
		}
		catch (TemplateNotFoundException $e){}
    }

    /**
     * Method: load
     * Loads the Template
     * @param string $folder the template folder
     * @param string $template the template name
     * @param bool $without_ext extent the templateextension to the templatename automaticly
     */
    public function load($folder, $template, $without_ext=false){
        if(!$template || !$folder){
          throw new TemplateNotFoundException('No template provided');
        }

        $fqtp = Config::getVal('path', 'app_path') .
        		Config::getVal('path', 'template_path') .
        		DIRECTORY_SEPARATOR .
        		$folder .
        		DIRECTORY_SEPARATOR .
        		$template .
				($without_ext ? Config::getVal('view', 'extension') : "");

        if(file_exists($fqtp)){
        	$this->_template = $fqtp;
        } else {
        	throw new TemplateNotFoundException("$template not found");
        }
    }

    /**
     * Method: process
     * Processes the Template
     * @throws TemplateNotFoundException
     */
    public function process(){
		if(!$this->_template) throw new TemplateNotFoundException('No template loaded!');

		$layout = ($this->_layout) ? new ContextSandbox(
			$this->_layout, $this->_vars, &$this->_helper) : false;
		$content = new ContextSandbox(
			$this->_template, $this->_vars, &$this->_helper);

		if($layout) {
			$this->_output = str_replace(
				Config::getVal('view', 'content_variable', true), $content, $layout);
			return ;
		}
		$this->_output = $content;
    }


    /**
     * Method: disableOutput
     * Disables automatic output on destruction of the object
     */
    public function disableOutput(){
		$this->_disallow_output = true;
    }

    public function disableLayout(){
    	$this->_layout =false;
    }

    /**
     * Method: getOutput
     * Procresses the template if its not procressed and returns the output
     * @return string static::$_output
     */
    public function getOutput(){
    	if(!$this->_output)
    		$this->process();
        return $this->_output;
    }

    public function __set($key, $val){

    	$this->assign($key, $val);
    }

    /**
     * Method: assign
     * Assigns an Element to an key, so it kann be called in the template
     * @param string $key
     * @param mixed $val
     */
    public function assign($key, $val){
    	$this->_vars[$key] = $val;
    }

    /**
     * Method: assignArray
     * Assings an Array of key/value pairs to the template
     * @param unknown_type $arr
     */
    public function assignArray($arr){
		array_merge($this->_vars, $arr);
    }

    /**
     * Method: show
     * Prints the Processed template
     * @throws TemplateNotFoundException
     */
    public function show(){
    	if(!$this->_output) throw new TemplateNotFoundException("Template not processed!");
        print $this->_output;
    }

    public function loadLayout($folder=false, $template=false, $w_ext=false){
		$path = $fqtp = Config::getVal('path', 'app_path') .
        		Config::getVal('path', 'template_path') . DIRECTORY_SEPARATOR;

        $path .= ($folder ? $folder : "layout") . DIRECTORY_SEPARATOR;
        $path .= $template ? $template : "default" . Config::getVal('view', 'extension');
        $path .= ($w_ext) ? Config::getVal('view', 'extension') : "";

		if(file_exists($path)){
			$this->_layout = $path;
		} else {
			throw new TemplateNotFoundException("Layout not found in $path");
		}
    }

    public function handleHelper($name, $instance){
		$this->_helper[$name] = $instance;
    }

    //TODO: Add Sendboxcode here
    public function __get($key){

		if(array_key_exists($key, $this->_helper))
    		return $this->_helper[$key];
    }

    /**
     * Method: finally
     * Calls the process and show function of this class
     */
    public function finally(){
    	if(!$this->_template){
    		print "No file loaded when __destruct was called";
    	}
    	try {
    		if(!$this->_disallow_output){
    			$this->process();
    			$this->show();
    		}
    	}
    	catch(Exception $e){}
    }

}

?>