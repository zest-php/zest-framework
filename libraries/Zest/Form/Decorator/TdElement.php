<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage Decorator
 */
class Zest_Form_Decorator_TdElement extends Zest_Form_Decorator_Abstract{
	
	/**
	 * @param string $content
	 * @return string
	 */
	public function render($content){
		$element = $this->getElement();
		if(!$element instanceof Zend_Form_Element){
			return $content;
		}
		
		$htmlTag = new Zend_Form_Decorator_HtmlTag(array(
			'tag' => 'td',
			'id' => $this->getElementIdAttrib().'-element',
			'class' => 'form-element'
		));
		$htmlTag->setElement($element);
		$content = $htmlTag->render($content);
		
		return $content;
	}
	
}