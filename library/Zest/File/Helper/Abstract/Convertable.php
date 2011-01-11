<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage Helper
 */
abstract class Zest_File_Helper_Abstract_Convertable extends Zest_File_Helper_Abstract{
	
	/**
	 * @var string
	 */
	protected static $_defaultCacheDir = null;
	
	/**
	 * @var string
	 */
	protected $_cacheDir = null;
	
	/**
	 * @var integer
	 */
	protected static $_gcLifetime = 600;
	
	/**
	 * @var integer
	 */
	protected static $_gcFreq = 10;
	
	/**
	 * @return string
	 */
	public static function getDefaultCacheDir(){
		return self::$_defaultCacheDir;
	}
	
	/**
	 * @param string $dir
	 * @return void
	 */
	public static function setDefaultCacheDir($dir){
		self::$_defaultCacheDir = $dir;
	}
	
	/**
	 * @return string
	 */
	public function getCacheDir(){
		if(!$this->_cacheDir){
			$this->_cacheDir = self::getDefaultCacheDir();
		}
		return $this->_cacheDir;
	}
	
	/**
	 * @param string $dir
	 * @return Zest_File_Helper_Abstract_Convertable
	 */
	public function setCacheDir($dir){
		$this->_cacheDir = $dir;
		return $this;
	}
	
	/**
	 * @param array $options
	 * @return boolean
	 */
	public function canBeConverted(array $options){
		return true;
	}
	
	/**
	 * @param array $options
	 * @return Zest_File|null
	 */
	public function getConvertedFile(array $options){
		// uniformisation du tableau d'options pour la construction de l'identifiant de cache
		$options = array_change_key_case($options, CASE_LOWER);
		ksort($options);
		
		$file = null;
		
		$cacheDir = $this->getCacheDir();
		$cachable = $cacheDir && (!isset($options['cache']) || $options['cache']);
		
		$canBeConverted = $this->canBeConverted($options);
		
		$extension = $this->_file->getExtension();
		if(isset($options['extension']) && $canBeConverted){
			$extension = $options['extension'];
		}
		
		if($cachable){
			$cache_id = hash('md5', $this->_file->getPathname().$this->_file->getMTime().serialize($options));
			$file = new Zest_File($cacheDir.'/'.$this->_file->getBasename('.'.$this->_file->getExtension()).'_'.$cache_id.'.'.$extension);
		
			// nettoyage du cache
			Zest_Dir::factory($cacheDir)->cleanGarbage(self::$_gcLifetime, self::$_gcFreq);
		}
		else{
			// s'il n'y a pas de cache, utilisation du dossier temporaire
			$file = new Zest_File(sys_get_temp_dir().'/'.$this->_file->getBasename($this->_file->getExtension()).$extension);
		}
		
		// conversion
		if(!$cachable || !$file->isReadable()){
			if($options && $canBeConverted){
				$this->convertTo($file, $options);
			}
			else{
				$this->_file->copy($file);
			}
		}
		
		// si le fichier n'est pas lisible ou dont la taille est null
		if(!$file->isReadable() || !$file->getSize()){
			$file = null;
		}
		
		return $file;
	}
	
	/**
	 * @param integer $width
	 * @param integer $height
	 * @param array $options
	 * @return array
	 */
	protected function _getSize($width, $height, array $options){
		$options = array_change_key_case($options, CASE_LOWER);
		
		if( (isset($options['max']) || isset($options['min'])) && isset($options['mode'])){
			throw new Zest_File_Exception('Si les options "min" ou "max" sont renseignées, l\'option "mode" est inutile.');
		}
	
		if(isset($options['min'])){
			$options['mode'] = 'min';
			if(!isset($options['width'])){
				$options['width'] = $options['min'];
			}
			if(!isset($options['height'])){
				$options['height'] = $options['min'];
			}
		}
		if(isset($options['max'])){
			$options['mode'] = 'max';
			if(!isset($options['width'])){
				$options['width'] = $options['max'];
			}
			if(!isset($options['height'])){
				$options['height'] = $options['max'];
			}
		}
		
		// choix du mode
		$mode = isset($options['mode']) && $options['mode']=='min' ? 'min' : 'max';
		
		// forcé le redimensionnement
		$forceResize = !empty($options['forceresize']);
		
		if(isset($options['width']) || isset($options['height'])){
			
			// gestion du max
			if($mode == 'max'){
				$needResize = (isset($options['width']) && $width > $options['width']) || (isset($options['height']) && $height > $options['height']);
			}
			
			// gestion du min
			if($mode == 'min'){
				$needResize = (!isset($options['width']) || $width > $options['width']) && (!isset($options['height']) || $height > $options['height']);
			}
			
			if($forceResize || $needResize){
				$propWidth = $propHeight = null;
				if(isset($options['width'])){
					$propWidth = $options['width']/$width;
				}
				if(isset($options['height'])){
					$propHeight = $options['height']/$height;
				}
				
				// gestion du max
				if($mode == 'max'){
					if($propWidth && $propHeight){
						$prop = min($propWidth, $propHeight);
					}
					else{
						$prop = max($propWidth, $propHeight);
					}
				}
				
				// gestion du min
				if($mode == 'min'){
					$prop = max($propWidth, $propHeight);
				}
				
				$width *= $prop;
				$height *= $prop;
			}
		}
		
		return array(round($width), round($height));
	}
	
	/**
	 * @param array $options
	 * @return Zest_File_Helper_Abstract_Convertable
	 */
	public function convert(array $options){
		$this->convertTo($this->_file, $options);
		return $this;
	}
	
	/**
	 * @param Zest_File|string $file
	 * @param array $options
	 * @return Zest_File_Helper_Abstract_Convertable
	 */
	public function convertTo($file, array $options = array()){
		if(is_string($file)){
			$file = new Zest_File($file);
		}
		if(!$file instanceof Zest_File){
			throw new Zest_File_Exception('Le fichier doit hériter de Zest_File.');
		}
		if($this->canBeConverted($options)){
			$this->_convertTo($file, $options);
		}
		else{
			throw new Zest_File_Exception('Le fichier ne peut pas être converti.');
		}
		return $this;
	}
	
	/**
	 * @param Zest_File $file
	 * @param array $options
	 * @return Zest_File_Helper_Abstract_Convertable
	 */
	abstract protected function _convertTo(Zest_File $file, array $options);
	
}