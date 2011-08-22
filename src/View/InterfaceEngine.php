<?php

/**
 * InterfaceEngine.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package View
 * @subpackage Engine
 */

namespace dioxid\view;

interface InterfaceEngine {

    public static function getInstance();

    public function load($folder, $template);

    public function assign($key, $value);

    public function process();

    public function show();

    public function handleHelper($name, $instance);

    public function finally();

}

?>