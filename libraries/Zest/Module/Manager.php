<?php

abstract class Zest_Module_Manager{
	
	/**
	 * @var Zend_Session_Namespace
	 */
	protected $_session = null;
	
	/**
	 * @var string
	 */
	protected $_namespace = null;
	
	/**
	 * @return string
	 */
	public function getNamespace(){
		if(is_null($this->_namespace)){
			$class = get_class($this);
			$this->_namespace = substr($class, 0, strpos($class, '_'));
		}
		return $this->_namespace;
	}
	
	/**
	 * @return string
	 */
	public function isEnvironmentDev(){
		return substr(Zend_Registry::get('environment'), 0, 3) == 'dev';
	}
	
	/**
	 * @return string
	 */
	public function getModuleName(){
		return strtolower($this->getNamespace());
	}

	/**
	 * @return Zend_Session_Namespace
	 */
	public function getSession(){
		if(is_null($this->_session)){
			$namespace = $this->getModuleName().'_manager';
			$this->_session = new Zend_Session_Namespace($namespace);
		}
		return $this->_session;
	}

	/**
	 * @param string $key
	 * @param boolean $throwExceptions
	 * @return array|string
	 */
	public function getConfig($key = null, $throwExceptions = false){
		return Zest_Config::get('module.'.$this->getModuleName().($key ? '.'.$key : ''), $throwExceptions);
	}
	
	/**
	 * @return string
	 */
	public function getUrl(){
		$controller = Zest_Controller_Front::getInstance();
		if($controller instanceof Zest_Controller_Front){
			return $controller->getModuleUrl($this->getModuleName());
		}
		throw new Zest_Acl_Exception('Le frontcontroller doit être une instance de Zest_Controller_Front.');
	}
	
}