<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage Decorator
 */
abstract class Zest_Form_Decorator_Abstract extends Zend_Form_Decorator_Abstract{
	
	/**
	 * c.f. Zend_Form_Decorator_ViewHelper::getElementAttribs
	 * 
	 * @return string
	 */
	public function getElementIdAttrib(){
		$id = $this->getElement()->getFullyQualifiedName();
		$id = str_replace(array('[', ']'), '-', $id);
		$id = trim($id, '-');
		$id = preg_replace('/-+/', '-', $id);
		return $id;
	}
	
}