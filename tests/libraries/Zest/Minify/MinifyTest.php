<?php

/**
 * @category Zest
 * @package Zest_Minify
 * @subpackage UnitTests
 */
class Zest_Minify_MinifyTest extends PHPUnit_Framework_TestCase{
	
	public function testCss(){
		$minify = Zest_Minify::minifyCss(file_get_contents(Zest_AllTests::getDataDir().'/minify/css.css'));
		$minified = file_get_contents(Zest_AllTests::getDataDir().'/minify/css_minified.css');
		$this->assertEquals($minified, $minify);
	}
	
	public function testHtml(){
		$minify = Zest_Minify::minifyHtml(file_get_contents(Zest_AllTests::getDataDir().'/minify/html.html'));
		$minified = file_get_contents(Zest_AllTests::getDataDir().'/minify/html_minified.html');
		$this->assertEquals($minified, $minify);
	}
	
	public function testScript(){
		$minify = Zest_Minify::minifyScript(file_get_contents(Zest_AllTests::getDataDir().'/minify/script.js'));
		$minified = file_get_contents(Zest_AllTests::getDataDir().'/minify/script_minified.js');
		$this->assertEquals($minified, $minify);
	}
	
}