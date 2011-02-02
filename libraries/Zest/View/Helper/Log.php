<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage Helper
 */
class Zest_View_Helper_Log extends Zend_View_Helper_Abstract{
	
	/**
	 * @return Zest_View_Helper_Log
	 */
	public function log($data, $type = 'debug'){
		call_user_func(array('Zest_Log', $type), $data);
		return $this;
	}
	
}