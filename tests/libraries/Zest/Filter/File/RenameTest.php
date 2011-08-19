<?php

/**
 * @category Zest
 * @package Zest_Filter
 * @subpackage UnitTests
 */
class Zest_Filter_File_RenameTest extends PHPUnit_Framework_TestCase{
	
	protected function setUp(){
		$dir = dirname($this->_getTarget());
		if(file_exists($dir)){
			foreach(glob($dir.'/*') as $file){
				unlink($file);
			}
		}
		else{
			mkdir($dir);
		}
	}
	
	public function testFilterNotExists(){
		$source = $this->_getSource();
		$target = new Zest_File($this->_getTarget());
		
		$filter = new Zest_Filter_File_Rename();
		$filter->setFile(array(
			'source' => $source,
			'target' => $target->getPathname(),
			'overwrite' => false
		));
		
		$this->assertEquals($target->getPathname(), $filter->filter($source));
		$this->assertTrue(file_exists($target->getPathname()));
	}
	
	public function testFilterExists(){
		$source = $this->_getSource();
		$target = new Zest_File($this->_getTarget());
		$target->touch();
		$alternative = $target->getPathnameAlternative();
		
		$filter = new Zest_Filter_File_Rename();
		$filter->setFile(array(
			'source' => $source,
			'target' => $target->getPathname(),
			'overwrite' => false
		));
		
		$this->assertEquals($alternative, $filter->filter($source));
		$this->assertTrue(file_exists($alternative));
	}
	
	public function testFilterExistsOverwrite(){
		$source = $this->_getSource();
		$target = new Zest_File($this->_getTarget());
		$target->touch();
		
		$filter = new Zest_Filter_File_Rename();
		$filter->setFile(array(
			'source' => $source,
			'target' => $target->getPathname(),
			'overwrite' => true
		));
		
		$this->assertEquals($target->getPathname(), $filter->filter($source));
		$this->assertTrue(file_exists($target->getPathname()));
	}
	
	protected function _getSource(){
		$dir = Zest_AllTests::getTempDir().'/Zest_Filter_File_RenameTest';
		if(!file_exists($dir)){
			mkdir($dir);
		}
		copy(Zest_AllTests::getDataDir().'/filter/file.png', $dir.'/source.png');
		return str_replace(DIRECTORY_SEPARATOR, '/', $dir).'/source.png';
	}
	
	protected function _getTarget(){
		return Zest_AllTests::getTempDir().'/Zest_Filter_File_RenameTest/testFilter.png';
	}
	
}