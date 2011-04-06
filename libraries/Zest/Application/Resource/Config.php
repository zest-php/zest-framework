<?php

/**
 * @category Zest
 * @package Zest_Application
 * @subpackage Resource
 */
class Zest_Application_Resource_Config extends Zend_Application_Resource_ResourceAbstract{
	
	/**
	 * @var boolean
	 */
	protected $_loadConfigs = false;
	
	/**
	 * @return Zest_Config
	 */
	public function init(){
		$application = $this->getBootstrap()->getApplication();
		
		// récupération de la configuration
		$config = Zest_Config::initInstance($application->getEnvironment(), $this->getOptions(), array($this, 'getModulesDirectories'));
		$options = Zest_Config::get();
		
		// attributions des options à l'application et au bootstrap
		$options = $application->mergeOptions($application->getOptions(), $options);
		$application->setOptions($options);
		
		if(!$this->_loadConfigs){
			$this->getBootstrap()->setOptions(array_diff_key($options, array_flip(array('module'))));
		}
		
		return $config;
	}
	
	/**
	 * @param Zest_Config_Application $config
	 * @return array
	 */
	public function getModulesDirectories(Zest_Config_Application $config){
		$this->_loadConfigs = true;
		return $this->getBootstrap()
				->setOptions($config->getConfig())
				->bootstrap('frontcontroller')
				->getPluginResource('frontcontroller')->getModulesDirectories();
	}
	
}