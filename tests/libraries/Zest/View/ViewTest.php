<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage UnitTests
 */
class Zest_View_ViewTest extends Zest_View_AbstractTest{
	
	public function testHelperPath(){
		$paths = $this->_view->getHelperPaths();
		$this->assertArrayHasKey('Zest_View_Helper_', $paths);
		$this->assertEquals('Zest_View_Helper_Url', $this->_view->getPluginLoader('helper')->load('url'));
	}
	
	public function testFilterPath(){
		$paths = $this->_view->getFilterPaths();
		$this->assertArrayHasKey('Zest_View_Filter_', $paths);
	}
	
	public function testSetOptions(){
		$encoding = $this->_view->getEncoding();
		$scriptPaths = $this->_view->getScriptPaths();
		
		$this->_view->setOptions(array(
			'encoding' => 'iso-8859-1',
			'scriptPath' => $this->_getScriptPath()
		));
		$this->assertNotEquals($encoding, $this->_view->getEncoding());
		$this->assertNotEquals($scriptPaths, $this->_view->getScriptPaths());
		$this->assertEquals(1, count($this->_view->getScriptPaths()));
	}
	
	public function testDoctypeException(){
		$this->setExpectedException('Zest_View_Exception');
		$this->_view->setDoctype('testDoctype');
	}
	
	public function testDoctype(){
		$this->_view->setDoctype('xhtml1_transitional');
		$this->assertEquals(Zend_View_Helper_Doctype::XHTML1_TRANSITIONAL, $this->_view->doctype()->getDoctype());
	}
	
	public function testStaticView(){
		$this->assertInstanceOf('Zest_View', Zest_View::getStaticView());
	}
	
	public function testSetEngineException(){
		$this->setExpectedException('Zest_View_Exception');
		$this->_view->setEngine('testSetEngineException');
	}
	
	public function testDefaultEngine(){
		$this->assertInstanceOf('Zest_View', $this->_view->getEngine());
	}
	
	public function testRender(){
		$this->_view->addScriptPath($this->_getScriptPath());
		$this->_view->variable = 'testRender';
		$this->assertEquals('testRender', $this->_view->render('view-test-render.phtml'));
	}
	
	public function testClone(){
		$this->_view->setEngine('smarty');
		$this->assertInstanceOf('Zest_View_Engine_Smarty', $this->_view->getEngine());
		$view = clone $this->_view;
		$this->assertFalse($this->_view->getEngine() === $view->getEngine());
	}
	
}