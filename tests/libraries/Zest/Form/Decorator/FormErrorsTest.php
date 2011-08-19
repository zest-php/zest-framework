<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage UnitTests
 */
class Zest_Form_Decorator_FormErrorsTest extends Zest_Form_Decorator_AbstractTest{
	
	public function testRenderWithoutError(){
		$form = new Zest_Form(array('decorators' => array('formErrors', 'form')));
		$xml = new SimpleXMLElement($form->render(self::$_view));
		$this->assertEmpty((string) $xml->ul);
	}
	
	public function testRenderWithError(){
		$form = new Zest_Form(array('decorators' => array('formErrors', 'form')));
		$form->addError('error on testRenderWithError');
		$xml = new SimpleXMLElement($form->render(self::$_view));
		$this->assertEquals('errors', (string) $xml->ul['class']);
		$this->assertEquals('error on testRenderWithError', (string) $xml->ul->li);
	}
	
}