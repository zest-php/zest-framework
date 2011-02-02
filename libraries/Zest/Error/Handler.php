<?php

/**
 * @category Zest
 * @package Zest_Error
 */
class Zest_Error_Handler extends Zest_Log_Abstract{
	
	/**
	 * @var Zest_Error_Handler
	 */
	protected static $_instance = null;
	
	/**
	 */
	const TYPE_FATAL = 'fatal';
	
	/**
	 */
	const TYPE_WARNING = 'warning';
	
	/**
	 */
	const TYPE_NOTICE = 'notice';
	
	/**
	 * @return void
	 */
	protected function __construct(){
	}
	
	/**
	 * @return Zest_Error_Handler
	 */
	protected static function _getInstance(){
		if(!self::$_instance){
			self::$_instance = new self();
			self::$_instance->_init();
		}
		return self::$_instance;
	}
	
	/**
	 * @return void
	 */
	protected function _init(){
		set_error_handler(array($this, 'handleError')) ;
		register_shutdown_function(array($this, 'handleFatal')) ;
	}
	
	/**
	 * @param string $errorType
	 * @param string $writerType
	 * @param array $args
	 * @return void
	 */
	public static function addNotification($errorType, $writerType, $args = null){
		self::_getInstance()->_addNotification($errorType, $writerType, $args);
	}
	
	/**
	 * @param string $errorType
	 * @param string $writerType
	 * @param array $args
	 * @return void
	 */	
	protected function _addNotification($errorType, $writerType, $args){
		$writer = $this->_addWriter($writerType, $args);
		
		if(!$priority = $this->_getPriority($errorType)){
			throw new Zest_Error_Handler_Exception(sprintf('Le type "%s" n\'existe pas.', $errorType));
		}
		
		$writer->addFilter(new Zend_Log_Filter_Priority($priority, '=='));
	}
	
	/**
	 * @param integer|string $error
	 * @return integer|null
	 */
	protected function _getPriority($error){
		if(is_numeric($error)){
			switch($error){
				case E_STRICT:					// 2048
					return null;
					
				case E_ERROR:					// 1
				case E_PARSE:					// 4
				case E_CORE_ERROR:				// 16
				case E_COMPILE_ERROR:			// 64
				case E_USER_ERROR:				// 256
				case E_RECOVERABLE_ERROR:		// 4096
					$error = self::TYPE_FATAL;
					break;
				
				case E_WARNING:					// 2
				case E_CORE_WARNING:			// 32
				case E_COMPILE_WARNING:			// 128
				case E_USER_WARNING:			// 512
					$error = self::TYPE_WARNING;
					break;
					
				case E_NOTICE:					// 8
				case E_USER_NOTICE:				// 1024
					$error = self::TYPE_NOTICE;
					break;
			}
		}
		switch($error){
			case self::TYPE_FATAL:
				return Zend_Log::ERR;
			case self::TYPE_WARNING:
				return Zend_Log::WARN;
			case self::TYPE_NOTICE:
				return Zend_Log::NOTICE;
		}
		return null;
	}
	
	/**
	 * @param array $error
	 * @return false
	 */
	protected function _handleError(array $error){
		if($priority = $this->_getPriority($error['type'])){
			$message = print_r($error, true);
			$this->_getLogger()->log($message, $priority);
		}			
		
		return false; // PHP 5.2.0 : false doit être retourné pour peupler $php_errormsg
	}
	
	/**
	 * @param integer $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param integer $errline
	 * @param array $errcontext
	 * @return void|false
	 */
	public function handleError($errno, $errstr, $errfile = null, $errline = null, $errcontext = null){
		// si l'erreur est supprimée par un @
		if(error_reporting() == 0) return;
		
		return $this->_handleError(array(
			'type' => $errno,
			'message' => $errstr,
			'file' => $errfile,
			'line' => $errline
		));
	}
	
	/**
	 * @return void|false
	 */
	public function handleFatal(){
		if($error = error_get_last()){
			return $this->_handleError($error);
		}
	}
	
}