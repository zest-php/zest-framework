<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage UnitTests
 */
class Zest_Form_Decorator_TrSubFormTest extends Zest_Form_Decorator_AbstractTest{
	
	public function testRender(){
		$form = new Zest_Form();
		
		$sub = new Zest_Form();
		$sub->addElement('text', 'testRender', array('label' => 'testRender'));
		$form->addSubForm($sub, 'sub', array('deocrators' => array('trSubForm')));
		
		$xml = new SimpleXMLElement($form->sub->render(self::$_view));
		$this->assertEquals('form-subform', (string) $xml->td['class']);
		$this->assertNotEmpty((string) $xml->td->table->asXml());
		$this->assertNotEmpty((string) $xml->td->table->tr->td[1]->input->asXml());
	}
	
}