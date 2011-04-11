<?php

/**
 * @see Zend_Loader_Autoloader
 */
require_once 'Zend/Loader/Autoloader.php';

/**
 * @category Zest
 * @package Zest_Loader
 */
class Zest_Loader_Autoloader extends Zend_Loader_Autoloader{
	
	/**
	 * @var array
	 */
	protected $_namespaces = array(
		'Zend_' => true,
		'Zest_' => true,
		'ZendX_' => true
	);
	
	/**
	 * @var string
	 */
	protected static $_includeFileCache;
	
	/**
	 * @return void
	 */
	protected function __construct(){
		spl_autoload_register(array(__CLASS__, 'autoload'));
		$this->_internalAutoloader = array($this, '_autoload');
	}
	
	/**
	 * @return Zest_Loader_Autoloader
	 */
	public static function getInstance(){
		if(is_null(self::$_instance)){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * @param string $class
	 * @return boolean
	 */
	public static function autoload($class){
		$autoload = parent::autoload($class);
		if($autoload && !is_null(self::$_includeFileCache)){
			$reflection = new ReflectionClass($class);
			self::_appendIncFile($reflection->getFileName());
		}
		return $autoload;
	}
	
	/**
	 * @param string $file
	 * @return void
	 */
	public static function setIncludeFileCache($file){
		if(null === $file){
			self::$_includeFileCache = null;
			return;
		}

		if(!file_exists($file) && !file_exists(dirname($file))){
			throw new Zend_Loader_PluginLoader_Exception('Specified file does not exist and/or directory does not exist (' . $file . ')');
		}
		if(file_exists($file) && !is_writable($file)){
			throw new Zend_Loader_PluginLoader_Exception('Specified file is not writeable (' . $file . ')');
		}
		if(!file_exists($file) && file_exists(dirname($file)) && !is_writable(dirname($file))){
			throw new Zend_Loader_PluginLoader_Exception('Specified file is not writeable (' . $file . ')');
		}

		self::$_includeFileCache = $file;
	}
	
	/**
	 * @param string $incFile
	 * @return void
	 */
	protected static function _appendIncFile($incFile){
		if(!file_exists(self::$_includeFileCache)){
			$file = '<?php';
		} else{
			$file = file_get_contents(self::$_includeFileCache);
		}
		if(!strstr($file, $incFile)){
			$file .= "\ninclude_once '$incFile';";
			file_put_contents(self::$_includeFileCache, $file);
		}
	}
	
}