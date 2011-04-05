<?php

/**
 * @see HTML2PDF
 */
require_once 'html2pdf/html2pdf.class.php';

/**
 * @category Zest
 * @package Zest_File
 * @subpackage Helper
 */
class Zest_File_Helper_Pdf_Html2Pdf extends Zest_File_Helper_Pdf_Abstract{
	
	/**
	 * @var HTML2PDF
	 */
	protected $_html2Pdf = null;
	
	/**
	 * @var array
	 */
	protected $_orientation = array('P', 'PORTRAIT', 'L', 'PAYSAGE', 'LANDSCAPE');

	/**
	 * @var array
	 */
	protected $_format = array(
		'4A0', '2A0',
		'A0', 'A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'A8', 'A9', 'A10',
		'B0', 'B1', 'B2', 'B3', 'B4', 'B5', 'B6', 'B7', 'B8', 'B9', 'B10',
		'C0', 'C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7', 'C8', 'C9', 'C10',
		'RA0', 'RA1', 'RA2', 'RA3', 'RA4',
		'SRA0', 'SRA1', 'SRA2', 'SRA3', 'SRA4',
		'LETTER', 'LEGAL', 'EXECUTIVE', 'FOLIO'
	);
	
	/**
	 * @param string $name
	 * @param array $vars
	 * @param string $orientation
	 * @param string $format
	 * @return Zest_File_Helper_Pdf_Html2Pdf
	 */
	public function putRender($name, array $vars = array(), $orientation = 'P', $format = 'A4'){
		if(!in_array($orientation, $this->_orientation)){
			throw new Zest_File_Exception(sprintf('L\'orientation "%s" n\'existe pas.', $orientation));
		}
		if(!in_array($format, $this->_format)){
			throw new Zest_File_Exception(sprintf('Le format "%s" n\'existe pas.', $format));
		}
		
		// rendu du script
		$content = Zest_View::getStaticView()->partial($name, $vars);
		
		// génération du PDF
		$this->_html2Pdf = new HTML2PDF($orientation, $format);
		$this->_html2Pdf->WriteHTML($content);
		
		// écriture du PDF
		$this->_file->putContents($this->_html2Pdf->Output(null, true));
		
		return $this;
	}
	
}