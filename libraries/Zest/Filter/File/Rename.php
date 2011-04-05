<?php

/**
 * @category Zest
 * @package Zest_Filter
 * @subpackage File
 */
class Zest_Filter_File_Rename extends Zend_Filter_File_Rename{

	/**
	 * @param string|array|Zend_Config $options
	 * @return void
	 */
	public function __construct($options = null){
		if(is_null($options)){
			$options = array();
		}
		parent::__construct($options);
	}
	
	/**
	 * @param string $value
	 * @param boolean $source
	 * @return string
	 */
	public function getNewName($value, $source = false){
		try{
			$newName = parent::getNewName($value, $source);
		}
		catch(Zend_Filter_Exception $e){
			$fileArray = $this->_getFileName($value);
			if(!is_array($fileArray)){
				throw $e;
			}
			$file = new Zest_File($fileArray['source']);
			$file->rename($fileArray['target'], Zest_File::RENAME_ALTERNATIVE);
			$newName = $file->getPathname();
		}
		return $newName;
	}

}