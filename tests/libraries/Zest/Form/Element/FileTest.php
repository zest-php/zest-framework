<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage UnitTests
 */
class Zest_Form_Element_FileTest extends Zest_Form_Element_AbstractTest{
	
	protected function setUp(){
		$dir = $this->_getDestinationDir();
		if(file_exists($dir)){
			foreach(glob($dir.'/*') as $file){
				unlink($file);
			}
		}
		else{
			mkdir($dir);
		}
	}

	public function testInstance(){
		$form = new Zest_Form();
		$form->addElement('file', 'testInstance');
		$this->assertInstanceOf('Zest_Form_Element_File', $form->testInstance);
	}
	
	public function testPluginLoader(){
		$form = new Zest_Form();
		$form->addElement('file', 'testPluginLoader');
		$pluginLoader = $form->testPluginLoader->getPluginLoader(Zest_Form_Element_File::TRANSFER_ADAPTER);
		$this->assertArrayHasKey('Zest_File_Transfer_Adapter_', $pluginLoader->getPaths());
		$this->assertEquals('Zest_File_Transfer_Adapter_Http', $pluginLoader->load('http'));
	}
	
	public function testName(){
		$form = new Zest_Form();
		$form->addElement('file', 'testName', array(
			'destination' => $this->_getDestinationDir()
		));
		$element = $form->testName;
		$files = $element->getTransferAdapter()->getFileInfo();
		$this->assertArrayHasKey('testName', $files);
		$element->setName('newName');
		$files = $element->getTransferAdapter()->getFileInfo();
		$this->assertArrayHasKey('newName', $files);
	}
	
	public function testRender(){
		$form = new Zest_Form();
		$form->addElement('file', 'testRender', array(
			'decorators' => array('tdElement')
		));
		$xml = new SimpleXMLElement($form->testRender->render(self::$_view));
		$this->assertEquals('hidden', (string) $xml->input[0]['type']);
		$this->assertEquals('file', (string) $xml->input[1]['type']);
	}
	
	protected function _getDestinationDir(){
		return sys_get_temp_dir().'/Zest_Form_Element_FileTest';
	}
	
}