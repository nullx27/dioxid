<?php
/**
 * MagicCacheable.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package Common
 */

namespace dioxid\common;

use dioxid\cache\Cache;

class MagicCacheable extends Base {

	const postfix = '__';

	public static function __callstatic($func, $param) {
		if(strpos($func, self::postfix) == strlen($func) - strlen(self::postfix)) {


		} //end if
	}
}

?>