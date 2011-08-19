<?php

/**
 * @category Zest
 * @package Zest_Config
 * @subpackage UnitTests
 */
class Zest_Config_ApplicationTest extends PHPUnit_Framework_TestCase{
	
	/**
	 * @var string
	 */
	protected static $_environment = 'test';
	
	public static function setUpBeforeClass(){
		define('ZEST_CONFIG_APPLICATIONTEST_CONSTANT', 'constant');
		define('ZEST_CONFIG_APPLICATIONTEST_DIR', dirname(self::_getPathname()));
		
		$options = array(
			'pathname' => self::_getPathname(),
			'modules_config_format' => '/configs/module.ini'
		);
		Zest_Config_Application::initInstance(self::$_environment, $options, 'Zest_Config_ApplicationTest_GetModulesDirectories');
	}
	
	public function testGet(){
		$this->assertEquals('just a string value', Zest_Config_Application::get('get'));
	}

	public function testModuleGet(){
		$this->assertEquals('my module config', $this->_moduleConfigGet('get'));
	}

	public function testEnvironment(){
		$this->assertEquals(self::$_environment, $this->_moduleConfigGet('environment'));
	}

	public function testConstant(){
		$this->assertEquals(ZEST_CONFIG_APPLICATIONTEST_CONSTANT, $this->_moduleConfigGet('constant'));
	}
	
	public function testInternalVar(){
		$this->assertEquals('my module config', $this->_moduleConfigGet('var.internal'));
	}
	
	public function testGlobalVar(){
		$this->assertEquals('global var', $this->_moduleConfigGet('var.global'));
	}
	
	public function testRecursiveVar(){
		$this->assertEquals('my module config', $this->_moduleConfigGet('var.recursive'));
	}
	
	public function testChildren(){
		$this->assertEquals('child', $this->_moduleConfigGet('inherit'));
	}
	
	public function testChildrenVarsOverride(){
		$this->assertEquals('I override you', $this->_moduleConfigGet('child.override_var'));
	}
	
	public function testRequestArray(){
		$keys = array('base_path', 'http_host', 'scheme');
		foreach($keys as $key){
			$this->assertNotEmpty(Zest_Config_Application::get('request.'.$key));
		}
	}
	
	public function testModuleDirectory(){
		$this->assertNotEmpty($this->_moduleConfigGet('module_directory'));
	}
	
	protected static function _moduleConfigGet($key){
		return Zest_Config_Application::get('module.application_test.'.$key);
	}
	
	protected static function _getPathname(){
		return Zest_AllTests::getDataDir().'/config/application.ini';
	}
	   
}

function Zest_Config_ApplicationTest_GetModulesDirectories(){
	return array(
		'application_test' => Zest_AllTests::getDataDir().'/config/application/modules/application_test/private/default'
	);
}