<?php
/**
 * CouldNOtWriteToChacheExcpetion.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package Lib
 * @subpackage Cookiejar
 */

namespace dioxid\lib\cookiejar\exception;
use dioxid\common\exception\BaseException;

class CouldNotWriteToCacheException extends BaseException {
	protected $code = 500;
}

?>