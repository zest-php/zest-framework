<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage UnitTests
 */
class Zest_View_Helper_LogTest extends Zest_View_Helper_AbstractTest{
	
	public static function setUpBeforeClass(){
		// priority : err, warn, notice, debug
		Zest_Log::addWriter('warn', 'print');
	}
	
	public function testLog(){
		ob_start();
		$this->_view->log('testLog', 'warn');
		$contents = ob_get_clean();
		$this->assertEquals('testLog', strip_tags($contents));
	}
	
}