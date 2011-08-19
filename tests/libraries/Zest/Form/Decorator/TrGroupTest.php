<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage UnitTests
 */
class Zest_Form_Decorator_TrGroupTest extends Zest_Form_Decorator_AbstractTest{
	
	public function testRender(){
		$form = new Zest_Form();
		$form->addElement('text', 'testRender', array('label' => 'testRender'));
		$form->addDisplayGroup(array('testRender'), 'grp', array('decorators' => array('trGroup')));
		
		$xml = new SimpleXMLElement($form->grp->render(self::$_view));
		$this->assertEquals('form-group', (string) $xml->td['class']);
	}
	
}