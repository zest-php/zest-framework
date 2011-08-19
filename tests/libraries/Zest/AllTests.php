<?php

/**
 * Hooks
 *	  setUpBeforeClass, setUp, assertPreConditions
 *	  # runTest #
 *	  assertPostConditions
 *	  tearDown, tearDownAfterClass
 *	  onNotSuccessfulTest
 */

/**
 * requirements : mcrypt, sqlite, zip
 * 
 * @category Zest
 * @package Zest
 * @subpackage UnitTests
 */
class Zest_AllTests{

	public static function main(){
//		// Run buffered tests as a separate suite first
//		ob_start();
//		PHPUnit_TextUI_TestRunner::run(self::suiteBuffered());
//		if(ob_get_level()){
//			ob_end_flush();
//		}

		PHPUnit_TextUI_TestRunner::run(self::suite());
	}

	/**
	 * Buffered test suites
	 *
	 * These tests require no output be sent prior to running as they rely
	 * on internal PHP functions.
	 *
	 * @return PHPUnit_Framework_TestSuite
	 */
	public static function suiteBuffered(){
		$suite = new PHPUnit_Framework_TestSuite('Zest Framework - Zest - Buffered Test Suites');

		// // These tests require no output be sent prior to running as they rely
		// // on internal PHP functions
		// $suite->addTestSuite('Zend_OpenIdTest');
		// $suite->addTest(Zend_OpenId_AllTests::suite());
		// $suite->addTest(Zend_Session_AllTests::suite());
		// $suite->addTest(Zend_Soap_AllTests::suite());

		return $suite;
	}

	/**
	 * Regular suite
	 *
	 * All tests except those that require output buffering.
	 *
	 * @return PHPUnit_Framework_TestSuite
	 */
	public static function suite(){
		$suite = new PHPUnit_Framework_TestSuite('Zest Framework - Zest');
		$suite->addTest(Zest_Acl_AllTests::suite());
		$suite->addTest(Zest_Captcha_AllTests::suite());
		$suite->addTest(Zest_Config_AllTests::suite());
		$suite->addTest(Zest_Crypt_AllTests::suite());
		$suite->addTest(Zest_Data_AllTests::suite());
		$suite->addTest(Zest_Db_AllTests::suite());
		$suite->addTest(Zest_Event_AllTests::suite());
		$suite->addTest(Zest_File_AllTests::suite());
		$suite->addTest(Zest_Filter_AllTests::suite());
		$suite->addTest(Zest_Form_AllTests::suite());
		$suite->addTest(Zest_Layout_AllTests::suite());
		$suite->addTest(Zest_Log_AllTests::suite());
		$suite->addTest(Zest_Mail_AllTests::suite());
		$suite->addTest(Zest_Minify_AllTests::suite());
		$suite->addTest(Zest_Translate_AllTests::suite());
		$suite->addTest(Zest_View_AllTests::suite());
		return $suite;
	}
	
	public static function getDataDir(){
		return dirname(dirname(__FILE__)).'/data';
	}
	
	public static function getTempDir(){
		return rtrim(sys_get_temp_dir(), '/\\');
	}

}

if(!defined('PHPUnit_MAIN_METHOD')){
	define('PHPUnit_MAIN_METHOD', 'Zest_AllTests::main');
}

if(PHPUnit_MAIN_METHOD == 'Zest_AllTests::main'){
	Zest_AllTests::main();
}
else{
	Zest_AllTests::suite();
}