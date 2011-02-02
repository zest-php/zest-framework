<?php

/**
 * @category Zest
 * @package Zest_Controller
 * @subpackage Dispatcher
 */
class Zest_Controller_Dispatcher_Standard extends Zend_Controller_Dispatcher_Standard{
	
	/**
	 * @param string $formatted
	 * @return string
	 */
	public function unformatModuleName($formatted){
		return $this->_unformatName($formatted, '');
	}
	
	/**
	 * @param string $formatted
	 * @return string
	 */
	public function unformatControllerName($formatted){
		return $this->_unformatName($formatted, 'controller');
	}

	/**
	 * @param string $formatted
	 * @return string
	 */
	public function unformatActionName($formatted){
		return $this->_unformatName($formatted, 'action');
	}
	
	/**
	 * @param string $formatted
	 * @param string $type
	 * @return string
	 */
	private function _unformatName($formatted, $type){
		if($type){
			$formatted = substr($formatted, 0, -strlen($type));
		}
		
		switch($type){
			case 'action':
				$segments = (array) $formatted;
				break;
			case 'controller':
				$segments = explode($this->getPathDelimiter(), $formatted);
				if(isset($segments[0]) && $this->isValidModule($segments[0])){
					array_shift($segments);
				}
				break;
			default:
				$segments = explode($this->getPathDelimiter(), $formatted);
				break;
		}
		
		$wordDelimiter = current($this->getWordDelimiter());
		
		foreach($segments as $key => $segment){
			$segment = preg_replace('/([A-Z])/', ' \\1', $segment);
			$segment = str_replace(' ', $wordDelimiter, strtolower($segment));
			$segments[$key] = trim($segment, $wordDelimiter);
		}
		
		return implode($this->getPathDelimiter(), $segments);
	}
	
}