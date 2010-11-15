<?php

/**
 * @see Zend_Application
 */
require_once 'Zend/Application.php';

/**
 * @category Zest
 * @package Zest_Application
 */
class Zest_Application extends Zend_Application{
	
	/**
	 * @param array $options
	 * @return void
	 */
	public function __construct($environment, $options = null){
		parent::__construct($environment, $options);
		
		// initialisations propres au framework Zest
		$this->setAutoloaderNamespaces(array('Zest'));
		
		// frontcontroller.actionhelperpaths
		Zend_Controller_Action_HelperBroker::addPrefix('Zest_Controller_Action_Helper');
		
		// pluginpaths
		$this->getBootstrap()->getPluginLoader()->addPrefixPath('Zest_Application_Resource', 'Zest/Application/Resource');
		
		if(!is_null($options)){
			// même container pour tous les boostraps (!= new Zend_Registry)
			$container = Zend_Registry::getInstance();
			$this->getBootstrap()->setContainer($container);
			
			$methods = array('config', 'frontcontroller', 'bootstraps');
			foreach($methods as $method){
				if(!isset($this->_options[$method])){
					$this->_options[$method] = array();
				}
				if(!is_array($this->_options[$method])){
					continue;
				}
				$container->$method = $this->{'_init'.ucfirst($method)}($this->_options[$method]);
			}
		}
	}
	
	/**
	 * @param array $options
	 * @return Zest_Config
	 */
	protected function _initConfig(array $options){
		if($options){
			Zest_Config::init($this, $options);
			
			// récupération de la configuration
			$options = Zest_Config::get();
			$options = array_change_key_case($options, CASE_LOWER);			
			
			// filtrage des clefs
			$keys = array('phpsettings', 'includepaths', 'autoloadernamespaces', 'bootstrap', 'pluginpaths', 'resources');
			$options = array_intersect_key($options, array_flip($keys));
			
			// attributions des options à l'application et au bootstrap
			$options = $this->mergeOptions($this->getOptions(), $options);
			$this->setOptions($options);
			$this->getBootstrap()->setOptions($options);
		}		
		return Zest_Config::getInstance();
	}
	
	/**
	 * @param array $options
	 * @return Zest_Application
	 */
	protected function _initFrontcontroller(array $options){
		$modulesDirectories = $this->getModulesDirectories();
		$frontController = Zest_Controller_Front::getInstance();
		
		// default
		$defaultModule = $frontController->getDefaultModule();
		if(!isset($modulesDirectories[$defaultModule])){
			throw new Zest_Application_Exception(sprintf('Le module par défaut "%s" n\'est pas défini.', $defaultModule));
		}
		
		// modules
		foreach($modulesDirectories as $module => $moduleDir){
			$frontController->addControllerDirectory($moduleDir.'/controllers', $module);
		}
		
		return $frontController;
	}
	
	/**
	 * @param array $options
	 * @return Zend_Application_Resource_Modules
	 */
	protected function _initBootstraps(array $options){
		// (bootstrap + autoloader) pour chaque module
		$resourceModules = new Zend_Application_Resource_Modules(array('bootstrap' => $this->getBootstrap()));
		return $resourceModules;
	}
	
	/**
	 * @param null|string|array $resource
	 * @return Zest_Application
	 */
	public function bootstrap($resource = null){		
		if($this->getBootstrap()->hasResource('bootstraps')){
			$this->getBootstrap()->getResource('bootstraps')->init();
		}
		parent::bootstrap($resource);
		
		return $this;
	}
	
	/**
	 * @return array
	 */
	public function getModulesDirectories(){
		if($this->hasOption('modules_directories_loaded')){
			return $this->getOption('modules_directories');
		}
		
		$directories = array();
		$versions = array();
	
		$modules = (array) $this->getOption('modules');
		$modulesDirs = (array) $this->getOption('modules_directories');
		$modulesDirsFormat = $this->getOption('modules_directories_format');
		
		if($modules){
			if(!$modulesDirs){
				throw new Zest_Application_Exception('Le chemin des modules n\'est pas renseigné.');
			}
			foreach($modules as $module => $version){
				$module = strtolower($module);
				
				// il ne s'agit pas de versioning
				if(is_numeric($module)){
					$moduleDirname = $module = $version;
					$versions[$module] = null;
				}
				// il s'agit de versioning
				else{
					$moduleDirname = $module.'/'.$version;
					$versions[$module] = $version;
				}
				
				foreach($modulesDirs as $modulesDir){
					
					/**
					 * permet de gérer un répertoire de modules
					 * le dossier des controleurs ne se trouve pas directement à la racine
					 * 
					 * ".../modules/[module]/private" (ex : .../modules/cms/private)
					 * 		avec ".../modules" = $modulesDir
					 * 		avec "/[module]/private" = $modulesDirsFormat (personnalisable)
					 */
					if($modulesDirsFormat){
						$moduleDir = $modulesDir.sprintf($modulesDirsFormat, $moduleDirname);
						if(is_readable($moduleDir)){
							$directories[$module] = $moduleDir;
							break;
						}
					}
					
					/**
					 * le dossier des controleurs se trouve directement à la racine
					 * 
					 * "private/[module]" (ex : private/default)
					 * 		avec ".../private" = $modulesDir
					 * 		avec "[module]" = $moduleDirname
					 */
					$moduleDir = $modulesDir.'/'.$moduleDirname;
					if(is_readable($moduleDir)){
						$directories[$module] = $moduleDir;
						break;
					}
					
				}
			}
		}
		
		$this->setOptions(array(
			'modules_directories_loaded' => true,
			'modules_directories' => $directories,
			'modules_versions' => $versions
		));
		
		return $directories;
	}
	
	/**
	 * @return array
	 */
	public function getModulesVersions(){
		if(!$this->hasOption('modules_directories_loaded')){
			$this->getModulesDirectories();
		}
		return $this->getOption('modules_versions');
	}
	
	/**
	 * @param string $key
	 * @return mixed
	 */
	public function getOption($key){
		if($this->hasOption($key)){
			$value = parent::getOption($key);
			if(is_string($value) && $tmp = Zest_Config::get($value)){
				$value = $tmp;
			}
			return $value;
		}
		return null;
	}
	
	/**
	 * @param string $file
	 * @return array
	 */
	protected function _loadConfig($file){
		return array();
	}
	
}