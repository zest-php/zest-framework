<?php

/**
 * @category Zest
 * @package Zest_Crypt
 * @subpackage UnitTests
 */
class Zest_Crypt_CryptTest extends PHPUnit_Framework_TestCase{
	
	public static function setUpBeforeClass(){
	}
	
	protected function setUp(){
	}
	
	protected function tearDown(){
	}
	
	public function testDefaultAdapter(){
		$this->assertInstanceOf('Zest_Crypt_Mcrypt', Zest_Crypt::getInstance()->getAdapter());
	}
	
	public function testPasswordGenerate(){
		$this->assertEquals(8, strlen(Zest_Crypt::getRandomPassword()));
	}
	
}