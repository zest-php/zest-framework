<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage UnitTests
 */
class Zest_View_Engine_SmartyTest extends Zest_View_AbstractTest{
	
	protected function setUp(){
		parent::setUp();
		
		$compileDir = $this->_getCompileDir();
		if(file_exists($compileDir)){
			foreach(glob($compileDir.'/*') as $file){
				unlink($file);
			}
		}
		else{
			mkdir($compileDir);
		}
		
		$this->_view->setEngine('smarty');
		$this->_view->getEngine()->setOptions(array(
			'compile_dir' => $compileDir
		));
	}
	
	public function testRender(){
		$this->_view->setScriptPath($this->_getScriptPath());
		$this->_view->variable = 'testRender';
		$this->assertEquals('testRender', $this->_view->render('engine-smarty-test-render.phtml'));
	}
	
	protected function _getCompileDir(){
		return Zest_AllTests::getTempDir().'/Zest_View_Engine_SmartyTest';
	}
	
}