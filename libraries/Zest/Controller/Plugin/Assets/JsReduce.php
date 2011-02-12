<?php

/**
 * @category Zest
 * @package Zest_Controller
 * @subpackage Plugin
 */
class Zest_Controller_Plugin_Assets_JsReduce extends Zest_Controller_Plugin_Assets_Reduce_Abstract{
	
	/**
	 * @var string
	 */
	protected $_gcPattern = '*.js';
	
	/**
	 * @param boolean $minify
	 * @return void
	 */
	public function process($minify = false){
		parent::process();
		
		if(!$this->_cache) return;
		
		$js = $this->_selectElements('js');
		if(!$js) return;
		
		// fusion des js
		$fusionJs = '';
		foreach($js as $key => $helperElement){
			$mixed = $this->_getSource($helperElement->attributes['src']);
			if(!$mixed) continue;
			extract($mixed);		// $source, $pathname
			
			$fusionJs .= file_get_contents($pathname).';'.PHP_EOL;
			unset($js[$key]);
		}
		
		// réattribution des js qui n'ont pas été sélectionnés
		foreach($js as $helperElement){
			$this->_headAppend($helperElement);
		}
	
		// écriture du fichier de cache
		$this->_writeCache($fusionJs, $minify, 'js');
	}
	
	/**
	 * @param string $code
	 * @return string
	 */
	protected function _minify($code){
		return $this->_view->minify()->script($code);
	}
	
	/**
	 * @param string $url
	 * @return Zest_Controller_Plugin_Assets_JsReduce
	 */
	protected function _headAppend($url){
		if(is_object($url)){
			$url = $url->attributes['src'];
		}
		$this->_view->head()->js($url);
		return $this;
	}
	
}