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
		$this->_getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, $this->_getTags($module, $controller, $action));
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
	 * @param string $module
	 * @return Zest_Controller_Plugin_Cache
	 */
	public function cleanModule($module){
		$this->_getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array($this->_getTagModule($module)));
		return $this;
	}
	
	/**
	 * @param string $controller
	 * @return Zest_Controller_Plugin_Cache
	 */
	public function cleanController($controller){
		$this->_getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array($this->_getTagController($controller)));
		return $this;
	}
	
	/**
	 * @param string $action
	 * @return Zest_Controller_Plugin_Cache
	 */
	public function cleanAction($action){
		$this->_getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array($this->_getTagAction($action)));
		return $this;
	}
	
	/**
	 * @param string $module
	 * @param string $controller
	 * @return Zest_Controller_Plugin_Cache
	 */
	public function cleanModuleController($module, $controller){
		$this->_getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array($this->_getTagModule($module), $this->_getTagController($controller)));
		return $this;
	}
	
	/**
	 * @param string $controller
	 * @param string $action
	 * @return Zest_Controller_Plugin_Cache
	 */
	public function cleanControllerAction($controller, $action){
		$this->_getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array($this->_getTagController($controller), $this->_getTagAction($action)));
		return $this;
	}
	
	/**
	 * @param string $module
	 * @param string $controller
	 * @param string $action
	 * @return string
	 */
	protected function _getTags($module, $controller, $action){
		return array($this->_getTagModule($module), $this->_getTagController($controller), $this->_getTagAction($action));
	}
	
	/**
	 * @param string $module
	 * @return string
	 */
	protected function _getTagModule($module){
		$front = Zest_Controller_Front::getInstance();
		$dispatcher = $front->getDispatcher();
		
		$module = $module ? $module : $front->getDefaultModule();
		return $dispatcher->formatModuleName($module);
	}
	
	/**
	 * @param string $controller
	 * @return string
	 */
	protected function _getTagController($controller){
		$front = Zest_Controller_Front::getInstance();
		$dispatcher = $front->getDispatcher();
		
		$controller = $controller ? $controller : $front->getDefaultControllerName();
		return $dispatcher->formatControllerName($controller);
	}
	
	/**
	 * @param string $action
	 * @return string
	 */
	protected function _getTagAction($action){
		$front = Zest_Controller_Front::getInstance();
		$dispatcher = $front->getDispatcher();
		
		$action = $action ? $action : $front->getDefaultAction();
		return $dispatcher->formatActionName($action);
	}
	
	/**
	 * @param Zend_Controller_Request_Abstract $request
	 * @return string
	 */
	protected function _getCacheId(Zend_Controller_Request_Abstract $request){
		return ($this->_cacheIdPrefix ? $this->_cacheIdPrefix.'_' : '').md5($request->getRequestUri());
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
	 * @param Zend_Controller_Request_Abstract $request
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
		
		$cacheId = $this->_getCacheId($request);
		$cache = $this->_getCache();
		
		if($mtime = $cache->test($cacheId)){
			header('Last-Modified: ' . date('r', $mtime));
				
			$if_modified_since = $request->getHeader('if-modified-since');
			if($if_modified_since){
				$if_modified_since = strtotime($if_modified_since);
				if(!($if_modified_since < $mtime)){
					header('HTTP/1.1 304 Not Modified');
					exit;
				}
			}
			
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
		
		$cacheId = $this->_getCacheId($request);
		$tags = $this->_getTags($request->getModuleName(), $request->getControllerName(), $request->getActionName());
		
		$body = $this->getResponse()->getBody();
		$this->_getCache()->save($body, $cacheId, $tags);
		
		$this->getResponse()->setHeader('Last-Modified', date('r'));
	}
	
}