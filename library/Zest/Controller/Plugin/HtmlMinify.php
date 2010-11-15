<?php

/**
 * @category Zest
 * @package Zest_Controller
 * @subpackage Plugin
 */
class Zest_Controller_Plugin_HtmlMinify extends Zend_Controller_Plugin_Abstract{
	
	/**
	 * @param Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function postDispatch(Zend_Controller_Request_Abstract $request){
		$body = $this->getResponse()->getBody('default');
		$body = Zest_View::getStaticView()->minify('html', $body);
		$this->getResponse()->setBody($body, 'default');
	}
	
}