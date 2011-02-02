<?php

/**
 * @category Zest
 * @package Zest_Log
 * @subpackage Writer
 */
class Zest_Log_Writer_Print extends Zend_Log_Writer_Abstract{
	
	/**
	 * @param mixed $event
	 * @return void
	 */
	 protected function _write($event) {
		echo '<pre>'.print_r($event['message'], true).'</pre>';
	}
	
	/**
	 * @param  array|Zend_Config $config
	 * @return Zest_Log_Writer_Print
	 */
	public static function factory($config){
		return new self();
	}
	
}