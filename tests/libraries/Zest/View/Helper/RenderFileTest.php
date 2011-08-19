<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage UnitTests
 */
class Zest_View_Helper_RenderFileTest extends Zest_View_Helper_AbstractTest{
	
	public function testRenderFile(){
		$this->_view->setScriptPath($this->_getScriptPath());
		$render = $this->_view->renderFile($this->_getPathname());
		$this->assertRegExp('/<a href=".*\/tests\/libraries\/file\/[a-z0-9]+\/image.png">image.png<\/a>/', $render);
	}
	
	protected function _getPathname(){
		return Zest_AllTests::getDataDir().'/view/image.png';
	}
	
}