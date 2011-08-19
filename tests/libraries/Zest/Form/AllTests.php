<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage UnitTests
 */
class Zest_Form_AllTests{

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
		$suite = new PHPUnit_Framework_TestSuite('Zest Framework - Zest_Form');
		$suite->addTestSuite('Zest_Form_FormTest');
		$suite->addTestSuite('Zest_Form_DbTest');
		$suite->addTest(Zest_Form_Decorator_AllTests::suite());
		$suite->addTest(Zest_Form_Element_AllTests::suite());
		return $suite;
	}

}

if(!defined('PHPUnit_MAIN_METHOD')){
	define('PHPUnit_MAIN_METHOD', 'Zest_Form_AllTests::main');
}

if(PHPUnit_MAIN_METHOD == 'Zest_Form_AllTests::main'){
	Zest_Form_AllTests::main();
}
else{
	Zest_Form_AllTests::suite();
}