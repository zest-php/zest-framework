<?php

/**
 * @category Zest
 * @package Zest_Db
 * @subpackage Object
 */
class Zest_Db_Object_Mapper extends Zest_Db_Model{
	
	/**
	 * @var string
	 */
	protected $_objectClass = 'Zest_Db_Object';

	/**
	 * @param string $objectClass
	 * @return void
	 */
	public function __construct($objectClass = null){
		if(is_object($objectClass)){
			$objectClass = get_class($objectClass);
		}
		
		if(is_null($objectClass)){
			$objectClass = get_class($this);
			$objectClass = substr($objectClass, 0, -strlen('mapper'));
			if(!@class_exists($objectClass)){
				$objectClass = null;
			}
		}
		
		$tableClass = null;
		if(!is_null($objectClass) && $objectClass != 'Zest_Db_Object'){
			$this->setObjectClass($objectClass);
			$namespace = str_replace(strstr($objectClass, '_'), '', $objectClass);
			$tableClass = str_replace($namespace.'_Model_', $namespace.'_Model_DbTable_', $objectClass);
			if(!@class_exists($tableClass)){
				$tableClass = null;
			}
		}
		
		parent::__construct($tableClass);
	}
	
	/**
	 * @param array $data
	 * @param Zest_Db_Object $object
	 * @return Zest_Db_Object
	 */
	public function create(array $data, Zest_Db_Object $object = null, array $options = array()){
		if(is_null($object)){
			$object = new $this->_objectClass();
			if($object instanceof Zest_Db_Object){
				$object->setMapper($this);
			}
		}
		$request = Zest_Db_Model_Request::factory($data);
		$request->setOption($options);
		$create = parent::create($request);
		if($create){
			$object->setData($create->toArray());
		}
		return $object;
	}
	
	/**
	 * @param ineteger|array|Zest_Db_Model_Request $request
	 * @param Zest_Db_Object $object
	 * @return Zest_Db_Object
	 */
	public function find($request, Zest_Db_Object $object = null, array $options = array()){
		if(is_null($object)){
			$object = new $this->_objectClass();
			if($object instanceof Zest_Db_Object){
				$object->setMapper($this);
			}
		}
		if(!$request instanceof Zest_Db_Model_Request){
			if(!is_array($request)){
				$primaryCols = $this->getDbTable()->info(Zest_Db_Table::PRIMARY);
				$request = array(current($primaryCols) => $request);
			}
			$request = Zest_Db_Model_Request::factory($request);
		}
		$request->setOption($options);
		$find = $this->get($request);
		if($find){
			$object->setData($find->toArray())->pushData();
		}
		return $object;
	}
	
	/**
	 * @param Zest_Db_Object $object
	 * @return boolean
	 */
	public function save(Zest_Db_Object $object, array $options = array()){
		$request = Zest_Db_Model_Request::factory(array('object' => $object));
		$request->setOption($options);
		return parent::save($request);
	}
	
	/**
	 * @param Zest_Db_Object $object
	 * @return boolean
	 */
	public function delete(Zest_Db_Object $object, array $options = array()){
		$intersectPrimary = $this->getIntersectPrimary($object->toArray());
		if($intersectPrimary){
			$request = Zest_Db_Model_Request::factory($intersectPrimary);
			$request->setOption($options);
			return parent::delete($request);
		}
		return false;
	}
	
}