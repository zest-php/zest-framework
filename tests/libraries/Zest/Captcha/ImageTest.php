<?php

/**
 * @category Zest
 * @package Zest_Captcha
 * @subpackage UnitTests
 */
class Zest_Captcha_ImageTest extends PHPUnit_Framework_TestCase{
	
	/**
	 * @var Zest_Acl
	 */
	protected $_acl = null;
	
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
	
	protected function tearDown(){
		unset($this->_acl);
	}

	public function testDefaultImgUrl(){
		$url = mt_rand();
		Zest_Captcha_Image::setDefaultImgUrl($url);
		$captcha = new Zest_Captcha_Image();
		$this->assertEquals($url.'/', $captcha->getImgUrl());
	}
	
	public function testDefaultImgDir(){
		$dir = $this->_getImgDir();
		Zest_Captcha_Image::setDefaultImgDir($dir);
		$captcha = new Zest_Captcha_Image();
		$this->assertEquals($dir.'/', $captcha->getImgDir());
	}
	
	public function testDefaultFont(){
		$reflection = new ReflectionClass('Zest_Captcha_Image');
		$captcha = new Zest_Captcha_Image();
		$this->assertEquals(dirname($reflection->getFileName()).'/fonts/arial.ttf', $captcha->getFont());
	}
	
	public function testFontNotFound(){
		$this->setExpectedException('Zest_Captcha_Exception');
		new Zest_Captcha_Image(array('font' => mt_rand()));
	}
	
	public function testGeneratePassword(){
		$imgDir = $this->_getImgDir();
		Zest_Captcha_Image::setDefaultImgDir($imgDir);
		Zest_Captcha_Image::setGeneratePasswordHook(array($this, 'generatePasswordHook'));
		$captcha = new Zest_Captcha_Image();
		$captcha->generate();
		
		$children = glob($imgDir.'/*');
		$this->assertEquals(1, count($children));
		$this->assertTrue($captcha->isValid(array('id' => 'test', 'input' => $this->_generatedPassword)));
	}
	
	protected $_generatedPassword = null;
	
	public function generatePasswordHook($captcha){
		$this->assertInstanceOf('Zest_Captcha_Image', $captcha);
		$this->_generatedPassword = mt_rand();
		return $this->_generatedPassword;
	}
	
	protected function _getImgDir(){
		return Zest_AllTests::getTempDir().'/Zest_Captcha_ImageTest';
	}
	   
}