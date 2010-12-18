<?php

/**
 * @category Zest
 * @package Zest_Db
 * @subpackage Adapter
 */
class Zest_Db_Adapter_Pdo_Sqlite extends Zend_Db_Adapter_Pdo_Sqlite{
	
	/**
	 * @param mixed $table
	 * @param array $bind
	 * @return integer
	 */
	public function insertOrReplace($table, array $bind){
		// extract and quote col names from the array keys
		$cols = array();
		$vals = array();
		foreach($bind as $col => $val){
			$cols[] = $this->quoteIdentifier($col, true);
			if($val instanceof Zend_Db_Expr){
				$vals[] = $val->__toString();
				unset($bind[$col]);
			}
			else{
				$vals[] = '?';
			}
		}

		// build the statement
		$sql = "INSERT OR REPLACE INTO "
			 . $this->quoteIdentifier($table, true)
			 . ' (' . implode(', ', $cols) . ') '
			 . 'VALUES (' . implode(', ', $vals) . ')';
			 
		// execute the statement and return the number of affected rows
		$stmt = $this->query($sql, array_values($bind));
		$result = $stmt->rowCount();
		return $result;		
	}
}