<?php

/**
 * @category Zest
 * @package Zest_Filter
 * @subpackage UnitTests
 */
class Zest_Filter_File_AllTests{

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
		$suite = new PHPUnit_Framework_TestSuite('Zest Framework - Zest_Filter_File');
		$suite->addTestSuite('Zest_Filter_File_RenameTest');
		return $suite;
	}

}

if(!defined('PHPUnit_MAIN_METHOD')){
	define('PHPUnit_MAIN_METHOD', 'Zest_Filter_File_AllTests::main');
}

if(PHPUnit_MAIN_METHOD == 'Zest_Filter_File_AllTests::main'){
	Zest_Filter_File_AllTests::main();
}
else{
	Zest_Filter_File_AllTests::suite();
}