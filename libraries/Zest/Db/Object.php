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
	protected $_clean = array();
	
	/**
	 * @param array $data
	 * @return void
	 */
	public function __construct(array $data = null){
		parent::__construct();
		
		if(!is_null($data)){
			$this->create($data);
		}
	}
	
	/**
	 * @return Zest_Db_Object
	 */
	public function setDataToClean(){
		$this->_clean = $this->_data;
		return $this;
	}
	
	/**
	 * @return Zest_Db_Object
	 */
	public function setCleanToData(){
		$this->_data = $this->_clean;
		return $this;
	}
	
	/**
	 * @return Zest_Db_Object
	 */
	public function hasClean(){
		return !empty($this->_clean);
	}
	
	/**
	 * @param mixed $data
	 * @param mixed $clean
	 * @return boolean
	 */
	protected function _isClean($data, $clean){
		// Zest_Db_Object
		if($data instanceof Zest_Db_Object){
			/**
			 * remplacement de l'objet
			 * $data et $clean ne font pas référence au même objet
			 * on compare les données de chaque objet (_data)
			 */
			if($clean instanceof Zest_Db_Object){
				if(!$this->_isClean($data->_data, $clean->_data)){
					return false;
				}
			}
			
			/*
			 * modification de l'objet
			 * $data et $clean font référence au même objet
			 * on compare les données uniquement sur $data (_data et _clean)
			 */
			return $this->_isClean($data->_data, $data->_clean);
		}
		
		// integer, float, string, boolean
		if(is_scalar($data) && is_scalar($clean)){
			return $data == $clean;
		}
		
		if(gettype($data) != gettype($clean)){
			return false;
		}
		
		// array, object
		$data = (array) $data;
		$clean = (array) $clean;
		
		// vérification de la cohérence entre les deux tableaux
		if(array_keys($data) != array_keys($clean)){
			return false;
		}
		
		foreach($data as $key => $value){
			if(!$this->_isClean($value, $clean[$key])){
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
			return $this->_isClean($this->_data, $this->_clean);
		}
		
		// une des deux valeurs existe et l'autre non
		if(isset($this->_data[$key]) != isset($this->_clean[$key])){
			return false;
		}
		
		// les deux valeurs n'existe pas
		if(!isset($this->_data[$key])){
			return true;
		}
		
		return $this->_isClean($this->_data[$key], $this->_clean[$key]);
	}
	
	/**
	 * @return Zest_Db_Object
	 */
	public function getCleanObject(){
		$object = new $this();
		$object->setData($this->_clean)->setDataToClean();
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
	 * @param array $options
	 * @return Zest_Db_Object
	 */
	public function create(array $data = array(), array $options = array()){
		$this->_data = array();
		$this->getMapper()->create($data, $this, $options);
		return $this;
	}
	
	/**
	 * @param mixed|array $primary
	 * @param array $options
	 * @return Zest_Db_Object
	 */
	public function find($primary, array $options = array()){
		$this->_data = array();
		$this->getMapper()->find($primary, $this, $options);
		return $this;
	}
	
	/**
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 */
	public function __call($method, $args){
		if(substr(strtolower($method), 0, 6) == 'findby'){
			$this->_data = array();
			if(!isset($args[0])) $args[0] = null;
			array_splice($args, 1, 0, array($this));
			return call_user_func_array(array($this->getMapper(), $method), $args);
		}
		throw new Zest_Db_Exception(sprintf('La méthode "%s" n\'existe pas.', $method));
	}
	
	/**
	 * @param array $options
	 * @return Zest_Db_Object
	 */
	public function save(array $options = array()){
		$this->getMapper()->save($this, $options);
		return $this;
	}
	
	/**
	 * @param array $options
	 * @return Zest_Db_Object
	 */
	public function delete(array $options = array()){
		$this->getMapper()->delete($this, $options);
		return $this;
	}
	
}