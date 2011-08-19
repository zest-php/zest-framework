<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage UnitTests
 */
class Zest_View_Helper_ConfigTest extends Zest_View_Helper_AbstractTest{
	
	public function testConfig(){
		$options = array(
			'pathname' => $this->_getPathname()
		);
		Zest_Config_Application::initInstance(null, $options);
		
		$this->assertTrue(is_array($this->_view->config(null)));
		$this->assertEquals('testConfig', $this->_view->config('variable'));
	}
	
	protected function _getPathname(){
		return Zest_AllTests::getDataDir().'/view/config.ini';
	}
	
}