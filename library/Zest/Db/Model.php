<?php

/**
 * @category Zest
 * @package Zest_Db
 */
class Zest_Db_Model{

	/**
	 * @var Zest_Db_Table
	 */
	protected $_dbTable = null;
	
	/**
	 * @var string
	 */
	protected $_objectClass = 'stdClass';
	
	/**
	 * @var Zest_Db_Model_Adapter_Abstract
	 */
	protected $_adapter = null;
	
	/**
	 * @var Zest_Db_Model_NestedSet
	 */
	protected $_nestedSet = null;
	
	/**
	 * @var array
	 */
	protected static $_instances = array();
	
	/**
	 * @var string
	 */
	const GETARRAY_KEY_SEPARATOR = '-';

	/**
	 * @param string|Zest_Db_Table $dbTable
	 * @return void
	 */
	public function __construct($dbTable = null){
		if(!is_null($dbTable)){
			$this->setDbTable($dbTable);
		}
		$this->_nestedSet = new Zest_Db_Model_NestedSet();
	}
	
	/**
	 * @param string $className
	 * @return Zest_Db_Model
	 */
	public static function getInstance($className, $arg = null){
		if(!isset(self::$_instances[$className])){
			self::$_instances[$className] = new $className($arg);
		}
		return self::$_instances[$className];
	}
	
	/**
	 * @param string|Zest_Db_Table $dbTable
	 * @return Zest_Db_Model
	 */
	public function setDbTable($dbTable){
		if(is_string($dbTable)){
			$dbTable = Zest_Db_Table::getInstance($dbTable);
		}
		$this->_dbTable = $dbTable;
		if(!$this->_dbTable instanceof Zest_Db_Table){
			throw new Zest_Db_Exception('La table doit hériter de Zest_Db_Table.');
		}
		return $this;
	}
	
	/**
	 * @return Zest_Db_Table
	 */
	public function getDbTable(){
		if(!$this->_dbTable){
			throw new Zest_Db_Exception('Aucune table définie.');
		}
		return $this->_dbTable;
	}
	
	/**
	 * @return Zend_Db_Adapter_Abstract
	 */
	public function getDbAdapter(){
		return $this->getDbTable()->getAdapter();
	}
	
	/**
	 * @param integer $queryType
	 * @param boolean $showUnfinished
	 * @return array|false
	 */
	public function getDbQueryProfiles($queryType = null, $showUnfinished = false){
		return $this->getDbTable()->getAdapter()->getProfiler()->getQueryProfiles($queryType, $showUnfinished);
	}
	
	/**
	 * @return string
	 */
	public function getObjectClass(){
		return $this->_objectClass;
	}
	
	/**
	 * @param string $className
	 * @return Zest_Db_Model
	 */
	public function setObjectClass($className){
		$this->_objectClass = $className;
		return $this;
	}
	
	/**
	 * @param string|Zest_Db_Model $foreignModel
	 * @param string $foreignCol
	 * @param string $localCol
	 * @param string $property
	 * @return Zest_Db_Model
	 */
	public function hasMany($foreignModel, $foreignCol, $localCol, $property){
		$this->_nestedSet->addNested(new Zest_Db_Model_Nested($foreignModel, $foreignCol, $localCol, Zest_Db_Model_Nested::MODE_MANY, $property));
		return $this;
	}
	
	/**
	 * @param string|Zest_Db_Model $foreignModel
	 * @param string $foreignCol
	 * @param string $localCol
	 * @param string $property
	 * @return Zest_Db_Model
	 */
	public function hasOne($foreignModel, $foreignCol, $localCol, $property){
		$this->_nestedSet->addNested(new Zest_Db_Model_Nested($foreignModel, $foreignCol, $localCol, Zest_Db_Model_Nested::MODE_ONE, $property));
		return $this;
	}
	
	/**
	 * @param string|Zest_Db_Model $foreignModel
	 * @param string $foreignCol
	 * @param string $localCol
	 * @return Zest_Db_Model
	 */
	public function isAlso($foreignModel, $foreignCol, $localCol){
		$this->_nestedSet->addNested(new Zest_Db_Model_Nested($foreignModel, $foreignCol, $localCol, Zest_Db_Model_Nested::MODE_ALSO));
		return $this;
	}
	
	/**
	 * @param object $object
	 * @return array
	 */
	public function toArray($object){
		if(method_exists($object, 'toArray')){
			return $object->toArray();
		}
		else{
			throw new Zest_Db_Exception('La méthode "toArray" doit être définie.');
		}
	}
	
	/**
	 * @return Zest_Db_Model_NestedSet
	 */
	public function getNestedSet(){
		return $this->_nestedSet;
	}
	
	/**
	 * @param object|array $source
	 * @return object
	 */
	public function toObject($source){
		if(!is_array($source)){
			$source = $this->toArray($source);
		}
		$object = new $this->_objectClass();
		if($object instanceof Zest_Db_Object){
			$object->setData($source)->refreshClean();
		}
		else{
			foreach($source as $col => $value){
				$object->$col = $value;
			}
		}
		return $object;
	}
	
	/**
	 * @param array $array
	 * @return array
	 */
	public function getIntersectPrimary(array $array){
		$table = $this->getDbTable();
		$primary = array_flip($table->info(Zest_Db_Table::PRIMARY));
		$intersect = array_intersect_key($array, $primary);
		
		// une clef primaire ne peut pas être null
		foreach($intersect as $key => $value){
			if(is_null($value)){
				unset($intersect[$key]);
			}
		}
		
		return $intersect;
	}
	
