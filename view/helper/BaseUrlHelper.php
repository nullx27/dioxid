<?php
/**
 * BaseUrlHelper.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package package_name
 */

namespace dioxid\view\helper;

use dioxid\lib\Base;
use dioxid\config\Config;

class BaseUrlHelper {

	protected $baseUrl = false;

	public function __construct(){
		$trace = debug_backtrace();
		$caller = $trace[8];
		$class = $caller['class'];
		if(is_subclass_of($class, 'dioxid\controller\Controller')){
			$this->baseUrl = $class::__getFullRequest();
		}
	}

	public function base(){
		return $this->baseUrl['scheme'] . '://' . $this->baseUrl['host'] .
			(@$this->baseUrl['port'])? ":".$this->baseUrl['port'] :"" .
			(Config::getVal('misc', 'dispatcher_limit') !=0) ?
		 	DIRECTORY_SEPARATOR . Config::getVal('misc', 'dispatcher_limit'):"";
	}

	public function add($path){
		return $this->base() . '/' .  trim($path, "/");
	}

	public function changeProtocol($proto){
		return $proto . '://' . $this->baseUrl['host'];
	}

	public function __toString(){
		return $this->base();

	}

}

?>