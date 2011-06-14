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
		$isHead = is_int(strpos($body, '</head>'));
		
		$view = Zest_View::getStaticView();
		
		if($isHead){
			$helpers = null;
			
			// encoding
			$view->head()->contentType('text/html; charset='.strtolower($view->getEncoding()));
		}
		else{
			$helpers = array('headLink', 'headStyle', 'headScript');
		}
		
		
		if($head = $view->head()->toString($helpers)){
			if($isHead){
				$body = str_replace('</head>', $head.'</head>', $body);
			}
			else if(method_exists($request, 'isXmlHttpRequest') && $request->isXmlHttpRequest()){
				$body = $head.$body;
			}
			$this->getResponse()->setBody($body, 'default');
		}
	}
	
}