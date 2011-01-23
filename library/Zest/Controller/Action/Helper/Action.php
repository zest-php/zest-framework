<?php

/**
 * @category Zest
 * @package Zest_Controller
 * @subpackage Action
 */
class Zest_Controller_Action_Helper_Action extends Zend_Controller_Action_Helper_Abstract{
	
	/**
	 * @var Zend_Controller_Dispatcher_Interface
	 */
	protected $_dispatcher = null;
	
	/**
	 * @var Zend_Controller_Request_Abstract
	 */
	protected $_request = null;
	
	/**
	 * @var Zend_Controller_Response_Abstract
	 */
	protected $_response = null;
	
	/**
	 * @return void
	 */
	public function __construct(){
		$front = Zend_Controller_Front::getInstance();
		$this->_request = clone $front->getRequest();
		$this->_response = clone $front->getResponse();
		$this->_dispatcher = clone $front->getDispatcher();
	}
	
	/**
	 * @return void
	 */
	protected function _resetObjects(){
		$params = $this->_request->getUserParams();
		foreach (array_keys($params) as $key) {
			$this->_request->setParam($key, null);
		}
		
		$this->_response	->clearHeaders()
					   		->clearRawHeaders()
					   		->clearBody();
	}
	
	/**
	 * @param string $action
	 * @param string $controller
	 * @param string $module
	 * @param array $params
	 * @return void
	 */
	public function dispatch($action, $controller, $module = null, array $params = array()){
		$this->_resetObjects();
		
		if(is_array($module)){
			$params = $module;
			$module = null;
		}
		if(is_null($module)){
			$module = $this->_request->getModuleName();
		}

		// clone the view object to prevent over-writing of view variables
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$viewRendererClone = clone $viewRenderer;
		$viewRendererClone->setNeverRender(true);
		Zend_Controller_Action_HelperBroker::addHelper($viewRendererClone);
		
		$this->_request	->setParams($params)
					  	->setModuleName($module)
					  	->setControllerName($controller)
					  	->setActionName($action)
					  	->setDispatched(true);

		$this->_dispatcher->dispatch($this->_request, $this->_response);

		// reset the viewRenderer object to it's original state
		Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
		
		$this->_resetObjects();
	}
	
}