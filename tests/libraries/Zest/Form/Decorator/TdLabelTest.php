<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage UnitTests
 */
class Zest_Form_Decorator_TdLabelTest extends Zest_Form_Decorator_AbstractTest{
	
	public function testRender(){
		$form = new Zest_Form();
		$form->addElement('text', 'testRender', array(
			'label' => 'texte',
			'decorators' => array('tdLabel'))
		);
		$xml = new SimpleXMLElement($form->testRender->render(self::$_view));
		$this->assertContains('form-label', (string) $xml['class']);
		$this->assertNotEmpty((string) $xml->label->asXml());
	}
	
}