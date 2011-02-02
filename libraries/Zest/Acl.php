<?php

/**
 * @category Zest
 * @package Zest_Acl
 */
class Zest_Acl extends Zend_Acl{
	
	/**
	 * @var Zest_Acl_Adapter_Abstract
	 */
	protected $_adapter = null;
	
	/**
	 * @return Zest_Acl_Adapter_Abstract
	 */
	public function getAdapter(){
		if(!$this->_adapter){
			$this->setAdapter('Zest_Acl_Adapter_File');
		}
		return $this->_adapter;
	}
	
	/**
	 * @param Zest_Acl_Adapter_Abstract|string $adapter
	 * @return Zest_Acl
	 */
	public function setAdapter($adapter){
		if(is_string($adapter)){
			$adapter = new $adapter($this);
		}
		if($adapter instanceof Zest_Acl_Adapter_Abstract){
			$this->_adapter = $adapter;
		}
		else{
			throw new Zest_Acl_Exception('L\'adaptateur doit être une instance de Zest_Acl_Adapter_Abstract.');
		}
		
		return $this;
	}
	
	/**
	 * @param Zend_Acl_Resource_Interface|string $resource
	 * @param Zend_Acl_Resource_Interface|string $parent
	 * @param string $idProperty
	 * @return Zest_Acl
	 */
	public function addResource($resource, $parent = null, $idProperty = null){
		if($resource instanceof Zend_Acl_Resource_Interface){
			$resource = $resource->getResourceId();
		}
		$resource = new Zest_Acl_Resource($resource, $this);
		if($idProperty){
			$resource->setIdProperty($idProperty);
		}
		
		if(!$this->has($resource)){
			parent::addResource($resource, $parent);	
		}
		
		return $this;
	}
	
	/**
	 * @param Zend_Acl_Role_Interface|string $role
	 * @param Zend_Acl_Role_Interface|string $parents
	 * @return Zest_Acl
	 */
	public function addRole($role, $parents = null){
		if($role instanceof Zend_Acl_Role_Interface){
			$role = $role->getRoleId();
		}
		$role = new Zest_Acl_Role($role, $this);
		
		if(!$this->_getRoleRegistry()->has($role)){
			parent::addRole($role, $parents);
		}
		
		return $this;
	}
	
	/**
	 * @param string $operation
	 * @param string $type
	 * @param Zend_Acl_Role_Interface|string|array $roles
	 * @param Zend_Acl_Resource_Interface|string|array $resources
	 * @param string|array $privileges
	 * @param Zend_Acl_Assert_Interface $assert
	 * @return Zest_Acl
	 */
	public function setRule($operation, $type, $roles = null, $resources = null, $privileges = null, Zend_Acl_Assert_Interface $assert = null){
		if(is_array($resources)){
			$privileges = (array) $privileges;
			
			foreach($resources as $resourceId => $values){
				if(is_string($resourceId)){
					$resource = $this->get($resourceId);
					
					$values = (array) $values;
					foreach($values as $value){
						$value = $this->_getResourceValue($resource, $value);
						if($privileges){
							foreach($privileges as $privilege){
								parent::setRule($operation, $type, $roles, $resource, $privilege.$value, $assert);
							}
						}
						else{
							parent::setRule($operation, $type, $roles, $resource, $value, $assert);
						}
					}
					unset($resources[$resourceId]);
				}
			}
		}

		if($resources){
			parent::setRule($operation, $type, $roles, $resources, $privileges, $assert);
		}
		
		return $this;
	}
	
	/**
	 * @param Zend_Acl_Role_Interface|string $role
	 * @param Zend_Acl_Resource_Interface|string|array $resource
	 * @param string $privilege
	 * @return boolean
	 */
	public function isAllowed($role = null, $resource = null, $privilege = null){
		if(is_array($resource)){
			$resource = array_pad($resource, 2, null);
			list($resource, $value) = $resource;
			
			$resource = $this->get($resource);
			$privilege .= $this->_getResourceValue($resource, $value);
		}
		
		return parent::isAllowed($role, $resource, $privilege);
	}
	
	/**
	 * @param Zest_Acl_Resource $resource
	 * @param object|array $value
	 * @return string
	 */
	protected function _getResourceValue(Zest_Acl_Resource $resource, $value){
		if(is_object($value) || is_array($value)){
			if(!$property = $resource->getIdProperty()){
				throw new Zest_Acl_Exception(sprintf('Aucune propriété identifiante défini sur la ressource "%s".', $resource->getResourceId()));
			}
			
			// clef public
			if(array_key_exists($property, $value)){
				if(is_object($value)){
					$value = $value->$property;
				}
				else if(is_array($value)){
					$value = $value[$property];
				}
			}
			else{
				// accesseur
				$method = 'get'.ucfirst($property);
				if(method_exists($value, $method)){
					$value = $value->$method();
				}
				else{
					throw new Zest_Acl_Exception(sprintf('La propriété "%s" est introuvable.', $property));
				}
			}
		}
		return '['.$value.']';
	}
	
	/**
	 * @return Zend_Acl_Role_Registry
	 */
	public function getRoleRegistry(){
		return $this->_getRoleRegistry();
	}
	
	/**
	 * @param Zend_Acl_Role_Registry $roleRegistry
	 * @param array $resources
	 * @param array $rules
	 * @return Zest_Acl
	 */
	public function init(Zend_Acl_Role_Registry $roleRegistry, array $resources, array $rules){
		$this->_roleRegistry = $roleRegistry;
		$this->_resources = $resources;
		$this->_rules = $rules;
		return $this;
	}
	
	/**
	 * @return Zest_Acl
	 */
	public function save(){
		$this->getAdapter()->save($this->_getRoleRegistry(), $this->_resources, $this->_rules);
		return $this;
	}
	
	/**
	 * @return Zest_Acl
	 */
	public function load(){
		$this->getAdapter()->load($this);
		return $this;
	}
	
}