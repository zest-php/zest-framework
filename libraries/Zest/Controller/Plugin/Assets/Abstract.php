<?php

/**
 * @category Zest
 * @package Zest_Controller
 * @subpackage Plugin
 */
abstract class Zest_Controller_Plugin_Assets_Abstract{
	
	/**
	 * @var Zend_Controller_Request_Abstract
	 */
	protected $_request = null;
	
	/**
	 * @var Zest_View
	 */
	protected $_view = null;
	
	/**
	 * @var array
	 */
	protected $_source = array();
	
	/**
	 * @var array
	 */
	protected $_cache = array();
	
	/**
	 * @var integer
	 */
	protected $_gcLifetime = null;
	
	/**
	 * @var integer
	 */
	protected $_gcFreq = 10;
	
	/**
	 * @var string
	 */
	protected $_gcPattern = null;
	
	/**
	 * @param integer $gcLifetime
	 * @return Zest_Controller_Plugin_Assets_Abstract
	 */
	public function setGcLifetime($gcLifetime){
		$this->_gcLifetime = $gcLifetime;
		return $this;
	}
	
	/**
	 * @param Zend_Controller_Request_Abstract $request
	 * @return Zest_Controller_Plugin_Assets_Abstract
	 */
	public function setRequest($request){
		$this->_request = $request;
		return $this;
	}
	
	/**
	 * @param Zest_View $view
	 * @return Zest_Controller_Plugin_Assets_Abstract
	 */
	public function setView($view){
		$this->_view = $view;
		return $this;
	}
	
	/**
	 * @param string $url
	 * @param string $path
	 * @return Zest_Controller_Plugin_Assets_Abstract
	 */
	public function addSource($url, $path){
		if(substr($url, -1) != '/'){
			$url .= '/';
		}
		
		$path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
		$path = rtrim($path, '/');
		
		$this->_source[] = array($url, $path);
		return $this;
	}
	
	/**
	 * @param string $url
	 * @param string $path
	 * @return Zest_Controller_Plugin_Assets_Abstract
	 */
	public function setCache($url, $path){
		$path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
		$path = rtrim($path, '/');
		
		$this->_cache = array($url, $path);
		return $this;
	}
	
	/**
	 * @return void
	 */
	public function process(){
		if(!$this->_cache) return;
		
		// nettoyage du cache
		if(!is_null($this->_gcLifetime)){
			Zest_Dir::factory($this->_cache[1])->cleanGarbage($this->_gcLifetime, $this->_gcFreq, $this->_gcPattern);
		}
	}
	
	/**
	 * @param string $content
	 * @param string $urlSourceDir
	 * @param string $urlElement
	 * @return string
	 */
	protected function _replaceCacheToSourceUrls($content, $urlSourceDir, $urlElement){
		// suppression des url en "./" (ex : url(./../image/) => url(../image/))
		$content = preg_replace('/url\(("|\')?\.\//', 'url(\\1', $content);
		
		// calcul du préfix pour les url des images
		$sourceDir = str_replace($urlSourceDir, '', dirname($urlElement));
		$destinationDir = $this->_cache[0];
		
		$relativeDir = $this->_getRelativeDir($sourceDir, $destinationDir);
		if($relativeDir && substr($relativeDir, -1) != '/'){
			$relativeDir .= '/';
		}
		
		// réécriture des url (ex : url(./../image/) => url(../../css/../image/))
		$content = preg_replace('/url\(("|\')?([^\/|http:\/\/])/', 'url(\\1'.$relativeDir.'\\2', $content);
		
		return $content;
	}
	
	/**
	 * @param string $url
	 * @return string|array
	 */
	protected function _getSource($url){
		foreach($this->_source as $source){
			if(is_int(strpos($url, $source[0]))){
				$pathname = $source[1].'/'.str_replace($source[0], '', $url);
				if(file_exists($pathname)){
					return array(
						'source' => $source,
						'pathname' => $pathname
					);
				}
			}
		}
		return null;
	}
	
	/**
	 * @param Zend_View_Helper_Placeholder_Container_Standalone $helper
	 * @return array
	 */
	protected function _selectElements($type){
		$helper = null;
		$array = array();
		$offsetUnset = array();
		
		switch($type){
			case 'css':
				$helper = $this->_view->headLink();
				foreach($helper as $offset => $helperElement){
					if($helperElement->type == 'text/css'){
						$helperElement->href = $this->_normalizeUrl($helperElement->href);
						$offsetUnset[] = $offset;
						$array[] = $helperElement;
					}
				}
				break;
			case 'js':
				$helper = $this->_view->headScript();
				foreach($helper as $offset => $helperElement){
					if(!empty($helperElement->attributes['src'])){
						$helperElement->attributes['src'] = $this->_normalizeUrl($helperElement->attributes['src']);
						$offsetUnset[] = $offset;
						$array[] = $helperElement;
					}
				}
				break;
		}
		
		// suppression des éléments
		foreach($offsetUnset as $offset){
			$helper->offsetUnset($offset);
		}
		
		return $array;
	}
	
	/**
	 * @param string $sourceDir
	 * @param string $destinationDir
	 * @return string
	 */
	private function _getRelativeDir($sourceDir, $destinationDir){		
		$source = explode('/', trim($sourceDir, '/'));
		$destination = explode('/', trim($destinationDir, '/'));
		foreach($source as $key => $part){
			if($part === $destination[$key]){
				unset($source[$key], $destination[$key]);
			}
			else{
				break;
			}
		}
		return str_repeat('../', count($destination)).implode('/', $source);
	}

	/**
	 * @param string $url
	 * @return string
	 */
	private function _normalizeUrl($url){
		if(substr($url, 0, 7) != 'http://'){
			$view = Zest_View::getStaticView();
			
			if(substr($url, 0, 1) == '/'){
				$url = $view->serverUrl($url);
			}
			else{
				$url = $view->serverUrl($this->_request->getBaseUrl().'/'.$url);
			}
		}
		return $url;
	}
	
}