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
	 * @param Zest_File $file
	 * @return void
	 */
	public function __construct(Zest_File $file){
		parent::__construct($file);
		$this->_xml = new SimpleXMLElement($file->getContents());
	}
	
	/**
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 */
	public function __call($method, $args){
		return $this->_call($this->_xml, $method, $args);
	}
	
	/**
	 * @param string $name
	 * @return SimpleXMLElement
	 */
	public function __get($name){
		return $this->_xml->$name;
	}
	
//	/**
//	 * @param string $name
//	 * @param mixed $value
//	 * @return mixed
//	 */
//	public function __set($name, $value){
//		$this->_xml->$name = $value;
//	}
	
}