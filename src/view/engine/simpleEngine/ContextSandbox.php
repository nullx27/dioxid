<?php
/**
 * ContextSandbox.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package View
 * @subpackage Engine
 */

namespace dioxid\view\engine\simpleEngine;

use Exception;
use dioxid\view\exception\TemplateNotFoundException;

class ContextSandbox {

	protected $helper = array();
	protected $__output = false;

	public function __construct($__path, $__context, $__helper){

		$this->helper = $__helper;

		extract($__context);

		unset($__context);
		unset($__helper);

		try {
			ob_start();
			@include($__path);
			$this->__output = ob_get_clean();
		} catch (Exception $e) {
			throw new TemplateNotFoundException($e->getMessage());
		}
	}

	public function __get($key){

		if(array_key_exists($key, $this->helper))
			return $this->helper[$key];
	}

	public function __toString(){
		return $this->__output;
	}
}

?>