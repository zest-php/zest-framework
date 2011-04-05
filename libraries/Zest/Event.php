<?php

/**
 * @category Zest
 * @package Zest_Event
 */
class Zest_Event{
	
	/**
	 * @var array
	 */
	protected static $_events = array();
	
	/**
	 * @var string
	 */
	protected $_type = null;
	
	/**
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 * @var boolean
	 */
	protected $_stoppable = true;
	
	/**
	 * @var boolean
	 */
	protected $_stopPropagation = false;
	
	/**
	 * @return void
	 */
	protected function __construct($type, $data, $stoppable){
		$this->_type = $type;
		$this->_data = $data;
		$this->_stoppable = $stoppable;
	}
	
	/**
	 * @return void
	 */
	public function stopPropagation(){
		if(!$this->_stoppable){
			throw new Zest_Event_Exception('La propagation de cet évènement ne peut pas être stoppée.');
		}
		$this->_stopPropagation = true;
	}
	
	/**
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name){
		if(array_key_exists($name, $this->_data)){
			return $this->_data[$name];
		}
		throw new Zest_Event_Exception(sprintf('Le paramètre "%s" n\'existe pas.', $name));
	}
	
	/**
	 * @param string $type
	 * @return void
	 */
	protected static function _checkType($type){
		if(!isset(self::$_events[$type])){
			self::$_events[$type] = array();
		}
	}
	
	/**
	 * @param string $type
	 * @return void
	 */
	protected static function _checkCallback($callback){
		if(!is_callable($callback)){
			throw new Zest_Event_Exception('La fonction de callback n\'est pas valide.');
		}
	}
	
	/**
	 * @param string $type
	 * @param callback $callback
	 * @param integer $offset
	 * @return void
	 */
	public static function addEventListener($type, $callback, $offset = null){
		if(is_null($offset)){
			self::appendEventListener($type, $callback);
		}
		else{
			self::offsetSetEventListener($type, $callback, $offset);
		}
	}
	
	/**
	 * @param string $type
	 * @param callback $callback
	 * @return void
	 */
	public static function appendEventListener($type, $callback){
		self::_checkCallback($callback);
		self::_checkType($type);
		array_push(self::$_events[$type], $callback);
	}
	
	/**
	 * @param string $type
	 * @param callback $callback
	 * @return void
	 */
	public static function prependEventListener($type, $callback){
		self::_checkCallback($callback);
		self::_checkType($type);
		array_unshift(self::$_events[$type], $callback);
	}
	
	/**
	 * @param string $type
	 * @param callback $callback
	 * @return void
	 */
	public static function offsetSetEventListener($type, $callback, $offset){
		self::_checkCallback($callback);
		self::_checkType($type);
		if(isset(self::$_events[$type][$offset])){
			throw new Zest_Event_Exception(sprintf('Une fonction de callback est déjà présente à l\'offset "%d".', $offset));
		}
		self::$_events[$type][$offset] = $callback;
		ksort(self::$_events[$type]);
	}
	
	/**
	 * @param string $type
	 * @return void
	 */
	public static function dispatchEvent($type, array $data = array(), $stoppable = true){
		if(isset(self::$_events[$type])){
			$event = new self($type, $data, $stoppable);
			foreach(self::$_events[$type] as $callback){
				call_user_func_array($callback, array($event));
				if($event->_stopPropagation){
					break;
				}
			}
		}
	}
	
	/**
	 * @param string $type
	 * @param callback $callback
	 * @return boolean
	 */
	public static function hasEventListener($type, $callback = null){
		if(isset(self::$_events[$type]) && count(self::$_events[$type])){
			if(is_null($callback)){
				return true;
			}
			else{
				foreach(self::$_events[$type] as $index => $call){
					if($call == $callback){
						return true;
					}
				}
			}
		}
		return false;
	}
	
	/**
	 * @param string $type
	 * @param callback $callback
	 * @return void
	 */
	public static function removeEventListener($type, $callback = null){
		if(isset(self::$_events[$type])){
			if(is_null($callback)){
				unset(self::$_events[$type]);
			}
			else{
				foreach(self::$_events[$type] as $index => $call){
					if($call == $callback){
						unset(self::$_events[$type][$index]);
					}
				}
			}
		}
	}
	
}