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
class Zest_Db_ModelTest extends Zest_Db_AbstractTest{
	
	public function testGetInstanceNotModel(){
		$this->setExpectedException('Zest_Db_Exception');
		Zest_Db_Model::getInstance('Default_Model_DirectoryTest');
	}
	
	public function testDbTableNotZestDbTable(){
		$this->setExpectedException('Zest_Db_Exception');
		$model = new Zest_Db_Model();
		$model->setDbTable('Default_Model_DirectoryTest');
	}
	
	public function testUndefinedTable(){
		$this->setExpectedException('Zest_Db_Exception');
		$model = new Zest_Db_Model();
		$model->getDbTable();
	}
	
	public function testDbAdapterInstance(){
		$model = new Default_Model_DirectoryTestMapper();
		$this->assertInstanceOf('Zend_Db_Adapter_Abstract', $model->getDbAdapter());
	}
	
	public function testDbProfiles(){
		$model = new Default_Model_DirectoryTestMapper();
		$this->assertEquals('array', gettype($model->getDbQueryProfiles()));
	}
	
	public function testDefaultObjectClass(){
		$model = new Default_Model_DirectoryTestMapper();
		$this->assertEquals('Default_Model_DirectoryTest', $model->getObjectClass());
	}
	
	public function testCreate(){
		$mapper = new Default_Model_DirectoryTestMapper();
		$object = $mapper->createDbObject();
		$this->assertEquals(array('id' => null, 'name' => null, 'parent_id' => null), $object->toArray());
		$this->assertInstanceOf('Default_Model_DirectoryTest', $object);
	}
	
	public function testSave(){
		$dir = new Default_Model_DirectoryTest();
		$dir->save()->delete();
	}
	
	public function testGet(){
		$dir = new Default_Model_DirectoryTest();
		$dir->save();
		
		$mapper = new Default_Model_DirectoryTestMapper();
		$same = $mapper->get(Zest_Db_Model_Request::factory(array('id' => $dir->id)));
		
		$this->assertEquals($dir->toArray(), $same->toArray());
		$dir->delete();
	}
	
	public function testGetBy(){
		$dir = new Default_Model_DirectoryTest();
		$dir->save();
		
		$mapper = new Default_Model_DirectoryTestMapper();
		$same = $mapper->getById($dir->id);
		
		$this->assertEquals($dir->toArray(), $same->toArray());
		$dir->delete();
	}
	
	public function testGetArray(){
		$dir1 = new Default_Model_DirectoryTest();
		$dir1->save();
		$dir2 = new Default_Model_DirectoryTest();
		$dir2->save();
		
		$mapper = new Default_Model_DirectoryTestMapper();
		$array = $mapper->getArray();
		ksort($array);
		
		$this->assertEquals(array($dir1->id, $dir2->id), array_keys($array));
		
		$same1 = current($array);
		$this->assertEquals($dir1->id, $same1->id);
		
		$same2 = next($array);
		$this->assertEquals($dir2->id, $same2->id);
		
		$dir1->delete();
		$dir2->delete();
	}
	
	public function testGetArrayBy(){
		$dir1 = new Default_Model_DirectoryTest(array('name' => 'images'));
		$dir1->save();
		$dir2 = new Default_Model_DirectoryTest(array('name' => 'videos'));
		$dir2->save();
		
		$mapper = new Default_Model_DirectoryTestMapper();
		$array = $mapper->getArrayByName('images');
		ksort($array);
		
		$this->assertEquals(1, count($array));
		$this->assertEquals(array($dir1->id), array_keys($array));
		
		$same1 = current($array);
		$this->assertEquals($dir1->id, $same1->id);
		
		$dir1->delete();
		$dir2->delete();
	}
	
	public function testDelete(){
		$object = new Default_Model_DirectoryTest();
		$object->save()->delete();
	}
	
	public function testToArray(){
		$dir = new Default_Model_DirectoryTest();
		$this->assertEquals(array(), $dir->getMapper()->toArray($dir));
		
		$dir = new Default_Model_DirectoryTest(array());
		$array = $dir->getMapper()->toArray($dir);
		ksort($array);
		$this->assertEquals(array('id' => null, 'name' => null, 'parent_id' => null), $array);
	}
	
