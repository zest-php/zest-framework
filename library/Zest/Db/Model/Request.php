<?php

/**
 * @category Zest
 * @package Zest_Db
 * @subpackage Model
 */
class Zest_Db_Model_Request extends Zest_Data{
	
	/**
	 * @var array
	 */
	protected $_options = array();

	/**
	 * @param array $data
	 * @return void
	 */
	public function __construct(array $data = array(), array $options = array()){
		parent::__construct($data);
		$this->setOption($options);
	}
	
	/**
	 * @param array $data
	 * @return Zest_Db_Model_Request
	 */
	public static function factory(array $data = array()){
		return new self($data);
	}
	
	/**
	 * @param string $key
	 * @return mixed
	 */
	public function getOption($key){
		if($this->hasOption($key)){
			$key = strtolower($key);
			return $this->_options[$key];
		}
		return null;
	}
	
	/**
	 * @return array
	 */
	public function getOptions(){
		return $this->_options;
	}
	
	/**
	 * @param string $key
	 * @param mixed $value
	 * @return Zest_Db_Model_Request
	 */
	public function setOption($key, $value = null){
		if(is_array($key)){
			$this->setOptions($key);
		}
		else{
			$key = strtolower($key);
			$this->_options[$key] = $value;
		}
		return $this;
	}
	
	/**
	 * @param array $options
	 * @return Zest_Db_Model_Request
	 */
	public function setOptions(array $options){
		foreach($options as $key => $value){
			$this->setOption($key, $value);
		}
		return $this;
	}
	
	/**
	 * @param string $key
	 * @return boolean
	 */
	public function hasOption($key){
		$key = strtolower($key);
		return array_key_exists($key, $this->_options);
	}
	
	/**
	 * @param string $key
	 * @return Zest_Db_Model_Request
	 */
	public function removeOption($key){
		if($this->hasOption($key)){
			$key = strtolower($key);
			unset($this->_options[$key]);
		}
		return $this;
	}
	
}