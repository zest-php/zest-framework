<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage UnitTests
 */
class Zest_Form_Decorator_LabelTest extends Zest_Form_Decorator_AbstractTest{
	
	public function testRenderWithoutLabel(){
		$form = new Zest_Form();
		$form->addElement('text', 'testRenderWithoutLabel', array('decorators' => array('trLabelElement')));
		$render = $form->testRenderWithoutLabel->render(self::$_view);
		$render = preg_replace('/(<td[^>]+>)(&nbsp;)/', '\\1<![CDATA[\\2]]>', $render);
		$xml = new SimpleXMLElement($render);
		$this->assertEquals('&nbsp;', (string) $xml->td);
	}
	
	public function testRenderWithLabel(){
		$form = new Zest_Form();
		$form->addElement('text', 'testRenderWithLabel', array(
			'label' => 'testRenderWithLabel',
			'decorators' => array('trLabelElement')
		));
		$xml = new SimpleXMLElement($form->testRenderWithLabel->render(self::$_view));
		$this->assertEquals('testRenderWithLabel', (string) $xml->td->label);
	}
	
}