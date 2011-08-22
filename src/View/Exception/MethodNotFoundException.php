<?php
/**
 * MethodNotFoundException.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package View
 */

namespace dioxid\view\exception;
use dioxid\common\exception\BaseException;

class MethodNotFoundException extends BaseException {
	protected $code = 500;
}

?>