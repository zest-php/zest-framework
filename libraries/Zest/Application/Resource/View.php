<?php

/**
 * @category Zest
 * @package Zest_Application
 * @subpackage Resource
 */
class Zest_Application_Resource_View extends Zend_Application_Resource_ResourceAbstract{
	
	/**
	 * @var Zest_View
	 */
	protected $_view;
	
	/**
	 * @return void
	 */
	public function init(){
		$this->getView();
		$this->_initView();
	}
	
	/**
	 * @return Zest_View
	 */
	public function getView(){
		if(is_null($this->_view)){
			if(Zend_Controller_Action_HelperBroker::hasHelper('viewRenderer')){
				$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
			}
			else{
				$viewRenderer = new Zest_Controller_Action_Helper_ViewRenderer();
				Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
			}
			
			if($viewRenderer->view){
				$this->_view = $viewRenderer->view;
			}
			else{
				$this->_view = new Zest_View();
				$viewRenderer->setView($this->_view);
			}
		}
		return $this->_view;
	}
	
	/**
	 * @return void
	 */
	protected function _initView(){
		$options = $this->getOptions();
		$engine = isset($options['engine']) ? strtolower($options['engine']) : null;
		
		if($engine == 'zend'){
			unset($options['engine']);
		}
		else{
			if($engine && isset($options[$engine])){
				$options = array_merge($options, $options[$engine]);
				unset($options[$engine]);
			}
		}
		
		$this->_view->setOptions($options);
	}
	
	
}