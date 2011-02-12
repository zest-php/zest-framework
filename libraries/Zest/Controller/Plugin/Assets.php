<?php

/**
 * @category Zest
 * @package Zest_Controller
 * @subpackage Plugin
 */
class Zest_Controller_Plugin_Assets extends Zend_Controller_Plugin_Abstract{
	
	/**
	 * @var boolean
	 */
	protected $_sprite = false;
	
	/**
	 * @var boolean
	 */
	protected $_minifyCss = true;
	
	/**
	 * @var boolean
	 */
	protected $_minifyJs = false;
	
	/**
	 * @var boolean
	 */
	protected $_reduceCss = true;
	
	/**
	 * @var boolean
	 */
	protected $_reduceJs = true;
	
	/**
	 * @var Zest_Controller_Plugin_Assets_CssSprite
	 */
	protected $_cssSprite = null;
	
	/**
	 * @var Zest_Controller_Plugin_Assets_CssReduce
	 */
	protected $_cssReduce = null;
	
	/**
	 * @var Zest_Controller_Plugin_Assets_JsReduce
	 */
	protected $_jsReduce = null;
	
	/**
	 * @return void
	 */
	public function __construct(){
		$this->_cssSprite = new Zest_Controller_Plugin_Assets_CssSprite();
		$this->_cssReduce = new Zest_Controller_Plugin_Assets_CssReduce();
		$this->_jsReduce = new Zest_Controller_Plugin_Assets_JsReduce();
	}
	
	/**
	 * @param Zend_Controller_Request_Abstract $request
	 * @return Zest_Controller_Plugin_Assets
	 */
	public function setRequest(Zend_Controller_Request_Abstract $request){
		$this->_cssReduce->setRequest($request);
		$this->_jsReduce->setRequest($request);
		
		$view = Zest_View::getStaticView();
		
		$webPath = $view->serverUrl();
		$publicPath = $request->getServer('DOCUMENT_ROOT');
		
		$this->_cssSprite->addSource($webPath, $publicPath);
		$this->_cssReduce->addSource($webPath, $publicPath);
		$this->_jsReduce->addSource($webPath, $publicPath);
		
		return parent::setRequest($request);
	}
	
	/**
	 * @return Zest_Controller_Plugin_Assets_CssSprite
	 */
	public function getCssSprite(){
		return $this->_cssSprite;
	}
	
	/**
	 * @return Zest_Controller_Plugin_Assets_CssReduce
	 */
	public function getCssReduce(){
		return $this->_cssReduce;
	}
	
	/**
	 * @return Zest_Controller_Plugin_Assets_JsReduce
	 */
	public function getJsReduce(){
		return $this->_jsReduce;
	}
	
	/**
	 * @param boolean $minifyCss
	 * @return Zest_Controller_Plugin_Assets
	 */
	public function setMinifyCss($minifyCss){
		$this->_minifyCss = (boolean) $minifyCss;
		return $this;
	}
	
	/**
	 * @param boolean $minifyJs
	 * @return Zest_Controller_Plugin_Assets
	 */
	public function setMinifyJs($minifyJs){
		$this->_minifyJs = (boolean) $minifyJs;
		return $this;
	}
	
	/**
	 * @param boolean $sprite
	 * @return Zest_Controller_Plugin_Assets
	 */
	public function setSprite($sprite){
		$this->_sprite = (boolean) $sprite;
		return $this;
	}
	
	/**
	 * @param boolean $reduceCss
	 * @return Zest_Controller_Plugin_Assets
	 */
	public function setReduceCss($reduceCss){
		$this->_reduceCss = (boolean) $reduceCss;
		return $this;
	}
	
	/**
	 * @param boolean $reduceJs
	 * @return Zest_Controller_Plugin_Assets
	 */
	public function setReduceJs($reduceJs){
		$this->_reduceJs = (boolean) $reduceJs;
		return $this;
	}
	
	/**
	 * @param integer $lifetime
	 * @return Zest_Controller_Plugin_Assets
	 */
	public function setGcLifetime($lifetime){
		$this->_cssSprite->setGcLifetime($lifetime);
		$this->_cssReduce->setGcLifetime($lifetime);
		$this->_jsReduce->setGcLifetime($lifetime);
		return $this;
	}
	
	/**
	 * @param Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function postDispatch(Zend_Controller_Request_Abstract $request){
		$view = Zest_View::getStaticView();
		
		if($this->_sprite){
			$this->_cssSprite->setView($view)->process();
		}
		if($this->_reduceCss){
			$this->_cssReduce->setView($view)->process($this->_minifyCss);
		}
		if($this->_reduceJs){
			$this->_jsReduce->setView($view)->process($this->_minifyJs);
		}
	}
	
}