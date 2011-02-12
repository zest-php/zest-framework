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
	 * @return Zest_Module_Manager
	 */
	abstract public static function getInstance();
	
}