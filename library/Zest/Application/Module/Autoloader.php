<?php

/**
 * @category Zest
 * @package Zest_Application
 * @subpackage Module
 */
class Zest_Application_Module_Autoloader extends Zend_Application_Module_Autoloader{
	
	/**
	 * @return void
	 */
	public function initDefaultResourceTypes(){
		parent::initDefaultResourceTypes();
		$this->addResourceType('library', 'library', 'Library');
	}
	
	/**
	 * @param string $class
	 * @return boolean
	 */
	public function getClassPath($class){
		$classPath = parent::getClassPath($class);
		if(!$classPath){
//			$class = preg_replace('/^'.$this->_namespace.'_/', $this->_namespace.'_Library_', $class);
			$class = $this->_namespace.'_Library'.substr($class, strlen($this->_namespace));
			return parent::getClassPath($class);
		}
		return $classPath;
	}
	
}