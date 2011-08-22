<?php
/**
 * PBKDF2.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package Lib
 * @subpackage PBKDF2
 */

namespace dioxid\lib;

class PBKDF2 {

	public static function encrypt($str, $salt, $klength = 32, $rounds=1000, $binary=true, $algo='sha256'){
		$length = strlen(hash($algo, null, true));
		$blocks = ceil($klength / $length);

		for($i=1;$i<=$length;++$i){
			$hash = $b = hash_hmac($algo, $salt, pack('N', $i), $str, true);
			for($k=1;$k<=$rounds;++$k) $hash ^= ($b = hash_hmac($algo, $b, $str, true));
			 $o .= $hash;
		}

		return  $binary ? substr($o, 0, $klength) :  base64_encode(substr($o, 0, $klength));
	}
}

?>