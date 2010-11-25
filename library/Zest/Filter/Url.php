<?php

/**
 * @category Zest
 * @package Zest_Filter
 */
class Zest_Filter_Url implements Zend_Filter_Interface{
	
	/**
	 * @var array
	 */
	protected static $_normalizedChars = array(			
		'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
		'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
	
		'ç' => 'c', 'č' => 'c', 'ć' => 'c',
		'Ç' => 'C', 'Č' => 'C', 'Ć' => 'C',
	
		'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
		'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
	
		'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
		'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
	
		'ñ' => 'n',
		'Ñ' => 'N',
	
		'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ð' => 'o',
		'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O',
	
		'Ŕ' => 'R', 'ŕ' => 'r',
		'Š' => 'S', 'š' => 's',
		
		'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
		'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U',
	
		'ÿ' => 'y', 'ý' => 'y', 'ý' => 'y',
		'Ý' => 'Y',
	
		'Ž' => 'Z', 'ž' => 'z',
	
		'æ' => 'ae', 'Æ' => 'AE',
		'œ' => 'oe', 'Œ' => 'OE',
	
		'Đ' => 'Dj', 'đ' => 'dj',
		'Þ' => 'B', 'ß' => 'Ss', 'þ' => 'b'
	);
	
	/**
	 * @var string
	 */
	protected $_spaceChar = '-';		
	
	/**
	 * @param string $value
	 * @return string
	 */
	public function filter($value){
		$value = $this->_stripAccents($value);

		$spaceCharQuoted = preg_quote($this->_spaceChar, '/');
		
		$value = preg_replace('/[^a-z0-9'.$spaceCharQuoted.']/', $this->_spaceChar, strtolower($value));
		$value = preg_replace('/'.$spaceCharQuoted.'+/', $this->_spaceChar, $value);
		
		$value = trim($value, ' '.$this->_spaceChar);

		return $value;
	}
	
	/**
	 * @param string $char
	 * @return Zest_Filter_Url
	 */
	public function setSpaceChar($char){
		$this->_spaceChar = $char;
		return $this;
	}
	
	/**
	 * @param string $value
	 * @return string
	 */
	protected function _stripAccents($value){
		$view = Zest_View::getStaticView();
		
		if(strtolower(substr(PHP_OS, 0, 3)) == 'win' || !function_exists('iconv')){
//			$value = htmlentities($value, ENT_NOQUOTES, $view->getEncoding());
//			$value = preg_replace('#\&([A-za-z])(?:acute|cedil|circ|grave|ring|tilde|uml)\;#', '\1', $value);
//			$value = preg_replace('#\&([A-za-z]{2})(?:lig)\;#', '\1', $value);		// pour les ligatures e.g. '&oelig;'
//			$value = preg_replace('#\&[^;]+\;#', '', $value);						// supprime les autres caractères

			$value = strtr($value, self::$_normalizedChars);
		}
		else{
			$value = iconv($view->getEncoding(), 'ASCII//TRANSLIT', $value);
		}
		
		return $value;
	}
	
}