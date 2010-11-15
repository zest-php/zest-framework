<?php

/**
 * @category Zest
 * @package Zest_Dir
 */
class Zest_Dir extends Zest_File_Abstract implements IteratorAggregate{
	
	/**
	 * @var integer
	 */
	const SECURITY_COUNT_DIRECTORY_SEPARATOR = 2;
	
	/**
	 * @param string $pathname
	 * @return Zest_Dir
	 */
	public static function factory($pathname = null){
		return new self($pathname);
	}
	
	/**
	 * @return boolean
	 */
	public function mkdir(){
		if($this->fileExists()){
			$return = true;
		}
		else{
			$return = false;
			if(mkdir($this->getPathname())){
				$return = true;
				$this->chmod('0755');
			}
		}
		return $return;
	}
	
	/**
	 * @return boolean
	 */
	public function recursiveMkdir(){
		$return = true;
		
		$dir = new Zest_Dir();
		
		$array = preg_split('/\/|\\\/', $this->getPathname());
		
		$currentPath = '';
		for($i=0; $i<self::SECURITY_COUNT_DIRECTORY_SEPARATOR; $i++){
			$currentPath .= array_shift($array).'/';
		}
		
		do{
			$currentPath .= array_shift($array).'/';
			$return = $dir->setPathname($currentPath)->mkdir();
			if(!$return) break;
		}while($array);
		
		return $return;
	}
	
	/**
	 * @return boolean
	 */
	public function rmdir(){
		if(!$this->isWritable()) return;
		return rmdir($this->getPathname());
	}
	
	/**
	 * @return boolean
	 */
	public function recursiveRmdir(){
		if(!$this->isWritable()) return;
		
		$return = true;
		
		foreach($this as $res){
			if($res instanceof Zest_File){
				$return = $res->unlink();
			}
			else if($res instanceof Zest_Dir){
				$return = $res->recursiveRmdir();
			}
			if(!$return) break;
		}
		unset($res);
		
		return $return && $this->rmdir();
	}
	
	/**
	 * @param string $pathname
	 * @return Zest_Dir
	 */
	public function setPathname($pathname){
		parent::setPathname($pathname);
		
		$this->_pathname = rtrim($this->_pathname, '/');
		
		// sécurité
		if($this->_pathname && substr_count($this->_pathname, '/') < self::SECURITY_COUNT_DIRECTORY_SEPARATOR){
			$this->_throwSecurityException();
		}
		
		return $this;
	}
	
	/**
	 * @return void
	 */
	protected function _throwSecurityException(){
		throw new Zest_File_Exception(sprintf('Pour des raisons de sécurité, le chemin "%s" doit comporté au moins 2 caractères "%s"', $this->_pathname, '/'));
	}
	
	/**
	 * @param Zest_Dir|string $dest
	 * @param string $copyMode
	 * @return boolean
	 */
	public function copy($dest, $copyMode = self::COPY_OVER){
		if(!$this->isReadable()) return;
	
		if(is_string($dest)){
			$dest = new Zest_Dir($dest);
		}
		
		switch($copyMode){
			case self::COPY_ALTERNATIVE:
				$dest->setPathname($dest->getPathnameAlternative());
				break;
			case self::COPY_OVER:
				$dest->recursiveRmdir();
				break;
		}
	
		$dest->setPathname($dest->getPathname().'/'.$this->getBasename());
		$dest->recursiveMkdir();
		
		$return = true;
		
		foreach($this as $res){
			if($res instanceof Zest_File){
				$return = $res->copy($dest->getPathname().'/'.$res->getBasename());
			}
			else if($res instanceof Zest_Dir){
				$return = $res->copy($dest->getPathname(), null);
			}
			if(!$return) break;
		}
		unset($res);
		
		return $return;
	}
	
	/**
	 * @return array
	 */
	public function glob($pattern, $flags = 0){
		$paths = array();
		$glob = glob($this->getPathname().'/'.$pattern, $flags);
		foreach($glob as $key => $pathname){
			if(is_dir($pathname)){
				$paths[] = new Zest_Dir($pathname);
			}
			else{
				$paths[] = new Zest_File($pathname);
			}
		}
		return $paths;
	}
	
	/**
	 * @return array
	 */
	public function recursiveGlob($pattern, $flags = 0){
		$paths = $this->glob($pattern, $flags);
		
		$dirs = $this->glob('*', GLOB_ONLYDIR);
		foreach($dirs as $dir){
			$paths = array_merge($paths, $dir->recursiveGlob($pattern, $flags));
		}

		return $paths;
	}
	
	/**
	 * @return array
	 */
	public function getChildren(){
		return $this->glob('*');
	}
	
	/**
	 * @return array
	 */
	public function recursiveGetChildren(){
		return $this->recursiveGlob('*');
	}
	
	/**
	 * @return ArrayIterator
	 */
	public function getIterator(){
		return new ArrayIterator($this->getChildren());
	}
	
	/**
	 * @param integer $lifetime
	 * @param integer $freq
	 * @param integer $pattern
	 * @return Zest_Dir
	 */
	public function cleanGarbage($lifetime, $freq = null, $pattern = null){
		if(is_null($freq)){
			$freq = 10;
		}
		if(is_null($pattern)){
			$pattern = '*';
		}
		
		if(mt_rand(1, $freq) == 1){
			$expire = time() - $lifetime;
			
			$files = $this->glob($pattern);
			foreach($files as $file){
				if($file instanceof Zest_File && $file->getMTime() < $expire){
					$file->unlink();
				}
			}
		}
		
		return $this;
	}
	
}