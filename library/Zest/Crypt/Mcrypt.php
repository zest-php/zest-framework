<?php

/**
 * @category Zest
 * @package Zest_Crypt
 */
class Zest_Crypt_Mcrypt extends Zest_Crypt_Abstract{
	
	/**
	 * @var string
	 */
	protected $_key = 'Classe Zest_Crypt_Mcrypt du Zest Framework';
	
	/**
	 * @var array
	 */
	protected $_ciphers = array();
	
	/**
	 * @return void
	 */
	public function __construct(){
		if(!extension_loaded('mcrypt')){
			throw new Zest_Crypt_Exception('L\'extension PHP "mcrypt" n\'est pas chargée.');
		}
	}
	
	/**
	 * @param mixed $data
	 * @param string $key
	 * @return string
	 */
	public function encrypt($data, $key = null){
		$cipher = $this->_initCipher($key);
		
		$data = serialize($data);
		$data = base64_encode(mcrypt_generic($cipher, '!'.$data));
		return urlencode($data);
	}
	
	/**
	 * @param string $encrypted
	 * @param string $key
	 * @return mixed
	 */
	public function decrypt($encrypted, $key = null){
		$cipher = $this->_initCipher($key);
			
//		$encrypted = urldecode($encrypted);
		$encrypted = mdecrypt_generic($cipher, base64_decode($encrypted));
		if(substr($encrypted, 0, 1) != '!'){
			return false;
		}
		return unserialize(substr($encrypted, 1));
	}
	
	/**
	 * @param string $key
	 * @return Zest_Crypt_Mcrypt
	 */
	public function setKey($key){
		$this->_key = $key;
		return $this;
	}
	
	/**
	 * @param string $key
	 * @return void
	 */
	protected function _initCipher($key){
		if(!$key){
			$key = $this->_key;
		}

		if(isset($this->_ciphers[$key])){
			return $this->_ciphers[$key];
		}
		
		$cipher = mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_ECB, '');
		$vector = mcrypt_create_iv(mcrypt_enc_get_iv_size($cipher), MCRYPT_RAND);
		$encKey = substr(hash('md5', $key), 0, mcrypt_enc_get_key_size($cipher));
		mcrypt_generic_init($cipher, $encKey, $vector);
		return $this->_ciphers[$key] = $cipher;
	}
	
	/**
	 * @return void
	 */
	public function __destruct(){
		foreach($this->_ciphers as $cipher){
			mcrypt_generic_deinit($cipher);
			mcrypt_module_close($cipher);
		}
		$this->_ciphers = array();
	}
	
}