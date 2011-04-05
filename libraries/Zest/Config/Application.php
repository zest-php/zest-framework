<?php

/**
 * @category Zest
 * @package Zest_Config
 */
class Zest_Config_Application extends Zest_Config_Advanced{
	
	/**
	 * @var Zest_Config
	 */
	protected static $_instance = null;
	
	/**
	 * @var string
	 */
	protected $_modulesConfigFormat = null;
	
	/**
	 * @var array
	 */
	protected $_callModulesDirectories = null;
	
	/**
	 * @param string $environment
	 * @param string|array $options
	 * @param callback $callModulesDirectories
	 * @return void
	 */
	public function __construct($environment, $options, $callModulesDirectories = null, $instance = false){
		if(is_string($options)){
			$options = array('pathname' => $options);
		}
		$options = array_change_key_case($options, CASE_LOWER);
		
		$this->_section = $environment;
		$this->_callModulesDirectories = $callModulesDirectories;
		
		if(isset($options['modules_config_format'])){
			$this->_modulesConfigFormat = $options['modules_config_format'];
			unset($options['modules_config_format']);
		}
		
		if($instance){
			self::$_instance = $this;
		}
		
		if(isset($options['pathname'])){
			$pathname = $options['pathname'];
			unset($options['pathname']);
			parent::__construct($pathname, $options);
		}
		else{
			throw new Zest_Config_Exception('Aucun fichier de configuration défini.');
		}
	}
	
	/**
	 * @param string $key
	 * @param boolean $throwExceptions
	 * @return array|string
	 */
	public function get($key = null, $throwExceptions = false){
		if(is_null(self::$_instance)){
			throw new Zest_Config_Exception('L\'instance n\'a pas encore été créée.');
		}
		$return = self::$_instance->_get($key);
		if($throwExceptions && is_null($return)){
			throw new Zest_Config_Exception(sprintf('La clef de configuration "%s" n\'existe pas.', $key));
		}
		return $return;
	}
	
	/**
	 * @param string $environment
	 * @param string|array $options
	 * @param callback $callModulesDirectories
	 * @return void
	 */
	public static function initInstance($environment, $options, $callModulesDirectories = null){
		new self($environment, $options, $callModulesDirectories, true);
	}
	
	/**
	 * @param string $filename
	 * @return void
	 */
	protected function _loadConfigs($filename){
		$request = new Zend_Controller_Request_Http();	
		
		parent::_loadConfigs($filename, array('request' => array(
			'base_path' => $request->getBasePath(),
			'base_url' => $request->getBaseUrl(),
			'client_ip' => $request->getClientIp(),
			'http_host' => $request->getHttpHost(),
			'path_info' => $request->getPathInfo(),
			'request_uri' => $request->getRequestUri(),
			'scheme' => $request->getScheme()
		)));
		
		if($this->_callModulesDirectories){
			$modulesDirectories = call_user_func_array($this->_callModulesDirectories, array($this));
			
			if($modulesDirectories && $this->_modulesConfigFormat){
				// modules
				foreach($modulesDirectories as $module => $modulesDirectory){
					$this->_set('module.'.$module, array());
					$this->_set('module.'.$module.'.module_directory', $modulesDirectory);
					
					$iniFile = $modulesDirectory.sprintf($this->_modulesConfigFormat, $module);
					
					$this->_loadConfig($iniFile, 'module.'.$module);
					$dataVarsSource = $this->_merge($this->_data, $this->_get('module.'.$module));
					$this->_recursiveReplaceVars($this->_data, $dataVarsSource);
				}
				
				// sauvegarde du tableau permettant le recalcul du fichier de cache
				if($this->_cache){
					$this	->_unset('_cache_files')
							->_unset('_cache_creation')
							->_set('_cache_files', $this->_cacheFiles)
							->_set('_cache_creation', time());
				}
			}
		}
	}
	
}