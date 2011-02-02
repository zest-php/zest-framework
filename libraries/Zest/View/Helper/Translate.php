<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage Helper
 */
class Zest_View_Helper_Translate extends Zend_View_Helper_Translate{
	
	/**
	 * @param string $messageid Id
	 * @return string|Zend_View_Helper_Translate
	 */
	public function translate($messageId = null, $locale = null, array $vars = array()){
		if(is_null($messageId)){
			return $this;
		}
		
		if(!$vars && is_array($locale)){
			$vars = $locale;
			$locale = null;
		}
		
		$translate = $this->getTranslator();
		
		if($translate){
			$messageId = $translate->translate($messageId, $locale, $vars);
		}
		
		return $messageId;
	}
	
	/**
	 * @param Zend_Translate|Zend_Translate_Adapter $translate
	 * @return Zest_View_Helper_Translate
	 */
	public function setTranslator($translate){
		if($translate instanceof Zend_Translate){
			$this->_translator = $translate;
		}
		else{
			parent::setTranslator($translate);
		}
		return $this;
	}
	
}