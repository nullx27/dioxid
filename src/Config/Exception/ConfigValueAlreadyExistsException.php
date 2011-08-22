<?php
/**
 * ConfigValueAlreadyExistsException.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package Config
 */

namespace dioxid\config\exception;
use dioxid\common\exception\BaseException;


class ConfigValueAlreadyExistsException extends BaseException {
	protected $code = 500;
}

?>