<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage UnitTests
 */
class Zest_Form_Decorator_AllTests{

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
		$suite = new PHPUnit_Framework_TestSuite('Zest Framework - Zest_Form_Decorator');
		$suite->addTestSuite('Zest_Form_Decorator_FormErrorsTest');
		$suite->addTestSuite('Zest_Form_Decorator_LabelTest');
		$suite->addTestSuite('Zest_Form_Decorator_TableElementsTest');
		$suite->addTestSuite('Zest_Form_Decorator_TdElementTest');
		$suite->addTestSuite('Zest_Form_Decorator_TdLabelTest');
		$suite->addTestSuite('Zest_Form_Decorator_TdValueTest');
		$suite->addTestSuite('Zest_Form_Decorator_TrGroupTest');
		$suite->addTestSuite('Zest_Form_Decorator_TrLabelElementTest');
		$suite->addTestSuite('Zest_Form_Decorator_TrLabelValueTest');
		$suite->addTestSuite('Zest_Form_Decorator_TrSubFormTest');
		$suite->addTestSuite('Zest_Form_Decorator_ValueTest');
		return $suite;
	}

}

if(!defined('PHPUnit_MAIN_METHOD')){
	define('PHPUnit_MAIN_METHOD', 'Zest_Form_Decorator_AllTests::main');
}

if(PHPUnit_MAIN_METHOD == 'Zest_Form_Decorator_AllTests::main'){
	Zest_Form_Decorator_AllTests::main();
}
else{
	Zest_Form_Decorator_AllTests::suite();
}