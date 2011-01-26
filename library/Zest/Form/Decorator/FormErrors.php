<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage Decorator
 */
class Zest_Form_Decorator_FormErrors extends Zest_Form_Decorator_Abstract{
	
	/**
	 * @param array $array
	 * @return array
	 */
	protected function flatten(array $array){
		$return = array();
		foreach($array as $field => $sub){
			if(is_array($sub)){
				$sub = $this->flatten($sub);
			}
			$return = array_merge($return, (array) $sub);
		}
		return $return;
	}
	
	/**
	 * @param string $content
	 * @return string
	 */
	public function render($content){
		$form = $this->getElement();
		if( !$form instanceof Zend_Form || !$view = $form->getView() ){
			return $content;
		}
		
		// on récupère les erreurs du formulaire
		$errors = array_unique($this->flatten($form->getMessages()));
		if(!$errors){
			return $content;
		}
		
		$separator = $this->getSeparator();
		$placement = $this->getPlacement();
		$errors = $view->formErrors($errors, $this->getOptions()); 

		switch($placement){
			case self::PREPEND:
				return $errors . $separator . $content;
			case self::APPEND:
				return $content . $separator . $errors;
		}
		
		return $content;
	}
	
	/**
	 * @return string
	 */
	public function getPlacement(){
		if(!$placement = $this->getOption('placement')){
			$this->setOption('placement', self::PREPEND);
		}
		return parent::getPlacement();
	}
	
}