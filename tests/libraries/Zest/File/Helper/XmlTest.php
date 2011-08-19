<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage UnitTests
 */
class Zest_File_Helper_XmlTest extends PHPUnit_Framework_TestCase{

	public function testMagicGet(){
		$file = new Zest_File($this->_getPathname());
		$this->assertInstanceOf('SimpleXMLElement', $file->lorem);
		$this->assertStringStartsWith('Lorem ipsum dolor sit amet', trim($file->lorem));
	}
	
	public function testMagicCall(){
		$file = new Zest_File($this->_getPathname());
		$this->assertInstanceOf('SimpleXMLElement', $file->children());
		
		$keys = array('lorem', 'maecenas', 'donec', 'pellentesque', 'nunc');
		foreach($keys as $key){
			$this->assertNotEmpty((string) $file->children()->$key);
		}
	}
	
	protected function _getPathname(){
		return Zest_AllTests::getDataDir().'/file/xml.xml';
	}
	
}