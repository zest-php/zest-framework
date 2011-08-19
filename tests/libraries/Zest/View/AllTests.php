<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage UnitTests
 */
class Zest_View_AllTests{

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
		$suite = new PHPUnit_Framework_TestSuite('Zest Framework - Zest_View');
		$suite->addTestSuite('Zest_View_ViewTest');
		$suite->addTest(Zest_View_Engine_AllTests::suite());
		$suite->addTest(Zest_View_Helper_AllTests::suite());
		return $suite;
	}

}

if(!defined('PHPUnit_MAIN_METHOD')){
	define('PHPUnit_MAIN_METHOD', 'Zest_View_AllTests::main');
}

if(PHPUnit_MAIN_METHOD == 'Zest_View_AllTests::main'){
	Zest_View_AllTests::main();
}
else{
	Zest_View_AllTests::suite();
}