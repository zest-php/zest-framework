<?php

/**
 * @category Zest
 * @package Zest_Db
 * @subpackage UnitTests
 */
abstract class Zest_Db_AbstractTest extends PHPUnit_Framework_TestCase{
	
	/**
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected static $_db = null;
	
	public static function setUpBeforeClass(){
		if(!defined('ZEST_DB_ABSTRACTTEST_DIR')){
			define('ZEST_DB_ABSTRACTTEST_DIR', Zest_AllTests::getDataDir().'/db');
		}
		
		try{
			self::$_db = Zest_Db::getDbAdapter('default');
		}
		catch(Zest_Db_Exception $e){
			$config = new Zend_Config_Ini(self::_getConfigPathname());
			Zest_Db::setDbConfigs($config->toArray());
			self::$_db = Zest_Db::getDbAdapter('default');
		}
	}
	
	public static function tearDownAfterClass(){
		self::$_db->closeConnection();
		self::$_db = null;
	}
	
	protected function tearDown(){
		// @todo : vider la base
	}
	
	protected static function _getConfigPathname(){
		return Zest_AllTests::getDataDir().'/db/database.ini';
	}
	
}

class Default_Model_DirectoryTest extends Zest_Db_Object{
}

class Default_Model_DirectoryTestMapper extends Zest_Db_Object_Mapper{
}

class Default_Model_DbTable_DirectoryTest extends Zest_Db_Table{
	protected $_name = 'directory';
}

class Default_Model_FileTest extends Zest_Db_Object{
}

class Default_Model_FileTestMapper extends Zest_Db_Object_Mapper{
}

class Default_Model_DbTable_FileTest extends Zest_Db_Table{
	protected $_name = 'file';
}