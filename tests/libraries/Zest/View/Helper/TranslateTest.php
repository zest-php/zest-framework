<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage UnitTests
 */
class Zest_View_Helper_TranslateTest extends Zest_View_Helper_AbstractTest{
	
	public function testTranslate(){
		$translate = Zest_Translate::factory(Zend_Translate::AN_INI, $this->_getIniFiles(), 'en_GB');
		$this->_view->translate()->setTranslator($translate);
		
		// simple
		$this->assertEquals('hello', $this->_view->translate('bonjour'));
		
		// variables
		$translate->setLocale('fr_FR');
		$this->assertEquals('bonjour avec 2 variables', $this->_view->translate(array('hello.variable', 'hello.variables', 'num'), array('num' => 2)));
	}
	
	protected function _getIniFiles(){
		return array(
			'fr_FR' => Zest_AllTests::getDataDir().'/translate/fr_FR.ini',
			'en_GB' => Zest_AllTests::getDataDir().'/translate/en_GB.ini'
		);
	}
	
}