	public function testToObject(){
		$mapper = new Default_Model_DirectoryTestMapper();
		$object = $mapper->toObject(array('name' => 'images'));
		$this->assertInstanceOf('Default_Model_DirectoryTest', $object);
		$this->assertEquals('images', $object->name);
	}
	
	public function testIntersectPrimary(){
		$mapper = new Default_Model_DirectoryTestMapper();
		$array = $mapper->getIntersectPrimary(array('id' => 1, 'name' => 'images'));
		$this->assertEquals(array('id' => 1), $array);
	}
	
	public function testIntersectCols(){
		$mapper = new Default_Model_DirectoryTestMapper();
		$array = $mapper->getIntersectCols(array('id' => 1, 'name' => 'images', 'children' => array()));
		$this->assertEquals(array('id' => 1, 'name' => 'images'), $array);
	}
	
	public function testDbSelectDefault(){
		$mapper = new Default_Model_DirectoryTestMapper();
		$select = $mapper->getDbSelect(Zest_Db_Model_Request::factory(array()));
		$this->assertEquals('SELECT "directory"."id", "directory"."parent_id", "directory"."name" FROM "directory" WHERE (1)', $select->assemble());
	}
	
	public function testDbSelectCols(){
		$mapper = new Default_Model_DirectoryTestMapper();
		$select = $mapper->getDbSelect(Zest_Db_Model_Request::factory(array('id' => 1)));
		$this->assertEquals('SELECT "directory"."id", "directory"."parent_id", "directory"."name" FROM "directory" WHERE (("directory"."id" = 1))', $select->assemble());
	}
	
	public function testDbSelectMultiCols(){
		$mapper = new Default_Model_DirectoryTestMapper();
		$select = $mapper->getDbSelect(Zest_Db_Model_Request::factory(array('id' => 1, 'name' => 'images')));
		$this->assertEquals('SELECT "directory"."id", "directory"."parent_id", "directory"."name" FROM "directory" WHERE (("directory"."id" = 1) AND ("directory"."name" = \'images\'))', $select->assemble());
	}
	
	public function testDbSelectUndefinedCols(){
		$mapper = new Default_Model_DirectoryTestMapper();
		$select = $mapper->getDbSelect(Zest_Db_Model_Request::factory(array('id' => 1, 'children' => 'sea.jpg')));
		$this->assertEquals('SELECT "directory"."id", "directory"."parent_id", "directory"."name" FROM "directory" WHERE (("directory"."id" = 1))', $select->assemble());
	}
	
	public function testOptionOrder(){
		$mapper = new Default_Model_DirectoryTestMapper();
		$select = $mapper->getDbSelect(Zest_Db_Model_Request::factory(array('id' => 1), array('order' => 'id')));
		$this->assertEquals('SELECT "directory"."id", "directory"."parent_id", "directory"."name" FROM "directory" WHERE (("directory"."id" = 1)) ORDER BY "id" ASC', $select->assemble());
	}
	
	public function testOptionGroup(){
		$mapper = new Default_Model_DirectoryTestMapper();
		$select = $mapper->getDbSelect(Zest_Db_Model_Request::factory(array('id' => 1), array('group' => 'id')));
		$this->assertEquals('SELECT "directory"."id", "directory"."parent_id", "directory"."name" FROM "directory" WHERE (("directory"."id" = 1)) GROUP BY "id"', $select->assemble());
	}
	
	public function testOptionLimit(){
		$mapper = new Default_Model_DirectoryTestMapper();
		$select = $mapper->getDbSelect(Zest_Db_Model_Request::factory(array('id' => 1), array('limit' => array('count' => 1, 'offset' => 0))));
		$this->assertEquals('SELECT "directory"."id", "directory"."parent_id", "directory"."name" FROM "directory" WHERE (("directory"."id" = 1)) LIMIT 1', $select->assemble());
	}
	
	public function testOptionLimitPage(){
		$mapper = new Default_Model_DirectoryTestMapper();
		$select = $mapper->getDbSelect(Zest_Db_Model_Request::factory(array('id' => 1), array('limitPage' => array('page' => 1, 'rowCount' => 10))));
		$this->assertEquals('SELECT "directory"."id", "directory"."parent_id", "directory"."name" FROM "directory" WHERE (("directory"."id" = 1)) LIMIT 10', $select->assemble());
	}
	
}