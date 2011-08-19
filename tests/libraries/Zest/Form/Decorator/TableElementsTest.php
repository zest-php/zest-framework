<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage UnitTests
 */
class Zest_Form_Decorator_TableElementsTest extends Zest_Form_Decorator_AbstractTest{
	
	public function testRenderWithoutElement(){
		$form = new Zest_Form(array('decorators' => array('tableElements', 'formErrors', 'form')));
		$xml = new SimpleXMLElement($form->render(self::$_view));
		$this->assertEmpty((string) $xml->table->asXml());
	}
	
	public function testRenderWithElement(){
		$form = new Zest_Form(array('decorators' => array('tableElements', 'formErrors', 'form')));
		$form->addElement('text', 'testRenderWithElement', array('label' => 'texte'));
		$xml = new SimpleXMLElement($form->render(self::$_view));
		$this->assertNotEmpty((string) $xml->table->asXml());
	}
	
	public function testRenderWithoutInTableElement(){
		$form = new Zest_Form(array('decorators' => array('tableElements', 'formErrors', 'form')));
		$form->addElement('hidden', 'testRenderElementHidden', array('view' => self::$_view));
		$xml = new SimpleXMLElement($form->render(self::$_view));
		$this->assertEmpty((string) $xml->table->asXml());
	}
	
	public function testRenderElementHidden(){
		$form = new Zest_Form(array('decorators' => array('tableElements', 'formErrors', 'form')));
		$form->addElement('hidden', 'testRenderElementHidden', array('view' => self::$_view));
		$form->testRenderElementHidden->setView(self::$_view);
		$xml = new SimpleXMLElement($form->render(self::$_view));
		$this->assertEquals('hidden', (string) $xml->input['type']);
		
	}
	
}