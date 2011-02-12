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
	 * @return mixed
	 */
	public function autoload($class){
		$classPath = $this->getClassPath($class);
		
		if(strpos($class, $this->_namespace.'_Library') === 0){
			$expected = $this->_namespace.substr($class, strlen($this->_namespace.'_Library'));
			trigger_error(sprintf('L\'espace de nom "library" est mal utilisé ("%s" => "%s").', $class, $expected), E_USER_ERROR);
		}
		
		if(!$classPath){
			$class = $this->_namespace.'_Library'.substr($class, strlen($this->_namespace));
			$classPath = $this->getClassPath($class);
		}
		
		if($classPath){
			return include $classPath;
		}
		
		return false;
	}
	
}