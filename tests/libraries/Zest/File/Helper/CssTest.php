<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage UnitTests
 */
class Zest_File_Helper_CssTest extends PHPUnit_Framework_TestCase{

	/**
	 * @var Zest_File
	 */
	protected $_file = null;
	
	protected function setUp(){
		$this->_file = new Zest_File($this->_getPathname());
	}
	
	public function testGetCssHtmlTag(){
		$css = $this->_file->getCss('html_tag');
		ksort($css);
		$this->assertEquals(array('border' => '1px solid black'), $css);
	}
	
	public function testGetCssHtmlTagMulti(){
		$css = $this->_file->getCss('html_tag_multi');
		ksort($css);
		$this->assertEquals(array('border' => '1px solid black', 'font-size' => '10px'), $css);
	}
	
	public function testGetCssHtmlTagWithoutEndingSemicolon(){
		$css = $this->_file->getCss('html_tag_semicolon');
		ksort($css);
		$this->assertEquals(array('border' => '1px solid red'), $css);
	}
	
	public function testGetCssClass(){
	}
	
	public function testGetCssId(){
	}
	
	public function testGetCssSpeudo(){
	}
	
	public function testGetCssHtmlTagClass(){
	}
	
	public function testGetCssHtmlTagId(){
	}
	
	public function testGetCssHtmlTagSpeudo(){
	}
	
	public function testGetCssHtmlTagIdClass(){
	}
	
	public function testGetCssHtmlTagIdClassPseudo(){
	}
	
	public function testAddCssHtmlTag(){
	}
	
	public function testAddCssClass(){
	}
	
	public function testAddCssId(){
	}
	
	public function testAddCssPseudo(){
	}
	
	public function testAddCssHtmlTagClass(){
	}
	
	public function testAddCssHtmlTagId(){
	}
	
	public function testAddCssHtmlTagPseudo(){
	}
	
	public function testAddCssHtmlTagIdClass(){
	}
	
	public function testAddCssHtmlTagIdClassPseudo(){
	}
	
	protected function _getPathname(){
		return Zest_AllTests::getDataDir().'/file/css.css';
	}
	
}