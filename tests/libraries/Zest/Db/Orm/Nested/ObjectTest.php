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
class Zest_Db_Orm_Nested_ObjectTest extends Zest_Db_AbstractTest{
	
	public function testHasManyException(){
		$this->setExpectedException('Zest_Db_Exception');
		$dirChildren = new Default_Model_DirectoryObjectManyTest();
		$dirChildren->init();
	}
	
	public function testHasOneException(){
		$this->setExpectedException('Zest_Db_Exception');
		$dirChildren = new Default_Model_DirectoryObjectOneTest();
		$dirChildren->init();
	}
	
	public function testHasMany(){
		$dir = new Default_Model_DirectoryTest(array('name' => 'images'));
		$dir->save();
		
		$file1 = new Default_Model_FileTest(array('name' => 'sea.jpg', 'directory_id' => $dir->id));
		$file1->save();
		
		$file2 = new Default_Model_FileTest(array('name' => 'mountain.jpg', 'directory_id' => $dir->id));
		$file2->save();
		
		$dirChildren = new Default_Model_DirectoryObjectManyTest();
		$dirChildren->find($dir->id);
		
		try{
			$this->assertEquals('array', gettype($dirChildren->children));
			$names = array();
			foreach($dirChildren->children as $child){
				$names[] = $child->name;
			}
			sort($names);
			$this->assertEquals(array('mountain.jpg', 'sea.jpg'), $names);
		}
		catch(Exception $e){
			$dir->delete();
			$file1->delete();
			$file2->delete();
			throw $e;
		}
		
		$dir->delete();
		$file1->delete();
		$file2->delete();
	}
	
	public function testHasOne(){
		$dir = new Default_Model_DirectoryTest(array('name' => 'images'));
		$dir->save();
		
		$file = new Default_Model_FileTest(array('name' => 'sea.jpg', 'directory_id' => $dir->id));
		$file->save();
		
		$dirChild = new Default_Model_DirectoryObjectOneTest();
		$dirChild->find($dir->id);
		
		try{
			$this->assertInstanceOf('Default_Model_FileTest', $dirChild->child);
			$this->assertEquals('sea.jpg', $dirChild->child->name);
		}
		catch(Exception $e){
			$dir->delete();
			$file->delete();
			throw $e;
		}
		
		$dir->delete();
		$file->delete();
	}
	
}

class Default_Model_DirectoryObjectManyTest extends Zest_Db_Orm_Nested_Object{
	
	public function getMapper(){
		return Zest_Db_Model::getInstance('Default_Model_DirectoryTestMapper');
	}
	
	public function init(){
		$this->hasMany('Default_Model_FileTestMapper', 'directory_id', 'id', 'children');
	}
	
}

class Default_Model_DirectoryObjectOneTest extends Zest_Db_Orm_Nested_Object{
	
	public function getMapper(){
		return Zest_Db_Model::getInstance('Default_Model_DirectoryTestMapper');
	}
	
	public function init(){
		$this->hasOne('Default_Model_FileTestMapper', 'directory_id', 'id', 'child');
	}
	
}