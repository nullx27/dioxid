<?php
/**
 * file_name  - project_name
 * @author Andre 'Necrotex' Peiffer <necrotex@gmail.com>
 * @version 1.0
 * @package package_name
 */

namespace dioxid\model;

use dioxid\lib\Base;

class Query extends Base {

	protected $query = false;

	protected static $allowed_verbs = array('SELECT', 'INSERT', 'UPDATE', 'DELETE');
	protected static $allowed_methods = array(
		'SELECT' => array(
			'distinct',
			'field',
			'from',
			'join',
			'where',
			'groupby',
			'having',
			'orderby',
			'limit'
			),
		'INSERT' => array(
			'ignore',
			'intoTable',
			'intoField',
			'values',
			'select'
			),
		'UPDATE' => array(
			'ignore',
			'table',
			'join',
			'set',
			'where',
			'orderby',
			'limit'
			),
		'DELETE' => array(
			'ignore',
			'table',
			'from',
			'join',
			'where',
			'orderby',
			'limit'
			)
		);


	public function __call($name, $args){

	}

	public function insert(){}

	public function update(){}

	public function delete(){}

	public static function formatter(){

	}

	public static function escape(){

	}

}

?>