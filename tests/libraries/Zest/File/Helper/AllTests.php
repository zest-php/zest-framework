<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage UnitTests
 */
class Zest_File_Helper_AllTests{

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
		$suite = new PHPUnit_Framework_TestSuite('Zest Framework - Zest_File_Helper');
		$suite->addTestSuite('Zest_File_Helper_AudioTest');
		$suite->addTestSuite('Zest_File_Helper_CssTest');
		$suite->addTestSuite('Zest_File_Helper_CsvTest');
		$suite->addTestSuite('Zest_File_Helper_HtmlTest');
		$suite->addTestSuite('Zest_File_Helper_ImageTest');
		$suite->addTestSuite('Zest_File_Helper_IniTest');
		$suite->addTestSuite('Zest_File_Helper_PdfTest');
		$suite->addTestSuite('Zest_File_Helper_PsdTest');
		$suite->addTestSuite('Zest_File_Helper_UrlTest');
		$suite->addTestSuite('Zest_File_Helper_VideoTest');
		$suite->addTestSuite('Zest_File_Helper_XmlTest');
		$suite->addTestSuite('Zest_File_Helper_ZipTest');
		return $suite;
	}

}

if(!defined('PHPUnit_MAIN_METHOD')){
	define('PHPUnit_MAIN_METHOD', 'Zest_File_Helper_AllTests::main');
}

if(PHPUnit_MAIN_METHOD == 'Zest_File_Helper_AllTests::main'){
	Zest_File_Helper_AllTests::main();
}
else{
	Zest_File_Helper_AllTests::suite();
}