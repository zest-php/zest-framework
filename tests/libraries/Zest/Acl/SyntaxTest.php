<?php

/**
 * @category Zest
 * @package Zest_Acl
 * @subpackage UnitTests
 */
class Zest_Acl_SyntaxTest extends PHPUnit_Framework_TestCase{
	
	/**
	 * @var Zest_Acl
	 */
	protected $_acl = null;
	
	protected function setUp(){
		$this->_acl = new Zest_Acl();
		$this->_acl->addRole('merchant');
		$this->_acl->addResource('product', null, array('class_name' => 'stdClass', 'primary_attribute' => 'id'));
	}
	
	protected function tearDown(){
		unset($this->_acl);
	}
	
	public function testAllowObject(){
		$product = new stdClass();
		$product->id = 1;
		$this->_acl->allow('merchant', $product);
	}
	
	public function testAllowArrayResourceIdInteger(){
		$this->_acl->allow('merchant', array('product' => 1));
	}
	
	public function testAllowArrayResourceIdObject(){
		$product = new stdClass();
		$product->id = 1;
		$this->_acl->allow('merchant', array('product' => $product));
	}
	
	public function testAllowArrayResourceIdMultiple(){
		$product = new stdClass();
		$product->id = 1;
		$this->_acl->allow('merchant', array('product' => array(1, $product)));
	}
	
	public function testAllowObjectAccessor(){
		$this->_acl->get('product')->setClassName('ZestAclSyntaxTestProduct');
		$this->_acl->allow('merchant', new ZestAclSyntaxTestProduct());
	}
	
	public function testAllowWithPrivilege(){
		$product = new stdClass();
		$product->id = 1;
		$this->_acl->allow('merchant', $product, 'sell');
	}
	
	public function testIsAllowedObject(){
		$product = new stdClass();
		$product->id = 1;
		$this->_acl->allow('merchant', $product);
		$this->assertTrue($this->_acl->isAllowed('merchant', $product));
	}
	
	public function testIsAllowedArrayResourceIdInteger(){
		$this->_acl->allow('merchant', array('product' => 1));
		$this->assertTrue($this->_acl->isAllowed('merchant', array('product', 1)));
	}
	
	public function testIsAllowedArrayResourceIdObject(){
		$product = new stdClass();
		$product->id = 1;
		$this->_acl->allow('merchant', array('product' => $product));
		$this->assertTrue($this->_acl->isAllowed('merchant', array('product', $product)));
	}
	
	public function testIsAllowedArrayResourceIdMultiple(){
		$product = new stdClass();
		$product->id = 1;
		$this->_acl->allow('merchant', array('product' => array(1, $product)));
		$this->assertTrue($this->_acl->isAllowed('merchant', array('product', 1)));
		$this->assertTrue($this->_acl->isAllowed('merchant', array('product', $product)));
	}
	
	public function testIsAllowedObjectAccessor(){
		$this->_acl->get('product')->setClassName('ZestAclSyntaxTestProduct');
		$this->_acl->allow('merchant', new ZestAclSyntaxTestProduct());
		$this->assertTrue($this->_acl->isAllowed('merchant', array('product', 1)));
	}
	
	public function testIsAllowedWithPrivilege(){
		$this->_acl->allow('merchant', array('product' => 1), 'sell');
		$this->assertTrue($this->_acl->isAllowed('merchant', array('product', 1), 'sell'));
	}
	
}

class ZestAclSyntaxTestProduct{
	
	public function getId(){
		return 1;
	}
	
}