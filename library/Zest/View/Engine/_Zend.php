<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage Engine
 */
class Zest_View_Engine_Zend extends Zest_View_Engine_Abstract{
	
	/**
	 * @var Zend_View
	 */
	protected $_zend = null;
	
	/**
	 * @param Zest_View $view
	 * @return void
	 */
	public function __construct(Zest_View $view){
		parent::__construct($view);
		$this->_zend = new Zend_View();
	}
	
	/**
	 * @param string $scriptPath
	 * @return string
	 */
	public function render($scriptPath){
		$this->_zend->assign($this->_view->getVars());

		$this->_zend->setScriptPath(dirname($scriptPath));
		$content = $this->_zend->render(basename($scriptPath));
		
		return $content;
	}
	
	/**
	 * @param array $options
	 * @return Zest_View_Engine_Zend
	 */
	public function setOptions(array $options){
		return $this;
	}
	
	/**
	 * @return Zest_View_Engine_Zend
	 */
	public function clearVars(){
		$this->_zend->clearVars();
		return $this;
	}
	
	/**
	 * @return void
	 */
	public function __clone(){
		$this->_zend = clone $this->_zend;
	}
	
}