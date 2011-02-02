<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage Helper
 */
class Zest_View_Helper_Firebug extends Zend_View_Helper_Abstract{
	
	/**
	 * @return Zest_View_Helper_Firebug
	 */
	public function firebug($data, $type = 'debug'){
		call_user_func(array('Zest_Log_Firebug', $type), $data);
		return $this;
	}
	
}