<?php

/**
 * @see DOMPDF
 * 		ATTENTION
 * 		dans la fonction DOMPDF_autoload, il faut rajouter
 * 		if(file_exists(DOMPDF_INC_DIR . "/$filename")){
 */
require_once 'dompdf/dompdf_config.inc.php';

/**
 * @category Zest
 * @package Zest_File
 * @subpackage Helper
 */
class Zest_File_Helper_Pdf_DomPdf extends Zest_File_Helper_Pdf_Abstract{
	
	/**
	 * @var DOMPDF
	 */
	protected $_domPdf = null;
	
	/**
	 * @var array
	 */
	protected $_paper = array(
		'4a0', '2a0',
		'a0', 'a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10',
		'b0', 'b1', 'b2', 'b3', 'b4', 'b5', 'b6', 'b7', 'b8', 'b9', 'b10',
		'c0', 'c1', 'c2', 'c3', 'c4', 'c5', 'c6', 'c7', 'c8', 'c9', 'c10',
		'ra0', 'ra1', 'ra2', 'ra3', 'ra4',
		'sra0', 'sra1', 'sra2', 'sra3', 'sra4',
		'letter', 'legal', 'ledger', 'tabloid', 'executive', 'folio',
		'commerical #10 envelope', 'catalog #10 1/2 envelope',
		'8.5x11', '8.5x14', '11x17'
	);
	
	/**
	 * @var array
	 */
	protected $_orientation = array('portrait', 'landscape');
	
	/**
	 * @return void
	 */
	protected function _autoload(){
		$hasDomPdfAutoload = false;
		$autoloader = Zend_Loader_Autoloader::getInstance();
		foreach($autoloader->getAutoloaders() as $loader){
			if($loader === 'DOMPDF_autoload'){
				$hasDomPdfAutoload = true;
			}
		}
		if(!$hasDomPdfAutoload){
			$autoloader->pushAutoloader('DOMPDF_autoload');
		}
	}
	
	/**
	 * @param string $name
	 * @param array $vars
	 * @param string $size
	 * @param string $orientation
	 * @return Zest_File_Helper_Pdf_DomPdf
	 */
	public function putRender($name, array $vars = array(), $size = 'a4', $orientation = 'portrait'){
		$this->_autoload();
		
		if(!in_array($size, $this->_paper)){
			throw new Zest_File_Exception(sprintf('Le format "%s" n\'existe pas.', $size));
		}
		if(!in_array($orientation, $this->_orientation)){
			throw new Zest_File_Exception(sprintf('L\'orientation "%s" n\'existe pas.', $orientation));
		}
		
		// rendu du script
		$content = Zest_View::getStaticView()->partial($name, $vars);
		
		// génération du PDF
		$this->_domPdf = new DOMPDF();
		$this->_domPdf->load_html($content);
		$this->_domPdf->set_paper($size, $orientation);
		$this->_domPdf->render();
		
		
		// écriture du PDF
		$this->_file->putContents($this->_domPdf->output());
		
		return $this;
	}
	
}