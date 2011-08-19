<?php

/**
 * @category Zest
 * @package Zest_Acl
 * @subpackage UnitTests
 */
class Zest_Acl_NoConstrainTest extends PHPUnit_Framework_TestCase{
	
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
	
	public function testAddResourceString(){
		$this->_acl->addResource('product');
		$this->assertInstanceOf('Zend_Acl_Resource', $this->_acl->get('product'));
	}
	
	public function testAddResourceObject(){
		$resource = new Zend_Acl_Resource('product');
		$this->_acl->addResource($resource);
		$this->assertEquals('Zend_Acl_Resource', get_class($this->_acl->get('product')));
	}
	
	public function testAddExistingResource(){
		$this->_acl->addResource('product');
		$this->_acl->addResource('product');
	}
	
	public function testAddResourceParent(){
		$this->_acl->addResource('product');
		$this->_acl->addResource('phone', 'product');
		$this->assertTrue($this->_acl->inherits('phone', 'product'));
	}
	
	public function testAddRoleString(){
		$this->_acl->addRole('merchant');
		$this->assertInstanceOf('Zend_Acl_Role', $this->_acl->getRole('merchant'));
	}
	
	public function testAddRoleObject(){
		$role = new Zend_Acl_Role('merchant');
		$this->_acl->addRole($role);
		$this->assertEquals('Zend_Acl_Role', get_class($this->_acl->getRole('merchant')));
	}
	
	public function testAddExistingRole(){
		$this->_acl->addRole('merchant');
		$this->_acl->addRole('merchant');
	}
	
	public function testAddRoleParents(){
		$this->_acl->addRole('merchant');
		$this->_acl->addRole('phone_merchant', 'merchant');
		
		$this->assertTrue($this->_acl->inheritsRole('phone_merchant', 'merchant'));
	}
	
	public function testAllowRole(){
		$this->_acl->addRole('merchant');
		
		$this->_acl->allow('merchant');
		
		$this->assertTrue($this->_acl->isAllowed('merchant'));
		$this->assertFalse($this->_acl->isAllowed('merchant', 'product'));
		$this->assertFalse($this->_acl->isAllowed('merchant', 'product', 'sell'));
	}
	
	public function testAllowRoleResource(){
		$this->_acl->addRole('merchant');
		$this->_acl->addResource('product');
		
		$this->_acl->allow('merchant', 'product');
		
		$this->assertFalse($this->_acl->isAllowed('merchant'));
		$this->assertTrue($this->_acl->isAllowed('merchant', 'product'));
		$this->assertTrue($this->_acl->isAllowed('merchant', 'product', 'sell'));
	}
	
	public function testAllowRoleResourcePrivilege(){
		$this->_acl->addRole('merchant');
		$this->_acl->addResource('product');
		
		$this->_acl->allow('merchant', 'product', 'sell');
		
		$this->assertFalse($this->_acl->isAllowed('merchant'));
		$this->assertFalse($this->_acl->isAllowed('merchant', 'product'));
		$this->assertTrue($this->_acl->isAllowed('merchant', 'product', 'sell'));
	}
	
	public function testDenyRole(){
		$this->_acl->addRole('merchant');
		$this->_acl->addRole('particular', 'merchant');
		
		$this->_acl->allow('merchant');
		$this->_acl->deny('particular');
		
		$this->assertFalse($this->_acl->isAllowed('particular'));
		$this->assertFalse($this->_acl->isAllowed('particular', 'product'));
		$this->assertFalse($this->_acl->isAllowed('particular', 'product', 'sell'));
	}
	
	public function testDenyRoleResource(){
		$this->_acl->addRole('merchant');
		$this->_acl->addRole('particular', 'merchant');
		$this->_acl->addRole('license_4_merchant', 'merchant');
		
		$this->_acl->addResource('product');
		$this->_acl->addResource('alcohol', 'product');
		
		$this->_acl->allow('merchant', 'product');
		$this->_acl->deny('merchant', 'alcohol');
		$this->_acl->allow('license_4_merchant', 'alcohol');
		
		$this->assertFalse($this->_acl->isAllowed('particular'));
		$this->assertTrue($this->_acl->isAllowed('particular', 'product'));
		$this->assertFalse($this->_acl->isAllowed('particular', 'alcohol'));
	}
	
	public function testDenyRoleResourcePrivilege(){
		$this->_acl->addRole('merchant');
		$this->_acl->addRole('particular', 'merchant');
		$this->_acl->addRole('license_4_merchant', 'merchant');
		
		$this->_acl->addResource('product');
		$this->_acl->addResource('alcohol', 'product');
		
		$this->_acl->allow('merchant', 'product', 'sell');
		$this->_acl->deny('merchant', 'alcohol');
		$this->_acl->allow('license_4_merchant', 'alcohol', 'sell');
		$this->_acl->allow('particular', 'alcohol', 'drink');
		
		$this->assertFalse($this->_acl->isAllowed('particular'));
		$this->assertTrue($this->_acl->isAllowed('particular', 'product', 'sell'));
		$this->assertFalse($this->_acl->isAllowed('particular', 'alcohol', 'sell'));
		$this->assertTrue($this->_acl->isAllowed('particular', 'alcohol', 'drink'));
	}
	
}