<?php

/**
 * @category Zest
 * @package Zest_Application
 * @subpackage Resource
 */
class Zest_Application_Resource_ErrorHandler extends Zend_Application_Resource_ResourceAbstract{
	
	/**
	 * @return void
	 */
	public function init(){
		foreach($this->getOptions() as $type => $notifications){
			$notifications = (array) $notifications;
			foreach($notifications as $notification){
				$writerInfos = explode(' ', $notification);
				
				if(count($writerInfos) > 0){
					$writerType = array_shift($writerInfos);
					$args = $writerInfos;
					Zest_Error_Handler::addNotification($type, $writerType, $args);
				}
			}
		}
	}
	
}