<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage UnitTests
 */
class Zest_File_Helper_PsdTest extends PHPUnit_Framework_TestCase{
	
	/**
	 * @var Zest_File
	 */
	protected $_file = null;
	
	protected function setUp(){
		$this->_file = new Zest_File($this->_getPathname());
		
		$dir = $this->_getTemp();
		if(file_exists($dir)){
			foreach(glob($dir.'/*') as $file){
				unlink($file);
			}
		}
		else{
			mkdir($dir);
		}
	}
	
	public function testConvert(){
		$this->setExpectedException('Zest_File_Exception');
		$this->_file->convert(array());
	}
	
	public function testConvertTo(){
		$to = $this->_getTemp().'/testConvertTo.png';
		$this->_file->convertTo($to);
		$this->assertTrue(file_exists($to));
	}
	
	protected function _getTemp(){
		return Zest_AllTests::getTempDir().'/Zest_File_Helper_PsdTest';
	}
	
	protected function _getPathname(){
		return Zest_AllTests::getDataDir().'/file/image.psd';
	}
	
}