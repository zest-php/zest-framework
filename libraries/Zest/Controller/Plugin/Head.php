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
		$response = $this->getResponse();
		
		$body = $response->getBody('default');
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
	
		$headers = $response->getHeaders();
		foreach($headers as $header){
			if(strtolower($header['name']) == 'content-type' && !is_int(strpos($header['value'], 'html'))){
				return false;
			}
		}
		
		if($head = $view->head()->toString($helpers)){
			if($isHead){
				$body = str_replace('</head>', $head.'</head>', $body);
			}
			else if(method_exists($request, 'isXmlHttpRequest') && $request->isXmlHttpRequest()){
				$body = $head.$body;
			}
			$response->setBody($body, 'default');
		}
	}
	
}