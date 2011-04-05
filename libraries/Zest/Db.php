<?php

/**
 * @category Zest
 * @package Zest_Db
 */
class Zest_Db extends Zend_Db{
	
	/**
	 * @var array
	 */
	private static $_dbConfigs = array();
	
	/**
	 * @param array $configs
	 * @return void
	 */
	public static function setDbConfigs(array $configs){
		foreach($configs as $name => $config){
			self::setDbConfig($name, $config);
		}
	}
	
	/**
	 * @param string $name
	 * @param array $config
	 * @return void
	 */
	public static function setDbConfig($name, array $config){
		$name = strtolower($name);
		if(isset(self::$_dbConfigs[$name]) && self::$_dbConfigs[$name] instanceof Zend_Db_Adapter_Abstract){
			throw new Zest_Db_Exception(sprintf('Impossible de modifier la configuration de l\'adaptateur "%s" car il est déjà chargé.', $name));
		}
		self::$_dbConfigs[$name] = $config;
	}
	
	/**
	 * @param string $configName
	 * @return Zend_Db_Adapter_Abstract
	 */
	public static function getDbAdapter($configName){
		$configName = strtolower($configName);
		
		if(isset(self::$_dbConfigs[$configName])){
			$dbConfig = self::$_dbConfigs[$configName];
			if($dbConfig instanceof Zend_Db_Adapter_Abstract){
				return $dbConfig;
			}
			
			if(!is_array($dbConfig) || !isset($dbConfig['adapter'])){
				throw new Zest_Db_Exception(sprintf('Aucun adaptateur défini pour la connexion "%s".', $configName));
			}
			
			$adapter = $dbConfig['adapter'];
			unset($dbConfig['adapter']);
			
			try{
				$dbConfig['adapterNamespace'] = 'Zest_Db_Adapter';
				$dbAdapter = @Zend_Db::factory($adapter, $dbConfig);
			}
			catch(Zend_Exception $e){
				unset($dbConfig['adapterNamespace']);
				$dbAdapter = Zend_Db::factory($adapter, $dbConfig);
			}
			
			if(!$dbAdapter instanceof Zend_Db_Adapter_Abstract){
				throw new Zest_Db_Exception('L\'adaptateur doit hériter de Zend_Db_Adapter_Abstract.');
			}
			
			return self::$_dbConfigs[$configName] = $dbAdapter;
		}
		
		throw new Zest_Db_Exception(sprintf('L\'adaptateur "%s" n\'existe pas.', $configName));
	}
	
}