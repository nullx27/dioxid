<?php

/**
 * InterfaceEngine.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package view/engine
 */

namespace dioxid\view;

interface InterfaceEngine {

    public static function getInstance(){}

    public static function load(){}

    public static function process(){}

    public static function show(){}
}

?>