<?php
/**
 * BuildRegistry.php
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package Lib
 * @subpackage ACL
 */

namespace dioxid\lib\acl;

use dioxid\lib\acl\exception\NotFoundException;

use dioxid\common\Base;
use dioxid\lib\Session;
use dioxid\config\Config;

class ACLRegistry extends Base{

	protected static $registry = array();

	protected static $_SESSION_NAMESPACE = "_ACL_REG";

	protected static $_session;

	const DENY = 0;
	const OWNER = 2;
	const GROUP = 4;
	const READ = 8;
	const MODIFY = 16;


	protected static function _init() {

	}



	public static function registerSession() {

	}

	public static function registerDenyPage() {

	}

	public static function registerGroup() {

	}

	public static function getGroupMask() {
		$groups = func_get_args();

		foreach($groups as $c => $gorup){

		}
	}

	private static function _persistenceStore() {

	}

	protected static function bitmask(){}
}

?>