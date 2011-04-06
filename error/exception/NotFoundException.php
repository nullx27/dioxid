<?php

/**
 * NotFoundException.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package exceptions
 */


namespace dioxid\error\exception;

use dioxid\error\exception\BaseException;

class NotFoundException extends BaseException {
	protected $code = 404;
}

?>