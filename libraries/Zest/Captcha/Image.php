<?php

/**
 * @category Zest
 * @package Zest_Captcha
 */
class Zest_Captcha_Image extends Zend_Captcha_Image{
	
	/**
	 * @var string
	 */
	protected $_imgDir = null;
	
	/**
	 * @var string
	 */
	protected $_imgUrl = null;
	
	/**
	 * @var string
	 */
	protected static $_defaultImgDir = null;
	
	/**
	 * @var string
	 */
	protected static $_defaultImgUrl = null;
	
	/**
	 * @param array $options
	 * @return void
	 */
	public function __construct(array $options = array()){
		//parametres par defaut
		$default = array(
//			'wordlen' => 7,				// default : 8
			'font' => 'arial',			// default : undefined
//			'lineNoiseLevel' => 0,		// default : 5
//			'dotNoiseLevel' => 0,		// default : 100
//			'width' => 100,				// default : 200 / WG : 100
//			'height' => 25,				// default : 50 / WG : 25
//			'fontSize' => 10			// default : 24
		);
				
		$options = array_merge($default, $options);		
		parent::__construct($options);
	}

	/**
	 * @param string $imgDir
	 * @return void
	 */
	public static function setDefaultImgDir($imgDir){
		self::$_defaultImgDir = rtrim($imgDir, '/\\').'/';
	}

	/**
	 * @param string $imgUrl
	 * @return void
	 */
	public static function setDefaultImgUrl($imgUrl){
		self::$_defaultImgUrl = rtrim($imgUrl, '/\\').'/';
	}

	/**
	 * @return string
	 */
	public function getImgDir(){
		if(!$this->_imgDir){
			$this->_imgDir = self::$_defaultImgDir;
		}
		return $this->_imgDir;
	}

	/**
	 * @return string
	 */
	public function getImgUrl(){
		if(!$this->_imgUrl){
			$this->_imgUrl = self::$_defaultImgUrl;
		}
		return $this->_imgUrl;
	}
	
	/**
	 * @param string $fontName
	 * @return Zest_Captcha_Image
	 */
	public function setFont($fontName){
		if(file_exists($fontName)){
			$fontFile = $fontName;
		}
		else{
			$fontFile = dirname(__FILE__).'/fonts/'.strtolower($fontName).'.ttf';
		}
		if(file_exists($fontFile)){
			parent::setFont($fontFile);
		}
		else{
			throw new Zest_Captcha_Exception(sprintf('La police "%s" n\'existe pas.', $fontName));
		}
		return $this;
	}
	
//	/**
//	 * @return string
//	 */
//	protected function _generateWord(){
//		return parent::_generateWord();
//	}
	
	/**
	 * @return int
	 */
	protected function _randomSize(){
		$propW = $this->getWidth() / 200;
		$propH = $this->getHeight() / 50;
		return parent::_randomSize() * min($propW, $propH);
	}
	
}