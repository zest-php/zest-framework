<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage UnitTests
 */
class Zest_Form_Element_CaptchaTest extends Zest_Form_Element_AbstractTest{
	
	protected function setUp(){
		$this->_acl = new Zest_Acl();
		
		$dir = $this->_getImgDir();
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
		$form->addElement('captcha', 'testInstance');
		$this->assertInstanceOf('Zest_Form_Element_Captcha', $form->testInstance);
	}
	
	public function testCaptcha(){
		$form = new Zest_Form();
		$form->addElement('captcha', 'testCaptcha');
		$this->assertInstanceOf('Zest_Captcha_Image', $form->testCaptcha->getCaptcha());
	}
	
	public function testRender(){
		$dir = $this->_getImgDir();
		Zest_Captcha_Image::setDefaultImgDir($dir);
		
		$form = new Zest_Form();
		$form->addElement('captcha', 'testRender', array('decorators' => array('tdElement')));
		
		$xml = new SimpleXMLElement($form->testRender->render(self::$_view));
		$this->assertNotEmpty((string) $xml->img->asXml());
		$this->assertNotEmpty((string) $xml->input[0]->asXml());
		$this->assertNotEmpty((string) $xml->input[1]->asXml());
	}
	
	protected function _getImgDir(){
		return Zest_AllTests::getTempDir().'/Zest_Form_Element_CaptchaTest';
	}
	
}