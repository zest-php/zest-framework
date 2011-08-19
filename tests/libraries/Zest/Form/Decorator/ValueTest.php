<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage UnitTests
 */
class Zest_Form_Decorator_ValueTest extends Zest_Form_Decorator_AbstractTest{
	
	public function testRenderWithoutValue(){
		$form = new Zest_Form();
		$form->addElement('text', 'testRenderWithoutValue', array(
			'label' => 'texte',
			'decorators' => array('value'))
		);

		$render = $form->testRenderWithoutValue->render(self::$_view);
		$this->assertEquals('&nbsp;', $render);
	}
	
	public function testRenderWithValue(){
		$form = new Zest_Form();
		$form->addElement('text', 'testRenderWithValue', array(
			'label' => 'texte',
			'value' => 'lorem',
			'decorators' => array('value'))
		);
		$render = $form->testRenderWithValue->render(self::$_view);
		$this->assertEquals('lorem', $render);
	}
	
}