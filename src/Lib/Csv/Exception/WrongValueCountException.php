<?php
/**
 * WrongValueCountException.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package Lib
 * @subpackage Csv
 */

namespace dioxid\lib\csv\exception;

use dioxid\common\exception\BaseException;

class WrongValueCountException extends BaseException {
	protected $code = 500;
}

?>