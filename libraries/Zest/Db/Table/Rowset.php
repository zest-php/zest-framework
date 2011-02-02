<?php

/**
 * @category Zest
 * @package Zest_Db
 * @subpackage Table
 */
class Zest_Db_Table_Rowset extends Zend_Db_Table_Rowset{
	
	/**
	 * @return array
	 */
	public function toArray(){
		return $this->_data;
	}
	
}