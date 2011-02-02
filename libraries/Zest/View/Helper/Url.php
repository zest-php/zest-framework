<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage Helper
 */
class Zest_View_Helper_Url extends Zend_View_Helper_Url{
	
	/**
	 * @param array|string $urlOptions
	 * @param string|null $name
	 * @param boolean $reset
	 * @param boolean $encode
	 * @return string
	 */
	public function url($urlOptions = array(), $name = null, $reset = false, $encode = true, $prefix = true){
		if(is_string($urlOptions)){
			if(substr($urlOptions, 0, 7) != 'http://' && substr($urlOptions, 0, 1) != '/'){
				$urlOptions = Zest_Controller_Front::getInstance()->getRequest()->getBaseUrl().'/'.$urlOptions;
			}
			return $urlOptions;
		}
		$router = Zest_Controller_Front::getInstance()->getRouter();
		return $router->assemble($urlOptions, $name, $reset, $encode, $prefix);
	}
	
}