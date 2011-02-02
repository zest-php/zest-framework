<?php

/**
 * @category Zest
 * @package Zest_Translate
 */
class Zest_Translate extends Zend_Translate{
	
	/**
	 * @var string
	 */
	protected static $_cacheDir = null;
	
	/**
	 * @param string $adapter
	 * @param array $translations
	 * @param string $locale
	 * @param array $options
	 * @param boolean $registerAsDefault
	 * @return Zest_Translate
	 */
	public static function factory($adapter, array $translations, $locale = null, $options = array(), $registerAsDefault = true){
		if(!$translations){
			throw new Zest_Translate_Exception('Aucune langue renseignée.');
		}
		
		if(is_bool($options)){
			$registerAsDefault = $options;
			$options = array();
		}
		
		// ajout des fichiers de traduction
		$translate = null;
		foreach($translations as $localeTranslation => $data){
			if(!$translate){
				$options = array_merge((array) $options, array('adapter' => $adapter, 'content' => $data, 'locale' => $localeTranslation));
				$translate = new self($options);
			}
			else{
				$translate->addTranslation($data, $localeTranslation);
			}
		}
		
		// une fois tous les fichiers de traduction ajoutés, il est possible de choisir la langue utilisée pour traduire
		if($locale){
			$translate->setLocale($locale);
		}
		
		// utilisé dans plusieurs classes du Zend Framework comme traducteur par défaut
		if($registerAsDefault){
			Zend_Registry::set('Zend_Translate', $translate);
		}
		
		// on s'assure que le cache soit créé s'il le peut
		self::getCache();
		
		return $translate;
	}
	
	/**
	 * @param string|array $messageId
	 * @param string|Zend_Locale $locale
	 * @param array $vars
	 * @return string
	 */
	public function translate($messageId, $locale = null, array $vars = array()){
		if(!$vars && is_array($locale)){
			$vars = $locale;
			$locale = null;
		}
		
		// gestion des pluriels
		if(is_array($messageId) && count($messageId) > 2){
			/**
			 * il n'y a que 3 adaptateurs qui gèrent les pluriels
			 * 		Zend_Translate_Adapter_Array
			 * 		Zend_Translate_Adapter_Csv
			 * 		Zend_Translate_Adapter_Gettext
			 * on remédie à ce problème
			 */
			$number = array_pop($messageId);
			if(!is_numeric($number) && isset($vars[$number])){
				$number = $vars[$number];
			}
			
			if(is_null($locale)){
				$locale = $this->getAdapter()->getLocale();
			}
			$rule = Zend_Translate_Plural::getPlural($number, $locale);
			if(isset($messageId[$rule])){
				$messageId = $messageId[$rule];
			}
			else{
				reset($messageId);
				$messageId = current($messageId);
			}
		}
		
		// traduction
		$translate = $this->getAdapter()->translate($messageId, $locale);
		
		// gestion des variables
		if($vars){
			return vsprintf($translate, $vars);
		}
		
//		if($vars){
//			$vars = array_change_key_case($vars, CASE_LOWER);
//			foreach($vars as $name => $value){
//				$translate = str_replace('{$'.$name.'}', $value, $translate);
//			}
//		}
		
		return $translate;
	}
	
	/**
	 * @param string $dir
	 * @return void
	 */
	public static function setCacheDir($dir){
		self::$_cacheDir = $dir;
	}
	
	/**
	 * @return Zend_Cache_Core
	 */
	public static function getCache(){
		if(!parent::getCache() && self::$_cacheDir){
			$frontend = array('automatic_serialization' => true);
			$backend = array('cache_dir' => self::$_cacheDir);
			self::setCache(Zend_Cache::factory('Core', 'File', $frontend, $backend));
		}
		return parent::getCache();
	}

}