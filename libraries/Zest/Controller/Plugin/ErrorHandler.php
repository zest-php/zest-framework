<?php

/**
 * @category Zest
 * @package Zest_Controller
 * @subpackage Plugin
 */
class Zest_Controller_Plugin_ErrorHandler extends Zend_Controller_Plugin_ErrorHandler{
	
	/**
	 * @param Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	protected function _handleError(Zend_Controller_Request_Abstract $request){
		$frontController = Zend_Controller_Front::getInstance();
		$noErrorHandler = $frontController->getParam('noErrorHandler');
		
		// dans tous les cas s'il y a un exception on la log avec Zest_Error_Handler
		$response = $this->getResponse();
		if($response->isException() && !$this->_isInsideErrorHandlerLoop){
			$exceptions = $response->getException();
			Zest_Error_Handler::handleException($exceptions[0]);
		}
		
		if(!$noErrorHandler || $noErrorHandler === 'zest (false)'){
			// on réactive le plugin pour que parent::_handleError fonctionne
			$frontController->setParam('noErrorHandler', false);
		}
		
		parent::_handleError($request);
	}
	
}