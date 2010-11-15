<?php

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
	 * @param Zest_File $file
	 * @return void
	 */
	public function __construct(Zest_File $file){
		parent::__construct($file);
		$this->_zendPdf = new Zend_Pdf();
	}
	
	/**
	 * @return void
	 */
	public function test(){
		$page = $this->_zendPdf->pages[] = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
		
		$page	->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 32)
				->drawText('Hello world !', 72, 720, 'UTF-8');
		
		header('content-type: application/pdf');
		echo $this->_zendPdf->render();
		exit;
		
//		$this->_zendPdf->save($this->_file->getPathname());
//		$this->_file->putContents($this->_zendPdf->render());
	}
	
}