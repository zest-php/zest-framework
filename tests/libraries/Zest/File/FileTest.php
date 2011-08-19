<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage UnitTests
 */
class Zest_File_FileTest extends Zest_File_AbstractTest{

	protected function setUp(){
		$dir = $this->_getPathname();
		if(file_exists($dir)){
			$this->_rmdir($dir);
		}
		mkdir($dir);
	}
	
	public function testBasename(){
		$file = new Zest_File($this->_getPathname().'/testBasename.txt');
		$this->assertEquals('testBasename.txt', $file->getBasename());
		$this->assertEquals('testBasename', $file->getBasename('.txt'));
	}
	
	public function testDirname(){
		$pathname = str_replace(DIRECTORY_SEPARATOR, '/', $this->_getPathname());
		
		$file = new Zest_File($pathname.'/testDirname.txt');
		$this->assertEquals($pathname, $file->getDirname());
	}
	
	public function testIsReadable(){
		$file = new Zest_File($this->_getPathname().'/testIsReadable.txt');
		$file->touch();
		$this->assertTrue($file->isReadable());
	}
	
//	public function testIsExecutable(){
//		$file = new Zest_File($this->_getPathname().'/testIsExecutable.txt');
//		$file->touch();
//		$this->assertTrue($file->isExecutable());
//	}
	
	public function testIsWritable(){
		$file = new Zest_File($this->_getPathname().'/testIsWritable.txt');
		$file->touch();
		$this->assertTrue($file->isWritable());
	}
	
	public function testFileExists(){
		$file = new Zest_File($this->_getPathname().'/testFileExists.txt');
		$file->touch();
		$this->assertTrue($file->fileExists());
	}
	
	public function testIsFile(){
		$file = new Zest_File($this->_getPathname().'/testIsFile.txt');
		$file->touch();
		$this->assertTrue($file->isFile());
	}
	
	public function testIsDir(){
		$file = new Zest_File($this->_getPathname().'/testIsDir.txt');
		$file->touch();
		$this->assertFalse($file->isDir());
	}
	
	public function testMTime(){
		$file = new Zest_File($this->_getPathname().'/testMTime.txt');
		$file->touch();
		$this->assertLessThanOrEqual(time(), $file->getMTime());
	}
	
	public function testRecursiveMkdirDirname(){
		$file = new Zest_File($this->_getPathname().'/testRecursiveMkdirDirname/file.txt');
		$this->assertFalse(file_exists($this->_getPathname().'/testRecursiveMkdirDirname'));
		$file->recursiveMkdirDirname();
		$this->assertTrue(file_exists($this->_getPathname().'/testRecursiveMkdirDirname'));
	}
	
	public function testPathnameAlternative(){
		$pathname = str_replace(DIRECTORY_SEPARATOR, '/', $this->_getPathname());
		
		$file = new Zest_File($pathname.'/testPathnameAlternative.txt');
		$this->assertEquals($pathname.'/testPathnameAlternative.txt', $file->getPathnameAlternative());
		$file->touch();
		$this->assertEquals($pathname.'/testPathnameAlternative_1.txt', $file->getPathnameAlternative());
	}
	
	public function testRenameOver(){
		$pathname = str_replace(DIRECTORY_SEPARATOR, '/', $this->_getPathname());
		
		$file = new Zest_File($pathname.'/testRenameOver.txt');
		$file->touch();
		$this->assertTrue(file_exists($file->getPathname()));
		
		$over = new Zest_File($pathname.'/testRenameOver_over.txt');
		$over->touch();
		$this->assertTrue(file_exists($over->getPathname()));
		
		$file->rename($over->getPathname(), Zest_File::RENAME_OVER);
		
		$this->assertFalse(file_exists($pathname.'/testRenameOver.txt'));
		$this->assertTrue(file_exists($over->getPathname()));
		$this->assertEquals($pathname.'/testRenameOver_over.txt', $file->getPathname());
	}
	
