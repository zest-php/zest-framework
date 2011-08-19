<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage UnitTests
 */
class Zest_Form_FormTest extends PHPUnit_Framework_TestCase{
	
	public function testPrefixPathElement(){
		$form = new Zest_Form();
		$form->addElement('file', 'testPrefixPathElement');
		$this->assertInstanceOf('Zest_Form_Element_File', $form->testPrefixPathElement);
	}
	
	public function testPrefixPathDecorator(){
		$form = new Zest_Form();
		$form->addDecorator('label');
		$this->assertInstanceOf('Zest_Form_Decorator_Label', $form->getDecorator('label'));
	}
	
	public function testPrefixPathFilter(){
		$form = new Zest_Form();
		$form->addElement('text', 'testPrefixPathFilter', array(
			'filters' => array('url')
		));
		$this->assertInstanceOf('Zest_Filter_Url', $form->testPrefixPathFilter->getFilter('url'));
	}
	
	public function testPrefixPathValidate(){
		$form = new Zest_Form();
		$form->addElement('text', 'testPrefixPathValidate');
		$pluginLoader = $form->testPrefixPathValidate->getPluginLoader(Zend_Form_Element::VALIDATE);
		$this->assertArrayHasKey('Zest_Validate_', $pluginLoader->getPaths());
	}
	
	public function testDefaultDecorators(){
		$form = new Zest_Form();
		$keys = array('Zest_Form_Decorator_TableElements', 'Zest_Form_Decorator_FormErrors', 'Zend_Form_Decorator_Form');
		$this->assertEquals($keys, array_keys($form->getDecorators()));
	}
	
	public function testDefaultDecoratorsDisabled(){
		$form = new Zest_Form(array('disableLoadDefaultDecorators' => true));
		$this->assertEmpty($form->getDecorators());
	}
	
	public function testDefaultDecoratorsOtherDecorators(){
		$form = new Zest_Form(array(
			'decorators' => array('tableElements', 'form')
		));
		$keys = array('Zest_Form_Decorator_TableElements', 'Zend_Form_Decorator_Form');
		$this->assertEquals($keys, array_keys($form->getDecorators()));
	}
	
	public function testElementDefaultDecorators(){
		$form = new Zest_Form();
		$form->addElement('text', 'testElementDefaultDecorators');
		$this->assertEquals(array('Zest_Form_Decorator_TrLabelElement'), array_keys($form->testElementDefaultDecorators->getDecorators()));
	}
	
	public function testElementDefaultDecoratorsDisabled(){
		$form = new Zest_Form();
		$form->addElement('text', 'testElementDefaultDecorators', array('disableLoadDefaultDecorators' => true));
		$this->assertEmpty($form->testElementDefaultDecorators->getDecorators());
	}
	
	public function testElementDefaultDecoratorsOtherDecorators(){
		$form = new Zest_Form();
		$form->addElement('text', 'testElementDefaultDecorators', array(
			'decorators' => array('label', 'viewHelper')
		));
		$keys = array('Zest_Form_Decorator_Label', 'Zend_Form_Decorator_ViewHelper');
		$this->assertEquals($keys, array_keys($form->testElementDefaultDecorators->getDecorators()));
	}
	
	public function testDisplayGroupDefaultDecorators(){
		$form = new Zest_Form();
		$form->addElement('text', 'testDisplayGroupDefaultDecorators');
		$form->addDisplayGroup(array('testDisplayGroupDefaultDecorators'), 'group');
		$keys = array('Zest_Form_Decorator_TableElements', 'Zend_Form_Decorator_Fieldset', 'Zest_Form_Decorator_TrGroup');
		$this->assertEquals($keys, array_keys($form->group->getDecorators()));
	}
	
	public function testDisplayGroupDefaultDecoratorsDisabled(){
		$form = new Zest_Form();
		$form->addElement('text', 'testDisplayGroupDefaultDecorators');
		$form->addDisplayGroup(array('testDisplayGroupDefaultDecorators'), 'group', array(
			'disableLoadDefaultDecorators' => true
		));
		$this->assertEmpty($form->group->getDecorators());
	}
	
