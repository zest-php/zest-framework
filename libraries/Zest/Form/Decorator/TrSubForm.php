<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage Decorator
 */
class Zest_Form_Decorator_TrSubForm extends Zest_Form_Decorator_Abstract{
	
	/**
	 * @param string $content
	 * @return string
	 */
	public function render($content){
		$element = $this->getElement();
		if(!$element instanceof Zend_Form){
			return $content;
		}
		
		$htmlTag = new Zend_Form_Decorator_HtmlTag(array(
			'tag' => 'td',
			'colspan' => 2,
			'class' => 'form-subform'
		));
		$htmlTag->setElement($element);
		$content = $htmlTag->render($content);
		
		$htmlTag = new Zend_Form_Decorator_HtmlTag(array('tag' => 'tr'));
		$htmlTag->setElement($element);
		$content = $htmlTag->render($content);
		
		return $content;
	}
	
}