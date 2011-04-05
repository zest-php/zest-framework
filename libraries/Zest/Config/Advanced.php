<?php

/**
 * @category Zest
 * @package Zest_Config
 */
class Zest_Config_Advanced{
	
	/**
	 * @var array
	 */
	protected $_data = null;
	
	/**
	 * @var Zest_File
	 */
	protected $_cache = null;
	
	/**
	 * @var array
	 */
	protected $_cacheFiles = array();
	
	/**
	 * @var string
	 */
	protected $_section = null;
	
	/**
	 * @var string
	 */
	protected $_zendConfigClass = null;
	
	/**
	 * @param array $options
	 * @return void
	 */
	public function __construct($filename, array $options = array()){
		$options = array_change_key_case($options, CASE_LOWER);
		
		if(!isset($options['adapter'])){
			$options['adapter'] = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		}
		if(isset($options['adapter'])){
			$this->_zendConfigClass = 'Zend_Config_'.ucfirst($options['adapter']);
			if(!@class_exists($this->_zendConfigClass)){
				throw new Zest_Config_Exception(sprintf('La classe "%s" n\existe pas.', $this->_zendConfigClass));
			}
		}
		else{
			throw new Zest_Config_Exception('Aucun adptateur défini.');
		}
		
		if(isset($options['section'])){
			$this->_section = $options['section'];
		}
		if(isset($options['cache_file']) && $this->_section){
			$this->_cache = new Zest_File(sprintf($options['cache_file'], $this->_section));
			if($this->_cache->getExtension() != 'php'){
				$this->_cache->setPathname($this->_cache->getPathname().'.php');
			}
			unset($options['cache_file']);
		}
		
		$this->_init($filename, $options);
	}
	
	/**
	 * @param string $key
	 * @return array|string
	 */
	public function get($key = null, $throwExceptions = false){
		$return = $this->_get($key);
		if($throwExceptions && is_null($return)){
			throw new Zest_Config_Exception(sprintf('La clef de configuration "%s" n\'existe pas.', $key));
		}
		return $return;
	}
	
	/**
	 * @param string $filename
	 * @return Zest_Config
	 */
	protected function _init($filename){
		if($this->_cache){
			// récupération du fichier en cache s'il existe
			if($this->_cache->isReadable()){
				include $this->_cache->getPathname();
			}
			
			// on vérifie la date de modification de chaque fichier pour savoir si un d'entre eux a été mis à jour
			if(isset($this->_data['_cache_files'])){
				foreach($this->_data['_cache_files'] as $fileInfos){
					list($pathname, $filemtime) = $fileInfos;
					
					if($filemtime){
						// le fichier existait et il a été modifié ou supprimé
						if(!is_readable($pathname) || filemtime($pathname) != $filemtime){
							$this->_data = null;
							break;
						}
					}
					else{
						// le fichier n'existait pas et il a été créé
						if(is_readable($pathname)){
							$this->_data = null;
							break;
						}
					}
				}
			}
		}
		
		if(!$this->_cache || !isset($this->_data['_cache_files'])){
			$this->_data = null;
		}
		
		// si le tableau est vide, on recalcule la configuration
		if(!$this->_data){
			$this->_loadConfigs($filename);
			if($this->_cache && $this->_data){
				$this->_cache->putContents('<?php $this->_data = '.var_export($this->_data, true).';');
			}
		}
		
		return $this;
	}
	
	/**
	 * @param string $filename
	 * @param array $initData
	 * @return array
	 */
	protected function _loadConfigs($filename, $initData = array()){
		$this->_data = $initData;
		
		// initialisation du tableau permettant le recalcul du fichier de cache
		$this->_cacheFiles = array();
		
		// configuration par défaut de l'application
		$this->_loadConfig($filename, null, $this->_data);
			
		// remplacement des variables
		$this->_recursiveReplaceVars($this->_data);
		
		// sauvegarde du tableau permettant le recalcul du fichier de cache
		if($this->_cache){
			$this->_data['_cache_files'] = $this->_cacheFiles;
			$this->_data['_cache_creation'] = time();
		}
	}
	
