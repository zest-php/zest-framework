<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage UnitTests
 */
class Zest_View_Helper_HeadTest extends Zest_View_Helper_AbstractTest{
	
	protected function setUp(){
		parent::setUp();
		
		$this->_view->setDoctype(Zend_View_Helper_Doctype::XHTML1_TRANSITIONAL);
		if(Zend_Registry::isRegistered('Zend_View_Helper_Placeholder_Registry')){
			Zend_Registry::_unsetInstance();
		}
	}
	
	public function testException(){
		$this->setExpectedException('Zest_View_Exception');
		$this->_view->head('text', 'testException');
	}
	
	public function testTitle(){
		$this->_view->head()->title('testTitle');
		$this->assertEquals('<title>testTitle</title>', $this->_clean($this->_view->head()));
	}
	
	public function testDescription(){
		$this->_view->head()->description('testDescription');
		$head = $this->_clean($this->_view->head());
		$this->assertEquals('<meta name="description" content="testDescription" />', $head);
	}
	
	public function testKeywords(){
		$this->_view->head()->keywords('testKeywords');
		$head = $this->_clean($this->_view->head());
		$this->assertEquals('<meta name="keywords" content="testKeywords" />', $head);
	}
	
	public function testCss(){
		$this->_view->head()->css('testCss');
		$head = $this->_clean($this->_view->head());
		$this->assertEquals('<link href="testCss" media="all" rel="stylesheet" type="text/css" />', $head);
	}
	
	public function testJs(){
		$this->_view->head()->js('testJs');
		$head = $this->_clean($this->_view->head());
		$this->assertEquals('<script type="text/javascript" src="testJs"></script>', $head);
	}
	
	public function testJsInline(){
		$this->_view->head()->jsInline('testJsInline');
		$head = $this->_clean($this->_view->head());
		$this->assertEquals('<script type="text/javascript">'.PHP_EOL.'    //<![CDATA['.PHP_EOL.'testJsInline    //]]>'.PHP_EOL.'</script>', $head);
	}
	
	public function testRss(){
		$this->_view->head()->rss('testRss');
		$head = $this->_clean($this->_view->head());
		$this->assertEquals('<link href="testRss" rel="alternate" type="application/rss+xml" title="" />', $head);
	}
	
	public function testFavicon(){
		$this->_view->head()->favicon('testFavicon');
		$head = $this->_clean($this->_view->head());
		$this->assertEquals('<link href="testFavicon" rel="shortcut icon" type="image/x-icon" />', $head);
	}
	
	public function testRobots(){
		$this->_view->head()->robots('testRobots');
		$head = $this->_clean($this->_view->head());
		$this->assertEquals('<meta name="robots" content="testRobots" />', $head);
	}
	
	public function testCanonical(){
		$this->_view->head()->canonical('testCanonical');
		$head = $this->_clean($this->_view->head());
		$this->assertEquals('<link href="testCanonical" rel="canonical" />', $head);
	}
	
	public function testLang(){
		$this->_view->head()->lang('testLang');
		$head = $this->_clean($this->_view->head());
		$this->assertEquals('<meta http-equiv="content-language" content="testLang" />'.PHP_EOL.'<meta name="language" content="testLang" />', $head);
	}
	
	public function testContentType(){
		$this->_view->head()->contentType('testContentType');
		$head = $this->_clean($this->_view->head());
		$this->assertEquals('<meta http-equiv="content-type" content="testContentType" />', $head);
	}
	
	public function testResourceType(){
		$this->_view->head()->resourceType('testResourceType');
		$head = $this->_clean($this->_view->head());
		$this->assertEquals('<meta name="resource-type" content="testResourceType" />', $head);
	}
	
	public function testToString(){
		// @todo
	}
	
	protected function _clean($string){
		return trim(str_replace('<title></title>', '', $string));
	}
	
}