<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage UnitTests
 */
class Zest_Form_Decorator_TrLabelValueTest extends Zest_Form_Decorator_AbstractTest{
	
	public function testRender(){
		$form = new Zest_Form();
		$form->addElement('text', 'testRender', array(
			'label' => 'testRender',
			'decorators' => array('trLabelValue'),
			'value' => 'lorem'
		));
		
		$xml = new SimpleXMLElement($form->testRender->render(self::$_view));
		$this->assertEquals('form-label', (string) $xml->td[0]['class']);
		$this->assertEquals('lorem', (string) $xml->td[1]);
	}
	
}