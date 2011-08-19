<?php

/**
 * @category Zest
 * @package Zest_Layout
 * @subpackage UnitTests
 */
class Zest_Layout_LayoutTest extends PHPUnit_Framework_TestCase{
	
	/**
	 * @var Zest_Layout
	 */
	protected $_layout = null;
	
	protected function setUp(){
		$this->_layout = new Zest_Layout();
		$this->_layout->setLayoutPath($this->_getLayoutPath());
		unset($this->_layout->content);
		
		$view = Zest_View::getStaticView();
		$this->_layout->setView($view);
		
		$view->getHelper('layout')->setLayout($this->_layout);
	}
	
	public function testInstance(){
		$this->assertInstanceOf('Zest_Layout', $this->_layout);
	}
	
	public function testExtends(){
		$this->_layout->setLayout('extends_sidebar');
		
		$xml = new SimpleXMLElement($this->_layout->render());
		$this->_testXml($xml);
	}
	
	public function testMultiExtends(){
		$this->_layout->setLayout('multiextends_sidebar');
		
		$xml = new SimpleXMLElement($this->_layout->render());
		$this->_testXml($xml);
	}
	
	public function testExtendsModule(){
		$this->_layout->setLayout('extendsmodule_sidebar');
		$this->_layout->setLayoutPath($this->_getLayoutPath(), 'testExtendsModule');
		
		$xml = new SimpleXMLElement($this->_layout->render());
		$this->_testXml($xml);
	}
	
	public function testExtendsExceptionOutsideRender(){
		$this->setExpectedException('Zest_Layout_Exception');
		$this->_layout->extends('testExtendsExceptionOutsideRender');
	}
	
	public function testExtendsExceptionNoLayoutPathForModule(){
		$this->setExpectedException('Zest_Layout_Exception');
		$this->_layout->setLayout('extendsmodule_exception_default');
		$this->_layout->render();
	}
	
	protected function _testXml(SimpleXMLElement $xml){
		$this->assertEquals('container', (string) $xml->body->div['id']);
		$this->assertEquals('left', (string) $xml->body->div->div[0]['id']);
		$this->assertEquals('sidebar', (string) $xml->body->div->div[1]['id']);
	}
	
	protected function _getLayoutPath(){
		return Zest_AllTests::getDataDir().'/layout';
	}
	
}