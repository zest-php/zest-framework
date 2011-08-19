<?php

/**
 * les classes
 * 		DirectoryTest, DirectoryTestMapper, Default_Model_DbTable_DirectoryTest
 * 		FileTest, FileTestMapper, Default_Model_DbTable_FileTest
 * sont déclarées dans le fichier Zest_Db_AbstractTest
 */

/**
 * @category Zest
 * @package Zest_Db
 * @subpackage UnitTests
 */
class Zest_Db_ObjectTest extends Zest_Db_AbstractTest{
	
	public function testDataToClean(){
		$dir = new Default_Model_DirectoryTest(array('name' => 'images'));
		$this->assertNull($dir->getCleanObject()->name);
		$dir->setDataToClean();
		$this->assertEquals('images', $dir->getCleanObject()->name);
	}
	
	public function testCleanToData(){
		$dir = new Default_Model_DirectoryTest(array('name' => 'images'));
		$dir->setDataToClean();
		$dir->name = 'videos';
		$this->assertEquals('videos', $dir->name);
		$dir->setCleanToData();
		$this->assertEquals('images', $dir->name);
	}
	
	public function testIsClean(){
		$dir = new Default_Model_DirectoryTest(array('name' => 'images'));
		$this->assertFalse($dir->isClean('name'));
		$dir->setDataToClean();
		$this->assertTrue($dir->isClean('name'));
		$dir->name = 'videos';
		$this->assertFalse($dir->isClean('name'));
	}
	
	public function testIsCleanRecursive(){
		$file = new Default_Model_FileTest(array('name' => 'sea.jpg'));
		$dir = new Default_Model_DirectoryTest(array('name' => 'images'));
		$dir->children = array($file);
		
		$dir->setDataToClean();
		$file->setDataToClean();
		
		$this->assertTrue($dir->isClean('children'));
		$file->name = 'mountain.jpg';
		$this->assertFalse($dir->isClean('children'));
	}
	
	public function testHasClean(){
		$dir = new Default_Model_DirectoryTest(array('name' => 'images'));
		$this->assertFalse($dir->hasClean());
		$dir->setDataToClean();
		$this->assertTrue($dir->hasClean());
	}
	
	public function testCleanObject(){
		$dir = new Default_Model_DirectoryTest(array('name' => 'images'));
		$this->assertNull($dir->getCleanObject()->name);
		$dir->setDataToClean();
		$this->assertEquals('images', $dir->getCleanObject()->name);
	}
	
	public function testMapperInstance(){
		$dir = new Default_Model_DirectoryTest(array('name' => 'images'));
		$this->assertInstanceOf('Zest_Db_Object_Mapper', $dir->getMapper());
	}
	
	public function testMapperGet(){
		$dir = new Default_Model_DirectoryTest();
		$this->assertInstanceOf('Default_Model_DirectoryTestMapper', $dir->getMapper());
	}
	
	public function testMapperSet(){
		$object = new Zest_Db_Object();
		$object->setMapper('Default_Model_DirectoryTestMapper');
		$this->assertInstanceOf('Default_Model_DirectoryTestMapper', $object->getMapper());
	}
	
	public function testMapperReset(){
		$dir = new Default_Model_DirectoryTest();
		$dir->setMapper('Default_Model_FileTestMapper');
		$this->assertInstanceOf('Default_Model_FileTestMapper', $dir->getMapper());
		$dir->resetMapper();
		$this->assertInstanceOf('Default_Model_DirectoryTestMapper', $dir->getMapper());
	}
	
	public function testMapperNotZestDbObjectMapper(){
		$this->setExpectedException('Zest_Db_Exception');
		$dir = new Default_Model_DirectoryTest(array('name' => 'images'));
		$dir->setMapper('stdClass');
	}
	
	public function testCreate(){
		$dir = new Default_Model_DirectoryTest(array('name' => 'images'));
		$this->assertEquals('images', $dir->name);
		$dir = new Default_Model_DirectoryTest();
		$this->assertNull($dir->name);
		$dir = new Default_Model_DirectoryTest();
		$dir->create(array('name' => 'images'));
		$this->assertEquals('images', $dir->name);
	}
	
	public function testFind(){
		$dir = new Default_Model_DirectoryTest(array('name' => 'images'));
		$dir->save();
		
		$same = new Default_Model_DirectoryTest();
		$this->assertNull($same->name);
		$same->find($dir->id);
		$this->assertEquals('images', $same->name);
		
		$same = new Default_Model_DirectoryTest();
		$same->find(Zest_Db_Model_Request::factory(array('name' => 'images')));
		$this->assertEquals('images', $same->name);
		
		$dir->delete();
	}
	
	public function testFindBy(){
		$dir = new Default_Model_DirectoryTest(array('name' => 'images'));
		$dir->save();
		
		$same = new Default_Model_DirectoryTest();
		$same->findByName('images');
		$this->assertEquals('images', $same->name);
		
		$dir->delete();
	}
	
	public function testSave(){
		$dir = new Default_Model_DirectoryTest(array('name' => 'images'));
		$dir->save();
		$this->assertNotNull($dir->id);
		$dir->delete();
	}
	
	public function testDelete(){
		$dir = new Default_Model_DirectoryTest(array('name' => 'images'));
		$dir->save();
		$this->assertNotNull($dir->id);
		$dir->delete();
		
		$dir = new Default_Model_DirectoryTest();
		$dir->findByName('images');
		$this->assertNull($dir->id);
	}
	
}