<?php

/**
 * @category Zest
 * @package Zest_Db
 * @subpackage UnitTests
 */
class Zest_Db_Orm_Nested_AllTests{

	/**
	 * @return void
	 */
	public static function main(){
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}

	/**
	 * @return PHPUnit_Framework_TestSuite
	 */
	public static function suite(){
		$suite = new PHPUnit_Framework_TestSuite('Zest Framework - Zest_Db_Orm_Nested');
		$suite->addTestSuite('Zest_Db_Orm_Nested_ModelTest');
		$suite->addTestSuite('Zest_Db_Orm_Nested_ObjectTest');
		return $suite;
	}

}

if(!defined('PHPUnit_MAIN_METHOD')){
	define('PHPUnit_MAIN_METHOD', 'Zest_Db_Orm_Nested_AllTests::main');
}

if(PHPUnit_MAIN_METHOD == 'Zest_Db_Orm_Nested_AllTests::main'){
	Zest_Db_Orm_Nested_AllTests::main();
}
else{
	Zest_Db_Orm_Nested_AllTests::suite();
}