<?php

/**
 * @category Zest
 * @package Zest_Mail
 * @subpackage UnitTests
 */
class Zest_Mail_AllTests{

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
		$suite = new PHPUnit_Framework_TestSuite('Zest Framework - Zest_Mail');
		$suite->addTestSuite('Zest_Mail_MailTest');
		return $suite;
	}

}

if(!defined('PHPUnit_MAIN_METHOD')){
	define('PHPUnit_MAIN_METHOD', 'Zest_Mail_AllTests::main');
}

if(PHPUnit_MAIN_METHOD == 'Zest_Mail_AllTests::main'){
	Zest_Mail_AllTests::main();
}
else{
	Zest_Mail_AllTests::suite();
}