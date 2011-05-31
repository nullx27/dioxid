<?php
/**
 * NoDatabaseTableNameException.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package exceptions
 */

namespace dioxid\error\exception;
use dioxid\error\exception\BaseException;

class NoDatabaseTableNameException extends BaseException {
	protected $code = 500;
}

?>