	/**
	 * @param string $iniFile
	 * @param array $data
	 * @param string $insertKey
	 * @return void
	 */
	protected function _loadConfig($iniFile, $insertKey = null, array &$data = null){
		if(file_exists($iniFile)){
			$this->_cacheFiles[] = array($iniFile, filemtime($iniFile));
		}
		else{
			$this->_cacheFiles[] = array($iniFile, null);
			return;
		}
		
		if(is_null($data)){
			$data =& $this->_data;
		}
		
		$configIni = new $this->_zendConfigClass($iniFile, $this->_section);
		$configData = $configIni->toArray();
		
		// merge des données
		if($insertKey){
			$insertData = (array) $this->_get($insertKey, $data);
			$insertData = $this->_merge($insertData, $configData);
			$this->_set($insertKey, $insertData, $data);
		}
		else{
			$data = $this->_merge($data, $configData);
		}
		
		// récupération des enfants
		$childrenKey = ($insertKey ? $insertKey.'.' : '').'children';
		$children = (array) $this->_get($childrenKey, $data);
		
		// suppression du tableau children
		$parent = $this->_get($insertKey, $data);
		if(isset($parent['children'])){
			unset($parent['children']);
		}
		$this->_set($insertKey, $parent, $data);
		
		// chargement des enfants
		if($children){			
			$this->_recursiveReplaceVars($children, $data);
			
			$children = (array) $children;
			foreach($children as $childIni){
				$this->_loadConfig($childIni, $insertKey, $data);
			}
		}
	}
	
	/**
	 * @param array $data
	 * @param array $dataVarsSource
	 * @return void
	 */
	protected function _recursiveReplaceVars(array &$data = null, array $dataVarsSource = null){
		if(is_null($data)){
			$data =& $this->_data;
		}
		if(is_null($dataVarsSource)){
			$dataVarsSource = $data;
		}
		
		$vars = $this->_parseVars($data);
		do{
			$this->_replaceVars($vars, $data, $dataVarsSource);
			$vars = $this->_parseVars($data);
		}
		while(count($vars));
	}
	
	/**
	 * @param array $vars
	 * @param array $data
	 * @param array $dataVarsSource
	 * @return void
	 */
	protected function _replaceVars(array $vars, array &$data = null, array $dataVarsSource = null){
		if(is_null($data)){
			$data =& $this->_data;
		}
		if(is_null($dataVarsSource)){
			$dataVarsSource = $data;
		}
		
		foreach($vars as $varKey => $keys){
			if(is_null($varValue = $this->_get($varKey, $dataVarsSource))){
				throw new Zest_Config_Exception(sprintf('La variable "%s" n\'existe pas.', $varKey));
			}
			if(!is_string($varValue)){
				throw new Zest_Config_Exception(sprintf('La variable "%s" n\'est pas une chaîne de caractère.', $varKey));
			}
			
			foreach($keys as $key){
				if(preg_match('/^'.preg_quote($varKey, '/').'\./', $key) || strcasecmp($varKey, $key) === 0){
					throw new Zest_Config_Exception(sprintf('Problème de récursivité de la variable "%s".', $key));
				}
				
				$newValue = str_replace('{$'.$varKey.'}', $varValue, $this->_get($key, $data));
				$this->_set($key, $newValue, $data);
			}
		}
	}
	
	/**
	 * @param array $data
	 * @param string $parentKey
	 * @return array
	 */
	protected function _parseVars(array $data = null, $parentKey = null){
		if(is_null($data)){
			$data = $this->_data;
		}
		
		$vars = array();
		foreach($data as $shortKey => $value){
			$key = ($parentKey ? $parentKey.'.' : '').$shortKey;
			
			if(is_array($value)){
				foreach($this->_parseVars($value, $key) as $var => $keys){
					if(!isset($vars[$var])){
						$vars[$var] = array();
					}
					$vars[$var] = array_merge($vars[$var], $keys);
				}
			}
			else{
				preg_match('/{\$([^}]*)}/', (string) $value, $matches);
				if(count($matches) == 2){
					$vars[$matches[1]][] = $key;
				}
				unset($matches);
			}
		}
		return $vars;
	}
	
