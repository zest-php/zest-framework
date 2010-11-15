<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage Helper
 */
class Zest_View_Helper_Minify extends Zend_View_Helper_Abstract{
	
	/**
	 * @param string $type
	 * @param string $content
	 * @return Zest_View_Helper_Minify|string
	 */
	public function minify($type = null, $content = null){
		if(is_null($type)){
			return $this;
		}
		$type = strtolower($type);
		if(method_exists($this, $type)){
			return $this->$type($content);
		}
		throw new Zest_View_Exception(sprintf('Le type "%s" n\'existe pas.', $type));
	}
	
	/**
	 * @param string $content
	 * @return string
	 */
	public function html($content){
		return Zest_Minify::minifyHtml($content);
	}
	
	/**
	 * @param string $content
	 * @return string
	 */
	public function css($content){
		return Zest_Minify::minifyCss($content);
	}
	
	/**
	 * @param string $content
	 * @return string
	 */
	public function script($content){
		return Zest_Minify::minifyScript($content);
	}
	
}