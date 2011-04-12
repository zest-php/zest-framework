<?php

/**
 * @category Zest
 * @package Zest_Data
 */
class Zest_Data implements IteratorAggregate{
	
	/**
	 * @var array
	 */
	protected $_data = array();

	/**
	 * @param array $data
	 * @return void
	 */
	public function __construct(array $data = array()){
		$this->setData($data);
		$this->init();
	}
	
	/**
	 * @return void
	 */
	public function init(){
	}
	
	/**
	 * @param string $key
	 * @return mixed
	 */
	public function getData($key){
		if($this->hasData($key)){
			return $this->_data[$key];
		}
		return null;
	}
	
	/**
	 * @param string $key
	 * @param mixed $value
	 * @return Zest_Data
	 */
	public function setData($key, $value = null){
		if(is_array($key)){
			foreach($key as $key => $value){
				$this->setData($key, $value);
			}
		}
		else{
			$this->_data[$key] = $value;
		}
		return $this;
	}
	
	/**
	 * @param string $key
	 * @return boolean
	 */
	public function hasData($key){
		return array_key_exists($key, $this->_data);
	}
	
	/**
	 * @param string $key
	 * @return Zest_Data
	 */
	public function removeData($key){
		if($this->hasData($key)){
			unset($this->_data[$key]);
		}
		return $this;
	}
	
	/**
	 * @param string $key
	 * @param mixed $value
	 * @return Zest_Data
	 */
	public function append($key, $value){
		if(!is_array($this->getData($key))){
			$this->setData($key, array());
		}
		$this->_data[$key][] = $value;
		return $this;
	}
	
	/**
	 * @param string $key
	 * @param mixed $value
	 * @return Zest_Data
	 */
	public function prepend($key, $value){
		if(!is_array($this->getData($key))){
			$this->setData($key, array());
		}
		array_unshift($this->_data[$key], $value);
		return $this;
	}
	
	/**
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name){
		return $this->getData($name);
	}
	
	/**
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function __set($name, $value){
		$this->setData($name, $value);
	}
	
	/**
	 * @param string $name
	 * @return boolean
	 */
	public function __isset($name){
		return $this->hasData($name);
	}
	
	/**
	 * @param string $name
	 * @return void
	 */
	public function __unset($name){
		$this->removeData($name);
	}
	
	/**
	 * @return ArrayIterator
	 */
	public function getIterator(){
		return new ArrayIterator($this->_data);
	}
	
	/**
	 * @return stdClass
	 */
	public function toStdClass(){
		return (object) $this->_data;
	}
	
	/**
	 * @return array
	 */
	public function toArray(){
		return $this->_data;
	}
	
}