<?php

/**
 * @category Zest
 * @package Zest_Crypt
 * @subpackage UnitTests
 */
class Zest_Crypt_Adapter_McryptTest extends PHPUnit_Framework_TestCase{
	
	/**
	 * @var Zest_Crypt_Mcrypt
	 */
	protected $_mcrypt = null;
	
	protected $_key = 'Zest_Crypt_McryptTest';
	
	protected function setUp(){
		$this->_mcrypt = Zest_Crypt::getInstance()->getAdapter();
	}
	
	protected function tearDown(){
		unset($this->_mcrypt);
	}
	
	public function testWithoutKeyException(){
		$this->_mcrypt->setKey(null);
		$this->setExpectedException('Zest_Crypt_Exception');
		$this->_mcrypt->encrypt('test');
	}
	
	public function testEncrypt(){
		$this->_mcrypt->setKey($this->_key);
		$this->assertEquals('LOnifpjN4KEhk5zdwwoNBw%3D%3D', $this->_mcrypt->encrypt('test'));
		
		$this->_mcrypt->setKey(null);
		$this->assertEquals('LOnifpjN4KEhk5zdwwoNBw%3D%3D', $this->_mcrypt->encrypt('test', $this->_key));
	}
	
	public function testDecrypt(){
		$this->_mcrypt->setKey($this->_key);
		$this->assertEquals('test', $this->_mcrypt->decrypt('LOnifpjN4KEhk5zdwwoNBw%3D%3D'));
		
		$this->_mcrypt->setKey(null);
		$this->assertEquals('test', $this->_mcrypt->decrypt('LOnifpjN4KEhk5zdwwoNBw%3D%3D', $this->_key));
	}

}