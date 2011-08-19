<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage UnitTests
 */
abstract class Zest_View_AbstractTest extends PHPUnit_Framework_TestCase{
	
	/**
	 * @var Zest_View
	 */
	protected $_view = null;
	
	protected function setUp(){
		$this->_view = new Zest_View();
	}
	
	protected function _getScriptPath(){
		return Zest_AllTests::getDataDir().'/view';
	}
	
}