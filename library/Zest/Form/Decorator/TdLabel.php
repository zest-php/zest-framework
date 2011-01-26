<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage Decorator
 */
class Zest_Form_Decorator_TdLabel extends Zest_Form_Decorator_Abstract{
	
	/**
	 * @param string $content
	 * @return string
	 */
	public function render($content){
		$element = $this->getElement();
		if( !$element instanceof Zend_Form_Element || !$view = $element->getView() ){
			return $content;
		}
		
		$label = trim($element->getLabel());
		if( !$label || $element instanceof Zend_Form_Element_Submit || $element instanceof Zend_Form_Element_Image ){
			$label = '&nbsp;';
		}
		else{
			$disableLabelDecorator = $this->getOption('disableLabelDecorator');
			
			if(is_null($disableLabelDecorator) || !$disableLabelDecorator){
				$decorator = new Zest_Form_Decorator_Label();
				$decorator->setElement($element);
				$label = $decorator->render('');
			}
			else if(!( $element instanceof Zend_Form_Element_Submit || $element instanceof Zend_Form_Element_Image )){
				$format = $element->isRequired() ? Zest_Form_Decorator_Label::getLabelFormatRequired() : Zest_Form_Decorator_Label::getLabelFormatNotRequired();
				if($format){
					$label = sprintf($format, $element->getLabel());
				}
			}
		}
		
		$htmlTag = new Zend_Form_Decorator_HtmlTag(array(
			'tag' => 'td',
			'id' => $this->getElementIdAttrib().'-label',
			'class' => 'form-label'
		));
		$htmlTag->setElement($element);
		
		switch($this->getPlacement()){
			case self::PREPEND:
				return $htmlTag->render($label).$content;
			case self::APPEND:
				return $content.$htmlTag->render($label);
		}
		
		return $content;
	}
	
}