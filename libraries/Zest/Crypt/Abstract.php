<?php

/**
 * @category Zest
 * @package Zest_Crypt
 */
abstract class Zest_Crypt_Abstract{
	
	/**
	 * @param mixed $data
	 * @param string $key
	 * @return string
	 */
	abstract public function encrypt($data, $key = null);
	
	/**
	 * @param string $encrypted
	 * @param string $key
	 * @return mixed
	 */
	abstract public function decrypt($encrypted, $key = null);
	
}