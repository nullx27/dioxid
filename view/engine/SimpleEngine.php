<?php

/**
 * SimpleEngine.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package view/engine
 */

namespace dioxid\view\engine;

use dioxid\lib\Base;
use dioxid\config\Config;
use dioxid\view\engine\InterfaceEngine;

use dioxid\exception\TemplateNotFoundException;

class SimpleEngine extends Base implements InterfaceEngine {

    public static $_file;
    public static $_output;

    public function __construct(){}

    public static function load($template=null){
        if(!$template){
          $class = get_called_class();
          $template = end(explode('\\', $class));
        }

        //fully quallified template path
        $fqtp = Config::getVal('path', 'app_path') . Config::getVal('path', 'template_path') . "/" .  $template . Config::getVal('view', 'extension');

        try {
            static::$file = file_get_contents($fqtp);
        } catch (Exception $e){
            throw TemplateNotFound;
        }
    }

    public static function show(){
        print static::$output;
        return;
    }
}

?>