<?php

/**
 * @category Zest
 * @package Zest_Layout
 */
class Zest_Layout extends Zend_Layout{

	/**
	 * @var array
	 */
	protected $_layoutPath = array();
	
	/**
	 * @var string
	 */
	protected $_currentLayout = null;
	
	/**
	 * @var array
	 */
	protected $_parents = array();
	
	/**
	 * @param string|array|Zend_Config $options
	 * @return Zest_Layout
	 */
	public static function startMvc($options = null){
		if(is_null(self::$_mvcInstance)){
			self::$_mvcInstance = new self($options, true);
		}
		return parent::startMvc($options);
	}
	
	/**
	 * @param string $path
	 * @param string $module
	 * @return Zest_Layout
	 */
	public function setLayoutPath($path, $module = null){
		if(is_null($module)){
			parent::setLayoutPath($path);
			$module = Zest_Controller_Front::getInstance()->getDefaultModule();
		}
		$this->_layoutPath[$module] = $path;
		return $this;
	}
	
	/**
	 * @param string $method
	 * @param array $args
	 * @return Zest_Layout
	 */
	public function __call($method, $args){
		if($method == 'extends'){
			return call_user_func_array(array($this, '_extends'), $args);
		}
		throw new Zest_Layout_Exception(sprintf('La méthode "%s" n\'existe pas.', $method));
	}
	
	/**
	 * @param string $name
	 * @param string $module
	 * @return Zest_Layout
	 */
	protected function _extends($name, $module = null){
		if(!$this->_currentLayout){
			throw new Zest_Layout_Exception('La méthode "extend" doit être appelée dans un render.');
		}
		
		if(is_null($module)){
			$module = Zest_Controller_Front::getInstance()->getDefaultModule();
		}
		if(!isset($this->_layoutPath[$module])){
			throw new Zest_Layout_Exception(sprintf('Aucun layoutPath n\'a été renseigné pour le module "%s".', $module));
		}
		
		$this->_parents[$this->_currentLayout] = array('layoutPath' => $this->_layoutPath[$module], 'layout' => $name);
		return $this;
	}
	
	/**
	 * @param string $name
	 * @return string
	 */
	public function render($name = null){
		if(is_null($name)){
			$name = $this->getLayout();
		}
		$this->_currentLayout = $name;
		$this->_parents[$this->_currentLayout] = null;
		
		$render = parent::render($name);
		
		if($this->_parents[$this->_currentLayout]){
			$this->{$this->_contentKey} = $render;
			$this->setViewScriptPath($this->_parents[$this->_currentLayout]['layoutPath']);
			$render = $this->render($this->_parents[$this->_currentLayout]['layout']);
		}
		
		return $render;
	}
	
}