<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage Element
 */
class Zest_Form_Element_File extends Zend_Form_Element_File{
	
	/**
	 * @param Zend_View_Interface $view
	 * @return string
	 */
	public function render(Zend_View_Interface $view = null){
		$viewHelperDecorator = $this->getDecorator('viewHelper');
		$fileDecorator = $this->getDecorator('file');
		
		// sauvegarde des décorateurs
		$decorators = $this->getDecorators();
		$this->clearDecorators();
		
		// restauration des décorateurs
		foreach($decorators as $name => $decorator){
			if($decorator === $viewHelperDecorator || !$viewHelperDecorator){
				
				// ajout du décorateur propre au file
				if(!$fileDecorator){
					$this->addDecorator('file');
				}
				
				if(!$viewHelperDecorator){
					$this->addDecorator($decorator);
				}
				
				$viewHelperDecorator = true;
			}
			else{
				$this->addDecorator($decorator);
			}
		}
		
		return parent::render($view);
	}
	
	/**
	 * @param string $method
	 * @param array $args
	 * @return string
	 */
	public function __call($method, $args){
		if(strtolower($method) == 'renderviewhelper'){
			$method = 'renderFile';
		} 
		return parent::__call($method, $args);
	}
	
}