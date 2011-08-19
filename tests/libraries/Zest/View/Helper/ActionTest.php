<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage UnitTests
 */
class Zest_View_Helper_ActionTest extends Zest_View_Helper_AbstractTest{
	
	public function testAction(){
		$default = Zest_AllTests::getDataDir().'/view/application/private/default';
		
		$request = new Zend_Controller_Request_Http();
		$request->setQuery('variable', 'testAction');
		$response = new Zend_Controller_Response_Http();
		
		Zest_Controller_Front::getInstance()
			->setControllerDirectory($default.'/controllers')
			->setRequest($request)
			->setResponse($response);
		
		$this->_view->setScriptPath($default.'/views/script');
		$render = $this->_view->action('index', 'index');
		
		$this->assertEquals('testAction', $render);
	}
	
}