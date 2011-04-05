<?php

/**
 * @category Zest
 * @package Zest_Db
 */
class Zest_Db_Table extends Zend_Db_Table{
	
	/**
	 * @var string
	 */
	protected $_rowsetClass = 'Zest_Db_Table_Rowset';
	
	/**
	 * @var string
	 */
	protected $_adapterConfigName = null;
	
	/**
	 * @var string
	 */
	protected $_metadataCacheDir = null;
	
	/**
	 * @var string
	 */
	protected static $_defaultMetadataCacheDir = null;
	
	/**
	 * @var array
	 */
	protected static $_instances = array();
	
	/**
	 * @param string $className
	 * @return Zest_Db_Table
	 */
	public static function getInstance($className){
		if(!isset(self::$_instances[$className])){
			$table = new $className();
			if(!$table instanceof Zest_Db_Table){
				throw new Zest_Db_Exception('La table doit hériter de Zest_Db_Table.');
			}
			self::$_instances[$className] = $table;
		}
		return self::$_instances[$className];
	}
	
	/**
	 * @return void
	 */
	protected function _setupDatabaseAdapter(){
		$this->_db = $this->getAdapter();
		if(!$this->_db){
			$this->_db = self::getDefaultAdapter();
		}
	}
	
	/**
	 * @return Zend_Db_Adapter_Abstract
	 */
	public function getAdapter(){
		if(!$this->_db && $this->_adapterConfigName){
			$this->_db = Zest_Db::getDbAdapter($this->_adapterConfigName);
		}
		return $this->_db;
	}
	
	/**
	 * @return Zend_Db_Adapter_Abstract
	 */
	public static function getDefaultAdapter(){
		if(!self::$_defaultDb){
			self::$_defaultDb = Zest_Db::getDbAdapter('default');
		}
		return self::$_defaultDb;
	}
	
	/**
	 * @return boolean
	 */
	protected function _setupMetadata(){
		if($this->metadataCacheInClass() && (count($this->_metadata) > 0)){
			return true;
		}
		$this->_metadataCache = $this->getMetadataCache();
		if(!$this->_metadataCache){
			self::$_defaultMetadataCache = self::getDefaultMetadataCache();
		}
		return parent::_setupMetadata();
	}
	
	/**
	 * @return Zend_Cache_Core
	 */
	public function getMetadataCache(){
		if(!$this->_metadataCache && $this->_metadataCacheDir){
			$this->_metadataCache = self::_createZendCache($this->_metadataCacheDir);
		}
		return $this->_metadataCache;
	}
	
	/**
	 * @return Zend_Cache_Core
	 */
	public static function getDefaultMetadataCache(){
		if(!self::$_defaultMetadataCache && self::$_defaultMetadataCacheDir){
			self::$_defaultMetadataCache = self::_createZendCache(self::$_defaultMetadataCacheDir);
		}
		return self::$_defaultMetadataCache;
	}
	
	/**
	 * @param string $dir
	 * @return void
	 */
	public static function setDefaultMetadataCacheDir($dir){
		self::$_defaultMetadataCacheDir = $dir;
	}
	
	/**
	 * @param string $dir
	 * @return Zest_Db_Table
	 */
	public function setMetadataCacheDir($dir){
		$this->_metadataCacheDir = $dir;
		return $this;
	}
	
	/**
	 * @param string|Zend_Cache_Core $metadataCache
	 * @return Zest_Db_Table
	 */
	public function setMetadataCache($metadataCache = null){
		$this->_setMetadataCache($metadataCache);
		return $this;
	}
	
	/**
	 * @param string $dir
	 * @return Zend_Cache_Core
	 */
	protected static function _createZendCache($dir){
		$frontend = array('automatic_serialization' => true);
		$backend = array('cache_dir' => $dir);
		return Zend_Cache::factory('Core', 'File', $frontend, $backend);
	}
	
	/**
	 * @return boolean
	 */
	public function exists(){
		try{
			$this->_setupMetadata();
			return true;
		}
		catch(Zend_Exception $e){
			return false;
		}
	}
	
	/**
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 */
	public function __call($method, $args){
		if(substr($method, 0, 8) == 'insertOr'){
			$args[] = $method;
			return call_user_func_array(array($this, '_insertOr'), $args);
		}
		throw new Zest_Db_Exception(sprintf('La méthode "%s" n\'existe pas.', $method));
	}
	
	/**
	 * @param array $data
	 * @param string $dbMethod
	 * @return mixed
	 */
	protected function _insertOr(array $data, $dbMethod){
		$this->_setupPrimaryKey();
		
		/**
		 * Zend_Db_Table assumes that if you have a compound primary key
		 * and one of the columns in the key uses a sequence,
		 * it's the _first_ column in the compound key.
		 */
		$primary = (array) $this->_primary;
		$pkIdentity = $primary[(int)$this->_identity];
		
		/**
		 * If this table uses a database sequence object and the data does not
		 * specify a value, then get the next ID from the sequence and add it
		 * to the row.  We assume that only the first column in a compound
		 * primary key takes a value from a sequence.
		 */
		if(is_string($this->_sequence) && !isset($data[$pkIdentity])){
			$data[$pkIdentity] = $this->_db->nextSequenceId($this->_sequence);
		}
		
		/**
		 * If the primary key can be generated automatically, and no value was
		 * specified in the user-supplied data, then omit it from the tuple.
		 */
		if(array_key_exists($pkIdentity, $data) && $data[$pkIdentity] === null){
			unset($data[$pkIdentity]);
		}
		
		/**
		 * INSERT the new row.
		 */
		$tableSpec = ($this->_schema ? $this->_schema . '.' : '') . $this->_name;
		$this->_db->$dbMethod($tableSpec, $data);
		
		/**
		 * Fetch the most recent ID generated by an auto-increment
		 * or IDENTITY column, unless the user has specified a value,
		 * overriding the auto-increment mechanism.
		 */
		if($this->_sequence === true && !isset($data[$pkIdentity])){
			$data[$pkIdentity] = $this->_db->lastInsertId();
		}
		
		/**
		 * Return the primary key value if the PK is a single column,
		 * else return an associative array of the PK column/value pairs.
		 */
		$pkData = array_intersect_key($data, array_flip($primary));
		if(count($primary) == 1){
			reset($pkData);
			return current($pkData);
		}
		
		return $pkData;
	}
	
	/**
	 * @param boolean $withFromPart
	 * @return Zest_Db_Table_Select
	 */
	public function select($withFromPart = self::SELECT_WITHOUT_FROM_PART){
		$select = new Zest_Db_Table_Select($this);
		if ($withFromPart == self::SELECT_WITH_FROM_PART) {
			$select->from($this->info(self::NAME), Zend_Db_Table_Select::SQL_WILDCARD, $this->info(self::SCHEMA));
		}
		return $select;
	}
	
}