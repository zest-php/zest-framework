<?php

/**
 * @category Zest
 * @package Zest_Log
 * @subpackage Formatter
 */
class Zest_Log_Formatter_Simple extends Zend_Log_Formatter_Simple{
	
	/**
	 * @param array $event
	 * @return string
	 */
	public function format($event){
		if(is_array($event['message']) || is_object($event['message'])){
			$event['message'] = print_r($event['message'], true);
		}
		return parent::format($event);
	}
	
}