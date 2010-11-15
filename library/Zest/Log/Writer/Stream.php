<?php

/**
 * @category Zest
 * @package Zest_Log
 * @subpackage Writer
 */
class Zest_Log_Writer_Stream extends Zend_Log_Writer_Stream{

	/**
	 * @param string $streamOrUrl
	 * @param string $mode
	 * @return void
	 */
	public function __construct($streamOrUrl, $mode = 'a'){
		parent::__construct($streamOrUrl, $mode);
		$this->_formatter = new Zest_Log_Formatter_Simple();
	}
	
}