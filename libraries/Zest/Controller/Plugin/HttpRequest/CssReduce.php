<?php

/**
 * @category Zest
 * @package Zest_Controller
 * @subpackage Plugin
 */
class Zest_Controller_Plugin_HttpRequest_CssReduce extends Zest_Controller_Plugin_HttpRequest_Reduce_Abstract{
	
	/**
	 * @var string
	 */
	protected $_gcPattern = '*.css';
	
	/**
	 * @param boolean $minify
	 * @return void
	 */
	public function process($minify = false){
		parent::process();
		
		if(!$this->_cache) return;
		
		$css = $this->_selectElements('css');
		if(!$css) return;
									
		// fusion des css en fonction du type de media
		$fusionCss = array();
		foreach($css as $key => $helperElement){
			$mixed = $this->_getSource($helperElement->href);
			if(!$mixed) continue;
			extract($mixed);		// $source, $pathname
			
			$content = file_get_contents($pathname);
			
			// réécriture des url des images
			$content = $this->_replaceCacheToSourceUrls($content, $source[0], $helperElement->href);
			
			if(!isset($fusionCss[$helperElement->media])){
				$fusionCss[$helperElement->media] = '';
			}
			$fusionCss[$helperElement->media] .= $content.PHP_EOL;
			
			unset($css[$key]);
		}
		
		// réattribution des css qui n'ont pas été sélectionnées
		foreach($css as $helperElement){
			$this->_headAppend($helperElement->href, $helperElement->media);
		}
	
		// écriture du fichier de cache
		foreach($fusionCss as $media => $code){
			$this->_writeCache($code, $minify, 'css', $media);
		}
	}
	
	/**
	 * @param string $code
	 * @return string
	 */
	protected function _minify($code){
		return $this->_view->minify()->css($code);
	}
	
	/**
	 * @param string $url
	 * @param string $media
	 * @return Zest_Controller_Plugin_HttpRequest_CssReduce
	 */
	protected function _headAppend($url, $media = null){
		$this->_view->head()->css($url, $media);
		return $this;
	}
	
}