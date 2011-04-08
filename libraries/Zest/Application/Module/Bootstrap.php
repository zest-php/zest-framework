<?php

/**
 * @category Zest
 * @package Zest_Application
 * @subpackage Module
 */
class Zest_Application_Module_Bootstrap extends Zend_Application_Module_Bootstrap{
	
	/**
	 * @param Zend_Application|Zend_Application_Bootstrap_Bootstrapper $application
	 * @return void
	 */
	public function __construct($application){
		if($application instanceof Zend_Application_Bootstrap_Bootstrap){
			$application = $application->getApplication();
		}
		
		parent::__construct($application);
   		$this->setContainer($application->getBootstrap()->getContainer());
	}
		
	/**
	 * si $module < $version : return -1
	 * si $module == $version : return 0
	 * si $module > $version : return 1
	 * 
	 * @param string $version
	 * @return integer
	 */
	public function compareModuleVersion($module, $version, $operator = null){
		if(is_null($operator)){
			return version_compare($this->getModuleVersion($module), $version);
		}
		return version_compare($this->getModuleVersion($module), $version, $operator);
	}
	
	/**
	 * @param string $module
	 * @param string $version
	 * @return boolean
	 */
	public function requireAtLeastModule($module, $version){
		if(!$this->compareModuleVersion($module, $version, '>=')){
			throw new Zest_Controller_Exception(sprintf('La version minimale requise pour le module "%s" est "%s".', $module, $version));
		}
	}
	
	/**
	 * @param string $module
	 * @param string $version
	 * @return boolean
	 */
	public function requireAtMostModule($module, $version){
		if(!$this->compareModuleVersion($module, $version, '<=')){
			throw new Zest_Controller_Exception(sprintf('La version maximale requise pour le module "%s" est "%s".', $module, $version));
		}
	}
	
	/**
	 * @return string
	 */
	public function getVersion(){
		return $this->getModuleVersion($this->getModuleName());
	}
	
	/**
	 * @return string
	 */
	public function getDirectory(){
		return $this->getModuleDirectory($this->getModuleName());
	}
	
	/**
	 * @param string $module
	 * @return boolean
	 */
	public function hasModule($module){
		return !is_null($this->getModuleDirectory($module));
	}
	
	/**
	 * @param string $module
	 * @return string
	 */
	public function getModuleDirectory($module){
		$module = strtolower($module);
		$dirs = $this->getApplication()->getBootstrap()->getPluginResource('frontcontroller')->getModulesDirectories();
		if(isset($dirs[$module])){
			return $dirs[$module];
		}
		return null;
	}
	
	/**
	 * @param string $module
	 * @return string
	 */
	public function getModuleVersion($module){
		$module = strtolower($module);
		$versions = $this->getApplication()->getBootstrap()->getPluginResource('frontcontroller')->getModulesVersions();
		if(isset($versions[$module])){
			return $versions[$module];
		}
		return null;
	}
	
	/**
	 * @return Zest_Loader_Autoloader_Resource
	 */
	public function getResourceLoader(){
		if(null === $this->_resourceLoader){
			$reflection = new ReflectionClass($this);
			$path = $reflection->getFileName();
			$this->setResourceLoader(new Zest_Application_Module_Autoloader(array(
				'namespace' => $this->getModuleName(),
				'basePath'  => dirname($path),
			)));
		}
		return parent::getResourceLoader();
	}
	
	/**
	 * @return string
	 */
	public function getModuleName(){
		$moduleName = parent::getModuleName();
		$moduleName = ucfirst(strtolower($moduleName));
		return $moduleName;
	}
	
	/**
	 * @return array
	 */
	public function getResourceNames(){
		return array_keys((array) $this->getContainer());
	}
	
	/**
	 * @return Zest_View
	 */
	public function getView(){
		return Zest_View::getStaticView();
	}
	
	/**
	 * @return Zest_Controller_Front
	 */
	public function getFrontController(){
		return $this->getResource('frontController');
	}
	
	/**
	 * @return Zend_Loader_Autoloader
	 */
	public function getAutoloader(){
		return $this->getApplication()->getAutoloader();
	}
	
	/**
	 * @param Zend_Controller_Plugin_Abstract $plugin
	 * @param integer $stackIndex
	 * @return Zest_Application_Module_Bootstrap
	 */
	public function registerPlugin(Zend_Controller_Plugin_Abstract $plugin, $stackIndex = null){
		$this->getFrontController()->registerPlugin($plugin, $stackIndex);
		return $this;
	}
	
	/**
	 * @return Zest_Controller_Router_Rewrite
	 */
	public function getRouter(){
		return $this->getFrontController()->getRouter();
	}
	
	/**
	 * @param string $name
	 * @param Zend_Controller_Router_Route_Interface $route
	 * @return Zest_Application_Module_Bootstrap
	 */
	public function addRoute($name, Zend_Controller_Router_Route_Interface $route){
		$this->getRouter()->addRoute($name, $route);
		return $this;
	}
	
	/**
	 * @param array $routes
	 * @return Zest_Application_Module_Bootstrap
	 */
	public function addRoutes(array $routes){
		$this->getRouter()->addRoutes($routes);
		return $this;
	}
	
}