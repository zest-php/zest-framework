<?php

/**
 * @category Zest
 * @package Zest_Log
 * @subpackage UnitTests
 */
class Zest_Log_LogTest extends PHPUnit_Framework_TestCase{
	
	public static function setUpBeforeClass(){
		$dir = dirname(self::_getPathname());
		if(!file_exists($dir)){
			mkdir($dir);
		}
		
		// priority : err, warn, notice, debug
		Zest_Log::addWriter('debug', 'stream', array(self::_getPathname(), 'w+'));
		Zest_Log::addWriter('notice', 'print');
	}
	
	public function testStream(){
		Zest_Log::debug('testStream');
		$contents = file_get_contents(self::_getPathname());
		$this->assertStringEndsWith('DEBUG (7): testStream', trim($contents));
	}
	
	public function testPrint(){
		ob_start();
		Zest_Log::notice('testPrint');
		$contents = ob_get_clean();
		$this->assertEquals('testPrint', strip_tags($contents));
	}
	
	protected static function _getPathname(){
		return Zest_AllTests::getTempDir().'/Zest_Log_LogTest/debug.log';
	}
	
}