	public function testRenameAlternative(){
		$pathname = str_replace(DIRECTORY_SEPARATOR, '/', $this->_getPathname());
		
		$file = new Zest_File($pathname.'/testRenameAlt.txt');
		$file->touch();
		$this->assertTrue(file_exists($file->getPathname()));
		
		$alt = new Zest_File($pathname.'/testRenameAlt_alt.txt');
		$alt->touch();
		$this->assertTrue(file_exists($alt->getPathname()));
		
		$file->rename($alt->getPathname(), Zest_File::RENAME_ALTERNATIVE);
		
		$this->assertFalse(file_exists($pathname.'/testRenameAlt.txt'));
		$this->assertTrue(file_exists($alt->getPathname()));
		$this->assertEquals($pathname.'/testRenameAlt_alt_1.txt', $file->getPathname());
	}
	
//	public function testChmod(){
//		$this->_getFileperms(__FILE__);
//		
//		$pathname = $this->_getPathname().'/testChmod';
//		
//		$file = new Zest_File($pathname);
//		$file->touch();
//		
//		$this->assertEquals('-rw-rw-rw-', $this->_getFileperms($pathname));
//		$file->chmod(0755);
//		$this->assertEquals('-rwxrw-rw-', $this->_getFileperms($pathname));
//	}
	
	public function testFactory(){
		$this->assertInstanceOf('Zest_File', Zest_File::factory());
		
		$pathname = str_replace(DIRECTORY_SEPARATOR, '/', $this->_getPathname()).'/file.txt';
		$this->assertEquals($pathname, Zest_Dir::factory($pathname)->getPathname());
	}
	
	public function testGetSize(){
		$file = new Zest_File($this->_getPathname().'/testGetSize.txt');
		$file->putContents('testGetSize');
		$this->assertEquals(11, $file->getSize());
	}
	
	public function testGetExtension(){
		$file = new Zest_File($this->_getPathname().'/testGetExtension.txt');
		$this->assertEquals('txt', $file->getExtension());
	}
	
	public function testGetMimeType(){
		$file = new Zest_File($this->_getPathname().'/testGetMimeType.txt');
		$this->assertEquals('text/plain', $file->getMimeType());
	}
	
	public function testSetPathname(){
		$pathname = str_replace(DIRECTORY_SEPARATOR, '/', $this->_getPathname().'/testSetPathname.txt');
		$file = new Zest_File();
		$file->setPathname($pathname);
		$this->assertEquals($pathname, $file->getPathname());
	}
	
	public function testGetPutContents(){
		$file = new Zest_File($this->_getPathname().'/testGetPutContents.txt');
		$file->putContents('testGetPutContents');
		$this->assertEquals('testGetPutContents', $file->getContents());
	}
	
	public function testAppendContents(){
		$file = new Zest_File($this->_getPathname().'/testAppendContents.txt');
		$file->putContents('testAppendContents');
		$file->appendContents('testAppendContents');
		$this->assertEquals('testAppendContentstestAppendContents', $file->getContents());
	}
	
	public function testTouch(){
		$file = new Zest_File($this->_getPathname().'/testTouch/file.txt');
		$file->touch();
		$this->assertTrue(file_exists($file->getPathname()));
	}
	
	public function testCopyOver(){
		$file = new Zest_File($this->_getPathname().'/testCopyOver.txt');
		$file->touch();
		
		$copy = new Zest_File($this->_getPathname().'/copy_over.txt');
		$this->assertTrue($file->copy($copy, Zest_File::COPY_OVER));
	}
	
	public function testCopyAlternative(){
		$file = new Zest_File($this->_getPathname().'/testCopyAlternative.txt');
		$file->touch();
		
		$copy = new Zest_File($this->_getPathname().'/copyTestCopyAlternative.txt');
		$copy->touch();
		
		$file->copy($copy, Zest_File::COPY_ALTERNATIVE);
		$this->assertTrue(file_exists($this->_getPathname().'/copyTestCopyAlternative_1.txt'));
	}
	
