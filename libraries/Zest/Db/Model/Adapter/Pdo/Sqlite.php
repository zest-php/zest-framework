<?php

/**
 * @category Zest
 * @package Zest_Db
 * @subpackage Model
 */
class Zest_Db_Model_Adapter_Pdo_Sqlite extends Zest_Db_Model_Adapter_Sql_Abstract{
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return boolean
	 */
	public function save(Zest_Db_Model_Request $request){
		if($request->object && is_object($request->object)){
			$object = $request->object;
		}
		else{
			throw new Zest_Db_Exception(sprintf('L\'objet à sauvegarder doit être stocké dans l\'objet Zest_Db_Model_Request à l\'aide de la clef "object".'));
		}
		
		$table = $this->_model->getDbTable();
		$rowArray = $this->_model->getIntersectCols($this->_model->toArray($object));
		
		return $this->_insertOrReplace($rowArray, $object);
	}
	
	/**
	 * @param array $rowArray
	 * @param Zest_Db_Object $object
	 * @return boolean
	 */
	protected function _insertOrReplace(array $rowArray, $object){
		$table = $this->_model->getDbTable();
		
		// insert or update
		$primary = $table->insertOrReplace($rowArray);
		
		// mise à jour des clefs primaires sur l'objet
		if(!is_array($primary)){
			$primary = array(current($table->info(Zest_Db_Table::PRIMARY)) => $primary);
		}
		$primary = $this->_model->getIntersectPrimary($primary);
		foreach($primary as $col => $value){
			$object->$col = $value;
		}
		
		return count($primary) > 0;
	}
	
}