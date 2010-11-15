<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage Decorator
 */
class Zest_Form_Decorator_TdValue extends Zest_Form_Decorator_Abstract{
	
	/**
	 * @param string $content
	 * @return string
	 */
	public function render($content){
		$element = $this->getElement();
		if(!$element instanceof Zend_Form_Element){
			return $content;
		}

		$decorator = new Zest_Form_Decorator_Value();
		$decorator->setElement($element);
		$value = $decorator->render('');
		
		$htmlTag = new Zend_Form_Decorator_HtmlTag(array(
			'tag' => 'td',
			'id' => $this->getElementIdAttrib().'-value',
			'class' => 'form-value'
		));
		$htmlTag->setElement($element);
		
		switch($this->getPlacement()){
			case self::PREPEND:
				return $htmlTag->render($value).$content;
			case self::APPEND:
				return $content.$htmlTag->render($value);
		}
		
		return $content;
	}
	
}