<?php

/**
 * @category Zest
 * @package Zest_Layout
 * @subpackage UnitTests
 */
class Zest_Layout_AllTests{

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
		$suite = new PHPUnit_Framework_TestSuite('Zest Framework - Zest_Layout');
		$suite->addTestSuite('Zest_Layout_LayoutTest');
		return $suite;
	}

}

if(!defined('PHPUnit_MAIN_METHOD')){
	define('PHPUnit_MAIN_METHOD', 'Zest_Layout_AllTests::main');
}

if(PHPUnit_MAIN_METHOD == 'Zest_Layout_AllTests::main'){
	Zest_Layout_AllTests::main();
}
else{
	Zest_Layout_AllTests::suite();
}