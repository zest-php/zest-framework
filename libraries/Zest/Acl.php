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
	 * @var boolean
	 */
	protected $_throwExceptions = false;
	
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
	 * @param array $options
	 * @return Zest_Acl
	 */
	public function addResource($resource, $parent = null, array $options = array()){
		if(is_string($resource)){
			$resource = new Zest_Acl_Resource($resource, $this);
		}
		
		if($resource instanceof Zest_Acl_Resource){
			$resource->setOptions($options);
		}
		
		if(!$this->has($resource) || $this->_throwExceptions){
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
		if(is_string($role)){
			$role = new Zest_Acl_Role($role, $this);
		}
		
		if(!$this->_getRoleRegistry()->has($role) || $this->_throwExceptions){
			parent::addRole($role, $parents);
		}
		
		return $this;
	}
	
	/**
	 * @param string $operation
	 * @param string $type
	 * @param Zend_Acl_Role_Interface|string|array $roles
	 * @param Zend_Acl_Resource_Interface|string|object $resources
	 * @param string|array $privileges
	 * @param Zend_Acl_Assert_Interface $assert
	 * @return Zest_Acl
	 */
	public function setRule($operation, $type, $roles = null, $resources = null, $privileges = null, Zend_Acl_Assert_Interface $assert = null){
		if(!is_array($resources)) $resources = array($resources);
		
		// si les ressources fournis sont directement des objets, on détecte la resource associée pour chaque objet
		foreach($resources as $key => $object){
			if(is_object($object) && !($object instanceof Zend_Acl_Resource_Interface)){
				if($resource = $this->_getResourceFromObject($object)){
					unset($resources[$key]);
					$resources[$resource->getResourceId()] = $object;
				}
				else{
					throw new Zest_Acl_Exception(sprintf('Aucune ressource attachée à la classe "%s".', get_class($object)));
				}
			}
		}
		
		// gère les tableaux de ressource sous la forme array(resourceId => values)
		$privileges = (array) $privileges;
		foreach($resources as $resourceId => $values){
			if(is_string($resourceId)){
				$resource = $this->get($resourceId);
				
				if(!$resource instanceof Zest_Acl_Resource){
					throw new Zest_Acl_Exception('Pour utiliser la syntaxe array(resourceId => values) la ressource doit être une instance de Zest_Acl_Resource.');
				}
				
				if(!is_array($values)) $values = array($values);
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
		
		// s'il reste des ressources dans le tableau (qui ont été indexées de manière naturelle : sans clef associative)
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
		if(is_object($resource) && !($resource instanceof Zend_Acl_Resource_Interface)){
			$object = $resource;
			$resource = $this->_getResourceFromObject($object);
			if(is_null($resource)){
				throw new Zest_Acl_Exception(sprintf('Aucune ressource attachée à la classe "%s".', get_class($object)));
			}
			$resource = array($resource->getResourceId(), $object);
		}
		
		if(is_array($resource)){
			if(count($resource) != 2){
				throw new Zest_Acl_Exception('Le paramètre $resource doit contenir deux cases s\'il est utilisé comme un tableau (resourceId, value).');
			}
			
			list($resource, $value) = $resource;
			$resource = $this->get($resource);
			
			if(!$resource instanceof Zest_Acl_Resource){
				throw new Zest_Acl_Exception('Pour utiliser la syntaxe array(resourceId, value) la ressource doit être une instance de Zest_Acl_Resource.');
			}
			$privilege .= $this->_getResourceValue($resource, $value);
		}
		
		try{
			return parent::isAllowed($role, $resource, $privilege);
		}
		catch(Zend_Exception $e){
			if($this->_throwExceptions){
				throw $e;
			}
			return false;
		}
	}
	
	/**
	 * @param object $object
	 * @return Zest_Acl_Resource
	 */
	protected function _getResourceFromObject($object){
		foreach($this->_resources as $resource){
			$resource = $resource['instance'];
			if($resource instanceof Zest_Acl_Resource && $className = $resource->getClassName()){
				if($object instanceof $className){
					return $resource;
				}
			}
		}
		return null;
	}
	
	/**
	 * @param Zest_Acl_Resource $resource
	 * @param object|array $value
	 * @return string
	 */
	protected function _getResourceValue(Zest_Acl_Resource $resource, $value){
		if(is_object($value) || is_array($value)){
			if(!$property = $resource->getPrimaryAttribute()){
				throw new Zest_Acl_Exception(sprintf('L\'attribut "primary_attribute" sur la ressource "%s" n\'est pas défini.', $resource->getResourceId()));
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