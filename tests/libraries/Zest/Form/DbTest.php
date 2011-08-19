<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage UnitTests
 */
class Zest_Form_DbTest extends PHPUnit_Framework_TestCase{
	
	/**
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected static $_db = null;
	
	public static function setUpBeforeClass(){
		if(!defined('ZEST_FORM_DBTEST_DIR')){
			define('ZEST_FORM_DBTEST_DIR', Zest_AllTests::getDataDir().'/form');
		}
		
		try{
			self::$_db = Zest_Db::getDbAdapter('form_db');
		}
		catch(Zest_Db_Exception $e){
			$config = new Zend_Config_Ini(self::_getConfigPathname());
			Zest_Db::setDbConfigs($config->toArray());
			self::$_db = Zest_Db::getDbAdapter('form_db');
		}
	}
	
	public static function tearDownAfterClass(){
		self::$_db->closeConnection();
		self::$_db = null;
	}
	
	protected function tearDown(){
		// @todo : vider la base
	}
	
	public function testGetDbMapperException(){
		$this->setExpectedException('Zest_Form_Exception');
		$form = new Zest_Form_Db();
		$form->getDbMapper();
	}
	
	public function testGetDbMapper(){
		$form = new Zest_Form_Db(array(
			'dbMapper' => 'Default_Model_UserTestMapper'
		));
		$this->assertInstanceOf('Zest_Db_Object_Mapper', $form->getDbMapper());
	}
	
	public function testGetDbMapperNotZestDbMapper(){
		$this->setExpectedException('Zest_Form_Exception');
		$form = new Zest_Form_Db(array(
			'dbMapper' => new Default_Model_UserTest()
		));
	}
	
	public function testSetAndGetDbObject(){
		$object = new Default_Model_UserTest();
		$form = new Zest_Form_Db(array(
			'dbMapper' => 'Default_Model_UserTestMapper',
			'dbObject' => $object
		));
		$this->assertTrue($form->getDbObject() === $object);
	}
	
	public function testSetAndGetDbObjectPrimary(){
		$user = $this->_getFirstUser();
		$form = new Zest_Form_Db(array(
			'dbMapper' => 'Default_Model_UserTestMapper',
			'dbObject' => $user->id
		));
		$this->assertEquals($user->id, $form->getDbObject()->id);
	}
	
	public function testGetDbObjectBuild(){
		$object = new Default_Model_UserTest();
		$form = new Zest_Form_Db(array(
			'dbMapper' => 'Default_Model_UserTestMapper'
		));
		$this->assertFalse($form->getDbObject() === $object);
	}
	
	public function testGetDbObjectNotBuild(){
		$form = new Zest_Form_Db(array(
			'dbMapper' => 'Default_Model_UserTestMapper'
		));
		$this->assertNull($form->getDbObject(false, false));
	}
	
	public function testGetDbObjectNotBuildException(){
		$this->setExpectedException('Zest_Form_Exception');
		$form = new Zest_Form_Db(array(
			'dbMapper' => 'Default_Model_UserTestMapper'
		));
		$this->assertNull($form->getDbObject(false));
	}
	
	public function testPopulateNoDbObject(){
		$form = new Zest_Form_Db(array(
			'dbMapper' => 'Default_Model_UserTestMapper'
		));
		$form->populateFromDbObject();
		$this->assertNotNull($form->getDbObject(false, false));
	}
	
	public function testPopulateDefaultDbObject(){
		$object = new Default_Model_UserTest();
		$form = new Zest_Form_Db(array(
			'dbMapper' => 'Default_Model_UserTestMapper',
			'dbObject' => $object
		));
		$form->populateFromDbObject();
	}
	
	public function testPopulateDirectDbObject(){
		$form = new Zest_Form_Db(array(
			'dbMapper' => 'Default_Model_UserTestMapper'
		));
		$object = new Default_Model_UserTest();
		$form->populateFromDbObject($object);
		$this->assertTrue($object === $form->getDbObject());
	}
	
	public function testGetValuesCreate(){
		$form = new Zest_Form_Db(array(
			'dbMapper' => 'Default_Model_UserTestMapper',
			'elements' => array(
				array('text', 'login', array('label' => 'Identifiant')),
				array('text', 'password', array('label' => 'Mot de passe'))
			)
		));
		$form->populate(array(
			'login' => 'testGetValues',
			'password' => '123456'
		));
		$dbObject = $form->getValuesToDbObject()->getDbObject();
		$this->assertEquals('testGetValues', $dbObject->login);
		$this->assertEquals('123456', $dbObject->password);
	}
	
	public function testGetValuesUpdate(){
		$form = new Zest_Form_Db(array(
			'dbMapper' => 'Default_Model_UserTestMapper',
			'dbObject' => $this->_getFirstUser()->id,
			'elements' => array(
				array('text', 'login', array('label' => 'Identifiant')),
				array('text', 'password', array('label' => 'Mot de passe'))
			)
		));
		$form->populate(array(
			'password' => '123456789'
		));
		$this->assertEquals('123456', $form->getDbObject()->password);
		$form->getValuesToDbObject();
		$this->assertEquals('123456789', $form->getDbObject()->password);
	}
	
	public function testGetValuesFileElement(){
		$detination = Zest_AllTests::getTempDir().'/Zest_Form_DbTest';
		if(!file_exists($detination)){
			mkdir($detination);
		}
		$form = new Zest_Form_Db(array(
			'dbMapper' => 'Default_Model_UserTestMapper',
			'dbObject' => $this->_getFirstUser()->id,
			'elements' => array(
				new Default_Form_Element_File('image', array(
					'label' => 'Miniature',
					'destination' => $detination
				))
			)
		));
		$this->assertEquals($detination.'/image.png', $form->image->getPathname());
		$dbObject = $form->getValuesToDbObject()->getDbObject();
		$this->assertEquals('image.png', $dbObject->image);
	}
	
//	public function testValidatorsFromDbTable(){
//		$form = new Zest_Form_Db(array(
//			'dbMapper' => 'Default_Model_UserTestMapper',
//			'elements' => array(
//				array('text', 'login', array('label' => 'Identifiant'))
//			)
//		));
//		$form->addValidatorsFromDbTable();
//		$this->assertGreaterThan(0, $form->login->getValidator('stringLength')->getMax());
//		$this->assertGreaterThan(0, $form->login->getAttrib('maxlength'));
//	}
//	
//	public function testValidatorsFromDbTableNoOverride(){
//		$form = new Zest_Form_Db(array(
//			'dbMapper' => 'Default_Model_UserTestMapper',
//			'elements' => array(
//				array('text', 'login', array('label' => 'Identifiant', 'validators' => array(
//					array('validator' => 'stringLength', 'options' => array('max' => 10))
//				)))
//			)
//		));
//		$form->addValidatorsFromDbTable();
//		$this->assertEquals(10, $form->login->getValidator('stringLength')->getMax());
//		$this->assertEquals(10, $form->login->getAttrib('maxlength'));
//	}
	
	protected function _getFirstUser(){
		$users = Zest_Db_Model::getInstance('Default_Model_UserTestMapper')->getArray();
		return reset($users);
	}
	
	protected static function _getConfigPathname(){
		return Zest_AllTests::getDataDir().'/form/database.ini';
	}
	
}

class Default_Form_Element_File extends Zest_Form_Element_File{
	protected $_value = 'image.png';
}

class Default_Model_UserTest extends Zest_Db_Object{
}

class Default_Model_UserTestMapper extends Zest_Db_Object_Mapper{
}

class Default_Model_DbTable_UserTest extends Zest_Db_Table{
	protected $_name = 'user';
	protected $_primary = 'id';
	protected $_adapterConfigName = 'form_db';
}