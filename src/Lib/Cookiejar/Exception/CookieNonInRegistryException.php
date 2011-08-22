<?php
/**
 * CookieNotInRegistryException.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package Lib
 * @subpackage Cookiejar
 */

namespace dioxid\lib\acl\exception;
use dioxid\common\exception\BaseException;

class CookieNonInRegistryException extends BaseException {
	protected $code =500;
}

?>