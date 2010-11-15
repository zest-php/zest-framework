<?php


/**
 * @category Zest
 * @package Zest_Form
 * @subpackage Decorator
 */
class Zest_Form_Decorator_TrLabelValue extends Zest_Form_Decorator_Abstract{
	
	/**
	 * @param string $content
	 * @return string
	 */
	public function render($content){
		$element = $this->getElement();
		if(!$element instanceof Zend_Form_Element){
			return $content;
		}
		if($element instanceof Zend_Form_Element_Submit || $element instanceof Zend_Form_Element_Image || $element instanceof Zend_Form_Element_Captcha){
			return '';
		}
		if($element instanceof Zend_Form_Element_File){
			$content = '';
		}
		
		$tdElement = new Zest_Form_Decorator_TdValue();
		$tdElement->setElement($element);
		$content = $tdElement->render($content);
		
		$tdLabel = new Zest_Form_Decorator_TdLabel(array(
			'placement' => Zend_Form_Decorator_Abstract::PREPEND,
			'disableLabelDecorator' => true
		));
		$tdLabel->setElement($element);
		$content = $tdLabel->render($content);
		$content = preg_replace('/ for="[^"]+"/', '', $content);
		
		$options = array('tag' => 'tr');
		if($element->getErrors()){
			$options['class'] = 'form-error';
		}
		$htmlTag = new Zend_Form_Decorator_HtmlTag($options);
		$htmlTag->setElement($element);
		$content = $htmlTag->render($content);
		
		return $content;
	}
	
}