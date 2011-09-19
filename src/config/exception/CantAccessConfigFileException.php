<?php
/**
 * CantAccessConfigFileException.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package Config
 * @subpackage Exception
 */

namespace dioxid\config\exception;
use dioxid\common\exception\BaseException;

class CantAccessConfigFileException extends BaseException {
	protected $code = 500;
}

?>