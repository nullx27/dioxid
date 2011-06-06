<?php

/**
 * Csv.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package Lib
 */

namespace dioxid\lib;

use dioxid\lib\Base;
use dioxid\error\exception\WrongValueCountException;

/**
 *
 * Better Interface to work with CSV files
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @date 22.03.2011 14:12:42
 *
 */
class CSV extends Base {

	/**
	 * Pareses a CSV file and returns an Assoc Array
	 * @param string $str The CSV file as a string
	 * @return array Assoc Array
	 */
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

?>