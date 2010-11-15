<?php

/**
 * @category Zest
 * @package Zest_Log
 * @subpackage Writer
 */
class Zest_Log_Writer_Mail extends Zend_Log_Writer_Mail{

	/**
	 * @param Zend_Mail|string $mail
	 * @param Zend_Layout|string $layout
	 * @return void
	 */
	public function __construct($mail, $layout = null){
		if(is_string($mail)){
			if(Zest_Mail::isValidEmailAddress($mail)){
				$object = new Zest_Mail();
				$mail = $object->addTo($mail);
			}
			else{
				throw new Zest_Log_Exception(sprintf('Le mail "%s" n\'est pas valide.', $mail));
			}
		}
		if(is_string($layout)){
			if(is_readable($layout)){
				$object = new Zend_Layout();
				$layout = $object	->setLayoutPath(dirname($layout))
									->setLayout(basename($layout, '.'.$object->getViewSuffix()));
			}
			else{
				throw new Zest_Log_Exception(sprintf('Le script "%s" n\'existe pas.', $layout));
			}
		}
		parent::__construct($mail, $layout);
		
		$this->_formatter = new Zest_Log_Formatter_Simple();
	}
	
}