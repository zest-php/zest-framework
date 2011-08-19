<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage UnitTests
 */
class Zest_View_Helper_UrlTest extends Zest_View_Helper_AbstractTest{
	
	public function testWeb(){
		Zest_Controller_Front::getInstance()->setRequest(new Zend_Controller_Request_Http());
		$this->assertRegExp('/^\/.*\/tests\/libraries\/testWeb$/', $this->_view->url('testWeb'));
	}
	
	public function testRoute(){
		Zest_Controller_Front::getInstance()->getRouter()->addDefaultRoutes();
		$this->assertRegExp('/\/.*\/tests\/libraries\/index\/testRoute/', $this->_view->url(array('action' => 'testRoute')));
	}
	
}