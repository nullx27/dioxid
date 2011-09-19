<?php
/**
 * NoEngine.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package View
 * @subpackage Engine
 */

namespace dioxid\view\engine;

use dioxid\common\Base;
use dioxid\view\InterfaceEngine;

class NoEngine extends Base implements InterfaceEngine {

	public static function load(){
		return false;
	}

	public static function process() {
		return false;
	}

	public static function show(){
		return false;
	}
}


?>