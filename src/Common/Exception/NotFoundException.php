<?php

/**
 * NotFoundException.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package Common
 * @subpackage Exception
 */


namespace dioxid\common\exception;
use dioxid\common\exception\BaseException;

class NotFoundException extends BaseException {
	protected $code = 404;
}

?>