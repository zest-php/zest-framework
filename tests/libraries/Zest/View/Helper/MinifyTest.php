<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage UnitTests
 */
class Zest_View_Helper_MinifyTest extends Zest_View_Helper_AbstractTest{
	
	public function testMinifyException(){
		$this->setExpectedException('Zest_View_Exception');
		$this->_view->minify('text', 'testMinifyException');
	}
	
	public function testMinify(){
		$minify = $this->_view->minify()->css(file_get_contents(Zest_AllTests::getDataDir().'/minify/css.css'));
		$minified = file_get_contents(Zest_AllTests::getDataDir().'/minify/css_minified.css');
		$this->assertEquals($minified, $minify);
	}
	
}