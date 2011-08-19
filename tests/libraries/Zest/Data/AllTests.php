<?php

/**
 * @category Zest
 * @package Zest_Data
 * @subpackage UnitTests
 */
class Zest_Data_AllTests{

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
		$suite = new PHPUnit_Framework_TestSuite('Zest Framework - Zest_Data');
		$suite->addTestSuite('Zest_Data_DataTest');
		return $suite;
	}

}

if(!defined('PHPUnit_MAIN_METHOD')){
	define('PHPUnit_MAIN_METHOD', 'Zest_Data_AllTests::main');
}

if(PHPUnit_MAIN_METHOD == 'Zest_Data_AllTests::main'){
	Zest_Data_AllTests::main();
}
else{
	Zest_Data_AllTests::suite();
}