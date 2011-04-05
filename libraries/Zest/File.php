<?php

/**
 * @category Zest
 * @package Zest_File
 */
class Zest_File extends Zest_File_Abstract{
	
	/**
	 * @var array
	 */
	protected $_helpers = array();
	
	/**
	 * @var array
	 */
	protected $_defaultHelpersInit = false;
	
	/**
	 * @var Zend_Loader_PluginLoader
	 */
	protected static $_pluginLoader = null;
	
	/**
	 * @param string $pathname
	 * @return Zest_File
	 */
	public static function factory($pathname = null){
		return new self($pathname);
	}
	
	/**
	 * @return integer
	 */
	public function getSize(){
		if(!$this->isReadable()) return;
		return filesize($this->getPathname());
	}
	
	/**
	 * @return string
	 */
	public function getExtension(){
		return strtolower(pathinfo($this->getPathname(), PATHINFO_EXTENSION));
	}
	
	/**
	 * The Internet Corporation for Assigned Names and Numbers : http://www.iana.org/assignments/media-types/index.html
	 * @return string
	 */
	public function getMimeType($getReal = false){
		if($getReal){
			// pour utiliser fileinfo, il faut que le fichier existe réellement
			if(!$this->isReadable()) return;
			
			if(!extension_loaded('fileinfo')){
				throw new Zest_File_Exception('L\'extension PHP "fileinfo" n\'est pas chargée.');
			}
			$finfo = finfo_open(FILEINFO_MIME);
			$mime = finfo_file($finfo, $this->getPathname());
			list($mime, $charset) = explode('; ', $mime);
			return $mime;
		}
		
		return Zest_File_MimeType::getMimeType($this->getPathname());
	}
	
