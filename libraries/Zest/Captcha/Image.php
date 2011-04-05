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
	 * @var callback
	 */
	protected static $_generateWordHook = null;
	
	/**
	 * @param array $options
	 * @return void
	 */
	public function __construct(array $options = array()){
		//parametres par defaut
		$options = array_merge(array('font' => 'arial'), $options);
		
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
	 * @param callback $callback
	 * @return void
	 */
	public static function setGeneratePasswordHook($callback){
		self::$_generateWordHook = $callback;
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
	
	/**
	 * @return string
	 */
	public function generate(){
		// désactivation du plugin de cache s'il est utilisé
		$frontcontroller = Zest_Controller_Front::getInstance();
		if($frontcontroller->hasPlugin('Zest_Controller_Plugin_Cache')){
			$frontcontroller->unregisterPlugin('Zest_Controller_Plugin_Cache');
		}
		return parent::generate();
	}
	
	/**
	 * @return string
	 */
	protected function _generateWord(){
		if(self::$_generateWordHook){
			return (string) call_user_func(self::$_generateWordHook, $this);
		}
		return parent::_generateWord();
	}
	
}