<?php

/**
 * @category Zest
 * @package Zest_Controller
 * @subpackage Plugin
 */
class Zest_Controller_Plugin_InternetExplorer extends Zend_Controller_Plugin_Abstract{
	
	/**
	 * @var array
	 */
	protected $_messages = array();
	
	/**
	 * @var array
	 */
	protected $_isInternetExplorer = null;
	
	/**
	 * @var float
	 */
	protected $_version = null;
	
	/**
	 * @param Zend_Controller_Request_Abstract $request
	 * @return Zest_Controller_Plugin_IE
	 */
	public function setRequest(Zend_Controller_Request_Abstract $request){
		$userAgent = $request->getHeader('user-agent');
		
		$this->_isInternetExplorer = (boolean) strpos($userAgent, 'MSIE');
		if($this->_isInternetExplorer){
			$this->_version = floatval(preg_replace('/.*MSIE ([0-6\.]+).*/', '\\1', $userAgent));
		}
//		$this->_isInternetExplorer = true;
		
		return parent::setRequest($request);
	}
	
	/**
	 * @return boolean
	 */
	public function isInternetExplorer(){
		return $this->_isInternetExplorer;
	}
	
	/**
	 * @return float
	 */
	public function getVersion(){
		return $this->_version;
	}
	
	/**
	 * @param Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function postDispatch(Zend_Controller_Request_Abstract $request){
		if(!$this->_isInternetExplorer) return;
		
		$body = $this->getResponse()->getBody('default');
		if(!is_int(strpos($body, '<body'))) return;
		
		if($this->_messages){
			foreach($this->_messages as $version => $message){
				if(!$version || $version == $this->_version){
					$body = preg_replace('/(\<body.*>)/', '\\1'.$message, $body);
				}
			}
			
			$this->getResponse()->setBody($body, 'default');
		}
	}
	
	/**
	 * @param string|array $version
	 * @param string $html
	 * @return Zest_Controller_Plugin_IE
	 */
	public function setMessage($html, $version = 0){
		$versions = (array) $version;
		
		foreach($versions as $version){
			$this->_messages[$version] = $html;
		}
		
		return $this;
	}
	
}