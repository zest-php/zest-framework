<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage UnitTests
 */
class Zest_View_Helper_RenderMediaTest extends Zest_View_Helper_AbstractTest{
	
	public function testRenderMedia(){
		$this->_view->setScriptPath($this->_getScriptPath());
		$render = $this->_view->renderMedia($this->_getPathname());
		$this->assertRegExp('/<img src=".*\/tests\/libraries\/file\/[a-z0-9]+\/image.png" alt="image.png" \/>/', $render);
	}
	
	protected function _getPathname(){
		return Zest_AllTests::getDataDir().'/view/image.png';
	}
	
}