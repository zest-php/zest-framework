<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage Helper
 */
class Zest_File_Helper_Html extends Zest_File_Helper_Abstract{
	
	/**
	 * @var Zend_Dom_Query
	 */
	protected $_dom = null;
	
	/**
	 * @return void
	 */
	protected function _init(){
		if(!$this->_dom){
			$this->_dom = new Zend_Dom_Query($this->_file->getContents());
		}
	}
	
	/**
	 * @param string $selector
	 * @return Zend_Dom_Query_Result
	 */
	public function find($selector){
		$this->_init();
		return $this->_dom->query($selector);
	}
	
}