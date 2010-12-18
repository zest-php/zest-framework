<?php

/**
 * @category Zest
 * @package Zest_Controller
 */
abstract class Zest_Controller_Action extends Zend_Controller_Action{
	
	/**
	 * @return void
	 */
	public function fileAction(){
		Zest_File::factory()->url()->send($this->_request);
	}
	
}