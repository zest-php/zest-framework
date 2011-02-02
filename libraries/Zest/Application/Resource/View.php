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
		$view = $this->getView();
		
		$viewRenderer = new Zest_Controller_Action_Helper_ViewRenderer();
		$viewRenderer->setView($view);
		Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
	}
	
	/**
	 * @return Zest_View
	 */
	public function getView(){
		if(is_null($this->_view)){
			$this->_view = new Zest_View();
			
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
		return $this->_view;
	}
	
	
}