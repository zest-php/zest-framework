<?php

/**
 * @category Zest
 * @package Zest_Controller
 * @subpackage Plugin
 */
class Zest_Controller_Plugin_HttpRequest extends Zend_Controller_Plugin_Abstract{
	
	/**
	 * @var boolean
	 */
	protected $_minify = true;
	
	/**
	 * @var boolean
	 */
	protected $_sprite = true;
	
	/**
	 * @var boolean
	 */
	protected $_reduce = true;
	
	/**
	 * @var Zest_Controller_Plugin_HttpRequest_CssSprite
	 */
	protected $_cssSprite = null;
	
	/**
	 * @var Zest_Controller_Plugin_HttpRequest_CssReduce
	 */
	protected $_cssReduce = null;
	
	/**
	 * @var Zest_Controller_Plugin_HttpRequest_JsReduce
	 */
	protected $_jsReduce = null;
	
	/**
	 * @return void
	 */
	public function __construct(){
		$this->_cssSprite = new Zest_Controller_Plugin_HttpRequest_CssSprite();
		$this->_cssReduce = new Zest_Controller_Plugin_HttpRequest_CssReduce();
		$this->_jsReduce = new Zest_Controller_Plugin_HttpRequest_JsReduce();
	}
	
	/**
	 * @param Zend_Controller_Request_Abstract $request
	 * @return Zest_Controller_Plugin_HttpRequest
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
	 * @return Zest_Controller_Plugin_HttpRequest_CssSprite
	 */
	public function getCssSprite(){
		return $this->_cssSprite;
	}
	
	/**
	 * @return Zest_Controller_Plugin_HttpRequest_CssReduce
	 */
	public function getCssReduce(){
		return $this->_cssReduce;
	}
	
	/**
	 * @return Zest_Controller_Plugin_HttpRequest_JsReduce
	 */
	public function getJsReduce(){
		return $this->_jsReduce;
	}
	
	/**
	 * @param boolean $minify
	 * @return Zest_Controller_Plugin_HttpRequest
	 */
	public function setMinify($minify){
		$this->_minify = (boolean) $minify;
		return $this;
	}
	
	/**
	 * @param boolean $sprite
	 * @return Zest_Controller_Plugin_HttpRequest
	 */
	public function setSprite($sprite){
		$this->_sprite = (boolean) $sprite;
		return $this;
	}
	
	/**
	 * @param boolean $reduce
	 * @return Zest_Controller_Plugin_HttpRequest
	 */
	public function setReduce($reduce){
		$this->_reduce = (boolean) $reduce;
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
		
		if($this->_reduce){
			$this->_cssReduce->setView($view)->process($this->_minify);
			$this->_jsReduce->setView($view)->process($this->_minify);
		}
	}
	
}