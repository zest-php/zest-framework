<?php

/**
 * @category Zest
 * @package Zest_Db
 * @subpackage Orm
 */
class Zest_Db_Orm_Nested_Model extends Zest_Db_Model{
	
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
	 * @return Zest_Db_Orm_Nested_Model
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
	 * @return Zest_Db_Orm_Nested_Model
	 */
	public function hasOne($foreignModel, $foreignCol, $localCol, $property, array $options = array()){
		if(isset($this->_nested[$property])){
			throw new Zest_Db_Exception(sprintf('La propriété "%s" est déjà utilisée pour une imbrication.', $property));
		}
		$this->_nested[$property] = array($foreignModel, $foreignCol, $localCol, $options, 'one');
		return $this;
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return array
	 */
	public function getArray(Zest_Db_Model_Request $request = null){
		$array = parent::getArray($request);
		return $this->_initNested($array);
	}
	
	/**
	 * @return array
	 */
	protected function _initNested($array){
		foreach($this->_nested as $name => $infos){
			list($foreignModel, $foreignCol, $localCol, $options, $mode) = $infos;
			
			if(is_string($foreignModel)){
				$foreignModel = Zest_Db_Model::getInstance($foreignModel);
			}
			if(!$foreignModel instanceof Zest_Db_Model){
				throw new Zest_Db_Exception('Le model doit hériter de Zest_Db_Model.');
			}
			
			$localValues = array();
			foreach($array as $object){
				if($object->$localCol){
					$localValues[] = $object->$localCol;
				}
			}
			
			if($localValues){
				$request = Zest_Db_Model_Request::factory(array(
					$foreignCol => $localValues
				), $options);
				
				switch($mode){
					case 'many':
						$nested = $foreignModel->getArray($request);
						foreach($array as $object){
							$children = array();
							foreach($nested as $child){
								if($child->$foreignCol == $object->$localCol){
									$children[] = $child;
								}
							}
							$object->$name = $children;
						}
						break;
					case 'one':
						$nested = $foreignModel->getArray($request);
						foreach($array as $object){
							$children = null;
							foreach($nested as $child){
								if($child->$foreignCol == $object->$localCol){
									$children = $child;
								}
							}
							$object->$name = $children;
						}
						break;
				}
			}
			else{
				switch($mode){
					case 'many':
						foreach($array as $object){
							$object->$name = array();
						}
						break;
					case 'one':
						foreach($array as $object){
							$object->$name = null;
						}
						break;
				}
			}
			
			
		}
		
		return $array;
	}
	
}