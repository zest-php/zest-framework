<?php

/**
 * @category Zest
 * @package Zest_Minify
 */
class Zest_Minify{
	
	/**
	 * @var Zest_Minify
	 */
	protected static $_instance = null;
	
	/**
	 * @return void
	 */
	protected function __construct(){
	}
	
	/**
	 * @return Zest_Minify
	 */
	protected static function _getInstance(){
		if(!self::$_instance){
			self::$_instance = new self();
			set_include_path(get_include_path().PATH_SEPARATOR.LIBRARIES_PATH.'/minify');
		}
		return self::$_instance;
	}
	
	/**
	 * @param string $html
	 * @param array $options
	 * @return string
	 */
	public static function minifyHtml($html, array $options = array()){
		return self::_getInstance()->_minifyHtml($html, $options);
	}
	
	/**
	 * @param string $css
	 * @return string
	 */
	public static function minifyCss($css){
		return self::_getInstance()->_minifyCss($css);
	}
	
	/**
	 * @param string $js
	 * @param array $options
	 * @return string
	 */
	public static function minifyScript($js, array $options = array()){
		return self::_getInstance()->_minifyScript($js, $options);
	}
	
	/**
	 * @param string $html
	 * @param array $options
	 * @return string
	 */
	protected function _minifyHtml($html, array $options = array()){		
		$options = array_merge(array(
			'cssMinifier' => array('Zest_Minify', 'minifyCss'),
			'jsMinifier' => array('Zest_Minify', 'minifyScript')
		), $options);
		require_once 'minify/Minify/HTML.php';
		return Minify_HTML::minify($html, $options);
	}
	
	/**
	 * @param string $css
	 * @param array $options
	 * @return string
	 */
	protected function _minifyCss($css, array $options = array()){
		require_once 'minify/Minify/CSS.php';
		return Minify_CSS::minify($css, $options);
	}
	
	/**
	 * @param string $js
	 * @return string
	 */
	protected function _minifyScript($js){
		require_once 'minify/JSMin.php';
		return trim(JSMin::minify($js));
	}
	
}