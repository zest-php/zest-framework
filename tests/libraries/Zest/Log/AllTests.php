<?php

/**
 * @category Zest
 * @package Zest_Log
 * @subpackage UnitTests
 */
class Zest_Log_AllTests{

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
		$suite = new PHPUnit_Framework_TestSuite('Zest Framework - Zest_Log');
		$suite->addTestSuite('Zest_Log_LogTest');
		return $suite;
	}

}

if(!defined('PHPUnit_MAIN_METHOD')){
	define('PHPUnit_MAIN_METHOD', 'Zest_Log_AllTests::main');
}

if(PHPUnit_MAIN_METHOD == 'Zest_Log_AllTests::main'){
	Zest_Log_AllTests::main();
}
else{
	Zest_Log_AllTests::suite();
}