	/**
	 * @param string $key
	 * @param mixed $value
	 * @param array $data
	 * @return Zest_Config_Advanced
	 */
	protected function _set($key, $value, array &$data = null){
		if(is_null($data)){
			$data =& $this->_data;
		}
		
		if(is_null($key)){
			return $data = $value;
		}
		
		$arrayKey = explode('.', $key);
		$currentKey = array_shift($arrayKey);
//		$currentKeyIsArray = preg_match('/\[\]$/', $currentKey);
		$currentKeyIsArray = substr($currentKey, -2)=='[]';
		
		if($currentKeyIsArray){
//			$currentKey = preg_replace('/\[\]$/', '', $currentKey);
			$currentKey = rtrim($currentKey, '[]');
			if(isset($data[$currentKey])){
				if(!is_array($data[$currentKey])){
					$data[$currentKey] = array($data[$currentKey]);
				}
			}
			else{
				$data[$currentKey] = array();
			}
		}
		
		if(count($arrayKey)){
			if(!isset($data[$currentKey])){
				$data[$currentKey] = array();
			}
			$this->_set(implode('.', $arrayKey), $value, $data[$currentKey]);
		}
		else{
			if($currentKeyIsArray){
				$data[$currentKey][] = $value;
			}
			else{
				$data[$currentKey] = $value;
			}
		}
		
		return $this;
	}
	
	/**
	 * @param string $key
	 * @param array $data
	 * @return Zest_Config_Advanced
	 */
	protected function _unset($key, array &$data = null){
		if(is_null($data)){
			$data =& $this->_data;
		}
		
		if(is_int(strpos($key, '.'))){
			$arrayKey = explode('.', $key);
			$unsetKey = array_pop($arrayKey);
			$getKey = implode('.', $arrayKey);
		}
		else{
			$unsetKey = $key;
			$getKey = null;
		}
		
		$unsetData = $this->_get($getKey, $data);
		if(array_key_exists($unsetKey, $unsetData)){
			unset($unsetData[$unsetKey]);
		}
		$this->_set($getKey, $unsetData, $data);
		
		return $this;
	}
	
	/**
	 * @param string $key
	 * @param array $data
	 * @return string|array
	 */
	protected function _get($key = null, array $data = null){
		if(is_null($data)){
			$data = $this->_data;
		}
		if(is_null($key)){
			return $data;
		}
		
		$return = null;
		
		if(is_int($pos = strpos($key, '.'))){
			
			$explode = explode('.', $key);
			foreach($explode as $index){
				if(is_null($return)){
					$return = $this->_get($index, $data);
				}
				else{
					if(is_array($return) && isset($return[$index])){
						$return = $return[$index];
					}
					else{
						$return = null;
					}
				}
				if(is_null($return)){
					break;
				}
			}
			
		}
		else if(isset($data[$key])){
			$return = $data[$key];
		}
		
		return $return;
	}
	
	/**
	 * @param array $arr1
	 * @param array $arr2
	 * @return array
	 */
	protected function _merge(array $arr1, array $arr2){
		foreach($arr2 as $key => $value){
			if(array_key_exists($key, $arr1)){
				if(is_array($value) && is_array($arr1[$key])){
					$arr1[$key] = $this->_merge($arr1[$key], $value);
				}
				else{
					if(is_numeric($key)){
						array_push($arr1, $value);
					}
					else{
						$arr1[$key] = $value;
					}
				}
			}
			else{
				$arr1[$key] = $value;
			}
		}
		return $arr1;
	}
	
}