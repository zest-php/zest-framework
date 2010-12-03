<?php

/**
 * @category Zest
 * @package Zest_Application
 * @subpackage Bootstrap
 */
class Zest_Application_Bootstrap_Bootstrap extends Zend_Application_Bootstrap_Bootstrap{
	
	/**
	 * @param  Zend_Application|Zend_Application_Bootstrap_Bootstrapper $application
	 * @return Zest_Application_Bootstrap_Bootstrap
	 */
	public function setApplication($application){
		if(is_object($application)){
			parent::setApplication($application);
		}
		return $this;
	}
	
}