<?php

/**
 * @category Zest
 * @package Zest_Controller
 * @subpackage Plugin
 */
class Zest_Controller_Plugin_Cache extends Zend_Controller_Plugin_Abstract{
	
	/**
	 * @var Zend_Cache_Core
	 */
	protected $_cache = null;
	
	/**
	 * @var array
	 */
	protected $_lifetimes = array();
	
	/**
	 * @var integer
	 */
	protected $_now = 0;
	
	/**
	 * @var array
	 */
	protected $_options = array();
	
	/**
	 * @return void
	 */
	public function __construct(){
		$this->_now = time();
		$this->_options = array(
			'cache_dir' => sys_get_temp_dir().'cache/',
			'lifetime' => null
		);
	}
	
	/**
	 * @param string $cacheDir
	 * @return Zest_Controller_Plugin_Cache
	 */
	public function setCacheDir($cacheDir){
		$this->_options['cache_dir'] = $cacheDir;
		return $this;
	}
	
	/**
	 * @param integer $lifetime
	 * @return Zest_Controller_Plugin_Cache
	 */
	public function setLifetime($lifetime){
		$this->_options['lifetime'] = $lifetime;
		return $this;
	}
	
	/**
	 * @param integer $lifetime
	 * @return Zest_Controller_Plugin_Cache
	 */
	public function addLifetime($lifetime){
		if(is_int($lifetime)){
			if($lifetime < 0){
				$lifetime = 0;
			}
			$this->_lifetimes[] = $lifetime;
		}
		return $this;
	}
	
	/**
	 * @param integer|string timestamp
	 * @return Zest_Controller_Plugin_Cache
	 */
	public function addExpire($timestamp){
		if(!is_numeric($timestamp)){
			$timestamp = strtotime($timestamp);
		}
		$this->addLifetime($timestamp-$this->_now);
		return $this;
	}
	
	/**
	 * @param string $module
	 * @param string $controller
	 * @param string $action
	 * @return Zest_Controller_Plugin_Cache
	 */
	public function clean($module, $controller, $action){
		$cacheId = $this->_getCacheId($module, $controller, $action);
		$this->_getCache()->remove($cacheId);
		return $this;
	}
	
	/**
	 * @return Zest_Controller_Plugin_Cache
	 */
	public function cleanAll(){
		$this->_getCache()->clean(Zend_Cache::CLEANING_MODE_ALL);
		return $this;
	}
	
	/**
	 * @return Zend_Cache_Core
	 */
	protected function _getCache(){
		if(!$this->_cache){
			if(!file_exists($this->_options['cache_dir'])){
				Zest_Dir::factory($this->_options['cache_dir'])->recursiveMkdir();
			}
			$frontend = array(
				'automatic_serialization' => true
			);
			$backend = array(
				'cache_dir' => $this->_options['cache_dir']
			);
			$this->_cache = Zend_Cache::factory('Core', 'File', $frontend, $backend);
		}
		$this->_cache->setLifetime($this->_options['lifetime']);
		return $this->_cache;
	}
	
	/**
	 * @return string
	 */
	protected function _getCacheId($module, $controller, $action){
		$module = $module ? $module : Zest_Controller_Front::getInstance()->getDefaultModule();
		$controller = $controller ? $controller : Zest_Controller_Front::getInstance()->getDefaultControllerName();
		$action = $action ? $action : Zest_Controller_Front::getInstance()->getDefaultAction();
		
		return $module.'_'.$controller.'_'.$action;
	}
	
	/**
	 * @return boolean
	 */
	protected function _isCacheable(Zend_Controller_Request_Abstract $request){
		$params = $request->getParams();
		unset($params['module'], $params['controller'], $params['action']);
		
		return $request->isGet() && !$params;
	}
	
	/**
	 * @param Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request){
		if(!$this->_isCacheable($request)) return;
		
		$cacheId = $this->_getCacheId($request->getModuleName(), $request->getControllerName(), $request->getActionName());
		$cache = $this->_getCache();
		
		if($cache->test($cacheId)){
			echo $cache->load($cacheId);
			exit;
		}
	}
	
	/**
	 * @param Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function postDispatch(Zend_Controller_Request_Abstract $request){
		if(!$this->_isCacheable($request)) return;
		
		if($this->_lifetimes){
			$this->_options['lifetime'] = min($this->_lifetimes);
		}
		
		$cacheId = $this->_getCacheId($request->getModuleName(), $request->getControllerName(), $request->getActionName());
		$cache = $this->_getCache();
		
		$body = $this->getResponse()->getBody();
		$cache->save($body, $cacheId);
	}
	
}