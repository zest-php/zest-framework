<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage UnitTests
 */
class Zest_View_Helper_AllTests{

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
		$suite = new PHPUnit_Framework_TestSuite('Zest Framework - Zest_View_Helper');
		$suite->addTestSuite('Zest_View_Helper_ActionTest');
		$suite->addTestSuite('Zest_View_Helper_ConfigTest');
		$suite->addTestSuite('Zest_View_Helper_FileTest');
		$suite->addTestSuite('Zest_View_Helper_HeadTest');
		$suite->addTestSuite('Zest_View_Helper_LogTest');
		$suite->addTestSuite('Zest_View_Helper_MinifyTest');
		$suite->addTestSuite('Zest_View_Helper_RenderFileTest');
		$suite->addTestSuite('Zest_View_Helper_RenderMediaTest');
		$suite->addTestSuite('Zest_View_Helper_TranslateTest');
		$suite->addTestSuite('Zest_View_Helper_UrlTest');
		return $suite;
	}

}

if(!defined('PHPUnit_MAIN_METHOD')){
	define('PHPUnit_MAIN_METHOD', 'Zest_View_Helper_AllTests::main');
}

if(PHPUnit_MAIN_METHOD == 'Zest_View_Helper_AllTests::main'){
	Zest_View_Helper_AllTests::main();
}
else{
	Zest_View_Helper_AllTests::suite();
}