<?php

/**
 * @category Zest
 * @package Zest_Controller
 */
class Zest_Controller_Front extends Zend_Controller_Front{
	
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
	 * @param Zest_Controller_Front $instance
	 * @return void
	 */
	public static function setInstance($instance){
		self::$_instance = $instance;
	}
	
	/**
	 * @param string $module
	 * @return string
	 */
	public function getModuleUrl($module){
		$source = dirname($this->getRequest()->getServer('SCRIPT_FILENAME'));
		$destination = dirname($this->getModuleDirectory('gui'));
		return rtrim($this->getRequest()->getBaseUrl(), '/').'/'.$this->_getRelativeDir($source, $destination);
	}
	
	/**
	 * @param string $sourceDir
	 * @param string $destinationDir
	 * @return string
	 */
	protected function _getRelativeDir($sourceDir, $destinationDir){
		$source = str_replace(DIRECTORY_SEPARATOR, '/', $sourceDir);
		$destination = str_replace(DIRECTORY_SEPARATOR, '/', $destinationDir);
				
		$source = explode('/', trim($source, '/'));
		$destination = explode('/', trim($destination, '/'));
		
		foreach($source as $key => $part){
			if($part === $destination[$key]){
				unset($source[$key], $destination[$key]);
			}
			else{
				break;
			}
		}
		return str_repeat('../', count($source)).implode('/', $destination);
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
	
}