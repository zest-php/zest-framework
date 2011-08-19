<?php

/**
 * @category Zest
 * @package Zest_Filter
 * @subpackage UnitTests
 */
class Zest_Filter_AllTests{

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
		$suite = new PHPUnit_Framework_TestSuite('Zest Framework - Zest_Filter');
		$suite->addTestSuite('Zest_Filter_UrlTest');
		$suite->addTest(Zest_Filter_File_AllTests::suite());
		return $suite;
	}

}

if(!defined('PHPUnit_MAIN_METHOD')){
	define('PHPUnit_MAIN_METHOD', 'Zest_Filter_AllTests::main');
}

if(PHPUnit_MAIN_METHOD == 'Zest_Filter_AllTests::main'){
	Zest_Filter_AllTests::main();
}
else{
	Zest_Filter_AllTests::suite();
}