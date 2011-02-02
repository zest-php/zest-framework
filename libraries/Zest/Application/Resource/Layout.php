<?php

/**
 * @category Zest
 * @package Zest_Application
 * @subpackage Resource
 */
class Zest_Application_Resource_Layout extends Zend_Application_Resource_Layout{
	
	/**
	 * @return Zest_Layout
	 */
	public function getLayout(){
		if(is_null($this->_layout)){
			$this->_layout = Zest_Layout::startMvc($this->getOptions());
		}
		return $this->_layout;
	}
	
	
}