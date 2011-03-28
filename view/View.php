<?php

/**
 * View.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package view
 */

namespace dioxid\view;

use dioxid\exception\TemplateNotFoundException;

use dioxid\config\Config;
use dioxid\exception\EngineNotFoundException;

class View {
	protected static $engine;

	public static function getView($tmpl=NUll){
		$engine_class = __NAMESPACE__ . '\\engine\\' . ucfirst(Config::getVal('view', 'engine')) . 'Engine';

		if(class_exists($engine_class)) {
			static::$engine = $engine_class::getInstance();
		} else {
			throw new EngineNotFoundException;
		}




		if($tmpl){
			call_user_func(static::$engine, Config::getVal('path', 'template_path') . Config::getVal('path', 'template_path') . DIRECTORY_SEPARATOR .  $tmpl);
		} else {

			$name = end(explode("\\", __CLASS__));

			if(file_exists(Config::getVal('path', 'app_path') . Config::getVal('path', 'template_path') . '/' . $name . 'phtml')){
				call_user_func(static::$engine, Config::getVal('path', 'app_path') . Config::getVal('path', 'template_path') . DIRECTORY_SEPARATOR . $name . 'phtml');
			} else {
				throw new TemplateNotFoundException();
			}
		}
	}
}

?>