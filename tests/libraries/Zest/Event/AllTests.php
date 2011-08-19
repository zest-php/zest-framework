<?php

/**
 * @category Zest
 * @package Zest_Event
 * @subpackage UnitTests
 */
class Zest_Event_AllTests{

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
		$suite = new PHPUnit_Framework_TestSuite('Zest Framework - Zest_Event');
		$suite->addTestSuite('Zest_Event_EventTest');
		return $suite;
	}

}

if(!defined('PHPUnit_MAIN_METHOD')){
	define('PHPUnit_MAIN_METHOD', 'Zest_Event_AllTests::main');
}

if(PHPUnit_MAIN_METHOD == 'Zest_Event_AllTests::main'){
	Zest_Event_AllTests::main();
}
else{
	Zest_Event_AllTests::suite();
}