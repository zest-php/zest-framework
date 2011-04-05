<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage Decorator
 */
class Zest_Form_Decorator_Value extends Zend_Form_Decorator_Abstract{
	
	/**
	 * @var string
	 */
	protected $_separator = ', ';
	
	/**
	 * @param string $content
	 * @return string
	 */
	public function render($content){
		$element = $this->getElement();
		$placement = $this->getPlacement();
	
		$value = $element->getValue();
		
		if(method_exists($element, 'getMultiOptions')){
			$multiOptions = $element->getMultiOptions();
			$value = (array) $value;
			$value = array_intersect_key($multiOptions, array_flip($value));
		}
		
		if(is_array($value)){
			$value = implode($this->getSeparator(), $value);
		}
		
		if(!$value){
			$value = '&nbsp;';
		}
		
		switch ($placement) {
			case self::APPEND:
				return $content . $value;
			case self::PREPEND:
				return $value . $content;
		}
		
		return $content;
	}
	
}