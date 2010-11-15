<?php

/**
 * @category Zest
 * @package Zest_Crypt
 */
class Zest_Crypt{
	
	/**
	 * @var Zest_Crypt_Abstract
	 */
	protected $_adapter = null;
	
	/**
	 * @var Zest_Crypt
	 */
	protected static $_instance = null;
	
	/**
	 * @return void
	 */
	protected function __construct(){
	}
	
	/**
	 * @return Zest_Crypt
	 */
	protected static function _getInstance(){
		if(!self::$_instance){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * @return Zest_Crypt_Abstract
	 */
	protected function _getAdapter(){
		if(!$this->_adapter){
			$this->_adapter = new Zest_Crypt_Mcrypt();
		}
		return $this->_adapter;
	}
	
	/**
	 * @param mixed $data
	 * @param string $key
	 * @return string
	 */
	public static function encrypt($data, $key = null){
		return self::_getInstance()->_getAdapter()->encrypt($data, $key);
	}
	
	/**
	 * @param string $encrypted
	 * @param string $key
	 * @return mixed
	 */
	public static function decrypt($encrypted, $key = null){
		return self::_getInstance()->_getAdapter()->decrypt($encrypted, $key);
	}
	
	/**
	 * @param integer $longueur
	 * @param boolean $nombres
	 * @param boolean $minuscules
	 * @param boolean $majuscules
	 * @param boolean $autres
	 * @return string
	 */
	public static function getRandomPassword($longueur = 8, $nombres = true, $minuscules = true, $majuscules = false, $autres = false){
		return self::_getInstance()->_getRandomPassword($longueur, $nombres, $minuscules, $majuscules, $autres);
	}
	
	/**
	 * @param integer $longueur
	 * @param boolean $nombres
	 * @param boolean $minuscules
	 * @param boolean $majuscules
	 * @param boolean $autres
	 * @return string
	 */
	protected function _getRandomPassword($longueur, $nombres, $minuscules, $majuscules, $autres){
		$resultat = '';
		for($i=0; $i<$longueur; $i++){
			$resultat .= $this->_getRandomChar($nombres, $minuscules, $majuscules, $autres);
		}
		return $resultat;
	}
	
	/**
	 * @param boolean $nombres
	 * @param boolean $minuscules
	 * @param boolean $majuscules
	 * @param boolean $autres
	 * @return string
	 */
	protected function _getRandomChar($nombres, $minuscules, $majuscules, $autres){
		$strChiffres = '0123456789';
		$strMinuscules = 'abcdefghijklmnopqrstuvwxyz';
		$strMajuscules = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$strAutres = '~!@#$%^&*()-_=+[{]}\\|;:\'",<.>/?';
	
		$str = '';
		$str .= $nombres ? $strChiffres : null;
		$str .= $minuscules ? $strMinuscules : null;
		$str .= $majuscules ? $strMajuscules : null;
		$str .= $autres ? $strAutres : null;
	
		if(!$str){
			throw new Zest_Crypt_Exception('Impossible de générer un caractère à partir d\une chaîne vide.');
		}
	
		return substr($str, mt_rand(0, strlen($str)-1), 1);
	}
	
}