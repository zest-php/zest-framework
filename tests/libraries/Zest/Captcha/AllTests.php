<?php

/**
 * @category Zest
 * @package Zest_Captcha
 * @subpackage UnitTests
 */
class Zest_Captcha_AllTests{

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
		$suite = new PHPUnit_Framework_TestSuite('Zest Framework - Zest_Captcha');
		$suite->addTestSuite('Zest_Captcha_ImageTest');
		return $suite;
	}

}

if(!defined('PHPUnit_MAIN_METHOD')){
	define('PHPUnit_MAIN_METHOD', 'Zest_Captcha_AllTests::main');
}

if(PHPUnit_MAIN_METHOD == 'Zest_Captcha_AllTests::main'){
	Zest_Captcha_AllTests::main();
}
else{
	Zest_Captcha_AllTests::suite();
}