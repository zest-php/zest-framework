<?php

/**
 * @category Zest
 * @package Zest_Db
 * @subpackage Model
 */
class Zest_Db_Model_Adapter_Pdo_Mysql extends Zest_Db_Model_Adapter_Abstract{
	
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
//		// update, puis insert en cas d'exception
//		$return = false;
//		
//		try{
//			$return = $this->_insert($rowArray, $object);
//		}
//		catch(Zend_Db_Exception $e){
//			preg_match('/: ([0-9]+)/', $e->getMessage(), $matches);
//			$code = isset($matches[1]) ? intval($matches[1]) : null;
//			
//			// duplicate entry
//			if($code !== 1062){
//				throw $e;
//			}
//			$return = $this->_update($rowArray);
//		}
//		catch(Exception $e){
//			// erreur MySQL (Zend_Db_Statement_Exception) ou autres
//			throw $e;
//		}

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
	protected function _insert(array $rowArray, $object){
		$table = $this->_model->getDbTable();
		
		// insert
		$primary = $table->insert($rowArray);
		
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
	protected function _update(array $rowArray){
		$table = $this->_model->getDbTable();
		
		// création de la condition
		$intersectPrimary = $this->_model->getIntersectPrimary($rowArray);
		$where = $this->_getWhereQuery($table, $intersectPrimary, '=');
		
		if($where === 1){
			throw new Zest_Db_Exception(sprintf('Il n\'est pas autorisé de mettre à jour tous les tuples de la table "%s".', $table->info(Zest_Db_Table::NAME)));
		}
		else{
			// update
			return $table->update($rowArray, $where)>0;
		}
		
		return false;
	}

	/**
	 * @param Zest_Db_Model_Request $request
	 * @return boolean
	 */
	public function delete(Zest_Db_Model_Request $request){
		$table = $this->_model->getDbTable();
		
		// création de la condition
		$where = $this->_getWhereQuery($table, $request->toArray(), '=');
		
		if($where === 1){
			throw new Zest_Db_Exception(sprintf('Il n\'est pas autorisé de supprimer tous les tuples de la table "%s".', $table->info(Zest_Db_Table::NAME)));
		}
		else{
			return $table->delete($where)>0;
		}
		return false;
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return Zend_Db_Table_Select
	 */
	public function getDbSelect(Zest_Db_Model_Request $request){
		$table = $this->_model->getDbTable();
		
		$select = $table->select();
		
		// création de la condition
		$select->where($this->_getWhereQuery($table, $request->toArray(), '='));
		
		// remplacement du SELECT * par chaque nom de colonne
		$select->from($table->info(Zest_Db_Table::NAME), $table->info(Zest_Db_Table::COLS));
		
		return $select;
	}
	
	/**
	 * @param Zend_Db_Table_Select $select
	 * @param array $like
	 * @return void
	 */
	public function optionLike(Zend_Db_Table_Select $select, array $like){
		$select->where($this->_getWhereQuery($select->getTable(), $like, 'LIKE'));
	}
	
	/**
	 * quote("O'Reilly")
	 * quoteInto("SELECT * FROM bugs WHERE reported_by = ?", "O'Reilly")
	 * quoteIdentifier("order")
	 * 
	 * @param Zest_Db_Table $table
	 * @param array $arrayValues
	 * @param string $operator
	 * @return string
	 */
	protected function _getWhereQuery(Zest_Db_Table $table, array $arrayValues, $operator){
		$adapter = $table->getAdapter();
		$cols = $table->info(Zest_Db_Table::COLS);
		
		$andWhere = array();
		foreach($arrayValues as $col => $values){
			if(!in_array($col, $cols)){
				// @todo : peut être mettre un warning ou une exception ?
				continue;
			}
			
			$values = (array) $values;
			
			$expr = $adapter->quoteIdentifier($col, true).' '.$operator.' ?';
			$orWhere = array();
			foreach($values as $value){
				$orWhere[] = $adapter->quoteInto($expr, $value);
			}
			$orWhere = implode(' OR ', $orWhere);
			if($orWhere){
				$andWhere[] = '('.$orWhere.')';
			}
		}
		return $andWhere ? implode(' AND ', $andWhere) : 1;
	}

}