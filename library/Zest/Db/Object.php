<?php

/**
 * @category Zest
 * @package Zest_Db
 */
class Zest_Db_Object extends Zest_Data{

	/**
	 * @var Zest_Db_Object_Mapper
	 */
	protected $_mapper = null;
	
	/**
	 * @var array
	 */
	protected $_cleanData = array();
	
	/**
	 * @param array $data
	 * @return void
	 */
	public function __construct(array $data = array()){
		parent::__construct();
		
		if($data){
			$this->create($data);
		}
	}
	
	/**
	 * @return Zest_Db_Object
	 */
	public function pushData(){
		$this->_cleanData = $this->_data;
		return $this;
	}
	
	/**
	 * @return Zest_Db_Object
	 */
	public function pullCleanData(){
		$this->_data = $this->_cleanData;
		return $this;
	}
	
	/**
	 * @return Zest_Db_Object
	 */
	public function hasCleanData(){
		return !empty($this->_cleanData);
	}
	
	/**
	 * @param mixed $data
	 * @param mixed $cleanData
	 * @return boolean
	 */
	protected function _isClean($data, $cleanData){
		// Zest_Db_Object
		if($data instanceof Zest_Db_Object){
			/**
			 * remplacement de l'objet
			 * $data et $cleanData ne font pas référence au même objet
			 * on compare les données de chaque objet (_data)
			 */
			if($cleanData instanceof Zest_Db_Object){
				if(!$this->_isClean($data->_data, $cleanData->_data)){
					return false;
				}
			}
			
			/*
			 * modification de l'objet
			 * $data et $cleanData font référence au même objet
			 * on compare les données uniquement sur $data (_data et _cleanData)
			 */
			return $this->_isClean($data->_data, $data->_cleanData);
		}
		
		// integer, float, string, boolean
		if(is_scalar($data) && is_scalar($cleanData)){
			return $data == $cleanData;
		}
		
		if(gettype($data) != gettype($cleanData)){
			return false;
		}
		
		// array, object
		$data = (array) $data;
		$cleanData = (array) $cleanData;
		
		// vérification de la cohérence entre les deux tableaux
		if(array_keys($data) != array_keys($cleanData)){
			return false;
		}
		
		foreach($data as $key => $value){
			if(!$this->_isClean($value, $cleanData[$key])){
				return false;
			}
		}
		
		return true;
	}
		
	/**
	 * @param string $key
	 * @return boolean
	 */
	public function isClean($key = null){
		if(is_null($key)){
			return $this->_isClean($this->_data, $this->_cleanData);
		}
		
		// une des deux valeurs existe et l'autre non
		if(isset($this->_data[$key]) != isset($this->_cleanData[$key])){
			return false;
		}
		
		// les deux valeurs n'existe pas
		if(!isset($this->_data[$key])){
			return true;
		}
		
		return $this->_isClean($this->_data[$key], $this->_cleanData[$key]);
	}
	
	/**
	 * @return Zest_Db_Object
	 */
	public function getCleanObject(){
		$object = new $this();
		$object->setData($this->_cleanData)->pushData();
		return $object;
	}

	/**
	 * @param string|Zest_Db_Object_Mapper $mapper
	 * @return Zest_Db_Object
	 */
	public function setMapper($mapper){
		if(is_string($mapper)){
			$mapper = Zest_Db_Object_Mapper::getInstance($mapper, $this);
		}
		$this->_mapper = $mapper;
		if(!$this->_mapper instanceof Zest_Db_Object_Mapper){
			throw new Zest_Db_Exception('Le mapper doit hériter de Zest_Db_Object_Mapper.');
		}
		return $this;
	}
	
	/**
	 * @return Zest_Db_Object_Mapper
	 */
	public function getMapper(){
		if(!$this->_mapper){
			$className = get_class($this);
			if($className == 'Zest_Db_Object'){
				throw new Zest_Db_Exception('La classe Zest_Db_Object doit être étendue.');
			}
			$this->setMapper($className.'Mapper');
		}
		return $this->_mapper;
	}

	/**
	 * @return Zest_Db_Object
	 */
	public function resetMapper(){
		$this->_mapper = null;
		return $this;
	}
	
	/**
	 * @param array $data
	 * @return Zest_Db_Object
	 */
	public function create(array $data = array(), array $options = array()){
		$this->_data = array();
		$this->getMapper()->create($data, $this, $options);
		return $this;
	}
	
	/**
	 * @param mixed|array $primary
	 * @return Zest_Db_Object
	 */
	public function find($primary, array $options = array()){
		$this->_data = array();
		$this->getMapper()->find($primary, $this, $options);
		return $this;
	}
	
	/**
	 * @return Zest_Db_Object
	 */
	public function save(array $options = array()){
		$this->getMapper()->save($this, $options);
		return $this;
	}
	
	/**
	 * @return Zest_Db_Object
	 */
	public function delete(array $options = array()){
		$this->getMapper()->delete($this, $options);
		return $this;
	}
	
}