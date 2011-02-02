<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage Decorator
 */
class Zest_Form_Decorator_TableElements extends Zest_Form_Decorator_Abstract{
	
	/**
	 * @param string $content
	 * @return string
	 */
	public function render($content){
		$form = $this->getElement();
		if( !$form instanceof Zend_Form && !$form instanceof Zend_Form_DisplayGroup ){
			return $content;
		}
		
		// on sauvegarde les éléments du formulaire
		$elements = $form->getElements();
		if(!$elements) return $content;
		
		// on supprime les element cachés (type="hidden")
		foreach($elements as $element){
			if( $element instanceof Zend_Form_Element_Hidden || $element instanceof Zend_Form_Element_Hash ){
				$form->removeElement($element->getName());
			}
		}
		
		// on génère le rendu à partir du décorateur Zend_Form_Decorator_FormElements
		$formElements = new Zend_Form_Decorator_FormElements();
		$formElements->setElement($form);
		$content = $formElements->render($content);
		
		// on génère le rendu à partir du décorateur Zend_Form_Decorator_HtmlTag
		$htmlTag = new Zend_Form_Decorator_HtmlTag(array('tag' => 'table', 'cellpadding' => 0, 'cellspacing' => 0));
		$htmlTag->setElement($form);
		$content = $htmlTag->render($content);
		
		// on génère le rendu des éléments cachés
		foreach($elements as $element){
			if( $element instanceof Zend_Form_Element_Hidden || $element instanceof Zend_Form_Element_Hash ){
				$element->setDecorators(array(array('decorator' => 'viewHelper')));
				$content = $element->render().$content;
			}
		}
		
		// on restaure les éléments du formulaire
		$form->setElements($elements);
		
		return $content;
	}
	
}