<?php
/**
 * BaseUrlHelper.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package View
 * @subpackage Helper
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
			((@$this->baseUrl['port']) ? ':' .@$this->baseUrl['port'] : "") .
			 (Config::getVal('misc', 'dispatcher_limit') ? '/' . trim(Config::getVal('misc', 'dispatcher_limit'), '/') :"");
	}

	public function add($path){
		return $this->base() . '/' .  trim($path, "/");
	}

	public function setProtocol($proto){
		$this->baseUrl['scheme'] = $proto;
	}

	public function __toString(){
		return $this->base();
	}

}

?>
