<?php

/**
 * @category Zest
 * @package Zest_Controller
 * @subpackage Plugin
 */
abstract class Zest_Controller_Plugin_HttpRequest_Reduce_Abstract extends Zest_Controller_Plugin_HttpRequest_Abstract{
	
	/**
	 * @param string $code
	 * @return $code
	 */
	abstract protected function _minify($code);
	
	/**
	 * @param string $url
	 * @return Zest_Controller_Plugin_HttpRequest_CssReduce
	 */
	abstract protected function _headAppend($url);
		
	/**
	 * @param string $code
	 * @param string $extension
	 * @param string $headAppend2ndArg
	 * @return Zest_Controller_Plugin_HttpRequest_Abstract
	 */
	protected function _writeCache($code, $minify, $extension, $headAppend2ndArg = null){
		$code = trim($code);
			
		if($code){
			$filename = md5($code).'.'.$extension;
			$pathname = $this->_cache[1].'/'.$filename;
			if(!file_exists($pathname)){
				if($minify){
					$code = $this->_minify($code);
				}
				file_put_contents($pathname, $code);
			}
			$this->_headAppend(rtrim($this->_cache[0], '/\\').'/'.$filename, $headAppend2ndArg);
		}
		
		return $this;
	}
	
}