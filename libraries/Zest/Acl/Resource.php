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
	protected $_className = null;
	
	/**
	 * @var string
	 */
	protected $_primaryAttribute = null;
	
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
	 * @param array $options
	 * @return Zest_Acl_Resource
	 */
	public function setOptions(array $options){
		foreach($options as $key => $value){
			$method = ucwords(str_replace('_', ' ', $key));
			$method = 'set'.str_replace(' ', '', $method);
			if(method_exists($this, $method)){
				$this->$method($value);
			}
		}
		return $this;
	}
	
	/**
	 * @param string $className
	 * @return Zest_Acl_Resource
	 */
	public function setClassName($className){
		$this->_className = $className;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getClassName(){
		return $this->_className;
	}
	
	/**
	 * @param string $primaryAttribute
	 * @return Zest_Acl_Resource
	 */
	public function setPrimaryAttribute($primaryAttribute){
		$this->_primaryAttribute = $primaryAttribute;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getPrimaryAttribute(){
		return $this->_primaryAttribute;
	}
	
}