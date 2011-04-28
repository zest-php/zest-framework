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
	
	/**
	 * @param string $ident
	 * @param string $alias
	 * @param boolean $auto
	 * @param string $as
	 * @return string
	 */
	protected function _quoteIdentifierAs($ident, $alias = null, $auto = false, $as = ' AS '){
		if(is_null($alias) || $ident == $alias){
			return parent::_quoteIdentifierAs($ident, $alias, $auto, $as);
		}
		/**
		 * corrige un problème quand on fait un select sur plusieurs tables
		 * exemple :
		 * 		SELECT "n"."id" FROM "directory" AS "n"
		 * 
		 * 		grâce à ce patch, il est possible de faire
		 * 		SELECT "n"."id" AS "id" FROM "directory" AS "n"
		 */
		$newAlias = '~'.$alias;
		$quoted = parent::_quoteIdentifierAs($ident, $newAlias, $auto, $as);
		return str_replace($newAlias, $alias, $quoted);
	}
	
}