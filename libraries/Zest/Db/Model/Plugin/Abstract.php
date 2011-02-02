<?php

/**
 * @category Zest
 * @package Zest_Db
 * @subpackage Model
 */
abstract class Zest_Db_Model_Plugin_Abstract{
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return void
	 */
	public function preCreate(Zest_Db_Model_Request $request){
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return void
	 */
	public function postCreate(Zest_Db_Model_Request $request){
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return void
	 */
	public function preSave(Zest_Db_Model_Request $request){
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return void
	 */
	public function postSave(Zest_Db_Model_Request $request){
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return void
	 */
	public function preGet(Zest_Db_Model_Request $request){
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return void
	 */
	public function postGet(Zest_Db_Model_Request $request){
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return void
	 */
	public function preGetArray(Zest_Db_Model_Request $request){
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return void
	 */
	public function postGetArray(Zest_Db_Model_Request $request){
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return void
	 */
	public function preDelete(Zest_Db_Model_Request $request){
	}
	
	/**
	 * @param Zest_Db_Model_Request $request
	 * @return void
	 */
	public function postDelete(Zest_Db_Model_Request $request){
	}
	
}