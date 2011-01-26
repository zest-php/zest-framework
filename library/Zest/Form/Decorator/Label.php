<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage Decorator
 */
class Zest_Form_Decorator_Label extends Zend_Form_Decorator_Label{
	
	/**
	 * @var string
	 */
	protected static $_labelFormatNotRequired = null;
	
	/**
	 * @var string
	 */
	protected static $_labelFormatRequired = null;
	
	/**
	 * @return string
	 */
	public static function getLabelFormatNotRequired(){
		return self::$_labelFormatNotRequired;
	}
	
	/**
	 * @param string $labelFormatRequired
	 * @return void
	 */
	public static function setLabelFormatNotRequired($labelFormatNotRequired){
		self::$_labelFormatNotRequired = $labelFormatNotRequired;
	}
	
	/**
	 * @return string
	 */
	public static function getLabelFormatRequired(){
		return self::$_labelFormatRequired;
	}
	
	/**
	 * @param string $labelFormatRequired
	 * @return void
	 */
	public static function setLabelFormatRequired($labelFormatRequired){
		self::$_labelFormatRequired = $labelFormatRequired;
	}
	
	/**
	 * @param string $content
	 * @return string
	 */
	public function render($content){
		$element = $this->getElement();
		
		// label format
		if(!( $element instanceof Zend_Form_Element_Submit || $element instanceof Zend_Form_Element_Image )){
			$format = $element->isRequired() ? self::$_labelFormatRequired : self::$_labelFormatNotRequired;
			if($format){
				$element->setLabel(sprintf($format, $element->getLabel()));
			}
		}
		
		// render
		$content = parent::render($content);
		$element = $this->getElement();
		if(
			$element instanceof Zend_Form_Element_MultiCheckbox
			|| $element instanceof Zend_Form_Element_Radio
			|| $element instanceof Zend_Form_Element_Captcha
			||($element instanceof Zend_Form_Element_File && $element->isArray())
		){
			$content = preg_replace('/ for="[^"]+"/', '', $content);
		}
		return $content;
	}
	
}