<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage UnitTests
 */
class Zest_File_MimeTypeTest extends PHPUnit_Framework_TestCase{

	public function testPathname(){
		$mime = Zest_File_MimeType::getMimeType(Zest_AllTests::getTempDir().'/testPathname.txt');
		$this->assertEquals('text/plain', $mime);
	}
	
	public function testFilename(){
		$mime = Zest_File_MimeType::getMimeType('testFilename.txt');
		$this->assertEquals('text/plain', $mime);
	}
	
	public function testExtension(){
		$mime = Zest_File_MimeType::getMimeType('txt');
		$this->assertEquals('text/plain', $mime);
	}
	
	public function testFilenameWrongExtension(){
		$mime = Zest_File_MimeType::getMimeType('testFilenameWrongExtension');
		$this->assertEquals('application/octet-stream', $mime);
	}
	
}