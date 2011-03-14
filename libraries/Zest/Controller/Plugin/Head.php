<?php

/**
 * @category Zest
 * @package Zest_Controller
 * @subpackage Plugin
 */
class Zest_Controller_Plugin_Head extends Zend_Controller_Plugin_Abstract{
	
	/**
	 * @param Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function postDispatch(Zend_Controller_Request_Abstract $request){
		$body = $this->getResponse()->getBody('default');
		if(!is_int(strpos($body, '</head>'))) return;
		
		$view = Zest_View::getStaticView();
		
		// encoding
		$view->head()->contentType('text/html; charset='.strtolower($view->getEncoding()));
		
		$helpers = array('headTitle', 'headMeta', 'headLink', 'headStyle', 'headScript');
		$head = '';
		foreach($helpers as $helper){
			if($html = $view->$helper()->toString()){
				$head .= $html.PHP_EOL;
			}
		}
		
		if($head){
			$body = str_replace('</head>', $head.'</head>', $body);
			$this->getResponse()->setBody($body, 'default');
		}
	}
	
}