	public function testDisplayGroupDefaultDecoratorsOtherDecorators(){
		$form = new Zest_Form();
		$form->addElement('text', 'testDisplayGroupDefaultDecorators');
		$form->addDisplayGroup(array('testDisplayGroupDefaultDecorators'), 'group', array(
			'decorators' => array('fieldset')
		));
		$this->assertEquals(array('Zend_Form_Decorator_Fieldset'), array_keys($form->group->getDecorators()));
		
	}
	
	public function testSubFormDefaultDecorators(){
		$form = new Zest_Form();
		$subform = new Zest_Form();
		$form->addSubForm($subform, 'sub');
		$keys = array('Zest_Form_Decorator_TableElements', 'Zest_Form_Decorator_TrSubForm');
		$this->assertEquals($keys, array_keys($form->sub->getDecorators()));
	}
	
	public function testSubFormDefaultDecoratorsDisabled(){
		$form = new Zest_Form();
		$subform = new Zest_Form(array('disableLoadDefaultDecorators' => true));
		$form->addSubForm($subform, 'sub');
		$this->assertEmpty($form->sub->getDecorators());
	}
	
	public function testSubFormDefaultDecoratorsOtherDecorators(){
		$form = new Zest_Form();
		$subform = new Zest_Form(array('decorators' => array('tableElements')));
		$form->addSubForm($subform, 'sub');
		$keys = array('Zest_Form_Decorator_TableElements', 'Zest_Form_Decorator_TrSubForm');
		$this->assertEquals($keys, array_keys($form->sub->getDecorators()));
	}
	
	public function testGetElementSubForm(){
		$form = new Zest_Form();
		$subform1 = new Zest_Form();
		$subform2 = new Zest_Form();
		$subform2->addElement('text', 'testGetElementSubForm');
		$subform1->addSubForm($subform2, 'sub2');
		$form->addSubForm($subform1, 'sub1');
		$this->assertNull($form->getElement('testGetElementSubForm'));
		$this->assertInstanceOf('Zend_Form_Element_Text', $form->getElement('testGetElementSubForm', true));
	}
	
	public function testDefaultAction(){
		$form = new Zest_Form();
		$request = new Zend_Controller_Request_Http();
		Zest_Controller_Front::getInstance()->setRequest($request);
		$this->assertNotEmpty($form->getAction());
	}
	
	public function testFileElementsNameUniqueness(){
		$form = new Zest_Form();
		$form->addElement('file', 'image');
		
		$subform1 = new Zest_Form();
		$subform1->addElement('file', 'image');
		
		$subform2 = new Zest_Form();
		$subform2->addElement('file', 'image');
		
		$subform3 = new Zest_Form();
		$subform3->addElement('file', 'image');
		
		/**
		 * si on fait dans cet ordre
		 * 
		 * $form->addSubForm($subform1, 'sub1');
		 * $subform1->addSubForm($subform2, 'sub2');
		 * $subform2->addSubForm($subform3, 'sub3');
		 * 
		 * $subform3 n'a alors pas conscience de $subform1
		 * $subform1 et $subform3 auront donc le même nom "image_1"
		 * 
		 * il faut donc respecter un ordre logique
		 */
		
		$subform2->addSubForm($subform3, 'sub3');
		$subform1->addSubForm($subform2, 'sub2');
		$form->addSubForm($subform1, 'sub1');
		
		$elements = $form->getElements();
		$this->assertEquals('image', reset($elements)->getName());
		
		$elements = $subform1->getElements();
		$this->assertEquals('image_r1', reset($elements)->getName());
		
		$elements = $subform2->getElements();
		$this->assertEquals('image_r2', reset($elements)->getName());
		
		$elements = $subform3->getElements();
		$this->assertEquals('image_r3', reset($elements)->getName());
	}
	
}