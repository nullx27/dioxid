<?php

/*
 * Author: Necro
 * File: InterfaceEngine.php
 */

namespace dioxid\view\engine;

interface InterfaceEngine {

    public static function getInstance(){}

    public static function load(){}

    public static function process(){}

    public static function show(){}
}

?>