<?php

/**
 * @see http://fr.php.net/manual/fr/class.splfileinfo.php
 * @category Zest
 * @package Zest_File
 */
abstract class Zest_File_Abstract{
	
	/**
	 * @var string
	 */
	const RENAME_ALTERNATIVE = 'rename_alternative';
	
	/**
	 * @var string
	 */
	const RENAME_OVER = 'rename_over';
	
	/**
	 * @var string
	 */
	const COPY_ALTERNATIVE = 'copy_alternative';
	
	/**
	 * @var string
	 */
	const COPY_OVER = 'copy_over';
	
	/**
	 * @var string
	 */
	protected static $_pathnameAlternativeFormat = '%s_%d';
	
	/**
	 * @var string
	 */
	protected $_pathname = null;
	
	/**
	 * @param string $pathname
	 * @return void
	 */
	public function __construct($pathname = null){
		$this->setPathname($pathname);
	}
	
	/**
	 * @return string
	 */
	public function getPathname(){
		return $this->_pathname;
	}
	
	/**
	 * @param string $pathname
	 * @return Zest_File_Abstract
	 */
	public function setPathname($pathname){
		$pathname = str_replace(DIRECTORY_SEPARATOR, '/', $pathname);
		$this->_pathname = $pathname;
		return $this;
	}
	
	/**
	 * @param string $suffix
	 * @return string
	 */
	public function getBasename($suffix = null){
		return basename($this->getPathname(), $suffix);
	}
	
	/**
	 * @return string
	 */
	public function getPath(){
		return $this->getDirname();
	}
	
	/**
	 * @return string
	 */
	public function getDirname(){
		return dirname($this->getPathname());
	}
	
	/**
	 * @return boolean
	 */
	public function isReadable(){
		return is_readable($this->getPathname());
	}
	
	/**
	 * @return boolean
	 */
	public function isExecutable(){
		return is_executable($this->getPathname());
	}
	
	/**
	 * @return boolean
	 */
	public function isWritable(){
		return is_writable($this->getPathname());
	}
	
	/**
	 * @return boolean
	 */
	public function fileExists(){
		return file_exists($this->getPathname());
	}
	
	/**
	 * @return boolean
	 */
	public function isFile(){
		return is_file($this->getPathname());
	}
	
	/**
	 * @return boolean
	 */
	public function isDir(){
		return is_dir($this->getPathname());
	}
	
	/**
	 * @return integer
	 */
	public function getMTime(){
		if(!$this->isReadable()) return;
		return filemtime($this->getPathname());
	}
	
	/**
	 * @return boolean
	 */
	public function recursiveMkdirDirname(){
		return Zest_Dir::factory($this->getDirname())->recursiveMkdir();
	}
	
	/**
	 * @param string $format
	 * @return void
	 */
	public static function setPathnameAlternativeFormat($format){
		self::$_pathnameAlternativeFormat = $format;
	}
	
	/**
	 * @return string
	 */
	public function getPathnameAlternative(){
		$pathname = $this->getPathname();
		$file = new $this($pathname);
		
		$incr = 0;
		
		if($file instanceof Zest_File){
			$dirname = $this->getDirname();
			$basename = $this->getBasename('.'.$this->getExtension());
			while($file->setPathname($pathname)->fileExists()){
				$pathname = $dirname.'/'.sprintf(self::$_pathnameAlternativeFormat, $basename, ++$incr).'.'.$this->getExtension();
			}
		}
		else if($file instanceof Zest_Dir){
			while($file->setPathname($pathname)->fileExists()){
				$pathname = $this->getPathname().'_'.++$incr;
			}
		}
		
		return $pathname;
	}
	
	/**
	 * @param string $newname
	 * @param string $renameMode
	 * @return boolean
	 */
	public function rename($newname, $renameMode = self::RENAME_OVER){
		if(!$this->isWritable()) return;
		
		switch($renameMode){
			case self::RENAME_ALTERNATIVE:
				$pathname = $this->getPathname();
				$this->setPathname($newname);
				$newname = $this->getPathnameAlternative();
				$this->setPathname($pathname);
				break;
			case self::RENAME_OVER:
				if($this instanceof Zest_File){
					Zest_File::factory($newname)->unlink();
				}
				else if($this instanceof Zest_Dir){
					Zest_Dir::factory($newname)->recursiveRmdir();
				}
				break;
		}
		
		Zest_Dir::factory($newname)->recursiveMkdirDirname();
		
		if(rename($this->getPathname(), $newname)){
			$this->setPathname($newname);
			return true;
		}
		return false;
	}
	
	/**
	 * @param string|integer $mode
	 * @return boolean
	 */
	public function chmod($mode){
		if(!$this->isWritable()) return;
		
		/*
		 * 0[owner][group][public]
		 * none : 0
		 * read : +4
		 * write : +2
		 * execute : +1
		 */
		return chmod($this->getPathname(), intval($mode, 8));
	}
	
}