<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage UnitTests
 */
class Zest_Form_Element_AbstractTest extends PHPUnit_Framework_TestCase{
	
	/**
	 * @var Zest_View
	 */
	protected static $_view = null;
	
	public static function setUpBeforeClass(){
		self::$_view = new Zest_View();
		self::$_view->setOptions(array(
			'doctype' => Zend_View_Helper_Doctype::XHTML1_TRANSITIONAL
		));
	}
	
}