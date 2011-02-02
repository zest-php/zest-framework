<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage Engine
 */
abstract class Zest_View_Engine_Abstract{
	
	/**
	 * @var Zest_View
	 */
	protected $_view = null;
	
	/**
	 * @param Zest_View $view
	 * @return void
	 */
	public function __construct($view){
		$this->setView($view);
	}
	
	/**
	 * @param Zest_View $view
	 * @return void
	 */
	public function setView(Zest_View $view){
		$this->_view = $view;
	}
	
	/**
	 * @param string $name
	 * @return string
	 */
	abstract public function render($name);
	
	/**
	 * @param array $options
	 * @return Zest_View_Engine_Abstract
	 */
	abstract public function setOptions(array $options);
	
}