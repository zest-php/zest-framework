<?php

/**
 * @category Zest
 * @package Zest_Controller
 */
abstract class Zest_Controller_Soap extends Zest_Controller_Action{

	/**
	 * soap_version : SOAP_1_1, SOAP_1_2
	 * cache_wsdl : WSDL_CACHE_NONE, WSDL_CACHE_DISK, WSDL_CACHE_MEMORY, WSDL_CACHE_BOTH
	 * compression : SOAP_COMPRESSION_ACCEPT, SOAP_COMPRESSION_GZIP, SOAP_COMPRESSION_DEFLATE
	 * features : SOAP_SINGLE_ELEMENT_ARRAYS, SOAP_USE_XSI_ARRAY_TYPE, SOAP_WAIT_ONE_WAY_CALLS
	 * 
	 * @var array
	 */
	protected $_defaults = array('soap_version' => SOAP_1_2);
	
	/**
	 * @return void
	 */
	public function preDispatch(){
		/* ini_set (cache WSDL)
		 * 		soap.wsdl_cache_enabled : activation du cache (default : 1)
		 * 		soap.wsdl_cache_dir : dossier du cache (default : /tmp)
		 * 		soap.wsdl_cache_ttl : durée de vie du cache (default : 86400)
		 * 		soap.wsdl_cache : type de cache (WSDL_CACHE_NONE, WSDL_CACHE_DISK, WSDL_CACHE_MEMORY, WSDL_CACHE_BOTH) (default : 1)
		 * 		soap.wsdl_cache_limit : nombre maximal de fichiers en cache (default : 5)
		 */
		ini_set('soap.wsdl_cache_enabled', 0);
	}
	
	/**
	 * @param boolean|string $autoDiscoverStrategy
	 * @param string $serverUrl
	 * @param string $soapObjectClass
	 * @return void
	 */
	public function wsdl($serverUrl, $soapObjectClass, $autoDiscoverStrategy = true){
		/* strategy
		 * 		true : Zend_Soap_Wsdl_Strategy_DefaultComplexType
		 * 		false : Zend_Soap_Wsdl_Strategy_AnyType
		 */
		
		$wsdl = new Zend_Soap_AutoDiscover($autoDiscoverStrategy, $serverUrl);
		$wsdl->setClass($soapObjectClass);
		$wsdl->handle();
		
		$this->_disableRender();
	}

	/**
	 * @param string $wsdlUrl
	 * @param string $soapObjectClass
	 * @param array $options
	 * @return void
	 */
	public function server($wsdlUrl, $soapObjectClass, array $options = array()){
		/* options
		 * 		soap_version, actor, classmap, encoding, uri, wsdl, featues, cache_wsdl
		 */
		
		$server = new Zend_Soap_Server($wsdlUrl, array_merge($this->_defaults, $options));
		$server->setClass($soapObjectClass);
		$server->handle();
		
		$this->_disableRender();
	}
	
	/**
	 * @return Zend_Soap_Client
	 */
	public function client($wsdlUrl, array $options = array()){
		/* options
		 * 		soap_version, classmap, encoding, wsdl, uri, location, style, use
		 * 		login, password, proxy_host, proxy_port, proxy_login, proxy_password
		 * 		local_cert, passphrase, compression, stream_context, features, cache_wsdl
		 */
		 
		return new Zend_Soap_Client($wsdlUrl, array_merge($this->_defaults, $options));
	}
	
}