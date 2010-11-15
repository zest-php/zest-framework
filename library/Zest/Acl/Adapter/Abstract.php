<?php

/**
 * @category Zest
 * @package Zest_Acl
 * @subpackage Adapter
 */
abstract class Zest_Acl_Adapter_Abstract{
	
	/**
	 * @var Zest_Acl
	 */
	protected $_acl = null;
	
	/**
	 * @param Zest_Acl $acl
	 * @return void
	 */
	public function __construct(Zest_Acl $acl){
		$this->_acl = $acl;
	}
	
	/**
	 * @param Zend_Acl_Role_Registry $roleRegistry
	 * @param array $resources
	 * @param array $rules
	 * @return Zest_Acl_Adapter_Abstract
	 */
	abstract public function save(Zend_Acl_Role_Registry $roleRegistry, array $resources, array $rules);
	
	/**
	 * @param Zest_Acl $acl
	 * @return Zest_Acl_Adapter_Abstract
	 */
	abstract public function load();
	
}