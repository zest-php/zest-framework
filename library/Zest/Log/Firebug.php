<?php

/**
 * @category Zest
 * @package Zest_Log
 */
class Zest_Log_Firebug extends Zest_Log_Abstract{
	
	/**
	 * @var Zest_Log
	 */
	protected static $_instance = null;
	
	/**
	 * @return void
	 */
	protected function __construct(){
		$writer = $this->_addWriter('firebug');
		
		$reflection = new ReflectionClass($this->_getLogger());
		$id = count($reflection->getConstants());
		
		$arrayStyles = array(
			$id++ => Zend_Wildfire_Plugin_FirePhp::TRACE,
			$id++ => Zend_Wildfire_Plugin_FirePhp::EXCEPTION,
			$id++ => Zend_Wildfire_Plugin_FirePhp::TABLE,
			$id++ => Zend_Wildfire_Plugin_FirePhp::DUMP,
			$id++ => Zend_Wildfire_Plugin_FirePhp::GROUP_START,
			$id++ => Zend_Wildfire_Plugin_FirePhp::GROUP_END
		);
		
		foreach($arrayStyles as $styleId => $styleMethod){
			$writer->setPriorityStyle($styleId, $styleMethod);
			$this->_getLogger()->addPriority($styleMethod, $styleId);
		}
	}
	
	/**
	 * @return Zest_Log_Firebug
	 */
	protected static function _getInstance(){
		if(!self::$_instance){
			self::$_instance = new self();
		}
		return self::$_instance;
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
	
	/**
	 * @param mixed $message
	 * @return void
	 */
	public static function trace($message){
		self::_getInstance()->_getLogger()->trace($message);
	}
	
	/**
	 * @param mixed $message
	 * @return void
	 */
	public static function groupStart($message){
		self::_getInstance()->_getLogger()->group_start($message);
	}
	
	/**
	 * @param mixed $message
	 * @return void
	 */
	public static function groupEnd(){
		self::_getInstance()->_getLogger()->group_end();
	}
	
}