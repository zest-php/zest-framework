<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage UnitTests
 */
class Zest_Form_Decorator_TdElementTest extends Zest_Form_Decorator_AbstractTest{
	
	public function testRender(){
		$form = new Zest_Form();
		$form->addElement('text', 'testRender', array(
			'label' => 'texte',
			'decorators' => array('viewHelper', 'tdElement'))
		);
		
		$xml = new SimpleXMLElement($form->testRender->render(self::$_view));
		$this->assertContains('form-element', (string) $xml['class']);
		$this->assertNotEmpty((string) $xml->input->asXml());
	}
	
}