<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage Helper
 */
class Zest_File_Helper_Zip extends Zest_File_Helper_Abstract{
	
	/**
	 * @var ZipArchive
	 */
	protected $_archive = null;
	
	/**
	 * @var boolean
	 */
	protected $_opened = false;
	
	/**
	 * @param Zest_File $file
	 * @return void
	 */
	public function __construct(Zest_File $file){
		parent::__construct($file);
		
		if(!extension_loaded('zip')){
			throw new Zest_File_Exception('L\'extension PHP "zip" n\'est pas chargée.');
		}
		
		$this->_archive = new ZipArchive();
	}
	
	/**
	 * @param integer $mode (ZipArchive::OVERWRITE, ZipArchive::CREATE, ZipArchive::EXCL, ZipArchive::CHECKCONS)
	 * @return Zest_File_Helper_Zip
	 */
	public function open($mode){
		if($this->_opened !== true){
			$this->_opened = $this->_archive->open($this->_file->getPathname(), $mode);
		}
		return $this;
	}
	
	/**
	 * @return void
	 */
	public function close(){
		if($this->_opened === true){
			if($this->_archive->close()){
				$this->_opened = false;
			}
		}
		return $this;
	}
	
	/**
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 */
	public function __call($method, $args){
		$this->open(ZipArchive::CREATE);
		return $this->_call($this->_archive, $method, $args);
	}
	
}