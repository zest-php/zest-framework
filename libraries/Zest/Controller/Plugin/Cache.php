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
	 * @var string
	 */
	protected $_cacheDir = null;
	
	/**
	 * @var string
	 */
	protected $_defaultLifetime = null;
	
	/**
	 * @var string
	 */
	protected $_cacheIdPrefix = null;
	
	/**
	 * @return void
	 */
	public function __construct(){
		$this->_now = time();
		$this->_cacheDir = sys_get_temp_dir().'cache/';
	}
	
	/**
	 * @param string $cacheDir
	 * @return Zest_Controller_Plugin_Cache
	 */
	public function setCacheIdPrefix($prefix){
		$this->_cacheIdPrefix = $prefix;
		return $this;
	}
	
	/**
	 * @param string $cacheDir
	 * @return Zest_Controller_Plugin_Cache
	 */
	public function setCacheDir($cacheDir){
		$this->_cacheDir = $cacheDir;
		return $this;
	}
	
	/**
	 * @param integer $lifetime
	 * @return Zest_Controller_Plugin_Cache
	 */
	public function setDefaultLifetime($lifetime){
		$this->_defaultLifetime = $lifetime;
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
			if(!file_exists($this->_cacheDir)){
				Zest_Dir::factory($this->_cacheDir)->recursiveMkdir();
			}
			$frontend = array(
				'automatic_serialization' => true
			);
			$backend = array(
				'cache_dir' => $this->_cacheDir
			);
			$this->_cache = Zend_Cache::factory('Core', 'File', $frontend, $backend);
		}
		$this->_cache->setLifetime($this->_defaultLifetime);
		return $this->_cache;
	}
	
	/**
	 * @return string
	 */
	protected function _getCacheId($module, $controller, $action){
		$front = Zest_Controller_Front::getInstance();
		
		$module = $module ? $module : $front->getDefaultModule();
		$controller = $controller ? $controller : $front->getDefaultControllerName();
		$action = $action ? $action : $front->getDefaultAction();
		
		$dispatcher = $front->getDispatcher();
		$module = $dispatcher->formatModuleName($module);
		$controller = $dispatcher->formatControllerName($controller);
		$action = $dispatcher->formatActionName($action);
		
		return ($this->_cacheIdPrefix ? $this->_cacheIdPrefix.'_' : '').$module.'_'.$controller.'_'.$action;
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
			$this->_defaultLifetime = min($this->_lifetimes);
		}
		
		$cacheId = $this->_getCacheId($request->getModuleName(), $request->getControllerName(), $request->getActionName());
		$cache = $this->_getCache();
		
		$body = $this->getResponse()->getBody();
		$cache->save($body, $cacheId);
	}
	
}