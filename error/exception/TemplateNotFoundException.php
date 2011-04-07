<?php

/**
 * TemplateNotFoundException.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package exceptions
 */

namespace dioxid\error\exception;

use dioxid\error\exception\BaseException;

class TemplateNotFoundException extends BaseException {
	protected $code = 500;
}


?>