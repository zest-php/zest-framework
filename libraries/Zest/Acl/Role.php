<?php

/**
 * @category Zest
 * @package Zest_Acl
 */
class Zest_Acl_Role extends Zend_Acl_Role{
	
	/**
	 * @var Zest_Acl
	 */
	protected $_acl = null;
	
	/**
	 * @param string $roleId
	 * @param Zest_Acl $acl
	 * @return void
	 */
	public function __construct($roleId, Zest_Acl $acl){
		parent::__construct($roleId);
		$this->_acl = $acl;
	}
	
	/**
	 * @return array
	 */
	public function getParents(){
		$roles = $this->_acl->getRoleRegistry()->getRoles();
		if(isset($roles[$this->getRoleId()])){
			return $roles[$this->getRoleId()]['parents'];
		}
		return array();
	}
	
	/**
	 * @return array
	 */
	public function getChildren(){
		$roles = $this->_acl->getRoleRegistry()->getRoles();
		if(isset($roles[$this->getRoleId()])){
			return $roles[$this->getRoleId()]['children'];
		}
		return array();
	}
	
}