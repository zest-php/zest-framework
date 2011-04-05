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
	 * @return ZipArchive
	 */
	protected function _getZipArchive(){
		if(is_null($this->_archive)){
			if(!extension_loaded('zip')){
				throw new Zest_File_Exception('L\'extension PHP "zip" n\'est pas chargée.');
			}
			$this->_archive = new ZipArchive();
		}
		return $this->_archive;
	}
	
	/**
	 * @param integer $mode (ZipArchive::OVERWRITE, ZipArchive::CREATE, ZipArchive::EXCL, ZipArchive::CHECKCONS)
	 * @return Zest_File_Helper_Zip
	 */
	public function open($mode = ZIPARCHIVE::CHECKCONS){
		if($this->_opened !== true){
			$this->_opened = $this->_getZipArchive()->open($this->_file->getPathname(), $mode);
		}
		return $this;
	}
	
	/**
	 * @return void
	 */
	public function close(){
		if($this->_opened === true){
			if($this->_getZipArchive()->close()){
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
		return $this->_call($this->_getZipArchive(), $method, $args);
	}
	
}