<?php

/**
 * @category Zest
 * @package Zest_Config
 * @subpackage UnitTests
 */
class Zest_Config_AllTests{

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
		$suite = new PHPUnit_Framework_TestSuite('Zest Framework - Zest_Config');
		$suite->addTestSuite('Zest_Config_AdvancedTest');
		$suite->addTestSuite('Zest_Config_ApplicationTest');
		return $suite;
	}

}

if(!defined('PHPUnit_MAIN_METHOD')){
	define('PHPUnit_MAIN_METHOD', 'Zest_Config_AllTests::main');
}

if(PHPUnit_MAIN_METHOD == 'Zest_Config_AllTests::main'){
	Zest_Config_AllTests::main();
}
else{
	Zest_Config_AllTests::suite();
}