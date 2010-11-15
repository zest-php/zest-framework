<?php

/**
 * @category Zest
 * @package Zest_Controller
 * @subpackage Router
 */
class Zest_Controller_Router_Rewrite extends Zend_Controller_Router_Rewrite{
	
	/**
	 * @var array
	 */
	protected $_filteredParams = array();
	
	/**
	 * @var string
	 */
	protected $_paramsPrefix = null;
	
	/**
	 * @param array $paramName
	 * @return Zest_Controller_Router_Rewrite
	 */
	public function addFilteredParams(array $paramName){
		$this->_filteredParams = array_merge($this->_filteredParams, $paramName);
		$this->_filteredParams = array_unique($this->_filteredParams);
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getParamsPrefix(){
		return $this->_paramsPrefix;
	}
	
	/**
	 * @param string $prefix
	 * @return Zest_Controller_Router_Rewrite
	 */
	public function setParamsPrefix($prefix){
		$this->_paramsPrefix = $prefix;
		return $this;
	}
	
	/**
	 * @param string $name
	 * @param mixed $value
	 * @return Zest_Controller_Router_Rewrite
	 */
	public function setGlobalParam($name, $value){
		if($this->_paramsPrefix){
			$name = $this->_paramsPrefix.$name;
		}
		parent::setGlobalParam($name, $value);
		return $this;
	}
	
	/**
	 * @param array $userParams
	 * @param string $name
	 * @param boolean $reset
	 * @param boolean $encode
	 * @return string
	 */
	public function assemble($userParams, $name = null, $reset = false, $encode = true){
		if($this->_filteredParams){
			$filter = new Zest_Filter_Url();
			foreach($this->_filteredParams as $paramName){
				if(isset($userParams[$paramName])){
					$userParams[$paramName] = $filter->filter($userParams[$paramName]);
				}
			}
		}
		if($this->_paramsPrefix){
			foreach($userParams as $key => $value){
				unset($userParams[$key]);
				$userParams[$this->_paramsPrefix.$key] = $value;
			}
		}
		return parent::assemble($userParams, $name, $reset, $encode);
	}
	
}