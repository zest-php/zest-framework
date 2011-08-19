<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage UnitTests
 */
class Zest_Form_Decorator_TrLabelElementTest extends Zest_Form_Decorator_AbstractTest{
	
	public function testRender(){
		$form = new Zest_Form();
		$form->addElement('text', 'testRender', array(
			'label' => 'testRender',
			'decorators' => array('trLabelElement')
		));
		
		$xml = new SimpleXMLElement($form->testRender->render(self::$_view));
		$this->assertEquals('form-label', (string) $xml->td[0]['class']);
		$this->assertEquals('form-element', (string) $xml->td[1]['class']);
		$this->assertNotEmpty((string) $xml->td[1]->input->asXml());
	}
	
}