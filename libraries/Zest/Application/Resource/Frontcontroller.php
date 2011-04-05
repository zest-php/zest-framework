<?php

/**
 * @category Zest
 * @package Zest_Application
 * @subpackage Resource
 */
class Zest_Application_Resource_Frontcontroller extends Zend_Application_Resource_Frontcontroller{
	
	/**
	 * @var array
	 */
	protected $_modulesDirectories = null;
	
	/**
	 * @var array
	 */
	protected $_modulesVersions = null;

	/**
	 * @return Zest_Controller_Front
	 */
	public function init(){
		$front = $this->getFrontController();
		
		$moduledirectory = array();
		$modules = array();
		$moduledirectory_format = null;
		
		foreach($this->getOptions() as $key => $value){
			switch(strtolower($key)){
				case 'moduledirectory':
					unset($this->_options[$key]);
					$moduledirectory = (array) $value;
					break;
				case 'modules':
					$modules = (array) $value;
					break;
				case 'moduledirectory_format':
					$moduledirectory_format = $value;
					break;
				case 'plugins_stack':
					foreach($value as $stackIndex => $plugin){
						if(!$plugin) continue;
						
						$plugin = new $plugin();
						$front->registerPlugin($plugin, $stackIndex);
					}
					break;
				case 'plugins_options':
					foreach($value as $pluginName => $options){
						if($front->hasPlugin($pluginName)){
							$plugin = $front->getPlugin($pluginName);
							foreach($options as $key => $value){
								$key = ucwords(str_replace('_', ' ', $key));
								$method = 'set'.str_replace(' ', '', $key);
								$plugin->$method($value);
							}
						}
					}
					break;
			}
		}
		
		$modulesDirectories = $this->_getModulesDirectories($moduledirectory, $modules, $moduledirectory_format);
		
		// default
		$defaultModule = $front->getDefaultModule();
		if(!isset($modulesDirectories[$defaultModule])){
			throw new Zest_Application_Exception(sprintf('Le module par défaut "%s" n\'est pas défini.', $defaultModule));
		}
		
		// modules
		foreach($modulesDirectories as $module => $moduleDir){
			$front->addControllerDirectory($moduleDir.'/controllers', $module);
		}
		  
		return parent::init();
	}
	
	/**
	 * @return Zest_Controller_Front
	 */
	public function getFrontController(){
		if(is_null($this->_front)){
			$this->_front = Zest_Controller_Front::getInstance();
		}
		return $this->_front;
	}
	
	/**
	 * @return array
	 */
	public function getModulesDirectories(){
		if(is_null($this->_modulesDirectories)){
			throw new Zest_Application_Exception('La ressource n\'a pas encore été initialisée.');
		}
		return $this->_modulesDirectories;
	}
	
	/**
	 * @return array
	 */
	protected function _getModulesDirectories($moduledirectory, $modules, $moduledirectory_format){
		$this->_modulesDirectories = array();
		$this->_modulesVersions = array();
	
		if($modules){
			if(!$moduledirectory){
				throw new Zest_Application_Exception('Le chemin des modules n\'est pas renseigné.');
			}
			foreach($modules as $module => $version){
				$module = strtolower($module);
				
				// il ne s'agit pas de versioning
				if(is_numeric($module)){
					$moduleDirname = $module = $version;
					$this->_modulesVersions[$module] = null;
				}
				// il s'agit de versioning
				else{
					$moduleDirname = $module.'/'.$version;
					$this->_modulesVersions[$module] = $version;
				}
				
				foreach($moduledirectory as $modulesDir){
					
					/**
					 * permet de gérer un répertoire de modules
					 * le dossier des controleurs ne se trouve pas directement à la racine
					 * 
					 * ".../modules/[module]/private" (ex : .../modules/cms/private)
					 *	  avec ".../modules" = $modulesDir
					 *	  avec "/[module]/private" = $moduledirectory_format (personnalisable)
					 */
					if($moduledirectory_format){
						$moduleDir = $modulesDir.sprintf($moduledirectory_format, $moduleDirname);
						if(is_readable($moduleDir)){
							$this->_modulesDirectories[$module] = $moduleDir;
							break;
						}
					}
					
					/**
					 * le dossier des controleurs se trouve directement à la racine
					 * 
					 * "private/[module]" (ex : private/default)
					 *	  avec ".../private" = $modulesDir
					 *	  avec "[module]" = $moduleDirname
					 */
					$moduleDir = $modulesDir.'/'.$moduleDirname;
					if(is_readable($moduleDir)){
						$this->_modulesDirectories[$module] = $moduleDir;
						break;
					}
					
				}
			}
		}
		
		return $this->_modulesDirectories;
	}
	
}