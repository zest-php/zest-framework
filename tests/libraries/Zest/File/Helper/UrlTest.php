<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage UnitTests
 */
class Zest_File_Helper_UrlTest extends PHPUnit_Framework_TestCase{

	/**
	 * @var Zest_File
	 */
	protected $_file = null;
	
	public static function setUpBeforeClass(){
		$front = Zest_Controller_Front::getInstance();
		$front->setRequest(new Zend_Controller_Request_Http());
		$front->getRouter()->addRoute('zest-file', new Zend_Controller_Router_Route('file/:id/:filename'));
	}

	protected function setUp(){
		$this->_file = new Zest_File($this->_getPathname());
	}
	
	public function testGetUrlControlRoute(){
		$front = Zest_Controller_Front::getInstance();
		$front->getRouter()->addRoute('zest-file-special', new Zend_Controller_Router_Route('file/:id/:control/:filename'));
		
		$url = $this->_file->url()->getUrl(array('inside' => 300, 'route' => 'zest-file-special', 'control' => 1));
		$this->assertRegExp('/tests\/libraries\/file\/[a-z0-9]+\/1\/image.png$/', $url);
	}
	
	public function testGetUrlServerUrl(){
		$url = $this->_file->url()->getUrl(array('inside' => 300, 'serverUrl' => true));
		$compare = 'http://';
		$this->assertRegExp('/^'.preg_quote($compare, '/').'/', $url);
	}
	
	public function testGetUrlOptionsRoute(){
		$url = $this->_file->url()->getUrl(array('inside' => 300));
		$this->assertRegExp('/tests\/libraries\/file\/[a-z0-9]+\/image.png$/', $url);
	}
	
	public function testSendPublic(){
		$url = $this->_file->url()->getUrl(array('inside' => 300));
		
		$route = preg_replace('/^.*\/tests\/libraries\//', '/', $url);
		$match = (array) Zest_Controller_Front::getInstance()->getRouter()->getRoute('zest-file')->match($route);
		
		$compare = array('filename', 'id');
		$keys = array_keys($match);
		sort($keys);
		$this->assertEquals($compare, $keys);
		
//		$view = Zest_View::getStaticView();
//		$request = new Zend_Controller_Request_Http($view->serverUrl($url));
//		$request->setParams($match);
//		
//		ob_end_clean();
//		$this->_file->url()->send($request);
	}
	
	public function testSendPublicNotGoodControl(){
		$this->setExpectedException('Zest_File_Exception');
		
		$url = $this->_file->url()->getUrl(array('inside' => 300));
		$url .= 'alter-filename';
		
		$route = preg_replace('/^.*\/tests\/libraries\//', '/', $url);
		$match = (array) Zest_Controller_Front::getInstance()->getRouter()->getRoute('zest-file')->match($route);
		
		$view = Zest_View::getStaticView();
		$request = new Zend_Controller_Request_Http($view->serverUrl($url));
		$request->setParams($match);
		
//		ob_end_clean();
		$this->_file->url()->send($request);
	}
	
	public function testSendOptions(){
		$options = $this->_file->url()->getSendOptions(array('imageInside' => 200, 'videoInside' => 200));
		$keys = array_keys($options);
		sort($keys);
		$this->assertEquals(array('imageinside', 'inside', 'videoinside'), $keys);
	}
	
	protected function _getPathname(){
		return Zest_AllTests::getDataDir().'/file/image.png';
	}
	
}