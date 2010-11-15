<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage Helper
 */
class Zest_File_Helper_Pdf extends Zest_File_Helper_Abstract{
	
	/**
	 * @var object
	 */
	protected $_engine = null;
	
	/**
	 * @var Zend_Loader_PluginLoader
	 */
	protected static $_pluginLoader = null;
	
	/**
	 * @return object
	 */
	public function getEngine(){
		if(!$this->_engine){
			$this->setEngine('zend');
		}
		return $this->_engine;
	}
	
	/**
	 * @return Zest_File_Helper_Pdf
	 */
	public function setEngine($engine){
		if(is_string($engine)){
			if(!@class_exists($engine)){
				$engine = self::getPluginLoader()->load($engine);
			}
			if($engine){
				$engine = new $engine($this->_file);
			}
		}
		if($engine instanceof Zest_File_Helper_Pdf_Abstract){
			$this->_engine = $engine;
		}
		else{
			throw new Zest_File_Exception('Le moteur de rendu doit hériter de Zest_File_Helper_Pdf_Abstract.');
		}
		return $this;
	}
	
	/**
	 * @return Zend_Loader_PluginLoader
	 */
	public static function getPluginLoader(){
		if(!self::$_pluginLoader){
			self::$_pluginLoader = new Zend_Loader_PluginLoader(array(
				'Zest_File_Helper_Pdf' => 'Zest/File/Helper/Pdf'
			));
		}
		return self::$_pluginLoader;
	}
	
	/**
	 * @param Zend_Loader_PluginLoader $loader
	 * @return void
	 */
	public static function setPluginLoader(Zend_Loader_PluginLoader $loader){
		self::$_pluginLoader = $loader;
	}
	
	/**
	 * @param string $method
	 * @param array $args
	 * @return mmixed
	 */
	public function __call($method, $args){
		return $this->_call($this->getEngine(), $method, $args);
	}
	
}