<?php

/*
 * Author: Necro
 * File: Csv.php
 */

namespace dioxid\lib;

use dioxid\lib\Base;
use Exception;

class CSV extends Base {

    public static function parse($str) {
        $out = array();
        $chunks = explode("\n", $str);


        $keys = explode(';', $chunks[0]);

        $chunks = array_slice($chunks, 1);

        for($i=0; $i<=count($chunks)-1; $i++){

            $values = explode(';', $chunks[$i]);

            if(count($values) == 0) continue;

            if(count($values) > count($keys)) throw WrongValueCountException;

            for($k=0; $k <= count($keys)-1; $k++){
                $out[$keys[$k]][$i] = $values[$k];
            }
        }
        return $out;
    }
}

class WrongValueCountException extends Exception {}


?>