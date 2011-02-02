<?php

/**
 * @category Zest
 * @package Zest_Db
 * @subpackage Table
 */
class Zest_Db_Table_Select extends Zend_Db_Table_Select{
	
	/**
	 * @param string $type
	 * @param string $name
	 * @param string|array $cond
	 * @param string|array $cols
	 * @param string $schema
	 * @return Zest_Db_Table_Select
	 */
	public function joinUsing($type, $name, $cond, $cols = '*', $schema = null){
		if (empty($this->_parts[self::FROM])) {
			throw new Zend_Db_Select_Exception("You can only perform a joinUsing after specifying a FROM table");
		}

		$join  = $this->_adapter->quoteIdentifier(key($this->_parts[self::FROM]), true);
		$from  = $this->_adapter->quoteIdentifier($this->_uniqueCorrelation($name), true);

		if(is_array($cond)){
			list($cond1, $cond2) = $cond;
		}
		else{
			$cond1 = $cond;
			$cond2 = $cond;
		}
		
		$cond1 = $from . '.' . $cond1;
		$cond2 = $join . '.' . $cond2;
		$cond  = $cond1 . ' = ' . $cond2;

		return $this->_join($type, $name, $cond, $cols, $schema);
	}
	
	/**
	 * @param string|array $name
	 * @return string
	 */
	private function _uniqueCorrelation($name){
		if (is_array($name)) {
			$c = end($name);
		} else {
			// Extract just the last name of a qualified table name
			$dot = strrpos($name,'.');
			$c = ($dot === false) ? $name : substr($name, $dot+1);
		}
		for ($i = 2; array_key_exists($c, $this->_parts[self::FROM]); ++$i) {
			$c = $name . '_' . (string) $i;
		}
		return $c;
	}
	
}