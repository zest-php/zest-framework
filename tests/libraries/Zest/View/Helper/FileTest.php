<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage UnitTests
 */
class Zest_View_Helper_FileTest extends Zest_View_Helper_AbstractTest{
	
	public static function setUpBeforeClass(){
		$front = Zest_Controller_Front::getInstance();
		$front->setRequest(new Zend_Controller_Request_Http());
		if(!$front->getRouter()->hasRoute('zest-file')){
			$front->getRouter()->addRoute('zest-file', new Zend_Controller_Router_Route('file/:id/:filename'));
		}
	}
	
	public function testFile(){
		$file = $this->_view->file($this->_getPathname());
		$keys = array_keys((array) $file);
		sort($keys);
		
		$compare = array ('basename', 'displayed', 'exists', 'extension', 'isAudio', 'isImage', 'isVideo', 'mimetype', 'pathname', 'size', 'url');
		$this->assertEquals($compare, $keys);
		
		$this->assertRegExp('/tests\/libraries\/file\/[a-z0-9]+\/image.png$/', $file->url);
	}
	
	protected function _getPathname(){
		return Zest_AllTests::getDataDir().'/view/image.png';
	}
	
}