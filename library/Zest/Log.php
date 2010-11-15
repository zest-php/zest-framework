<?php

/**
 * @category Zest
 * @package Zest_Log
 */
class Zest_Log extends Zest_Log_Abstract{
	
	/**
	 * @var Zest_Log
	 */
	protected static $_instance = null;
	
	/**
	 * @var array
	 */
	protected $_priorities = null;
	
	/**
	 * @return void
	 */
	protected function __construct(){
		$reflection = new ReflectionClass($this->_getLogger());
		$this->_priorities = $reflection->getConstants();
	}
	
	/**
	 * @return Zest_Log
	 */
	protected static function _getInstance(){
		if(!self::$_instance){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * @param string $priority
	 * @param string $writerType
	 * @param array $args
	 * @return void
	 */
	public static function addWriter($priority, $writerType, $args = null){
		self::_getInstance()->_addWriter($priority, $writerType, $args);
	}
	
	/**
	 * @param string $priority
	 * @param string $writerType
	 * @param array $args
	 * @return void
	 */	
	protected function _addWriter($priority, $writerType, $args){
		$writer = parent::_addWriter($writerType, $args);
		
		if(is_string($priority)){
			$priority = strtoupper($priority);
			if(isset($this->_priorities[$priority])){
				$priority = $this->_priorities[$priority];
			}
		}
		
		if(!in_array($priority, $this->_priorities, true)){
			throw new Zest_Log_Exception(sprintf('Le type "%s" n\'existe pas.', $priority));
		}
		
		$writer->addFilter(new Zend_Log_Filter_Priority($priority, '=='));
	}
	
	/**
	 * uniquement compatible PHP 5.3.0
	 * 
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 */
	public static function __callStatic($method, $args){
		return call_user_func_array(array(self::_getInstance()->_getLogger(), $method), $args);
	}
	
	/**
	 * @param mixed $message
	 * @return void
	 */
	public static function err($message){
		self::_getInstance()->_getLogger()->err($message);
	}
	
	/**
	 * @param mixed $message
	 * @return void
	 */
	public static function warn($message){
		self::_getInstance()->_getLogger()->warn($message);
	}
	
	/**
	 * @param mixed $message
	 * @return void
	 */
	public static function notice($message){
		self::_getInstance()->_getLogger()->notice($message);
	}
	
	/**
	 * @param mixed $message
	 * @return void
	 */
	public static function debug($message){
		self::_getInstance()->_getLogger()->debug($message);
	}
	
	/**
	 * @param mixed $message
	 * @return void
	 */
	public static function timeStart($name){
		self::_getInstance()->_timeStart($name);
	}
	
	/**
	 * @param mixed $message
	 * @return void
	 */
	public static function timeEnd($name, $method = 'debug'){
		self::_getInstance()->_timeEnd($name, $method);
	}
	
}