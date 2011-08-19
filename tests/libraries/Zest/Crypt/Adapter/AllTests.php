<?php

/**
 * @category Zest
 * @package Zest_Crypt
 * @subpackage UnitTests
 */
class Zest_Crypt_Adapter_AllTests{

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
		$suite = new PHPUnit_Framework_TestSuite('Zest Framework - Zest_Crypt_Adapter');
		$suite->addTestSuite('Zest_Crypt_Adapter_McryptTest');
		return $suite;
	}

}

if(!defined('PHPUnit_MAIN_METHOD')){
	define('PHPUnit_MAIN_METHOD', 'Zest_Crypt_Adapter_AllTests::main');
}

if(PHPUnit_MAIN_METHOD == 'Zest_Crypt_Adapter_AllTests::main'){
	Zest_Crypt_Adapter_AllTests::main();
}
else{
	Zest_Crypt_Adapter_AllTests::suite();
}