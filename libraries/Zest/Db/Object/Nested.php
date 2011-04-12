<?php

/**
 * @category Zest
 * @package Zest_Db
 * @subpackage Object
 */
class Zest_Db_Object_Nested extends Zest_Db_Object{
	
	/**
	 * @var array
	 */
	protected $_nested = array();
	
	/**
	 * @param string $foreignModel
	 * @param string $foreignCol
	 * @param string $localCol
	 * @param string $property
	 * @param array $options
	 * @return Zest_Db_Object_Nested
	 */
	public function hasMany($foreignModel, $foreignCol, $localCol, $property, array $options = array()){
		if(isset($this->_nested[$property])){
			throw new Zest_Db_Exception(sprintf('La propriété "%s" est déjà utilisée pour une imbrication.', $property));
		}
		$this->_nested[$property] = array($foreignModel, $foreignCol, $localCol, $options, 'many');
		return $this;
	}
	
	/**
	 * @param string $foreignModel
	 * @param string $foreignCol
	 * @param string $localCol
	 * @param string $property
	 * @param array $options
	 * @return Zest_Db_Object_Nested
	 */
	public function hasOne($foreignModel, $foreignCol, $localCol, $property, array $options = array()){
		if(isset($this->_nested[$property])){
			throw new Zest_Db_Exception(sprintf('La propriété "%s" est déjà utilisée pour une imbrication.', $property));
		}
		$this->_nested[$property] = array($foreignModel, $foreignCol, $localCol, $options, 'one');
		return $this;
	}
	
	/**
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name){
		if(!isset($this->_data[$name])){
			$this->_initNested($name);
		}
		return parent::__get($name);
	}
	
	/**
	 * @param string $name
	 * @return void
	 */
	protected function _initNested($name){
		if(isset($this->_nested[$name])){
			list($foreignModel, $foreignCol, $localCol, $options, $mode) = $this->_nested[$name];
			
			$nested = null;
			if($this->hasData($localCol)){
				if(is_string($foreignModel)){
					$foreignModel = Zest_Db_Model::getInstance($foreignModel);
				}
				if(!$foreignModel instanceof Zest_Db_Model){
					throw new Zest_Db_Exception('Le model doit hériter de Zest_Db_Model.');
				}
				
				$request = Zest_Db_Model_Request::factory(array(
					$foreignCol => parent::__get($localCol)
				), $options);
				switch($mode){
					case 'many':
						$nested = $foreignModel->getArray($request);
						break;
					case 'one':
						$nested = $foreignModel->get($request);
						break;
				}
			}
			else if($mode == 'many'){
				$nested = array();
			}
			$this->_data[$name] = $nested;
		}
	}
	
}