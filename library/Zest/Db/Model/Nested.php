<?php

/**
 * @category Zest
 * @package Zest_Db
 * @subpackage Model
 */
class Zest_Db_Model_Nested{
	
	/**
	 * @var Zest_Db_Model
	 */
	protected $_foreignModel = null;
	
	/**
	 * @var string
	 */
	protected $_foreignCol = null;
	
	/**
	 * @var string
	 */
	protected $_localCol = null;
	
	/**
	 * @var string
	 */
	protected $_mode = null;
	
	/**
	 * @var string
	 */
	protected $_property = null;

	/**
	 * @var string
	 */
	const MODE_MANY = 'many';

	/**
	 * @var string
	 */
	const MODE_ONE = 'one';

	/**
	 * @var string
	 */
	const MODE_ALSO = 'also';
	
	/**
	 * @param string|Zest_Db_Model $foreignModel
	 * @param string $localCol
	 * @param string $foreignCol
	 * @param string $mode
	 * @param string $property
	 * @return void
	 */
	public function __construct($foreignModel, $foreignCol, $localCol, $mode, $property = null){
		if(is_string($foreignModel)){
			$foreignModel = Zest_Db_Model::getInstance($foreignModel);
		}
		if($foreignModel instanceof Zest_Db_Model){
			$this->_foreignModel = $foreignModel;
		}
		else{
			throw new Zest_Acl_Exception('Le model doit être une instance de Zest_Db_Model.');
		}
		$this->_foreignCol = $foreignCol;
		$this->_localCol = $localCol;
		$this->_mode = $mode;
		$this->_property = $property;
	}
	
	/**
	 * @return Zest_Db_Model
	 */
	public function getForeignModel(){
		return $this->_foreignModel;
	}
	
	/**
	 * @return string
	 */
	public function getForeignCol(){
		return $this->_foreignCol;
	}
	
	/**
	 * @return string
	 */
	public function getLocalCol(){
		return $this->_localCol;
	}
	
	/**
	 * @return string
	 */
	public function getMode(){
		return $this->_mode;
	}
	
	/**
	 * @return string
	 */
	public function getProperty(){
		return $this->_property;
	}
	
}