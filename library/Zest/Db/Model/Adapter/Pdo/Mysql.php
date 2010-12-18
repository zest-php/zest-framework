<?php

/**
 * @category Zest
 * @package Zest_Db
 * @subpackage Model
 */
class Zest_Db_Model_Adapter_Pdo_Mysql extends Zest_Db_Model_Adapter_Sql_Abstract{
	
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
		
		return $this->_insertOrUpdate($rowArray, $object);
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return boolean
	 */
	protected function _insertOrUpdate(array $rowArray, $object){
		$table = $this->_model->getDbTable();
		
		// insert or update
		$primary = $table->insertOrUpdate($rowArray);
		
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
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return boolean
	 */
	protected function _insertThenUpdate(array $rowArray, $object){
		// update, puis insert en cas d'exception
		$return = false;
		
		try{
			$return = $this->_insert($rowArray, $object);
		}
		catch(Zend_Db_Exception $e){
			preg_match('/: ([0-9]+)/', $e->getMessage(), $matches);
			$code = isset($matches[1]) ? intval($matches[1]) : null;
			
			// duplicate entry
			if($code !== 1062){
				throw $e;
			}
			$return = $this->_update($rowArray);
		}
		catch(Exception $e){
			// erreur MySQL (Zend_Db_Statement_Exception) ou autres
			throw $e;
		}
		
		return $return;
	}
	
}