<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage Helper
 */
class Zest_File_Helper_Ini extends Zest_File_Helper_Abstract{
	
	/**
	 * @var Zest_Config_Advanced
	 */
	protected $_config = null;
	
	/**
	 * @param array $data
	 * @return Zest_File_Helper_Ini
	 */
	public function putArray(array $data){
		$config = new Zend_Config($data);
		$writer = new Zend_Config_Writer_Ini();
		$writer->write($this->_file->getPathname(), $config, true);
		return $this;
	}
	
	/**
	 * @return array
	 */
	public function getArray(){
		$this->_init();
		return $this->_config->get();
	}
	
	/**
	 * @return array|string
	 */
	public function getConfig($key){
		$this->_init();
		return $this->_config->get($key);
	}
	
	/**
	 * @return void
	 */
	protected function _init(){
		if(!$this->_config){
			$this->_config = new Zest_Config_Advanced($this->_file->getPathname());
		}
	}
	
}