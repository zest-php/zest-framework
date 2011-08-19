<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage UnitTests
 */
class Zest_File_Helper_PdfTest extends PHPUnit_Framework_TestCase{
	
	protected function setUp(){
		$dir = $this->_getTemp();
		if(file_exists($dir)){
			foreach(glob($dir.'/*') as $file){
				unlink($file);
			}
		}
		else{
			mkdir($dir);
		}
	}
	
	public function testPluginLoader(){
		$file = new Zest_File($this->_getTemp().'/testPluginLoader.pdf');
		$this->assertInstanceOf('Zend_Loader_PluginLoader', $file->pdf()->getPluginLoader());
	}

//	public function testSetEngine(){
//		$file = new Zest_File($this->_getPathname());
//		$this->assertInstanceOf('Zest_File_Helper_Pdf_Zend', $file->getEngine());
//	}
	
	public function testDomPdfRender(){
		$file = new Zest_File($this->_getTemp().'/testDomPdfRender.pdf');
		$file->setEngine('dompdf');
		Zest_View::getStaticView()->addScriptPath(Zest_AllTests::getDataDir().'/file/pdf_render');
		$file->putRender('dompdf.phtml');
		$this->assertTrue($file->fileExists());
	}
	
	public function testHtml2PdfRender(){
		$file = new Zest_File($this->_getTemp().'/testHtml2PdfRender.pdf');
		$file->setEngine('html2pdf');
		Zest_View::getStaticView()->addScriptPath(Zest_AllTests::getDataDir().'/file/pdf_render');
		$file->putRender('html2pdf.phtml');
		$this->assertTrue($file->fileExists());
	}
	
	protected function _getTemp(){
		return Zest_AllTests::getTempDir().'/Zest_File_Helper_PdfTest';
	}
		
	protected function _getPathname(){
		return Zest_AllTests::getDataDir().'/file/pdf.pdf';
	}
	
}