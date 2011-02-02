<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage Helper
 */
class Zest_File_Helper_Css extends Zest_File_Helper_Abstract{
	
	/**
	 * @var array
	 */
	protected $_css = null;
	
	/**
	 * @param string $selector
	 * @param string $properties
	 * @return Zest_File_Helper_Css
	 */
	public function addCss($selector, $properties){
		$this->_init();
		
		$selector = trim(strtolower($selector));
		$properties = trim(strtolower($properties));
		
		if(!isset($this->_css[$selector])){
			$this->_css[$selector] = array();
		}
		
		$properties = explode(';', $properties);
		foreach($properties as $code){
			$code = trim($code);
			if(!$code) continue;
			
			@(list($property, $value) = explode(':', $code));
			
			$property = trim($property);
			if(!$property) continue;
			
			$this->_css[$selector][$property] = trim($value);
		}
		
		return $this;
	}
	
	/**
	 * @param string $selector
	 * @param string $property
	 * @return array|string
	 */
	public function getCss($selector, $property = null){
		$this->_init();
		
		$properties = $this->_getProperties($selector);
		
		if($property){
			$property = strtolower($property);
			if(isset($properties[$property])){
				return $properties[$property];
			}
			return null;
		}
		
		return $properties;
	}
	
	/**
	 * @return array
	 */
	public function getArray(){
		$this->_init();
		return $this->_css;
	}
	
	/**
	 * @return string
	 */
	public function toString(){
		$this->_init();
		
		$result = '';
		foreach($this->_css as $selector => $values){
			if($values){
				$result .= $selector.'{'.PHP_EOL;
				foreach($values as $selector => $value){
					$result .= "\t".$selector.': '.$value.';'.PHP_EOL;
				}
				$result .= '}'.PHP_EOL.PHP_EOL;
			}
		}
		return $result;
	}
	
	/**
	 * @return void
	 */
	protected function _init(){
		if(is_null($this->_css)){
			$this->_css = array();
 			$this->_parseContent($this->_file->getContents());
		}
	}
	
	/**
	 * @param string $str
	 * @return void
	 */
	protected function _parseContent($str){
		// suppression des commentaires
		$str = preg_replace('/\/\*(.*)?\*\//Usi', '', $str);
		
		// parsage du CSS
		$parts = explode('}', $str);
		foreach($parts as $part){
			$part = trim($part);
			if(!$part) continue;
			
			@(list($selector, $properties) = explode('{', $part));
			$selectors = explode(',', trim($selector));
			
			foreach($selectors as $selector){
				$selector = trim($selector);
				if(!$selector) continue;
				
				$selector = str_replace(PHP_EOL, '', $selector);
				$selector = str_replace('\\', '', $selector);
				$this->addCss($selector, trim($properties));
			}
		}
	}
	
	/**
	 * @param string $selector
	 * @return array
	 */
	protected function _getProperties($selector){
		$selector = strtolower($selector);
		
		// @todo : multiples selector (ex : a.class_name_1.class_name_2)
		@(list($tag, $subtag) = explode(':', $selector));
		@(list($tag, $class) = explode('.', $tag));
		@(list($tag, $id) = explode('#', $tag));
		
		$result = array();
		foreach($this->_css as $_tag => $value){
			@(list($_tag, $_subtag) = explode(':', $_tag));
			@(list($_tag, $_class) = explode('.', $_tag));
			@(list($_tag, $_id) = explode('#', $_tag));
			
			$tagmatch = !$_tag || $tag == $_tag;
			$subtagmatch = !$_subtag || $subtag == $_subtag;
			$classmatch = !$_class || $class == $_class;
			$idmatch = !$_id || $id === $_id;
			
			if($tagmatch && $subtagmatch && $classmatch && $idmatch){
				$temp = $_tag;
				if($temp && $_class){
					$temp .= '.'.$_class;
				}
				else if(!$temp){
					$temp = '.'.$_class;
				}
				if($temp && $_subtag){
					$temp .= ':'.$_subtag;
				}
				else if(!$temp){
					$temp = ':'.$_subtag;
				}
				foreach($this->_css[$temp] as $property => $value){
					$result[$property] = $value;
				}
			}
		}
		return $result;
	}
	
}