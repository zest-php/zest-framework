<?php

/**
 * @category Zest
 * @package Zest_Db
 * @subpackage Model
 */
class Zest_Db_Model_NestedSet{
	
	/**
	 * @var array
	 */
	protected $_nested = array();
	
	/**
	 * @var string
	 */
	const COL_SEPARATOR = '~';
	
	/**
	 * @return void
	 */
	public function __construct(){
	}
	
	/**
	 * @var Zest_Db_Model_Nested $nested
	 * @return Zest_Db_Model_NestedSet
	 */
	public function addNested($nested){
		$this->_nested[] = $nested;
	}
	
	/**
	 * @return boolean
	 */
	public function hasNested(){
		return count($this->_nested) != 0;
	}
	
	/**
	 * @var Zest_Db_Model $localModel
	 * @var Zend_Db_Table_Select $select
	 * @return Zest_Db_Model_NestedSet
	 */
	public function alterDbSelect(Zest_Db_Model $localModel, Zend_Db_Table_Select $select){
//		$localTableName = $localModel->getDbTable()->info(Zest_Db_Table::NAME);
//		$dbAdapter = $localModel->getDbAdapter();
		$select->setIntegrityCheck(false);
		
		foreach($this->_nested as $nested){
			$foreignTable = $nested->getForeignModel()->getDbTable();
			$foreignTableName = $foreignTable->info(Zest_Db_Table::NAME);
			$foreignTableCols = $foreignTable->info(Zest_Db_Table::COLS);
			
//			$cond1 = $dbAdapter->quoteIdentifier($foreignTableName).'.'.$dbAdapter->quoteIdentifier($nested->getForeignCol());
//			$cond2 = $dbAdapter->quoteIdentifier($localTableName).'.'.$dbAdapter->quoteIdentifier($nested->getLocalCol());
			
			foreach($foreignTableCols as $key => $col){
				unset($foreignTableCols[$key]);
				$foreignTableCols[$foreignTableName.self::COL_SEPARATOR.$col] = $col;
			}
			
			$select->joinUsing(Zest_Db_Table_Select::LEFT_JOIN, $foreignTableName, array($nested->getForeignCol(), $nested->getLocalCol()), $foreignTableCols);
//			$select->joinLeft($foreignTableName, $cond1.' = '.$cond2, $foreignTableCols);
		}
				
//		echo $select->assemble();
//		exit;
	}
	
	/**
	 * @param array $arrayObject
	 * @param Zest_Db_Model $model
	 * @return array
	 */
	public function alterObjects($arrayObjects, Zest_Db_Model $model){
		$registry = array();
		
		$tableName = $model->getDbTable()->info(Zest_Db_Table::NAME);
		
		foreach($arrayObjects as $key => $objects){
			// tableau qui permet de passer les doublons créés par les jointures
			$joinDuplicate = array();
		
			// objet final
			$newObject = null;
			
			// clef servant aux références objet dans le tableau $registry
			$registryKey = null;
			
			/**
			 * @todo
			 * 		1 : gérer les références de $child
			 * 		2 : gérer les références de $arrayObjects (ex : mes vidéos)
			 */
			
			foreach($objects as $object){
				
				// génération de la clef de registre
				if(!$registryKey){
					$registryKey = $tableName.self::COL_SEPARATOR.implode(Zest_Db_Model::GETARRAY_KEY_SEPARATOR, $model->getIntersectPrimary($object));
				}
				
				// si l'objet est présent dans le registre, on y fait référence
				if(isset($registry[$registryKey])){
					$newObject = $registry[$registryKey];
				}
					
				// initialisation de l'objet final
				if(!$newObject){
					$newObject = $model->toObject($object);
				}
				
				// parcours des imbrications
				foreach($this->_nested as $nested){
					$foreignModel = $nested->getForeignModel();
					$foreignTableName = $foreignModel->getDbTable()->info(Zest_Db_Table::NAME);
					$foreignTableCols = $foreignModel->getDbTable()->info(Zest_Db_Table::COLS);
					
					// récupération des propriétés étrangères
					$child = array();
					foreach($foreignTableCols as $col){
						$property = $foreignTableName.self::COL_SEPARATOR.$col;
						if(isset($object[$property])){
							$child[$col] = $object[$property];
						}
						unset($object[$property], $newObject->$property);
					}
					
					// génération de la clef de registre
					$foreignRegistryKey = $foreignTableName.self::COL_SEPARATOR.implode(Zest_Db_Model::GETARRAY_KEY_SEPARATOR, $foreignModel->getIntersectPrimary($child));
					
					$property = $nested->getProperty();
					
					// gestion des doublons au niveau de l'objet final
					if(!isset($joinDuplicate[$property])){
						$joinDuplicate[$property] = array();
					}
					
					// si l'objet est déjà présent, on ne l'ajoute pas
					if(in_array($foreignRegistryKey, $joinDuplicate[$property])){
						continue;
					}
					
					// ajout dans le tableau de duplication
					$joinDuplicate[$property][] = $foreignRegistryKey;
					
					if(isset($registry[$foreignRegistryKey])){
						// si l'objet est présent dans le registre, on y fait référence
						$child = $registry[$foreignRegistryKey];
					}
					else if($child){
						// ajout dans le registre
						$child = $registry[$foreignRegistryKey] = $foreignModel->toObject($child);
					}
					
					// ajout du tableau étranger dans l'objet final
					switch($nested->getMode()){
						case Zest_Db_Model_Nested::MODE_MANY:
							// initialisation de la propriété pour que ce soit dans tous les cas un tableau
							if(!isset($newObject->$property)){
								$newObject->$property = array();
							}
							if($child){
								$newObject->append($property, $child);
							}
							break;
						case Zest_Db_Model_Nested::MODE_ONE:
							if(!isset($newObject->$property)){
								$newObject->$property = $child ? $child : null;
							}
							break;
						case Zest_Db_Model_Nested::MODE_ALSO:
							if($child){
								$newObject->setData($child);
							}
							break;
					}
				}
				
			}
			
			// ajout dans le registre et remplacement dans la tableau retourné
			$registry[$registryKey] = $arrayObjects[$key] = $newObject->refreshClean();
		}
		
		return $arrayObjects;
	}
	
}