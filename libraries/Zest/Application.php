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
		parent::__construct($environment);
		
		// initialisations propres au framework Zest
		
			// autoloadernamespaces
			$this->setAutoloaderNamespaces(array('Zest'));
			
			// frontcontroller.actionhelperpaths
			Zend_Controller_Action_HelperBroker::addPrefix('Zest_Controller_Action_Helper');
			
			// pluginpaths
			$this->getBootstrap()->getPluginLoader()->addPrefixPath('Zest_Application_Resource', 'Zest/Application/Resource');
		
		// même container pour tous les boostraps (!= new Zend_Registry)
		$container = Zend_Registry::getInstance();
		$this->getBootstrap()->setContainer($container);
		
		// environnement
		$container->environment = $this->getEnvironment();
			
		if(!is_null($options)){
			// options
			if(is_string($options)){
				$options = array('config' => $options);
			}
			else if($options instanceof Zend_Config){
				$options = $options->toArray();
			}
			else if(!is_array($options)){
				throw new Zest_Application_Exception('Le paramètre "options" doit être une chaîne de caractères, un objet de configuration ou un tableau.');
			}
			$optionsDiff = array_diff_key($options, array_flip(array('config')));
			if($optionsDiff){
				$this->setOptions($optionsDiff);
			}
			
			if(isset($options['config'])){
				$this->getBootstrap()
					->registerPluginResource('config', $options['config'])
					->bootstrap('config');
			}
		}
	}
	
	/**
	 * @return Zest_Application_Bootstrap_Bootstrap
	 */
	public function getBootstrap(){
		if(is_null($this->_bootstrap)){
			$this->_bootstrap = new Zest_Application_Bootstrap_Bootstrap($this);
		}
		return $this->_bootstrap;
	}
	
}