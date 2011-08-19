<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage UnitTests
 */
class Zest_File_Helper_HtmlTest extends PHPUnit_Framework_TestCase{

	/**
	 * @var Zest_File
	 */
	protected $_file = null;
	
	protected function setUp(){
		$this->_file = new Zest_File($this->_getPathname());
	}

	public function testFindInstance(){
		$result = $this->_file->find('body');
		$this->assertInstanceOf('Zend_Dom_Query_Result', $result);
	}

	public function testFindResult(){
		$result = $this->_file->find('body');
		
		$body = null;
		foreach($result as $node){
			$this->assertInstanceOf('DOMElement', $node);
			$body = trim($node->textContent);
		}
		$this->assertEquals('body', $body);
	}
	
	protected function _getPathname(){
		return Zest_AllTests::getDataDir().'/file/html.html';
	}
	
}