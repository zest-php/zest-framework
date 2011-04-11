<?php

/**
 * @see Zend_Application
 */
require_once 'Zend/Application.php';

/**
 * @see Zest_Loader_Autoloader
 */
require_once 'Zest/Loader/Autoloader.php';

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
		Zest_Loader_Autoloader::getInstance();
		parent::__construct($environment);
		
		// options
		if(is_null($options)){
			$options = array();
		}
		else{
			if(is_string($options)){
				$options = array('config' => $options);
			}
			else if($options instanceof Zend_Config){
				$options = $options->toArray();
			}
			else if(!is_array($options)){
				throw new Zest_Application_Exception('Le paramètre "options" doit être une chaîne de caractères, un objet de configuration ou un tableau.');
			}
		}
		
		// configuration propre au framework Zest
		$options = $this->mergeOptions($options, array(
			'resources' => array(
				'frontcontroller' => array(
					'actionhelperpaths' => array('Zest_Controller_Action_Helper' => 'Zest/Controller/Action/Helper')
				)
			),
			'pluginpaths' => array('Zest_Application_Resource' => 'Zest/Application/Resource')
		));
		
		$this->setOptions(array_diff_key($options, array_flip(array('config'))));
		
		// même container pour tous les boostraps (!= new Zend_Registry)
		$container = Zend_Registry::getInstance();
		$this->getBootstrap()->setContainer($container);
		
		// environnement
		$container->environment = $this->getEnvironment();
		
		if(isset($options['config'])){
			if(is_string($options['config'])){
				$options['config'] = array('pathname' => $options['config']);
			}
			$this->getBootstrap()
				->registerPluginResource('config', $options['config'])
				->bootstrap('config');
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