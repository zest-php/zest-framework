<?php

/**
 * @category Zest
 * @package Zest_Db
 * @subpackage Model
 */
class Zest_Db_Model_Plugin_Broker{
	
	/**
	 * @var array
	 */
	protected $_plugins = array();
	
	/**
	 * @var boolean
	 */
	protected $_throwExceptions = false;
	
	/**
	 * @param Zest_Db_Model_Plugin_Abstract $plugin
	 * @return Zest_Db_Model_PluginBroker
	 */
	public function registerPlugin(Zest_Db_Model_Plugin_Abstract $plugin){
		$this->_plugins[get_class($plugin)] = $plugin;
		return $this;
	}
	
	/**
	 * @param string|Zest_Db_Model_Plugin_Abstract $plugin
	 */
	public function unregisterPlugin($plugin){
		if($plugin instanceof Zest_Db_Model_Plugin_Abstract){
			$plugin = get_class($plugin);
		}
		if($this->hasPlugin($plugin)){
			$plugin = (string) $plugin;
			unset($this->_plugins[$plugin]);
		}
	}
	
	/**
	 * @param string $class
	 * @return boolean
	 */
	public function hasPlugin($class){
		$class = (string) $class;
		return isset($this->_plugins[$class]);
	}
	
	/**
	 * @param string $class
	 * @return Zest_Db_Model_Plugin_Abstract
	 */
	public function getPlugin($class){
		if($this->hasPlugin($class)){
			$class = (string) $class;
			return $this->_plugins[$class];
		}
		return null;
	}
	
	/**
	 * @return array
	 */
	public function getPlugins(){
		return $this->_plugins;
	}
	
	/**
	 * @param null|boolean $flag
	 * @return boolean|Zest_Db_Model_Plugin_Broker
	 */
	public function throwExceptions($flag = null){
		if(is_null($flag)){
			return $this->_throwExceptions;
		}
		$this->_throwExceptions = (boolean) $flag;
		return $this;
	}
	
	/**
	 * @param string $method
	 * @param object $arg
	 * @return void
	 */
	protected function _call($method, $arg){
		foreach($this->_plugins as $plugin){
			try{
				$plugin->$method($arg);
			}
			catch(Exception $e){
				if($this->throwExceptions()){
					throw $e;
				}
				else{
					Zest_Controller_Front::getInstance()->getResponse()->setException($e);
				}
			}
		}
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return void
	 */
	public function preCreate(Zest_Db_Model_Request $request){
		$this->_call('preCreate', $request);
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return void
	 */
	public function postCreate(Zest_Db_Model_Request $request){
		$this->_call('postCreate', $request);
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return void
	 */
	public function preSave(Zest_Db_Model_Request $request){
		$this->_call('preSave', $request);
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return void
	 */
	public function postSave(Zest_Db_Model_Request $request){
		$this->_call('postSave', $request);
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return void
	 */
	public function preGet(Zest_Db_Model_Request $request){
		$this->_call('preGet', $request);
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return void
	 */
	public function postGet(Zest_Db_Model_Request $request){
		$this->_call('postGet', $request);
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return void
	 */
	public function preGetArray(Zest_Db_Model_Request $request){
		$this->_call('preGetArray', $request);
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return void
	 */
	public function postGetArray(Zest_Db_Model_Request $request){
		$this->_call('postGetArray', $request);
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return void
	 */
	public function preDelete(Zest_Db_Model_Request $request){
		$this->_call('preDelete', $request);
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return void
	 */
	public function postDelete(Zest_Db_Model_Request $request){
		$this->_call('postDelete', $request);
	}
	 
}