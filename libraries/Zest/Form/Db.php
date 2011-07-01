<?php

/**
 * @category Zest
 * @package Zest_Form
 */
class Zest_Form_Db extends Zest_Form{
	
	/**
	 * @var Zest_Db_Object_Mapper
	 */
	protected $_dbMapper = null;
	
	/**
	 * @var Zest_Db_Object
	 */
	protected $_dbObject = null;
	
	/**
	 * @param string|Zest_Db_Object_Mapper $dbMapper
	 * @return Zest_Form_Db
	 */
	public function setDbMapper($dbMapper){
		if(is_string($dbMapper)){
			$dbMapper = Zest_Db_Model::getInstance($dbMapper);
		}
		if(!$dbMapper instanceof Zest_Db_Object_Mapper){
			throw new Zest_Form_Exception('Le model doit hériter de Zest_Db_Object_Mapper.');
		}
		$this->_dbMapper = $dbMapper;
		return $this;
	}
	
	/**
	 * @return Zest_Db_Object_Mapper
	 */
	public function getDbMapper(){
		if(is_null($this->_dbMapper)){
			throw new Zest_Form_Exception('Aucun model renseigné.');
		}
		return $this->_dbMapper;
	}
	
	/**
	 * @return Zest_Db_Table
	 */
	public function getDbTable(){
		return $this->getDbMapper()->getDbTable();
	}
	
	/**
	 * @param boolean $build
	 * @param boolean $throwExceptions
	 * @return Zest_Db_Object
	 */
	public function getDbObject($build = true, $throwExceptions = true){
		if(is_null($this->_dbObject)){
			if($build){
				$this->_dbObject = $this->getDbMapper()->toObject(array());
			}
			else if($throwExceptions){
				throw new Zest_Form_Exception('Aucun DbObject renseigné.');
			}
		}
		return $this->_dbObject;
	}
	
	/**
	 * @param integer|array|Zest_Db_Object $dbObject
	 * @return Zest_Form_Db
	 */
	public function setDbObject($dbObject){
		if(!$dbObject instanceof Zest_Db_Object){
			$dbObject = $this->getDbMapper()->find($dbObject);
		}
		$this->_dbObject = $dbObject;
		return $this;
	}
	
	/**
	 * @param Zest_Db_Object $dbObject
	 * @return Zest_Form_Db
	 */
	public function populateFromDbObject(Zest_Db_Object $dbObject = null){
		return $this->setDefaultsFromDbObject($dbObject);
	}
	
	/**
	 * @param Zest_Db_Object $dbObject
	 * @return Zest_Form_Db
	 */
	public function setDefaultsFromDbObject(Zest_Db_Object $dbObject = null){
		if(is_null($dbObject)){
			$dbObject = $this->getDbObject();
		}
		$this->setDbObject($dbObject);
		foreach($dbObject as $key => $value){
			if($this->getElement($key)){
				$this->getElement($key)->setValue($value);
			}
		}
		return $this;
	}
	
	/**
	 * @return Zest_Form_Db
	 */
	public function getValuesToDbObject(Zest_Db_Object $dbObject = null){
		if(is_null($dbObject)){
			$dbObject = $this->getDbObject();
		}
		$this->setDbObject($dbObject);
		foreach($this->getElements() as $name => $element){
			$attr = $name;
			$value = $element->getValue();
			
			if($element instanceof Zest_Form_Element_File){
				if($value){
					$attr = preg_replace('/_r[0-9]+/', '', $attr);
					if($dbObject->$attr){
						$pathanme = $element->getPathname();
						if(file_exists($pathanme)){
							unlink($pathanme);
						}
					}
					$dbObject->$attr = $value;
				}
			}
			else{
				$dbObject->$attr = $value;
			}
		}
		return $this;
	}
	
	/**
	 * @return Zest_Form_Db
	 */
	public function addValidatorsFromDbTable(){
		foreach($this->getElements() as $element){
			$metadata = $this->getDbTable()->info(Zest_Db_Table::METADATA);
			if(!empty($metadata[$element->getName()]['LENGTH'])){
				if(!$element->getValidator('stringLength')){
					$length = $metadata[$element->getName()]['LENGTH'];
					$element->addValidator('stringLength', false, array('max' => $length));
				}
				$stringLength = $element->getValidator('stringLength');
				
				$type = strtolower(strrchr($element->getType(), '_'));
				if(substr($type, 1, 4) == 'text' && $max = $stringLength->getMax()){
					$element->setAttrib('maxlength', $max);
				}
			}
		}
		return $this;
	}
	
}