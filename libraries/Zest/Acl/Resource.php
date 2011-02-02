<?php

/**
 * @category Zest
 * @package Zest_Acl
 */
class Zest_Acl_Resource extends Zend_Acl_Resource{
	
	/**
	 * @var Zest_Acl
	 */
	protected $_acl = null;
	
	/**
	 * @var string
	 */
	protected $_idProperty = null;
	
	/**
	 * @param string $resourceId
	 * @param Zest_Acl $acl
	 * @return void
	 */
	public function __construct($resourceId, Zest_Acl $acl){
		parent::__construct($resourceId);
		$this->_acl = $acl;
	}
	
	/**
	 * @param string $idProperty
	 * @return Zest_Acl_Resource
	 */
	public function setIdProperty($idProperty){
		$this->_idProperty = $idProperty;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getIdProperty(){
		return $this->_idProperty;
	}
	
}