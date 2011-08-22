<?php

/**
 * NotFoundException.php - dioxid
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package Controller
 */


namespace dioxid\controller\exception;
use dioxid\common\exception\BaseException;

class NotFoundException extends BaseException {
	protected $code = 404;
}

?>