<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage Helper
 */
class Zest_View_Helper_RenderFile extends Zend_View_Helper_Abstract{
	
	/**
	 * @return Zest_View_Helper_RenderFile
	 */
	public function renderFile($pathname, array $options = array(), $module = null){
		return $this->view->partial('zest-file/render-file.phtml', $module, array(
			'pathname' => $pathname,
			'options' => $options
		));
	}
	
}