	/**
	 * @param string $pathname
	 * @return Zest_File
	 */
	public function setPathname($pathname){
		$oldPathname = $this->getPathname();
		parent::setPathname($pathname);
		if($pathname != $oldPathname){
			$this->_helpers = array();
			$this->_defaultHelpersInit = false;
		}
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getContents(){
		if(!$this->isReadable()) return;
		
		$contents = null;
		
		$handle = fopen($this->getPathname(), 'rb');
		if($handle && $this->_waitLock($handle, LOCK_SH)){
			$contents = stream_get_contents($handle);
			flock($handle, LOCK_UN);
			fclose($handle);
		}
		
		return $contents;
	}
	
	/**
	 * @param integer $length
	 * @return void
	 */
	protected function _streamContents($length = null){
		if(!$this->isReadable()) return;
		
		if(!$length){
			$length = 1 * (1024*1024); // 1 ko
		}
		
		$handle = fopen($this->getPathname(), 'rb');
		if($handle && $this->_waitLock($handle, LOCK_SH)){
			clearstatcache();
			while(!feof($handle)){
				echo fread($handle, $length);
//				ob_flush();
//				flush();
			}
			flock($handle, LOCK_UN);
			fclose($handle);
		}
	}
	
	/**
	 * @param resource $handle
	 * @param integer $timeLimit (par défaut 50ms)
	 * @return boolean
	 */
	protected function _waitLock($handle, $operation, $timeLimit = 0.05){
		$timeStart = microtime(true);
		
		while(true){
			if(flock($handle, $operation)){
				return true;
			}
			if(microtime(true) - $timeStart > $timeLimit){
				break;
			}
			usleep(round(rand(1, 9) * 1000));	// de 1 à 9 ms
		}
		
		return false;
	}
	
	/**
	 * @param string $str
	 * @param string $mode
	 * @return boolean
	 */
	protected function _write($str, $mode){
		if($this->fileExists() && !$this->isWritable()) return false;
		
		$handle = fopen($this->getPathname(), $mode);
		
		$this->recursiveMkdirDirname();
		
		$result = false;
		
		$handle = fopen($this->getPathname(), $mode);
		if($handle && $this->_waitLock($handle, LOCK_EX)){
			$result = fwrite($handle, $str);
			flock($handle, LOCK_UN);
			fclose($handle);
		}
		$this->chmod('0755');
		
		// fwrite retourne le nombre d'octets écris ou false
		return $result !== false;
	}
	
	/**
	 * @param string $str
	 * @return boolean
	 */
	public function putContents($str){
		return $this->_write($str, 'w');
	}
	
	/**
	 * @param string $str
	 * @return boolean
	 */
	public function appendContents($str){
		return $this->_write($str, 'a');
	}
	
	/**
	 * @return boolean
	 */
	public function touch(){
		if($this->fileExists()) return;
		
		$return = false;
		if($this->recursiveMkdirDirname()){
			$return = touch($this->getPathname());
		}
		return $return;
	}
	
	/**
	 * @param Zest_File|string $dest
	 * @param string $copyMode
	 * @return boolean
	 */
	public function copy($dest, $copyMode = self::COPY_OVER){
		if(!$this->isReadable()) return;
		
		if(is_string($dest)){
			$dest = new Zest_File($dest);
		}
		
		switch($copyMode){
			case self::COPY_ALTERNATIVE:
				$dest->setPathname($dest->getPathnameAlternative());
				break;
			case self::COPY_OVER:
				$dest->unlink();
				break;
		}
		
		$dest->recursiveMkdirDirname();
		return copy($this->getPathname(), $dest->getPathname());
	}
	
	/**
	 * @return boolean
	 */
	public function unlink(){
		if(!$this->isWritable()) return;
		return unlink($this->getPathname());
	}
	
	/**
	 * @param array $options
	 * @param Zend_Controller_Request_Http $request
	 * @return void
	 */
	public function send(array $options = array(), Zend_Controller_Request_Http $request = null){
		if(!$this->fileExists()){
			throw new Zest_File_Exception(sprintf('Le fichier "%s" n\'existe pas.', $this->getBasename()));
		}
		if(!$this->isReadable()){
			throw new Zest_File_Exception(sprintf('Le fichier "%s" ne peut pas être lu.', $this->getBasename()));
		}
		
		if(is_null($request)){
			$request = Zest_Controller_Front::getInstance()->getRequest();
		}
		
		$options = array_change_key_case($options, CASE_LOWER);
		
		$file = null;
		$stream = false;
		
		$this->_initDefaultHelpers();
		foreach($this->_helpers as $helper){
			if($helper instanceof Zest_File_Helper_Abstract_Convertable){
				$file = $helper->getConvertedFile($options);
				$stream = (boolean) $file;
				break;
			}
		}
		
		if(!$file){
			$file = $this;
		}
		
		session_cache_limiter('must-revalidate');
		header('Content-Length: '.$file->getSize());
		
		if(!isset($options['header']) || $options['header']){
			// filename
			$filename = $this->getBasename();
			if(isset($options['downloadfilename'])){
				$filename = $options['downloadfilename'];
			}
			
			// extension
			$filename = basename($filename, '.'.$this->getExtension());
			$filename = basename($filename, '.'.$file->getExtension());
			$filename .=  '.'.$file->getExtension();
			
			if(!empty($options['forcedownload'])){
				header('Content-Disposition: attachment; filename='.$filename);
				header('Content-Type: application/force-download');
				header('Content-Transfer-Encoding: '.$file->getMimeType());
			}
			else{
				header('Content-Type: '.$file->getMimeType());
			}
			
			header('Content-Description: file transfer');
			header('Cache-Control: public');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Expires: 0');
		
			// status 304
			if(!isset($options['304']) || $options['304']){
				$mtime = $this->getMTime();
				header('Last-Modified: ' . date('r', $mtime));
				$if_modified_since = $request->getHeader('if-modified-since');
				if($if_modified_since){
					$if_modified_since = strtotime($if_modified_since);
					if(!($if_modified_since < $mtime)){
						header('HTTP/1.1 304 Not Modified');
						exit;
					}
				}
			}
		}
		
		if($stream){
			$file->_streamContents();
		}
		else{
			echo $file->getContents();
		}
		
		exit;
	}
	
	/**
	 * @param string $name
	 * @param string $renameMode
	 * @return boolean
	 */
	public function receive($name, $renameMode = self::RENAME_OVER){
		if(!$this->getPathname()){
			throw new Zest_File_Exception('Le chemin est vide.');
		}
		
		$overwrite = false;
		switch($renameMode){
			case self::RENAME_ALTERNATIVE:
				$this->setPathname($this->getPathnameAlternative());
				break;
			case self::RENAME_OVER:
				$overwrite = true;
				break;
		}
		
		$this->recursiveMkdirDirname();
		
		$transfer = new Zend_File_Transfer_Adapter_Http();
		$transfer->addFilter('rename', array('overwrite' => $overwrite, 'target' => $this->getPathname()));
		$transfer->receive($name);
		return $transfer->isReceived();
	}
	
	/**
	 * @return Zend_Loader_PluginLoader
	 */
	public static function getPluginLoader(){
		if(!self::$_pluginLoader){
			self::$_pluginLoader = new Zend_Loader_PluginLoader(array(
				'Zest_File_Helper' => 'Zest/File/Helper'
			));
		}
		return self::$_pluginLoader;
	}
	
	/**
	 * @param Zend_Loader_PluginLoader $loader
	 * @return void
	 */
	public static function setPluginLoader(Zend_Loader_PluginLoader $loader){
		self::$_pluginLoader = $loader;
	}
	
	/**
	 * @param string $helper
	 * @param boolean $throwExceptions
	 * @return Zest_File_Helper_Abstract
	 */
	public function getHelper($helper, $throwExceptions = true){
		$this->_initDefaultHelpers();
		
		$helper = strtolower($helper);
		if(isset($this->_helpers[$helper])){
			return $this->_helpers[$helper];
		}
		if($throwExceptions){
			throw new Zest_File_Exception(sprintf('Le helper "%s" n\'existe pas.', $helper));
		}
		return null;
	}
	
	/**
	 * @return array
	 */
	public function getHelpers(){
		$this->_initDefaultHelpers();
		return $this->_helpers;
	}
	
	/**
	 * @param string|Zest_File_Helper_Abstract $helper
	 * @param boolean $throwExceptions
	 * @return Zest_File
	 */
	public function addHelper($helper, $throwExceptions = true){
		if(is_string($helper)){
			$className = $helper;
			if(!@class_exists($className)){
				$className = self::getPluginLoader()->load($helper, false);
			}
			if($className){
				$helper = new $className($this);
			}
		}
		if($helper instanceof Zest_File_Helper_Abstract){
			$suffix = strtolower(substr(strrchr(get_class($helper), '_'), 1));
			if(!isset($this->_helpers[$suffix])){
				$this->_helpers[$suffix] = $helper;
			}
		}
		else if($throwExceptions){
			throw new Zest_File_Exception('Le helper doit hériter de Zest_File_Helper_Abstract.');
		}
		return $this;
	}
	
	/**
	 * @return voir
	 */
	protected function _initDefaultHelpers(){
		if(!$this->_defaultHelpersInit){
			$this->_defaultHelpersInit = true;
			
			// helpers par défaut
			list($type1, $type2) = explode('/', $this->getMimeType());
			$this->addHelper($this->getExtension(), false);
			$this->addHelper($type2, false);
			$this->addHelper($type1, false);
		}
	}
	
	/**
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 */
	public function __call($method, $args){
		$this->_initDefaultHelpers();
		
		// permet de charger un helper spécifique (exemple : $this->url()->getUrl())
		$helper = $this->getHelper($method, false);
		if(!$helper){
			$this->addHelper($method, false);
			$helper = $this->getHelper($method, false);
		}
		if($helper){
			return $helper;
		}
		
		// utile pour les méthodes isImage, isAudio et isVideo (mais fonctionne d'autres également)
		if(substr($method, 0, 2) == 'is'){
			$type = preg_replace('/([A-Z])/', '-\\1', substr($method, 2));
			$type = strtolower(trim($type, '-'));
			return (boolean) preg_match('/'.$type.'\//', $this->getMimeType());
		}
		
		// récupération des helpers
		foreach($this->_helpers as $helper){
			if(method_exists($helper, $method) || method_exists($helper, '__call')){
				try{
					$return = call_user_func_array(array($helper, $method), $args);
					if($return === $helper){
						return $this;
					}
					return $return;
				}
				catch(Zest_File_Exception $e){
					// 1 : la méthode n'existe pas sur le helper
					if($e->getCode() === 1){
						continue;
					}
					throw $e;
				}
				catch(Exception $e){
					throw $e;
				}
			}
		}
		throw new Zest_File_Exception(sprintf('La méthode "%s" n\'existe pas pour les fichiers de type "%s".', $method, $this->getMimeType()));
	}
	
	/**
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name){
		$this->_initDefaultHelpers();
		
		foreach($this->_helpers as $helper){
			if(method_exists($helper, '__get') || ($helper && isset($helper->$name))){
				return $helper->$name;
			}
		}
		throw new Zest_File_Exception(sprintf('La propriété "%s" n\'existe pas pour les fichiers de type "%s".', $name, $this->getMimeType()));
	}
	
	/**
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function __set($name, $value){
		$this->_initDefaultHelpers();
		
		foreach($this->_helpers as $helper){
			if(method_exists($helper, '__set') || ($helper && isset($helper->$name))){
				$helper->$name = $value;
				return;
			}
		}
		throw new Zest_File_Exception(sprintf('La propriété "%s" n\'existe pas pour les fichiers de type "%s".', $name, $this->getMimeType()));
	}
	
	/**
	 * @return stdClass
	 */
	public function toStdClass(){
		return (object) array(
			'pathname' => $this->getPathname(),
			'basename' => $this->getBasename(),
			'extension' => $this->getExtension(),
			'mimetype' => $this->getMimeType(),
			'size' => $this->getSize(),
			'exists' => $this->isReadable()
		);
	}
	
}