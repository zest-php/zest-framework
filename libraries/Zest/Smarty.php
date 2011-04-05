<?php

/**
 * @see Smarty
 */
require_once 'Smarty/Smarty.class.php';

/**
 * @see Smarty_Compiler
 */
require_once 'Smarty/Smarty_Compiler.class.php';

/**
 * @category Zest
 * @package Zest_Smarty
 */
class Zest_Smarty extends Smarty{	
	
	/**
	 * @return void
	 */
	public function __construct(){
		parent::Smarty();
		$this->security_settings['MODIFIER_FUNCS'][] = 'rand';
	}
	
	/**
	 * @param string $name
	 * @param string $cache_id
	 * @param string $compile_id
	 * @param boolean $display
	 * @return string
	 */
	public function fetch($name, $cache_id, $compile_id, $display = false){
		$forceCompile = $this->force_compile;
		$content = parent::fetch($name, $cache_id, $compile_id, $display);
		if($forceCompile){
			$this->clear_compiled_tpl($name, $compile_id);
		}
		return $content;
	}
	
	// VARS
	
	/**
	 * @return array
	 */
	public function getVars(){
		return $this->get_template_vars();
	}
	
	/**
	 * @param array $vars
	 * @return Zest_Smarty
	 */
	public function setVars($vars){
		$this->clearVars()->assign($vars);
		return $this;
	}
	
	/**
	 * @return Zest_Smarty
	 */
	public function clearVars(){
		$this->clear_all_assign();
		return $this;
	}
	
	// DOSSIERS
	
	/**
	 * @param string $cacheDir
	 * @return Zest_Smarty
	 */
	public function setCacheDir($cacheDir){
		$this->cache_dir = $cacheDir;
		return $this;
	}
	
	/**
	 * @param string $compileDir
	 * @return Zest_Smarty
	 */
	public function setCompileDir($compileDir){
		$this->compile_dir = $compileDir;
		return $this;
	}
	
	/**
	 * @param string $configDir
	 * @return Zest_Smarty
	 */
	public function setConfigDir($configDir){
		$this->config_dir = $configDir;
		return $this;
	}
	
	/**
	 * @param array $dirs
	 * @return Zest_Smarty
	 */
	public function setPluginsDir($dirs){
		$this->plugins_dir = array();
		$this->addPluginDir($dirs);
		$this->addPluginDir('plugins');
		return $this;
	}
	
	/**
	 * @param array|string $pluginDir
	 * @return Zest_Smarty
	 */
	public function addPluginDir($pluginDir){
		if(is_string($pluginDir) && $pluginDir == 'auto'){
			$frontController = Zest_Controller_Front::getInstance();
			$controllerDirectories = $frontController->getControllerDirectory();
			
			$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
			$inflector = $viewRenderer->getInflector()->setTarget($viewRenderer->getViewBasePathSpec());
			
			$pluginDir = array();
			foreach($controllerDirectories as $module => $controllerDirectoriy){
				$pluginDir[] = $inflector->filter(array('moduleDir' => $frontController->getModuleDirectory($module))).'/plugins';
			}
		}
		if(is_array($pluginDir)){
			foreach($pluginDir as $dir){
				$this->addPluginDir($dir);
			}
		}
		else{
			$this->plugins_dir[] = $pluginDir;
		}
		return $this;
	}
	
	/**
	 * @param string $module
	 * @param string $filename
	 */
	public function getPluginPathname($module, $filename){
		$frontController = Zest_Controller_Front::getInstance();
		$moduleDir = $frontController->getModuleDirectory($module);
		
		if($moduleDir){
			$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
			$inflector = $viewRenderer->getInflector()->setTarget($viewRenderer->getViewBasePathSpec());
			
			$pluginPathname = $inflector->filter(array('moduleDir' => $moduleDir)).'/plugins/'.$filename;
			if(is_readable($pluginPathname)){
				return $pluginPathname;
			}
		}
		
		return null;
	}
	
	/**
	 * @param string $templateDir
	 * @return Zest_Smarty
	 */
	public function setTemplateDir($templateDir){
		$this->template_dir = $templateDir;
		return $this;
	}
	
	/**
	 * @param string|integer $filePerms
	 * @return Zest_Smarty
	 */
	public function setFilePerms($filePerms){
		$this->_file_perms = intval($filePerms, 8);
		return $this;
	}
	
	/**
	 * @param string|integer $dirPerms
	 * @return Zest_Smarty
	 */
	public function setDirPerms($dirPerms){
		$this->_dir_perms = intval($dirPerms, 8);
		return $this;
	}
	
	/**
	 * @param boolean $security
	 * @return Zest_Smarty
	 */
	public function setSecurity($security){
		$this->security = (bool) $security;
		return $this;
	}
	
	/**
	 * @param boolean $useSubDirs
	 * @return Zest_Smarty
	 */
	public function setUseSubDirs($useSubDirs){
		$this->use_sub_dirs = (bool) $useSubDirs;
		return $this;
	}
	
	// COMPILATION / CACHE
	
	/**
	 * @param string $compileId
	 * @return Zest_Smarty
	 */
	public function setCompileId($compileId){
		$this->compile_id = $compileId;
		return $this;
	}
	
	/**
	 * @param boolean $forceCompile
	 * @return Zest_Smarty
	 */
	public function setForceCompile($forceCompile){
		$this->force_compile = (bool) $forceCompile;
		return $this;
	}
	
	/**
	 * @param boolean $compileCheck
	 * @return Zest_Smarty
	 */
	public function setCompileCheck($compileCheck){
		$this->compile_check = (bool) $compileCheck;
		return $this;
	}
	
	/**
	 * @param integer $caching
	 * @return Zest_Smarty
	 */
	public function setCaching($caching){
		$this->caching = (int) $caching;
		return $this;
	}
	
	/**
	 * @param integer $cacheLifetime
	 * @return Zest_Smarty
	 */
	public function setCacheLifetime($cacheLifetime){
		$this->cache_lifetime = (int) $cacheLifetime;
		return $this;
	}
	
	/**
	 * @param boolean $cacheModifiedCheck
	 * @return Zest_Smarty
	 */
	public function setCacheModifiedCheck($cacheModifiedCheck){
		$this->cache_modified_check = (bool) $cacheModifiedCheck;
		return $this;
	}
	
	/**
	 * @param integer $errorReporting
	 * @return Zest_Smarty
	 */
	public function setErrorReporting($errorReporting){
		$this->error_reporting = (int) $errorReporting;
		if($this->error_reporting == 0){
			$this->error_reporting = null;
		}
		return $this;
	}
	
	/**
	 * @param array|object $foreachable
	 * @return array
	 */
	public static function recursiveToArray($foreachable){
		if(is_array($foreachable)){
			foreach($foreachable as $key => $value){
				$foreachable[$key] = self::recursiveToArray($foreachable[$key]);
			}
		}
		else if(is_object($foreachable)){
			foreach($foreachable as $key => $value){
				$foreachable->$key = self::recursiveToArray($foreachable->$key);
			}
		}
		else{
			return $foreachable;
		}
		return (array) $foreachable;
	}
	
}