	/**
	 * @param array $array
	 * @return array
	 */
	public function getIntersectCols(array $array){
		$table = $this->getDbTable();
		$primary = array_flip($table->info(Zest_Db_Table::COLS));
		return array_intersect_key($array, $primary);
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return Zend_Db_Table_Select
	 */
	public function getDbSelect(Zest_Db_Model_Request $request){
		$table = $this->getDbTable();
		$select = $table->select();
		
		// création de la condition
		$select->where($this->_getAdapter()->getWhereQuery($table, $request->toArray(), '='));
		
		// remplacement du SELECT * par chaque nom de colonne
		$select->from($table->info(Zest_Db_Table::NAME), $table->info(Zest_Db_Table::COLS));
		
		// nested set
		$this->_nestedSet->alterDbSelect($this, $select);
		
		// gestion des options
		foreach($request->getOptions() as $name => $value){
			$method = 'option'.ucfirst($name);
			if(method_exists($this, $method)){
				$this->$method($select, $value);
			}
			if(method_exists($this->_getAdapter(), $method)){
				$this->_getAdapter()->$method($select, $value);
			}
		}
		
		return $select;
	}
	
	/**
	 * @param Zend_Db_Table_Select $select
	 * @param mixed $order
	 * @return void
	 */
	public function optionOrder(Zend_Db_Table_Select $select, $order){
		$select->order($order);
	}
	
	/**
	 * @param Zend_Db_Table_Select $select
	 * @param mixed $group
	 * @return void
	 */
	public function optionGroup(Zend_Db_Table_Select $select, $group){
		$select->group($group);
	}
	
	/**
	 * @param Zend_Db_Table_Select $select
	 * @param array $limit
	 * @return void
	 */
	public function optionLimit(Zend_Db_Table_Select $select, array $limit){
		extract($limit);
		if(isset($count) && isset($offset)){
			$select->limit($count, $offset);
		}
		else{
			throw new Zest_Db_Exception('Les clefs pour l\'option "limit" sont "count" et "offset\'.');
		}
	}
	
	/**
	 * @param Zend_Db_Table_Select $select
	 * @param array $limit
	 * @return void
	 */
	public function optionLimitPage(Zend_Db_Table_Select $select, array $limit){
		extract($limit);
		if(isset($page) && isset($rowCount)){
			$select->limitPage($page, $rowCount);
		}
		else{
			throw new Zest_Db_Exception('Les clefs pour l\'option "limitPage" sont "page" et "rowCount\'.');
		}
	}
	
	/**
	 * @return Zest_Db_Model_Adapter_Abstract
	 */
	protected function _getAdapter(){
		if(!$this->_adapter){
			$dbAdapter = get_class($this->getDbTable()->getAdapter());
			$className = 'Zest_Db_Model_'.substr($dbAdapter, strpos($dbAdapter, 'Adapter'));
			if(@class_exists($className)){
				$this->_adapter = new $className($this);
			}
			else{
				throw new Zest_Db_Exception(sprintf('La classe "%s" n\'existe pas.', $className));
			}
		}
		return $this->_adapter;
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return object
	 */
	public function create(Zest_Db_Model_Request $request){
		if(method_exists($this->_getAdapter(), 'create')){
			return $this->_getAdapter()->create($request);
		}
		
		$table = $this->getDbTable();
		$row = $table->createRow($request->toArray(), Zest_Db_Table::DEFAULT_DB);
		return $this->toObject($row);
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return boolean
	 */
	public function save(Zest_Db_Model_Request $request){
		$save = $this->_getAdapter()->save($request);
		
		if($save && $request->object instanceof Zest_Db_Object){
			$refreshclean = $request->getOption('refreshclean');
			if(is_null($refreshclean) || $refreshclean){
				$request->object->refreshClean();
			}
		}
		
		return $save;
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return object
	 */
	public function get(Zest_Db_Model_Request $request){
		$request->setOption('limit', array('count' => 1, 'offset' => 0));
		$arrayObjects = $this->getArray($request);
		
		if($arrayObjects){
			reset($arrayObjects);
			return current($arrayObjects);
		}
		
		return null;
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return array
	 */
	public function getArray(Zest_Db_Model_Request $request){
		if(method_exists($this->_getAdapter(), 'getArray')){
			return $this->_getAdapter()->getArray($request);
		}
		
		$table = $this->getDbTable();
		$select = $this->getDbSelect($request);
		
		$arrayObjects = array();
		
		// envoi de la requête
		$rowSet = $table->fetchAll($select)->toArray();
		
		// nested set
		if($this->getNestedSet()->hasNested()){
			foreach($rowSet as $row){			
				// récupération des valeurs des clefs primaires pour l'indexation
				$key = implode(self::GETARRAY_KEY_SEPARATOR, $this->getIntersectPrimary($row));
				if(!isset($arrayObjects[$key])){
					$arrayObjects[$key] = array();
				}
				$arrayObjects[$key][] = $row;
			}
			$arrayObjects = $this->getNestedSet()->alterObjects($arrayObjects, $this);
		}
		else{
			foreach($rowSet as $row){			
				// récupération des valeurs des clefs primaires pour l'indexation
				$key = implode(self::GETARRAY_KEY_SEPARATOR, $this->getIntersectPrimary($row));
				$arrayObjects[$key] = $this->toObject($row);
			}
		}
		
		return $arrayObjects;
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return boolean
	 */
	public function delete(Zest_Db_Model_Request $request){
		return $this->_getAdapter()->delete($request);
	}
	
}