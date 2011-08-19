<?php

/**
 * @category Zest
 * @package Zest_Translate
 * @subpackage UnitTests
 */
class Zest_Translate_TranslateTest extends PHPUnit_Framework_TestCase{
	
	protected function setUp(){
		Zest_Translate::removeCache();
	}
	
	public function testFactory(){
		$translate = Zest_Translate::factory(Zend_Translate::AN_INI, $this->_getIniFiles(), 'en_GB');
		$this->assertInstanceOf('Zend_Translate_Adapter_Ini', $translate->getAdapter());
		$this->assertEquals('en_GB', $translate->getAdapter()->getLocale());
		
		$keys = array_keys($translate->getAdapter()->getList());
		sort($keys);
		$this->assertEquals(array('en_GB', 'fr_FR'), $keys);
	}
	
	public function testTranslateIni(){
		// 'logUntranslated' => true
		
		$translate = Zest_Translate::factory(Zend_Translate::AN_INI, $this->_getIniFiles(), 'en_GB');
		// traduction à partir de la langue courante
		$this->assertEquals('hello', $translate->translate('bonjour'));
		$this->assertEquals('hello', $translate->translate('hello'));
		
		// traduction à partir de la langue renseignée : conservation de la langue courante
		$this->assertEquals('bonjour', $translate->translate('hello', 'fr_FR'));
		$this->assertEquals('en_GB', $translate->getAdapter()->getLocale());
		
		// modification de la langue courante
		$translate->getAdapter()->setLocale('fr_FR');
		$this->assertEquals('bonjour', $translate->translate('hello'));
		$this->assertEquals('bonjour', $translate->translate('bonjour'));

		// utilisation de variables
		$this->assertEquals('bonjour avec 1 variable', $translate->translate('hello.variable', array('num' => 1)));

		// gestion du pluriel
		$this->assertEquals(
			'bonjour avec x > 1 variables',
			$translate->translate(array('hello.variable', 'hello.variables', 2), array('num' => 'x > 1'))
		);
		
		$this->assertEquals(
			'bonjour avec 2 variables',
			$translate->translate(array('hello.variable', 'hello.variables', 'num'), array('num' => 2))
		);
	}
	
	public function testTranslateGettext(){
		$translate = Zest_Translate::factory(Zend_Translate::AN_GETTEXT, $this->_getMoFiles(), 'en_GB');
		$this->assertEquals('hello', $translate->translate('bonjour'));
	}
	
	public function testCacheDir(){
		$dir = Zest_AllTests::getTempDir().'/Zest_Translate_TranslateTest';
		if(file_exists($dir)){
			foreach(glob($dir.'/*') as $file){
				unlink($file);
			}
		}
		else{
			mkdir($dir);
		}
		Zest_Translate::setCacheDir($dir);
		$translate = Zest_Translate::factory(Zend_Translate::AN_INI, $this->_getIniFiles(), 'en_GB');
		
		$this->assertInstanceOf('Zend_Cache_Backend_File', $translate->getCache()->getBackend());
		$this->assertEquals(6, count(glob($dir.'/*')));
	}
	
	protected function _getMoFiles(){
		return array(
			'fr_FR' => Zest_AllTests::getDataDir().'/translate/fr_FR.mo',
			'en_GB' => Zest_AllTests::getDataDir().'/translate/en_GB.mo'
		);
	}
	
	protected function _getIniFiles(){
		return array(
			'fr_FR' => Zest_AllTests::getDataDir().'/translate/fr_FR.ini',
			'en_GB' => Zest_AllTests::getDataDir().'/translate/en_GB.ini'
		);
	}
	
}