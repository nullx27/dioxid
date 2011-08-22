<?php
/**
 * HeaderAlreadySentException.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package Controller
 */

namespace dioxid\controller\exception;
use dioxid\common\exception\BaseException;

class HeaderAlreadySentException extends BaseException {

	protected $code = 500;

}

?>