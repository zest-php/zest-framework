<?php

/**
 * @category Zest
 * @package Zest_Acl
 * @subpackage Adapter
 */
class Zest_Acl_Adapter_File extends Zest_Acl_Adapter_Abstract{

	/**
	 * @var string
	 */
	protected static $_defaultCacheFile = null;
	
	/**
	 * @var string
	 */
	protected $_cacheFile = null;
	
	/**
	 * @return string
	 */
	public static function getDefaultCacheFile(){
		return self::$_defaultCacheFile;
	}
	
	/**
	 * @param string $file
	 * @return Zest_Acl_Adapter_File
	 */
	public static function setDefaultCacheFile($file){
		self::$_defaultCacheFile = $file;
	}
	
	/**
	 * @return string
	 */
	public function getCacheFile($throwExceptions = false){
		if(!$this->_cacheFile){
			$this->_cacheFile = self::getDefaultCacheFile();
		}
		if(!$this->_cacheFile && $throwExceptions){
			throw new Zest_Acl_Exception('Aucun chemin renseigné.');
		}
		return $this->_cacheFile;
	}
	
	/**
	 * @param string $file
	 * @return Zest_Acl_Adapter_File
	 */
	public function setCacheFile($file){
		$this->_cacheFile = $file;
		return $this;
	}
	
	/**
	 * @param Zend_Acl_Role_Registry $roleRegistry
	 * @param array $resources
	 * @param array $rules
	 * @return Zest_Acl_Adapter_File
	 */
	public function save(Zend_Acl_Role_Registry $roleRegistry, array $resources, array $rules){
		$content = array(
			'roleRegistry' => $roleRegistry,
			'resources' => $resources,
			'rules' => $rules,
		);
		
		$file = Zest_File::factory($this->getCacheFile(true));
		$file->putContents(serialize($content));
		
		return $this;
	}
	
	/**
	 * @return Zest_Acl_Adapter_File
	 */
	public function load(){
		$file = Zest_File::factory($this->getCacheFile(true));
		if($file->isReadable()){
			$content = @unserialize($file->getContents());
			if($content !== false && is_array($content)){
				$this->_acl->init($content['roleRegistry'], $content['resources'], $content['rules']);
			}
		}
		
		return $this;
	}
	
}