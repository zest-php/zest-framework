<?php

/**
 * @category Zest
 * @package Zest_Minify
 * @subpackage UnitTests
 */
class Zest_Minify_AllTests{

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
		$suite = new PHPUnit_Framework_TestSuite('Zest Framework - Zest_Minify');
		$suite->addTestSuite('Zest_Minify_MinifyTest');
		return $suite;
	}

}

if(!defined('PHPUnit_MAIN_METHOD')){
	define('PHPUnit_MAIN_METHOD', 'Zest_Minify_AllTests::main');
}

if(PHPUnit_MAIN_METHOD == 'Zest_Minify_AllTests::main'){
	Zest_Minify_AllTests::main();
}
else{
	Zest_Minify_AllTests::suite();
}