<?php
/**
 * file_name  - project_name
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package package_name
 */

namespace dioxid\lib\log;

use dioxid\lib\Base;
use dioxid\config\Config;

class Log extends Base {

	private static $engine;

	private static function init(){

	}

	public static function factory($engine=null){
		if(!$engine){
			//use config default
		}
	}
}

?>