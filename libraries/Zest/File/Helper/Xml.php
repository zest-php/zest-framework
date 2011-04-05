<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage Helper
 */
class Zest_File_Helper_Xml extends Zest_File_Helper_Abstract{
	
	/**
	 * @var SimpleXMLElement
	 */
	protected $_xml = null;
	
	/**
	 * @return SimpleXMLElement
	 */
	protected function _getSimpleXMLElement(){
		if(is_null($this->_xml)){
			$this->_xml = new SimpleXMLElement($this->_file->getContents());
		}
		return $this->_xml;
	}
	
	/**
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 */
	public function __call($method, $args){
		return $this->_call($this->_getSimpleXMLElement(), $method, $args);
	}
	
	/**
	 * @param string $name
	 * @return SimpleXMLElement
	 */
	public function __get($name){
		return $this->_getSimpleXMLElement()->$name;
	}
	
//	/**
//	 * @param string $name
//	 * @param mixed $value
//	 * @return mixed
//	 */
//	public function __set($name, $value){
//		$this->_getSimpleXMLElement()->$name = $value;
//	}
	
}