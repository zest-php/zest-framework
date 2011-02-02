<?php

/**
 * @category Zest
 * @package Zest_View
 */
class Zest_View extends Zend_View{
	
	/**
	 * @var object
	 */
	protected $_engine = null;
	
	/**
	 * @var array
	 */
	protected $_options = null;
	
	/**
	 * @param array $config
	 * @return void
	 */
	public function __construct(array $config = array()){
		$this->addHelperPath('Zest/View/Helper', 'Zest_View_Helper');
		$this->addFilterPath('Zest/View/Filter', 'Zest_View_Filter');
		
		parent::__construct($config);
	}
	
	/**
	 * @param array $options
	 * @return Zest_View
	 */
	public function setOptions(array $options){
		$this->_options = array_change_key_case($options, CASE_LOWER);
		
		foreach($this->_options as $key => $value){
			$key = ucwords(str_replace('_', ' ', $key));
			$method = 'set'.str_replace(' ', '', $key);
			if(method_exists($this, $method)){
				$this->$method($value);
			}
		}
		
		if($this->getEngine() != $this){
			$this->getEngine()->setOptions($this->_options);
		}
		
		return $this;
	}
	
	/**
	 * @return array
	 */
	public function getOptions(){
		return $this->_options;
	}
	
	/**
	 * @param string $encoding
	 * @return Zest_View
	 */
	public function setEncoding($encoding){
		return parent::setEncoding(strtoupper($encoding));
	}
	
	/**
	 * @param string $doctype
	 * @return Zest_View
	 */
	public function setDoctype($doctype){
		$doctype = strtoupper($doctype);
		
		// initialisation de l'aide de vue Zend_View_Helper_Doctype
		$helper = $this->doctype();

		// vérification que le doctype existe
		$doctypes = $helper->getDoctypes();
		if(isset($doctypes[$doctype])){
			$helper->setDoctype($doctype);
		}
		else{
			trigger_error(sprintf('le doctype "%s" n\'existe pas', $doctype), E_USER_ERROR);
		}
		return $this;
	}
	
	/**
	 * @return Zest_View
	 */
	public static function getStaticView(){
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		if($viewRenderer instanceof Zest_Controller_Action_Helper_ViewRenderer){
			return $viewRenderer->getView();
		}
		else{
			if(!$viewRenderer->view){
				$viewRenderer->initView();
			}
			return $viewRenderer->view;
		}
	}
	
	/**
	 * @param string|Zest_View_Engine_Abstract $engine
	 * @return Zest_View
	 */
	public function setEngine($engine){
		if(is_string($engine)){
			$class = 'Zest_View_Engine_'.ucfirst($engine);
			if(!@class_exists($class)){
				trigger_error(sprintf('la classe "%s" n\'existe pas.', $class), E_USER_ERROR);
			}
			$engine = new $class($this);
		}
		$this->_engine = $engine;
		if(!$this->_engine instanceof Zest_View_Engine_Abstract){
			trigger_error('le moteur de rendu doit hériter de Zest_View_Engine_Abstract.', E_USER_ERROR);
		}
		return $this;
	}
	
	/**
	 * @return Zest_View_Engine_Abstract
	 */
	public function getEngine(){
		if($this->_engine){
			return $this->_engine;
		}
		return $this;
	}
	
	/**
	 * @param string $name
	 * @return string
	 */
	public function render($name){
		if($this->getEngine() != $this){
			return $this->getEngine()->render($name);
		}
		else{
			return parent::render($name);
		}
	}
	
	/**
	 * @return void
	 */
	public function __clone(){
		if($this->getEngine() != $this){
			$engine = clone $this->getEngine();
			$engine->setView($this);
			$this->setEngine($engine);
		}
	}
	
}