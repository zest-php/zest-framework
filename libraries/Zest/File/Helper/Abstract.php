<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage Helper
 */
abstract class Zest_File_Helper_Abstract{
	
	/**
	 * @var Zest_File
	 */
	protected $_file = null;
	
	/**
	 * @param Zest_File $file
	 * @return void
	 */
	public final function __construct(Zest_File $file){
		$this->_file = $file;
	}
	
	/**
	 * @param object $object
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 */
	protected function _call($object, $method, $args){
		if(method_exists($object, $method) || method_exists($object, '__call')){
			$return = call_user_func_array(array($object, $method), $args);
			if($return === $object){
				return $this;
			}
			return $return;
		}
		throw new Zest_File_Exception(sprintf('La méthode "%s" n\'existe pas.', $method), 1);
	}
	
}