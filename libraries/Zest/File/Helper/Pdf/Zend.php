<?php

throw new Zest_File_Exception('Zest_File_Helper_Pdf_Zend pending');

/**
 * @category Zest
 * @package Zest_File
 * @subpackage Helper
 */
class Zest_File_Helper_Pdf_Zend extends Zest_File_Helper_Pdf_Abstract{
	
	/**
	 * @var Zend_Pdf
	 */
	protected $_zendPdf = null;
	
	/**
	 * @return Zend_Pdf
	 */
	protected function _getZendPdf(){
		if(is_null($this->_zendPdf)){
			$this->_zendPdf = new Zend_Pdf();
		}
		return $this->_zendPdf;
	}
	
	/**
	 * @return void
	 */
	public function test(){
		$page = $this->_getZendPdf()->pages[] = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
		
		$page	->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 32)
				->drawText('Hello world !', 72, 720, 'UTF-8');
		
		header('content-type: application/pdf');
		echo $this->_getZendPdf()->render();
		exit;
		
//		$this->_getZendPdf()->save($this->_file->getPathname());
//		$this->_file->putContents($this->_getZendPdf()->render());
	}
	
}