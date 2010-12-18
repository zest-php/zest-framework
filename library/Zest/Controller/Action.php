<?php

/**
 * @category Zest
 * @package Zest_Controller
 */
abstract class Zest_Controller_Action extends Zend_Controller_Action{
	
	/**
	 * @return void
	 */
	public function fileAction(){
		Zest_File::factory()->url()->send($this->_request);
	}
	
	/**
	 * @param string $segment
	 * @param string $action
	 * @param string $controller
	 * @param string $module
	 * @param array $params
	 * @return Zest_Controller_Action
	 */
	protected function _dispatchAction($segment, $action, $controller = null, $module = null, array $params = array()){
		$controller = $controller ? $controller : $this->_request->getControllerName();
		$module = $module ? $module : $this->_request->getModuleName();
		
		$render = $this->view->action($action, $controller, $module, $params);
		$this->_response->append($segment, $render);
		
		return $this;
	}
	
	/**
	 * @param array $data
	 * @return Zest_Controller_Action
	 */
	protected function _sendJson(array $data){
		$this->_helper->json->sendJson($data);
		return $this;
	}
	
	/**
	 * @param string $name
	 * @return Zest_Controller_Action
	 */
	protected function _setLayout($name){
		if($this->_helper->layout->isEnabled()){
			$this->_helper->layout->setLayout($name);
		}
		return $this;
	}
	
	/**
	 * @param string $action
	 * @param string $controller
	 * @param string $module
	 * @param array $params
	 * @return Zest_Controller_Action
	 */
	protected function _actionToStack($action, $controller = null, $module = null, array $params = array()){
		$this->_helper->actionStack->actionToStack($action, $controller, $module, $params);
		return $this;
	}
	
	/**
	 * le helper de vue "action" ne permet pas d'utiliser les méthodes "_forward" et "_redirect"
	 * 
	 * @param string $action
	 * @return Zest_Controller_Action
	 */
	protected function _execute($action, $useScript = true){
		$method = $this->_formatActionName($action);
		if(method_exists($this, $method)){
			if($useScript){
				$this->_setScriptAction($action);
			}
			$this->$method();
		}
		else{
			throw new Zest_Controller_Exception(sprintf('La méthode "%s" n\'existe pas.', $method));
		}
		return $this;
	}
	
	/**
	 * @param string $name
	 * @return Zest_Controller_Action
	 */
	protected function _setScriptAction($name){
		$this->_helper->viewRenderer->setScriptAction($name);
		return $this;
	}
	
	/**
	 * @param boolean $neverRender
	 * @return Zest_Controller_Action
	 */
	protected function _disableRender($neverRender = false){
		if($neverRender){
			// désactive tous les rendus : si on fait un _forward, aucun render ne sera fait sur l'action suivante
			$this->_helper->viewRenderer->setNeverRender(true);
		
			// désactive le rendu du layout
			$this->_helper->layout->disableLayout();
		}
		
		// désactive le rendu de l'action courante : si on fait un _forward, le render de l'action suivante sera fait
		$this->_helper->viewRenderer->setNoRender(true);
		
		return $this;
	}
	
	/**
	 * @param boolean $neverRender
	 * @return boolean
	 */
	protected function _renderIsDisabled($neverRender = false){
		if($neverRender){
			return $this->_helper->viewRenderer->getNeverRender() && !$this->_helper->layout->isEnabled();
		}
		else{
			return $this->_helper->viewRenderer->getNoRender();
		}
	}

	/**
	 * @param string $unformatted
	 * @return string
	 */
	protected function _formatControllerName($unformatted){
		return $this->getFrontController()->getDispatcher()->formatControllerName($unformatted);
	}

	/**
	 * @param string $unformatted
	 * @return string
	 */
	protected function _formatActionName($unformatted){
		return $this->getFrontController()->getDispatcher()->formatActionName($unformatted);
	}
	
	/**
	 * @param string $formatted
	 * @return string
	 */
	protected function _unformatControllerName($formatted){
		return $this->getFrontController()->getDispatcher()->unformatControllerName($formatted);
	}

	/**
	 * @param string $formatted
	 * @return string
	 */
	protected function _unformatActionName($formatted){
		return $this->getFrontController()->getDispatcher()->unformatActionName($formatted);
	}
	
}