<?php


/**
 * @category Zest
 * @package Zest_Form
 * @subpackage Decorator
 */
class Zest_Form_Decorator_TrLabelElement extends Zest_Form_Decorator_Abstract{
	
	/**
	 * @var array
	 */
	protected $_elementRenderDecorator = null;
	
	/**
	 * @param Zend_Form_Decorator_Abstract $decorator
	 * @return Zest_Form_Decorator_TrLabelElement
	 */
	public function setElementRenderDecorator(Zend_Form_Decorator_Abstract $decorator){
		$this->_elementRenderDecorator = $decorator;
		return $this;
	}
	
	/**
	 * @return Zend_Form_Decorator_Abstract
	 */
	public function getElementRenderDecorator(){
		if(is_null($this->_elementRenderDecorator)){
			$viewHelper = $this->getElement()->getPluginLoader(Zend_Form_Element::DECORATOR)->load('viewHelper');
			$this->_elementRenderDecorator = new $viewHelper();
		}
		return $this->_elementRenderDecorator;
	}
	
	/**
	 * @param string $content
	 * @return string
	 */
	public function render($content){
		$element = $this->getElement();
		if(!$element instanceof Zend_Form_Element){
			return $content;
		}
		
		if(!$element instanceof Zend_Form_Element_Captcha && !$element instanceof Zend_Form_Element_File){
			$viewHelper = $this->getElementRenderDecorator();
			$viewHelper->setElement($element);
			$content = $viewHelper->render($content);
			if( $element instanceof Zend_Form_Element_Hidden || $element instanceof Zend_Form_Element_Hash ){
				return $content;
			}
		}
		
		$tdElement = new Zest_Form_Decorator_TdElement();
		$tdElement->setElement($element);
		$content = $tdElement->render($content);
		
		$tdLabel = new Zest_Form_Decorator_TdLabel(array('placement' => Zend_Form_Decorator_Abstract::PREPEND));
		$tdLabel->setElement($element);
		$content = $tdLabel->render($content);
		
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