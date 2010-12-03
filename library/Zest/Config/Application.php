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
	 * @var Zend_Application
	 */
	protected $_application = null;
	
	/**
	 * @var string
	 */
	protected $_modulesConfigFormat = null;
	
	/**
	 * @param Zend_Application $application
	 * @param string|array $options
	 * @return void
	 */
	public function __construct(Zend_Application $application, $options){
		if(is_string($options)){
			$options = array('pathname' => $options);
		}
		$options = array_change_key_case($options, CASE_LOWER);
		
		$this->_application = $application;
		$this->_section = $this->_application->getEnvironment();
		
		if(isset($options['modules_config_format'])){
			$this->_modulesConfigFormat = $options['modules_config_format'];
			unset($options['modules_config_format']);
		}
		
		self::$_instance = $this;
		
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
	 * @param Zend_Application $application
	 * @param string|array $options
	 * @return void
	 */
	public static function init(Zend_Application $application, $options){
		new self($application, $options);
	}
	
	/**
	 * @param string $key
	 * @return array|string
	 */
	public function get($key = null, $throwExceptions = false){
		$return = null;
		if(self::$_instance){
			$return = self::$_instance->_get($key);
		}
		if($throwExceptions && is_null($return)){
			throw new Zest_Config_Exception(sprintf('La clef de configuration "%s" n\'existe pas.', $key));
		}
		return $return;
	}
	
	/**
	 * @return Zest_Config_Application
	 */
	public static function getInstance(){
		return self::$_instance;
	}
	
	/**
	 * @param Zest_Config_Application $instance
	 * @return void
	 */
	public static function setInstance($instance){
		self::$_instance = $instance;
	}
	
	/**
	 * @param string $filename
	 * @return array
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
		
		if($this->_application && $this->_modulesConfigFormat){
			$modulesDirectories = $this->_application->getModulesDirectories();
			
			if($modulesDirectories){			
				// modules
				foreach($modulesDirectories as $module => $modulesDirectory){
					$this->_set('module.'.$module, array());
					
					$iniFile = $modulesDirectory.sprintf($this->_modulesConfigFormat, $module);
					$this->_set('base_path', $modulesDirectory);
					$this->_loadConfig($iniFile, 'module.'.$module);
					$this->_unset('base_path');
				}
				
				// sauvegarde du tableau permettant le recalcul du fichier de cache
				$this	->_unset('_cache_files')
						->_unset('_cache_creation')
						->_set('_cache_files', $this->_cacheFiles)
						->_set('_cache_creation', time());
			}
		}
		
		$this->_unset('request');
	}
	
}