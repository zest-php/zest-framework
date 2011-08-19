<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage UnitTests
 */
class Zest_Form_Decorator_TdValueTest extends Zest_Form_Decorator_AbstractTest{
	
	public function testRenderWithoutValue(){
		$form = new Zest_Form();
		$form->addElement('text', 'testRenderWithoutValue', array(
			'label' => 'texte',
			'decorators' => array('tdValue'))
		);

		$render = $form->testRenderWithoutValue->render(self::$_view);
		$render = preg_replace('/(<td[^>]+>)(&nbsp;)/', '\\1<![CDATA[\\2]]>', $render);
		
		$xml = new SimpleXMLElement($render);
		$this->assertEquals('form-value', (string) $xml['class']);
		$this->assertEquals('&nbsp;', (string) $xml);
	}
	
	public function testRenderWithValue(){
		$form = new Zest_Form();
		$form->addElement('text', 'testRenderWithValue', array(
			'label' => 'texte',
			'value' => 'lorem',
			'decorators' => array('tdValue'))
		);
		$xml = new SimpleXMLElement($form->testRenderWithValue->render(self::$_view));
		$this->assertEquals('lorem', (string) $xml);
	}
	
}