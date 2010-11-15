<?php

/**
 * @category Zest
 * @package Zest_Application
 * @subpackage Resource
 */
class Zest_Application_Resource_Frontcontroller extends Zend_Application_Resource_Frontcontroller{

	/**
	 * @return void
	 */
	public function init(){
		parent::init();
		
		$front = $this->getFrontController();
		
		foreach($this->getOptions() as $key => $value){
			switch(strtolower($key)){
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
}