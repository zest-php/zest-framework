<?php

/**
 * @category Zest
 * @package Zest_Form
 */
class Zest_Form extends Zend_Form{
	
	/**
	 * @param mixed $options
	 * @return void
	 */
	public function __construct($options = null){
		$this->_initPrefixPath();
		parent::__construct($options);
	}
	
	/**
	 * @return void
	 */
	public function loadDefaultDecorators(){
		if($this->loadDefaultDecoratorsIsDisabled()){
			return;
		}
		
		// décorateurs par défaut
		if(!$this->getDecorators()){
			$this	->addDecorator('tableElements')
					->addDecorator('formErrors')
					->addDecorator('form');
		}
	}
	
	/**
	 * @param string|Zend_Form_Element $element
	 * @param string $name
	 * @param array|Zend_Config $options
	 * @return Zest_Form
	 */
	public function addElement($element, $name = null, $options = null){
		if($options instanceof Zend_Config){
			$options = $options->toArray();
		}
		$options = (array) $options;
		
		$disableLoadDefaultDecorators = !empty($options['disableLoadDefaultDecorators']);
		$options['disableLoadDefaultDecorators'] = true;
		
		parent::addElement($element, $name, $options);
		
		if(!$element instanceof Zend_Form_Element){
			$element = $this->$name;
		}
		
		// décorateurs par défaut
		if(!$disableLoadDefaultDecorators && $element && !$element->getDecorators()){
			$element->addDecorators(array(array('decorator' => 'trLabelElement')));
		}
						
		return $this;
	}
	
	/**
	 * @param  array $elements
	 * @param  string $name
	 * @param  array|Zend_Config $options
	 * @return Zest_Form
	 */
	public function addDisplayGroup(array $elements, $name, $options = null){
		if($options instanceof Zend_Config){
			$options = $options->toArray();
		}
		$options = (array) $options;
		
		$disableLoadDefaultDecorators = !empty($options['disableLoadDefaultDecorators']);
		$options['disableLoadDefaultDecorators'] = true;
		
		parent::addDisplayGroup($elements, $name, $options);
		
		// décorateurs par défaut
		if(!$disableLoadDefaultDecorators && !$this->getDisplayGroup($name)->getDecorators()){
			$this->$name	->addDecorator('tableElements')
							->addDecorator('fieldset')
							->addDecorator('trGroup');
		}
		
		return $this;
	}
	
	/**
	 * @param string $name
	 * @param boolean $checkSubForms
	 * @return Zend_Form_Element|null
	 */
	public function getElement($name, $checkSubForms = false){
		$element = parent::getElement($name);
		
		if(!$element && $checkSubForms){
			foreach($this->getSubForms() as $subForm){
				$element = $subForm->getElement($name, $checkSubForms);
				if($element) break;
			}
		}
		
		return $element;
	}
	
	/**
	 * @param  Zend_Form $form 
	 * @param  string $name 
	 * @param  integer $order 
	 * @return Zest_Form
	 */
	public function addSubForm(Zend_Form $form, $name, $order = null){
		parent::addSubForm($form, $name, $order);
		$this->_autoRenameFileElements($form);
		
		// décorateurs par défaut
		if(!$form->loadDefaultDecoratorsIsDisabled()){
			$this->$name->removeDecorator('form');
			$this->$name->removeDecorator('formErrors');
			$this->$name->addDecorator('trSubForm');
		}
		
		return $this;
	}
	
	/**
	 * @return string
	 *
	 */
	public function getAction(){
		if(!$action = $this->getAttrib('action')){
			$action = Zest_Controller_Front::getInstance()->getRequest()->getRequestUri();
			$this->setAction($action);
		}
		return $action;
	}
	
	/**
	 * @return void
	 */
	protected function _initPrefixPath(){
		$this->addPrefixPath('Zest_Form_Element', 'Zest/Form/Element', Zend_Form::ELEMENT);
		$this->addPrefixPath('Zest_Form_Decorator', 'Zest/Form/Decorator', Zend_Form::DECORATOR);
		
//		$this->addElementPrefixPath('Zest_Form_Decorator', 'Zest/Form/Decorator', Zend_Form_Element::DECORATOR);
		$this->addElementPrefixPath('Zest_Filter', 'Zest/Filter', Zend_Form_Element::FILTER);
		$this->addElementPrefixPath('Zest_Validate', 'Zest/Validate', Zend_Form_Element::VALIDATE);
	}
	
	/**
	 * @param Zend_Form $form
	 * @return void
	 */
	protected function _autoRenameFileElements(Zend_Form $form){
		foreach($form->getSubForms() as $subForm){
			$this->_autoRenameFileElements($subForm);
		}
		
		// dans les subform, les éléments file ne prennent pas en compte la valeur belongsTo
		foreach($form->getElements() as $element){
			if(!$element instanceof Zend_Form_Element_File) continue;
			
			$suffix = 0;
			$newName = $initName = preg_replace('/_r[0-9]+$/', '', $element->getName());
			while($this->getElement($newName, true)){
				$newName = $initName.'_r'.++$suffix;
			}
			
			if($suffix){
				$elementsOrder = array_flip(array_keys($form->_order));
				$elementOrder = isset($elementsOrder[$element->getName()]) ? $elementsOrder[$element->getName()] : null;
				$form->removeElement($element->getName());
				
				$element->setName($newName);
				if(is_int($elementOrder)){
					$element->setOrder($elementOrder);
				}
				$form->addElement($element);
			}
		}
	}
	
}