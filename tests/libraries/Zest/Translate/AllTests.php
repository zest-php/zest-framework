<?php

/**
 * @category Zest
 * @package Zest_Translate
 * @subpackage UnitTests
 */
class Zest_Translate_AllTests{

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
		$suite = new PHPUnit_Framework_TestSuite('Zest Framework - Zest_Translate');
		$suite->addTestSuite('Zest_Translate_TranslateTest');
		return $suite;
	}

}

if(!defined('PHPUnit_MAIN_METHOD')){
	define('PHPUnit_MAIN_METHOD', 'Zest_Translate_AllTests::main');
}

if(PHPUnit_MAIN_METHOD == 'Zest_Translate_AllTests::main'){
	Zest_Translate_AllTests::main();
}
else{
	Zest_Translate_AllTests::suite();
}