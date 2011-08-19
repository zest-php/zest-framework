<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage UnitTests
 */
class Zest_File_AllTests{

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
		$suite = new PHPUnit_Framework_TestSuite('Zest Framework - Zest_File');
		$suite->addTestSuite('Zest_File_DirTest');
		$suite->addTestSuite('Zest_File_FileTest');
		$suite->addTestSuite('Zest_File_MimeTypeTest');
		$suite->addTest(Zest_File_Helper_AllTests::suite());
		$suite->addTest(Zest_File_Transfer_AllTests::suite());
		return $suite;
	}

}

if(!defined('PHPUnit_MAIN_METHOD')){
	define('PHPUnit_MAIN_METHOD', 'Zest_File_AllTests::main');
}

if(PHPUnit_MAIN_METHOD == 'Zest_File_AllTests::main'){
	Zest_File_AllTests::main();
}
else{
	Zest_File_AllTests::suite();
}