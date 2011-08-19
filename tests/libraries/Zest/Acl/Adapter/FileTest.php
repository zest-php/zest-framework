<?php

/**
 * @category Zest
 * @package Zest_Acl
 * @subpackage UnitTests
 */
class Zest_Acl_Adapter_FileTest extends PHPUnit_Framework_TestCase{
	
	/**
	 * @var Zest_Acl
	 */
	protected $_acl = null;
	
	protected function setUp(){
		$this->_acl = new Zest_Acl();
		
		$dir = dirname($this->_getPathname());
		
		if(file_exists($dir)){
			foreach(glob($dir.'/*') as $file){
				unlink($file);
			}
		}
		else{
			mkdir($dir);
		}
		
	}
		
	protected function tearDown(){
		unset($this->_acl);
	}
	
	public function testDefaultAdapter(){
		$this->assertInstanceOf('Zest_Acl_Adapter_File', $this->_acl->getAdapter());
	}
	
	public function testAdapterFileSave(){
		$cache = $this->_getPathname();
		if(file_exists($cache)){
			unlink($cache);
		}
		Zest_Acl_Adapter_File::setDefaultCacheFile($cache);
		$this->_acl->save();
		
		$children = glob(dirname($cache).'/*');
		$this->assertEquals(1, count($children));
	}
	
	public function testAdapterFileLoad(){
		$cache = $this->_getPathname();
		if(file_exists($cache)){
			unlink($cache);
		}
		Zest_Acl_Adapter_File::setDefaultCacheFile($cache);
		
		$this->_acl->addResource('product');
		$this->_acl->addRole('merchant');
		$this->_acl->allow('merchant', 'product', 'sell');
		$this->_acl->save();
		
		$this->_acl = new Zest_Acl();
		$this->_acl->load();
		$this->assertTrue($this->_acl->has('product'));
		$this->assertTrue($this->_acl->hasRole('merchant'));
		$this->assertTrue($this->_acl->isAllowed('merchant', 'product', 'sell'));
	}
	
	protected function _getPathname(){
		return Zest_AllTests::getTempDir().'/Zest_Acl_AdapterFileTest/cache';
	}
	
}