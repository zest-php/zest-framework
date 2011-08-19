<?php

/**
 * @category Zest
 * @package Zest_Acl
 * @subpackage UnitTests
 */
class Zest_Acl_ExceptionTest extends PHPUnit_Framework_TestCase{
	
	/**
	 * @var Zest_Acl
	 */
	protected $_acl = null;
	
	protected function setUp(){
		$this->_acl = new Zest_Acl();
	}
	
	protected function tearDown(){
		unset($this->_acl);
	}
	
	public function testAdapterInstanceOf(){
		$this->setExpectedException('Zest_Acl_Exception');
		$this->_acl->setAdapter('stdClass');
	}
	
	public function testAllowNoResourceForObject(){
		$this->_acl->addRole('merchant');
		$this->setExpectedException('Zest_Acl_Exception');
		$this->_acl->allow('merchant', new stdClass());
	}
	
	public function testAllowResourceIdKeyObjectValueZendResource(){
		$this->_acl->addRole('merchant');
		$this->_acl->addResource(new Zend_Acl_Resource('product'));
		$this->setExpectedException('Zest_Acl_Exception');
		$this->_acl->allow('merchant', array('product' => 1));
	}
	
	public function testIsAllowedNoResourceForObject(){
		$this->_acl->addRole('merchant');
		$this->setExpectedException('Zest_Acl_Exception');
		$this->_acl->isAllowed('merchant', new stdClass());
	}
	
	public function testIsAllowedResourceArray(){
		$this->_acl->addRole('merchant');
		$this->_acl->addResource('product');
		$this->setExpectedException('Zest_Acl_Exception');
		$this->_acl->isAllowed('merchant', array('product'));
	}
	
	public function testIsAllowedResourceArrayZendResource(){
		$this->_acl->addRole('merchant');
		$this->_acl->addResource(new Zend_Acl_Resource('product'));
		$this->setExpectedException('Zest_Acl_Exception');
		$this->_acl->isAllowed('merchant', array('product', 1));
	}
	
	public function testUndefinedPrimaryAttribute(){
		$this->_acl->addRole('merchant');
		$this->_acl->addResource('product');
		$this->setExpectedException('Zest_Acl_Exception');
		$this->_acl->isAllowed('merchant', array('product', new stdClass()));
	}
	
	public function testUnfoundablePrimaryAttribute(){
		$this->_acl->addRole('merchant');
		$this->_acl->addResource('product', null, array('primary_attribute' => 'id'));
		$this->setExpectedException('Zest_Acl_Exception');
		$this->_acl->isAllowed('merchant', array('product', new stdClass()));
	}
	
}