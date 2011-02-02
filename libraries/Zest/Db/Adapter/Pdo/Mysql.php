<?php

/**
 * @category Zest
 * @package Zest_Db
 * @subpackage Adapter
 */
class Zest_Db_Adapter_Pdo_Mysql extends Zend_Db_Adapter_Pdo_Mysql{
	
	/**
	 * @param mixed $table
	 * @param array $bind
	 * @return integer
	 */
	public function insertOrUpdate($table, array $bind){
		// extract and quote col names from the array keys
		$cols = array();
		$vals = array();
		$onDuplicateKey = array();
		foreach($bind as $col => $val){
			$quoteCol = $this->quoteIdentifier($col, true);
			$quoteVal = $this->quote($val);
			
			$cols[] = $quoteCol;
			if($val instanceof Zend_Db_Expr){
				$vals[] = $val->__toString();
				unset($bind[$col]);
			}
			else{
				$onDuplicateKey[] = $quoteCol.'='.$quoteVal;
				$vals[] = $quoteVal;
			}
		}

		// build the statement
		$sql = "INSERT INTO "
			 . $this->quoteIdentifier($table, true)
			 . ' (' . implode(', ', $cols) . ') '
			 . 'VALUES (' . implode(', ', $vals) . ') '
			 . 'ON DUPLICATE KEY UPDATE '.implode(', ', $onDuplicateKey);
			 
		// execute the statement and return the number of affected rows
		$stmt = $this->query($sql);
		$result = $stmt->rowCount();
		return $result;		
	}
	
}