<?php

/**
 * @category Zest
 * @package Zest_Log
 */
abstract class Zest_Log_Abstract{
	
	/**
	 * @var Zend_Loader_PluginLoader
	 */
	protected static $_pluginLoader = null;
	
	/**
	 * @var Zend_Log
	 */
	private $_logger = null;
	
	/**
	 * @var array
	 */
	protected $_times = array();
	
	/**
	 * @return Zend_Log
	 */
	protected function _getLogger(){
		if(!$this->_logger){
			$this->_logger = new Zend_Log();
		}
		return $this->_logger;
	}
	
	/**
	 * @param string $writerType
	 * @param array $args
	 * @return Zend_Log_Writer_Abstract
	 */
	protected function _addWriter($writerType, $args = null){
		$args = (array) $args;
		
		$writerType = !$writerType ? 'null' : strtolower($writerType);
		
		$loader = self::getPluginLoader();
		$className = $loader->load(strtolower($writerType), false);
		if($className){
			$class = new ReflectionClass($className);
			if($class->hasMethod('__construct')){
				$writer = $class->newInstanceArgs((array) $args);
			}
			else{
				$writer = $class->newInstance();
			}
		}
		else{
			throw new Zest_Log_Exception(sprintf('Le type "%s" n\'existe pas.', $writerType));
		}
		
		$this->_getLogger()->addWriter($writer);
		
		return $writer;
	}
	
	/**
	 * @return Zend_Loader_PluginLoader
	 */
	public static function getPluginLoader(){
		if(!self::$_pluginLoader){
			self::$_pluginLoader = new Zend_Loader_PluginLoader(array(
				'Zend_Log_Writer' => 'Zend/Log/Writer',
				'Zest_Log_Writer' => 'Zest/Log/Writer'
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
	 * @param mixed $message
	 * @return void
	 */
	protected function _timeStart($name){
		$this->_times[$name] = microtime(true);
	}
	
	/**
	 * @param mixed $message
	 * @return void
	 */
	protected function _timeEnd($name, $method = 'debug'){
		$times = $this->_times;
		if(isset($times[$name])){
			$duration = microtime(true)-$times[$name];
			$this->_getLogger()->$method($name.' : '.$duration);
		}
		else{
			throw new Zest_Log_Exception(sprintf('Le timer "%s" n\'est pas initialisé.', $name));
		}
	}
	
}