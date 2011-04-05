<?php

/**
 * @category Zest
 * @package Zest_Mail
 */
class Zest_Mail extends Zend_Mail{
	
	/**
	 * @var Zest_Layout
	 */
	protected $_layout = null;
	
	/**
	 * @var string
	 */
	protected $_charset = 'utf-8';
	
	/**
	 * @param string|array $charset
	 */
	public function __construct($options = array()){
		if(is_string($options)){
			$options = array('charset' => $options);
		}
		$this->setOptions($options);
	}
	
	/**
	 * @param array $options
	 * @return Zest_Mail
	 */
	public function setOptions(array $options){
		foreach($options as $key => $value){
			$method = 'set'.ucfirst($key);
			if(method_exists($this, $method)){
				$this->$method($value);
			}
		}
		return $this;
	}
	
	/**
	 * @param string $charset
	 * @return Zest_Mail
	 */
	public function setCharset($charset){
		$this->_charset = $charset;
		return $this;
	}
	
	/**
	 * @param string $html
	 * @param string $charset
	 * @param string $encoding
	 * @return Zest_Mail
	 */
	public function setBodyHtml($html, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE){
		if($this->_layout){
			$this->_layout->{$this->_layout->getContentKey()} = $html;
			$html = $this->_layout->render();
		}
		parent::setBodyHtml($html, $charset, $encoding);
		return $this;
	}
	
	/**
	 * @param string $name
	 * @param array $vars
	 * @param string $charset
	 * @param string $encoding
	 * @return Zest_Mail
	 */
	public function setBodyHtmlScript($name, array $vars = array(), $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE){
		$view = Zest_View::getStaticView();
		$content = $view->partial($name, $vars);
		$this->setBodyHtml($content, $charset, $encoding);
		return $this;
	}
	
	/**
	 * @param string|Zest_Layout $layout
	 * @param string $layoutPath
	 * @return Zest_Mail
	 */
	public function setBodyHtmlLayout($layout, $layoutPath = null){
		if($this->getBodyHtml()){
			throw new Zest_Mail_Exception('Le layout doit être renseigné avant le body.');
		}
		
		if($layout instanceof Zest_Layout){
			$this->_layout = $layout;
		}
		else{
			if(is_null($layoutPath)){
				if(!Zest_Layout::getMvcInstance()){
					throw new Zest_Mail_Exception('La variable $layoutPath doit être renseignée dans Zest_Mail::setBodyHtmlLayout($layout, $layoutPath).');
				}
				$layoutPath = Zest_Layout::getMvcInstance()->getLayoutPath();
			}
			$this->_layout = new Zest_Layout($layoutPath);
			$this->_layout->setLayout($layout);
		}
		
		return $this;
	}
	
	/**
	 * @param string|array $emails
	 * @param string $name
	 * @return Zest_Mail
	 */
	public function addTo($emails, $name = ''){
		if(is_array($emails)){
			foreach($emails as $email){
				if(is_array($email)){
					if($email){
						if(count($email) == 1){
							list($email) = $email;
							$name = '';
						}
						else{
							list($email, $name) = $email;
						}
					}
					else{
						continue;
					}
				}
				else{
					$name = '';
				}
				$this->addTo($email, $name);
			}
		}
		else{
			parent::addTo($emails, $name);
		}
		return $this;
	}
	
	/**
	 * @param string|Zest_File|Zend_Mime_Part $file
	 * @return Zest_Mail
	 */
	public function addAttachment($file){
		if(is_string($file)){
			$file = new Zest_File($file);
		}
		
		if($file instanceof Zend_Mime_Part){
			parent::addAttachment($file);
		}
		else if($file instanceof Zest_File){
			if($file->isReadable()){
				$this->createAttachment(
					$file->getContents(),
					$file->getMimeType(),
					Zend_Mime::DISPOSITION_ATTACHMENT,
					Zend_Mime::ENCODING_BASE64,
					$file->getBasename()
				);
			}
		}
		else{
			throw new Zest_Mail_Exception('La pièce jointe doit être le chemin du fichier, une instance de Zend_Mime_Part ou une instance de Zest_File.');
		}
		return $this;
	}
	
	/**
	 * @param string|Zest_File|Zend_Mime_Part $file
	 * @return string
	 */
	public function addInlineImage($file){
		if(is_string($file)){
			$file = new Zest_File($file);
		}
		
		if($file instanceof Zest_File){
			$file = $this->createAttachment(
				$file->getContents(),
				$file->getMimeType(),
				Zend_Mime::DISPOSITION_INLINE,
				Zend_Mime::ENCODING_BASE64,
				$file->getBasename()
			);
		}
		
		if($file instanceof Zend_Mime_Part){
			$file->id = 'cid_'.md5($file->filename);
		}
		else{
			throw new Zest_Mail_Exception('L\'image jointe doit être le chemin du fichier, une instance de Zend_Mime_Part ou une instance de Zest_File.');
		}
		
		$this->setType(Zend_Mime::MULTIPART_RELATED);
		
		return $file->id;
	}
	
	/**
	 * @param string $value
	 * @return boolean
	 */
	public static function isValidEmailAddress($value){
		$validator = new Zend_Validate_EmailAddress();
		return $validator->isValid($value);
	}
	
	/**
	 * @param string $value
	 * @param integer $pad
	 * @return string
	 */
	public static function obfuscate($value, $pad = 4) {
		$strlen = mb_strlen($value);
		$return = '';
		for($i = 0; $i < $strlen; $i++) {
			$return .= '&#'.sprintf('%0'.$pad.'d', ord(substr($value, $i, 1))).';';
		}
		return $return;
	}
	
}