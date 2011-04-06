<?php

/**
 * @category Zest
 * @package Zest_Controller
 */
class Zest_Controller_Front extends Zend_Controller_Front{
	
	/**
	 * @var array
	 */
	protected $_modulesUrls = array();
	
	/**
	 * @return Zest_Controller_Front
	 */
	public static function getInstance(){
		if(!self::$_instance){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * @param string $module
	 * @return string
	 */
	public function getModuleUrl($module, $clean = true){
		$module = strtolower($module);
		
		if(isset($this->_modulesUrls[$module])){
			return $this->_modulesUrls[$module];
		}
		
		$url = null;
		
		$destination = str_replace(DIRECTORY_SEPARATOR, '/', $this->getModuleDirectory($module));
		$destination = trim($destination, '/');
		
		$documentRoot = $this->getRequest()->getServer('DOCUMENT_ROOT');
		$scriptFilename = $this->getRequest()->getServer('SCRIPT_FILENAME');
		
		if(is_null($documentRoot)){
			$scriptFilename = str_replace(DIRECTORY_SEPARATOR, '/', dirname($scriptFilename));
			$scriptFilename = trim($scriptFilename, '/');
			
			$relativeDir = $this->_getRelativeDir($scriptFilename, $destination);
			$baseUrl = trim($this->getRequest()->getBasePath(), '/');
			$url = '/'.$baseUrl.'/'.$relativeDir;
			
			if($clean){
				$count = substr_count($url, '../');
				if($count){
					if($count > count(explode('/', $baseUrl))){
						throw new Zest_Controller_Exception(sprintf('Le module "%s" ne semble pas être accessible par HTTP.', $module));
					}
					$url = preg_replace('#(/[^/]+){'.$count.'}(/..){'.$count.'}#', '', $url);
				}
			}
		}
		else{
			$documentRoot = str_replace(DIRECTORY_SEPARATOR, '/', $documentRoot);
			$documentRoot = trim($documentRoot, '/');
			
			if(!is_int(strpos($destination, $documentRoot))){
				throw new Zest_Controller_Exception(sprintf('Le module "%s" ne semble pas être accessible par HTTP.', $module));
			}
			$url = str_replace($documentRoot, '', $destination).'/';
		}
		
		$this->_modulesUrls[$module] = $url;
		
		return $url;
	}
	
	/**
	 * @param string $sourceDir
	 * @param string $destinationDir
	 * @return string
	 */
	private function _getRelativeDir($sourceDir, $destinationDir){
		$source = explode('/', $sourceDir);
		$destination = explode('/', $destinationDir);
		
		foreach($source as $key => $part){
			if($part === $destination[$key]){
				unset($source[$key], $destination[$key]);
			}
			else{
				break;
			}
		}
		return str_repeat('../', count($source)).implode('/', $destination).'/';
	}
	
	/**
	 * @return Zend_Controller_Router_Interface
	 */
	public function getRouter(){
		if(!$this->_router){
			$this->_router = new Zest_Controller_Router_Rewrite();
		}
		return $this->_router;
	}
	
	/**
	 * @return Zest_Controller_Dispatcher_Standard
	 */
	public function getDispatcher(){
		if(!$this->_dispatcher){
			$this->_dispatcher = new Zest_Controller_Dispatcher_Standard();
		}
		return $this->_dispatcher;
	}
	
	/**
	 * @param Zend_Controller_Request_Abstract $request
	 * @param Zend_Controller_Response_Abstract $response
	 * @return void|Zend_Controller_Response_Abstract
	 */
	public function dispatch(Zend_Controller_Request_Abstract $request = null, Zend_Controller_Response_Abstract $response = null){
		if(!$this->getParam('noErrorHandler') && !$this->_plugins->hasPlugin('Zest_Controller_Plugin_ErrorHandler')){
			$this->_plugins->registerPlugin(new Zest_Controller_Plugin_ErrorHandler(), 100);
		}
		
		// on désactive ErrorHandler dans parent::dispatch pour gérer le plugin juste au dessus
		$this->setParam('noErrorHandler', 'zest (false)');
		
		return parent::dispatch($request, $response);
	}
	
}