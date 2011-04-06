<?php
/**
 * file_name  - project_name
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package package_name
 */

namespace dioxid\lib\log;

interface LogInterface {

	/**
	 * Method: init
	 * Initialise the engine (e.g. load file, etc)
	 */
	public static function init(){}

	public static function write($msg){}
}

?>