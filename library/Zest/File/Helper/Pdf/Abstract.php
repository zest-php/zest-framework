<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage Helper
 */
abstract class Zest_File_Helper_Pdf_Abstract{
	
	/**
	 * @var Zest_File
	 */
	protected $_file = null;
	
	/**
	 * @param Zest_File $file
	 * @return void
	 */
	public function __construct(Zest_File $file){
		$this->_file = $file;
	}
	
}