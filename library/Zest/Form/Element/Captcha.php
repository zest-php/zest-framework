<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage Element
 */
class Zest_Form_Element_Captcha extends Zend_Form_Element_Captcha{
	
	/**
	 * @param string|array|Zend_Config $spec
	 * @return void
	 */
	public function __construct($spec, $options = null){		
		$this->addPrefixPath('Zest_Captcha', 'Zest/Captcha', self::CAPTCHA);
		parent::__construct($spec, $options);
	}
	
	/**
	 * FIXME : bug dans Zend_Form_Element_Captcha::render
	 * 
	 * $decorators = $this->getDecorators();
	 * $this->setDecorators($decorators);
	 * 
	 * a pour effet de supprimer les alias (plusieurs décorateurs de même type)
	 * 
	 * @param  Zend_View_Interface $view
	 * @return string
	 */
	public function render(Zend_View_Interface $view = null){
		$captcha = $this->getCaptcha();
		$captcha->setName($this->getFullyQualifiedName());
		
		$viewHelperDecorator = $this->getDecorator('viewHelper');
		$captchaDecorator = $this->getDecorator('captcha');
		
		// sauvegarde des décorateurs
		$decorators = $this->getDecorators();
		$this->clearDecorators();
		
		// restauration des décorateurs
		foreach($decorators as $name => $decorator){
			if($decorator === $viewHelperDecorator || !$viewHelperDecorator){
				
				// ajout des décorateurs propre au captcha
				if(!$captchaDecorator){
					$this->addDecorator('captcha', array('captcha' => $captcha));
				}
				if($adapterDecorator = $captcha->getDecorator()){
					$this->addDecorator($adapterDecorator);
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
		
		$this->setValue($captcha->generate());
		
		// surclassement de la méthode Zend_Form_Element::render
		if($this->_isPartialRendering){
			return '';
		}
		
		if($view){
			$this->setView($view);
		}
		
		$content = '';
		foreach($this->getDecorators() as $decorator){
			$decorator->setElement($this);
			$content = $decorator->render($content);
		}
		
		return $content;
	}
	
	/**
	 * @return Zend_Captcha_Adapter
	 */
	public function getCaptcha(){
		if(!$this->_captcha){
			$this->setCaptcha(array('captcha' => 'image'));
		}
		return $this->_captcha;
	}
	
	/**
	 * @param string $method
	 * @param array $args
	 * @return string
	 */
	public function __call($method, $args){
		if(strtolower($method) == 'renderviewhelper'){
			$method = 'renderCaptcha';
		} 
		return parent::__call($method, $args);
	}
	
//	/**
//	 * @return void
//	 */
//	public function captchaBug(){
//		$form = new Zend_Form();
//		$form->setDecorators(array(
//			array('decorator' => 'formElements'),
//			array('decorator' => 'htmlTag', 'options' => array('tag' => 'table')),
//			array('decorator' => 'form')
//		));
//		
//		$form->addElement('text', 'my_text', array('label' => 'Prénom'));
//		$captcha = new Zend_Form_Element_Captcha('my_captcha', array(
//			'label' => 'Please enter the 5 letters displayed below:',
//			'required' => true,
//			'captcha' => array('captcha' => 'Figlet', 'wordLen' => 5, 'timeout' => 300)
//		));
//		$form->addElement($captcha);
//		
//		$decorators = array(
//			array('decorator' => 'viewHelper'),
//			array('decorator' => array('td' => 'htmlTag'), 'options' => array('tag' => 'td')),
//			array('decorator' => 'label', 'options' => array('tag' => 'td')),
//			array('decorator' => array('tr' => 'htmlTag'), 'options' => array('tag' => 'tr')),
//		);
//		$form->my_text->setDecorators($decorators);
//		$form->my_captcha->setDecorators($decorators);
//		
//		echo '<pre>'.htmlentities($form).'</pre>';
//		exit;
//	}
	
}