<?php
/**
 * CouldNotWriteToChacheException.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package Model
 */

namespace dioxid\model\exception;
use dioxid\common\exception\BaseException;

class CouldNotWriteToCacheException extends BaseException {
	protected $code = 500;
}

?>