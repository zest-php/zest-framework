<?php

/**
 * @category Zest
 * @package Zest_Db
 * @subpackage UnitTests
 */
class Zest_Db_Orm_AllTests{

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
		$suite = new PHPUnit_Framework_TestSuite('Zest Framework - Zest_Db_Orm');
		$suite->addTest(Zest_Db_Orm_Nested_AllTests::suite());
		$suite->addTest(Zest_Db_Orm_NestedSet_AllTests::suite());
		return $suite;
	}

}

if(!defined('PHPUnit_MAIN_METHOD')){
	define('PHPUnit_MAIN_METHOD', 'Zest_Db_Orm_AllTests::main');
}

if(PHPUnit_MAIN_METHOD == 'Zest_Db_Orm_AllTests::main'){
	Zest_Db_Orm_AllTests::main();
}
else{
	Zest_Db_Orm_AllTests::suite();
}