<?php

/**
 * TemplateNotFoundException.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package Error
 * @subpackage Exception
 */

namespace dioxid\error\exception;

use dioxid\common\exception\BaseException;

class TemplateNotFoundException extends BaseException {
	protected $code = 500;
}


?>