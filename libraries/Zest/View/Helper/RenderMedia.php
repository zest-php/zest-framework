<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage Helper
 */
class Zest_View_Helper_RenderMedia extends Zend_View_Helper_Abstract{
	
	/**
	 * @return Zest_View_Helper_RenderMedia
	 */
	public function renderMedia($pathname, array $options = array(), $module = null){
		return $this->view->partial('zest-file/render-media.phtml', $module, array(
			'pathname' => $pathname,
			'options' => $options
		));
	}
	
}