<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage UnitTests
 */
class Zest_File_Helper_ZipTest extends PHPUnit_Framework_TestCase{

	/**
	 * @var Zest_File
	 */
	protected $_file = null;
	
	protected function setUp(){
		$this->_file = new Zest_File($this->_getPathname());
	}
	
	public function testOpen(){
		$helper = $this->_file->zip()->open();
		$this->assertInstanceOf('Zest_File_Helper_Zip', $helper);
	}

	public function testClose(){
		$helper = $this->_file->zip()->open()->close();
		$this->assertInstanceOf('Zest_File_Helper_Zip', $helper);
	}

	public function testAddFile(){
		$zip = new Zest_File(Zest_AllTests::getTempDir().'/Zest_File_Helper_ZipTest/testAddFile.zip');
		$zip->touch();
		$zip->unlink();
		$this->_file->copy($zip);
		$original_size = $zip->getSize();
		$this->assertGreaterThan(0, $zip->getSize());
		$zip->addFile($this->_file->getPathname(), 'original_zip.zip');
		$zip->close();
//		$this->assertGreaterThan($original_size, $zip->getSize());
	}

	public function testExtractTo(){
		$dir = new Zest_Dir(Zest_AllTests::getTempDir().'/Zest_File_Helper_ZipTest/testExtractTo');
		$dir->recursiveMkdir();
		$this->_file->extractTo($dir->getPathname());
		$this->assertTrue(file_exists($dir->getPathname().'/pdf_lorem.pdf'));
	}
	
	protected function _getPathname(){
		return Zest_AllTests::getDataDir().'/file/zip.zip';
	}
	
}