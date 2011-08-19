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
class Zest_Db_Orm_Nested_ModelTest extends Zest_Db_AbstractTest{
	
	public function testHasManyException(){
		$this->setExpectedException('Zest_Db_Exception');
		$dirChildren = new Default_Model_DirectoryModelManyTest();
		$dirChildren->init();
	}
	
	public function testHasOneException(){
		$this->setExpectedException('Zest_Db_Exception');
		$dirChildren = new Default_Model_DirectoryModelOneTest();
		$dirChildren->init();
	}
	
	public function testHasMany(){
		$dir = new Default_Model_DirectoryTest(array('name' => 'images'));
		$dir->save();
		
		$file1 = new Default_Model_FileTest(array('name' => 'sea.jpg', 'directory_id' => $dir->id));
		$file1->save();
		
		$file2 = new Default_Model_FileTest(array('name' => 'mountain.jpg', 'directory_id' => $dir->id));
		$file2->save();
		
		$model = new Default_Model_DirectoryModelManyTest();
		$dirChildren = $model->get(Zest_Db_Model_Request::factory(array('id' => $dir->id)));
		
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
	
	public function testHasManyEmpty(){
		$dir = new Default_Model_DirectoryTest(array('name' => 'images'));
		$dir->save();
		
		$model = new Default_Model_DirectoryModelManyTest();
		$dirChildren = $model->get(Zest_Db_Model_Request::factory(array('id' => $dir->id)));
		
		try{
			$this->assertEquals('array', gettype($dirChildren->children));
			$this->assertEmpty($dirChildren->children);
		}
		catch(Exception $e){
			$dir->delete();
			throw $e;
		}
		
		$dir->delete();
	}
	
	public function testHasOne(){
		$dir = new Default_Model_DirectoryTest(array('name' => 'images'));
		$dir->save();
		
		$file = new Default_Model_FileTest(array('name' => 'sea.jpg', 'directory_id' => $dir->id));
		$file->save();
		
		$model = new Default_Model_DirectoryModelOneTest();
		$dirChild = $model->get(Zest_Db_Model_Request::factory(array('id' => $dir->id)));
		
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
	
	public function testHasOneEmpty(){
		$dir = new Default_Model_DirectoryTest(array('name' => 'images'));
		$dir->save();
		
		$model = new Default_Model_DirectoryModelOneTest();
		$dirChild = $model->get(Zest_Db_Model_Request::factory(array('id' => $dir->id)));
		
		try{
			$this->assertNull($dirChild->child);
		}
		catch(Exception $e){
			$dir->delete();
			throw $e;
		}
		
		$dir->delete();
	}
	
}

class Default_Model_DirectoryModelManyTest extends Zest_Db_Orm_Nested_Model{
	
	public function getDbTable(){
		return Zest_Db_Table::getInstance('Default_Model_DbTable_DirectoryTest');
	}
	
	public function init(){
		$this->hasMany('Default_Model_FileTestMapper', 'directory_id', 'id', 'children');
	}
	
}

class Default_Model_DirectoryModelOneTest extends Zest_Db_Orm_Nested_Model{
	
	public function getDbTable(){
		return Zest_Db_Table::getInstance('Default_Model_DbTable_DirectoryTest');
	}
	
	public function init(){
		$this->hasOne('Default_Model_FileTestMapper', 'directory_id', 'id', 'child');
	}
	
}