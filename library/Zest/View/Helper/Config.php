<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage Helper
 */
class Zest_View_Helper_Config extends Zend_View_Helper_Abstract{
	
	/**
	 * @return mixed
	 */
	public function config($key){
		return Zest_Config::get($key);
	}
	
}