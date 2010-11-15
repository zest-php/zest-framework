<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage Helper
 */
class Zest_File_Helper_Csv extends Zest_File_Helper_Abstract{

	/**
	 * @var boolean
	 */
	protected $_hasHeader = true;
	
	/**
	 * @var array
	 */
	protected $_header = null;
	
	/**
	 * @var string
	 */
	protected $_delimiter = ';';
	
	/**
	 * @var string
	 */
	protected $_enclosure = '"';
	
	/**
	 * @var resource
	 */
	protected $_handle = null;
	
	/**
	 * @return array
	 */
	public function getHeader(){
		if(!$this->_hasHeader){
			return array();
		}
		if(!is_array($this->_header)){
			$this->_rewind();
			$header = $this->_fgetcsv();
			if($header === false){
				return array();
			}
			$this->setHeader($header);
		}
		return $this->_header;
	}

	/**
	 * @param boolean $hasHeader
	 * @return Zest_File_Helper_Csv
	 */
	public function setHasHeader($hasHeader = true){
		$this->_hasHeader = $hasHeader;
		return $this;
	}

	/**
	 * @param array $header
	 * @return Zest_File_Helper_Csv
	 */
	public function setHeader(array $header){
		$header = array_map('trim', $header);
		$header = array_map('strtolower', $header);
		$this->_header = $header;
		$this->setHasHeader(true);
		return $this;
	}
	
	/**
	 * @param string $delimiter
	 * @return Zest_File_Helper_Csv
	 */
	public function setDelimiter($delimiter){
		$this->_delimiter = $delimiter;
		return $this;
	}
	
	/**
	 * @return void
	 */
	protected function _open(){
		if(!$this->_handle){
			$this->_handle = fopen($this->_file->getPathname(), 'a+');
		}
	}
	
	/**
	 * @return void
	 */
	protected function _rewind(){
		$this->_open();
		rewind($this->_handle);
	}
	
	/**
	 * @return array|null
	 */
	protected function _fgetcsv(){
		$this->_open();
		return fgetcsv($this->_handle, null, $this->_delimiter, $this->_enclosure);
	}
	
	/**
	 * @return integer|false
	 */
	protected function _fputcsv(array $fields){
		$this->_open();
		return fputcsv($this->_handle, $fields, $this->_delimiter, $this->_enclosure);
	}
	
	/**
	 * @return array
	 */
	public function fgetcsv(){
		$header = $this->getHeader();

		$return = array();
		while($line = $this->_fgetcsv()){
			$array = array();
			foreach($line as $col => $value){
				if(isset($header[$col])){
					$col = $header[$col];
				}
				$array[$col] = $value;
			}
			$return[] = $array;
		}
		return $return;
	}

	/**
	 * @param array $lines
	 * @param integer $append
	 * @return Zest_File_Helper_Csv
	 */
	public function fputcsv(array $lines, $append = true){
		// $lines doit être un tableau de tableaux
		foreach($lines as $line){
			if(!is_array($line)){
				$lines = array($lines);
				break;
			}
		}

		$header = $this->getHeader();
		
		if($append){
			$contents = $this->_file->getContents();
			if(urlencode(substr($contents, -1)) != '%0A'){
				$this->_file->appendContents(PHP_EOL);
			}
		}
		else{
			$this->_file->putContents('');
			if($this->_hasHeader){
				$this->_fputcsv($header);
			}
		}

		foreach($lines as $line){
			foreach($header as $headerColId => $headerColName){
				if(isset($line[$headerColName])){
					$line[$headerColId] = $line[$headerColName];
					unset($line[$headerColName]);
				}
				else if(isset($line[$headerColId])){
					continue;
				}
				else{
					$line[$headerColId] = null;
				}
			}
			ksort($line);
			$this->_fputcsv($line);
		}
		
		return $this;
	}

}