	public function testUnlink(){
		$file = new Zest_File($this->_getPathname().'testUnlink.txt');
		$file->touch();
		
		$this->assertTrue(file_exists($file->getPathname()));
		$file->unlink();
		$this->assertFalse(file_exists($file->getPathname()));
	}
	
	public function testGetPluginLoader(){
		$this->assertInstanceOf('Zend_Loader_PluginLoader', Zest_File::getPluginLoader());
	}
	
	public function testSetPluginLoader(){
		$plugin_loader = Zest_File::getPluginLoader();
		
		$new_plugin_loader = new Zend_Loader_PluginLoader();
		Zest_File::setPluginLoader($new_plugin_loader);
		
		$this->assertNotEquals(Zest_File::getPluginLoader(), $plugin_loader);
		$this->assertEquals(Zest_File::getPluginLoader(), $new_plugin_loader);
		
		Zest_File::setPluginLoader($plugin_loader);
	}
	
	public function testGetHelper(){
		$file = new Zest_File($this->_getPathname().'testGetHelper.txt');
		$file->url();
		$this->assertInstanceOf('Zest_File_Helper_Url', $file->getHelper('url'));
	}
	
	public function testGetHelperException(){
		$this->setExpectedException('Zest_File_Exception');
		$file = new Zest_File($this->_getPathname().'testGetHelperException.txt');
		$file->getHelper('none');
	}
	
	public function testGetHelpers(){
		$file = new Zest_File($this->_getPathname().'testGetHelpers.txt');
		$file->url();
		$helpers = $file->getHelpers();
		$this->assertInstanceOf('Zest_File_Helper_Url', end($helpers));
	}
	
	public function testAddHelper(){
		$file = new Zest_File($this->_getPathname().'testAddHelper.txt');
		$file->addHelper('Zest_File_Helper_Url');
		$helpers = $file->getHelpers();
		$this->assertInstanceOf('Zest_File_Helper_Url', end($helpers));
		
		$file = new Zest_File($this->_getPathname().'testAddHelper.txt');
		$file->addHelper('url');
		$helpers = $file->getHelpers();
		$this->assertInstanceOf('Zest_File_Helper_Url', end($helpers));
	}
	
	public function testAddHelperException(){
		$this->setExpectedException('Zest_File_Exception');
		$file = new Zest_File($this->_getPathname().'testAddHelperException.txt');
		$file->addHelper('stdClass');
	}
	
	public function testDefaultHelpers(){
		$file = new Zest_File($this->_getPathname().'testDefaultHelpers.jpg');
		$helpers = $file->getHelpers();
		$this->assertInstanceOf('Zest_File_Helper_Image', current($helpers));
	}
	
	public function testCall(){
		$file = new Zest_File($this->_getPathname().'testCall.txt');
		$this->assertInstanceOf('Zest_File_Helper_Url', $file->url());
		$this->assertTrue($file->isText());
		$this->assertFalse($file->isImage());
		$file->getSendOptions(array());
	}
	
	public function testGet(){
		$this->setExpectedException('Zest_File_Exception');
		$file = new Zest_File($this->_getPathname().'testGet.txt');
		$file->test;
	}
	
	public function testSet(){
		$this->setExpectedException('Zest_File_Exception');
		$file = new Zest_File($this->_getPathname().'testSet.txt');
		$file->test = 'test';
	}
	
	public function testToStdClass(){
		$file = new Zest_File($this->_getPathname().'testCall.txt');
		$keys = array_keys((array) $file->toStdClass());
		sort($keys);
		$this->assertEquals(array('basename', 'exists', 'extension', 'mimetype', 'pathname', 'size'), $keys);
	}
	
	protected function _getPathname(){
		return Zest_AllTests::getTempDir().'/Zest_File_FileTest';
	}
	
}