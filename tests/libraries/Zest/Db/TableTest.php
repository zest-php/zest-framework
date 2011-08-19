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
class Zest_Db_TableTest extends Zest_Db_AbstractTest{
	
	protected function setUp(){
		$dir = $this->_getCacheDir();
		if(file_exists($dir)){
			foreach(glob($dir.'/*') as $file){
				unlink($file);
			}
		}
		else{
			mkdir($dir);
		}
	}
	
	public function testRowsetInstance(){
		$table = new Zest_Db_Table();
		$this->assertEquals('Zest_Db_Table_Rowset', $table->getRowsetClass());
	}
	
	public function testGetInstance(){
		$table = Zest_Db_Table::getInstance('Default_Model_DbTable_DirectoryTest');
		$this->assertInstanceOf('Default_Model_DbTable_DirectoryTest', $table);
	}
	
	public function testGetInstanceNotZestDbTable(){
		$this->setExpectedException('Zest_Db_Exception');
		Zest_Db_Table::getInstance('Default_Model_DirectoryTest');
	}
	
	public function testGetAdapter(){
		$table = Zest_Db_Table::getInstance('Default_Model_DbTable_DirectoryTest');
		$this->assertEquals(array('directory', 'file'), $table->getAdapter()->listTables());
	}
	
	public function testGetDefaultAdapter(){
		$table = Zest_Db_Table::getInstance('Default_Model_DbTable_DirectoryTest');
		$this->assertEquals(array('directory', 'file'), Zest_Db_Table::getDefaultAdapter()->listTables());
	}
	
	public function testDefaultMetadataCacheDir(){
		$cacheDir = $this->_getCacheDir();
		
		Zest_Db_Table::setDefaultMetadataCacheDir($cacheDir);
		$table = new Default_Model_DbTable_DirectoryTest();
		$table->exists();
		$this->assertEquals(2, count(glob($cacheDir.'/*')));
		
		Zest_Db_Table::setDefaultMetadataCache();
	}
	
	public function testMetadataCacheDir(){
		$cacheDir = $this->_getCacheDir();
		
		$table = new Default_Model_DbTable_DirectoryTest();
		$table->setMetadataCacheDir($cacheDir);
		$table->exists();
		$this->assertEquals(2, count(glob($cacheDir.'/*')));
	}
	
	public function testExists(){
		$table = Zest_Db_Table::getInstance('Default_Model_DbTable_DirectoryTest');
		$this->assertTrue($table->exists());
	}
	
	public function testInsertOr(){
		$table = Zest_Db_Table::getInstance('Default_Model_DbTable_DirectoryTest');
		$table->insertOrReplace(array('name' => 'images'));
		
		$dir = new Default_Model_DirectoryTest();
		$dir->findByName('images');
		$this->assertEquals('images', $dir->name);
		$dir->delete();
	}
	
	public function testDbSelectInstance(){
		$table = Zest_Db_Table::getInstance('Default_Model_DbTable_DirectoryTest');
		$this->assertInstanceOf('Zest_Db_Table_Select', $table->select());
	}
	
	public function testDbSelectFrom(){
		$table = Zest_Db_Table::getInstance('Default_Model_DbTable_DirectoryTest');
		$this->assertEquals('SELECT "directory".* FROM "directory"', $table->select()->assemble());
	}
	
	protected function _getCacheDir(){
		return Zest_AllTests::getTempDir().'/Zest_Db_TableTest';
	}
	
}