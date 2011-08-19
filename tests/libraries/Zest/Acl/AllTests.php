<?php

/**
 * @category Zest
 * @package Zest_Acl
 * @subpackage UnitTests
 */
class Zest_Acl_AllTests{

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
		$suite = new PHPUnit_Framework_TestSuite('Zest Framework - Zest_Acl');
		$suite->addTestSuite('Zest_Acl_ExceptionTest');
		$suite->addTestSuite('Zest_Acl_NoConstrainTest');
		$suite->addTestSuite('Zest_Acl_SyntaxTest');
		$suite->addTest(Zest_Acl_Adapter_AllTests::suite());
		return $suite;
	}

}

if(!defined('PHPUnit_MAIN_METHOD')){
	define('PHPUnit_MAIN_METHOD', 'Zest_Acl_AllTests::main');
}

if(PHPUnit_MAIN_METHOD == 'Zest_Acl_AllTests::main'){
	Zest_Acl_AllTests::main();
}
else{
	Zest_Acl_AllTests::suite();
}