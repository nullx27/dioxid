<?php

/**
 * InterfaceEngine.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package view/engine
 */

namespace dioxid\view;

interface InterfaceEngine {

    public static function getInstance();

    public function load($folder, $template);

    public function assign($key, $value);

    public function process();

    public function show();

    public function finally();
}

?>