<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage UnitTests
 */
class Zest_File_Helper_IniTest extends PHPUnit_Framework_TestCase{

	/**
	 * @var Zest_File
	 */
	protected $_file = null;
	
	protected function setUp(){
		$this->_file = new Zest_File($this->_getPathname());
	}

	public function testPutArray(){
		$file = new Zest_File(Zest_AllTests::getTempDir().'/Zest_File_Helper_IniTest/testPutArray.ini');
		$file->unlink();
		$file->touch();
		
		$file->putArray(array('key' => 'value'));
		$this->assertEquals("key = \"value\"\n", $file->getContents());
	}

	public function testGetArray(){
		$this->assertEquals(array('section' => array('key' => 'value')), $this->_file->getArray());
	}

	public function testGetConfig(){
		$this->assertEquals(array('key' => 'value'), $this->_file->getConfig('section'));
	}
	
	protected function _getPathname(){
		return Zest_AllTests::getDataDir().'/file/ini.ini';
	}
	
}