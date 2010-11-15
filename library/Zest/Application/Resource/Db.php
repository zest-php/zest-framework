<?php

/**
 * @category Zest
 * @package Zest_Application
 * @subpackage Resource
 */
class Zest_Application_Resource_Db extends Zend_Application_Resource_ResourceAbstract{
	
	/**
	 * @return void
	 */
	public function init(){
		Zest_Db::setDbConfigs($this->getOptions());
	}
	
}