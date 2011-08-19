<?php

/**
 * @category Zest
 * @package Zest_Db
 * @subpackage UnitTests
 */
class Zest_Db_DbTest extends Zest_Db_AbstractTest{
	
	/**
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected static $_otherDb = null;
	
	public static function setUpBeforeClass(){
		parent::setUpBeforeClass();
		
		try{
			self::$_otherDb = Zest_Db::getDbAdapter('other');
		}
		catch(Zest_Db_Exception $e){
			$config = new Zend_Config_Ini(self::_getConfigPathname());
			Zest_Db::setDbConfigs($config->toArray());
			self::$_otherDb = Zest_Db::getDbAdapter('other');
		}
	}
	
	public static function tearDownAfterClass(){
		self::$_otherDb->closeConnection();
		self::$_otherDb = null;
	}
	
	public function testConfig(){
		$tables = array('directory', 'file');
		$this->assertEquals($tables, self::$_db->listTables());
		
		$this->assertEquals('array', gettype(self::$_otherDb->listTables()));
	}
	
	public function testUndefinedAdapter(){
		$this->setExpectedException('Zest_Db_Exception');
		Zest_Db::getDbAdapter('another');
	}
	
}