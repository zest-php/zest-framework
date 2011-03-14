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
	 * @var string
	 */
	const TYPE_NOTICE = 'notice';
	
	/**
	 * @var string
	 */
	const TYPE_WARNING = 'warning';
	
	/**
	 * @var string
	 */
	const TYPE_EXCEPTION = 'exception';
	
	/**
	 * @var string
	 */
	const TYPE_FATAL = 'fatal';
	
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
		switch($error){
			case self::TYPE_NOTICE:
				return Zend_Log::NOTICE;
			case self::TYPE_WARNING:
				return Zend_Log::WARN;
			case self::TYPE_EXCEPTION:
				return Zend_Log::ERR;
			case self::TYPE_FATAL:
				return Zend_Log::ALERT;
		}
		return null;
	}
	
	/**
	 * @param array $error
	 * @param boolean $forceLog
	 * @return false
	 */
	protected function _handleError($type, array $error){
		if($priority = $this->_getPriority($type)){
			$message = array(
				'ERROR' => $error,
				'SERVER' => $_SERVER,
				'$_GET' => $_GET,
				'$_POST' => $_POST,
				'$_FILES' => $_FILES
			);
			try{
				$this->_getLogger()->log($message, $priority);
			}
			catch(Zend_Exception $e){
			}
		}
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
		
		$type = null;
		switch($errno){
			case E_NOTICE:					// 8
			case E_USER_NOTICE:				// 1024
				$type = self::TYPE_NOTICE;
				break;
			
			case E_WARNING:					// 2
			case E_CORE_WARNING:			// 32
			case E_COMPILE_WARNING:			// 128
			case E_USER_WARNING:			// 512
				$type = self::TYPE_WARNING;
				break;
				
			case E_ERROR:					// 1
			case E_PARSE:					// 4
			case E_CORE_ERROR:				// 16
			case E_COMPILE_ERROR:			// 64
			case E_USER_ERROR:				// 256
			case E_RECOVERABLE_ERROR:		// 4096
				$type = self::TYPE_FATAL;
				break;
				
			case E_STRICT:					// 2048
				$type = null;
				break;
		}
		
		if($type){
			$this->_handleError($type, array(
				'type' => $type,
				'code' => $errno,
				'message' => $errstr,
				'file' => $errfile,
				'line' => $errline
			));
		}
		
		/**
		 * PHP 5.2.0 :
		 * 
		 * 	false doit être retourné pour peupler $php_errormsg
		 * 	en cas de fatal error, stoppe le script
		 * 
		 * 	true doit être retourné pour effectuer des opérations : envoi de mail par exemple
		 * 	en cas de fatal error, continue le script
		 */
		return !(boolean) ini_get('display_errors');
	}
	
	/**
	 * @param Exception $e
	 * @return void
	 */
	public static function handleException(Exception $e){
		self::_getInstance()->_handleError(self::TYPE_EXCEPTION, array(
			'type' => self::TYPE_EXCEPTION,
			'code' => $e->getCode(),
			'message' => $e->getMessage(),
			'file' => $e->getFile(),
			'line' => $e->getLine(),
			'backtrace' => $e->getTraceAsString()
		));
	}
	
}