<?php

/**
 * @category Zest
 * @package Zest_Db
 * @subpackage Model
 */
abstract class Zest_Db_Model_Adapter_Abstract{
	
	/**
	 * @var Zest_Db_Model
	 */
	protected $_model;
	
	/**
	 * @param Zest_Db_Model $model
	 * @return void
	 */
	public function __construct(Zest_Db_Model $model){
		$this->_model = $model;
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return void
	 */
	abstract public function save(Zest_Db_Model_Request $request);
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return void
	 */
	abstract public function delete(Zest_Db_Model_Request $request);
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return Zend_Db_Table_Select
	 */
	abstract public function getDbSelect(Zest_Db_Model_Request $request);
	
}