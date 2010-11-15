<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage Helper
 */
class Zest_File_Helper_Image extends Zest_File_Helper_Abstract_Convertable{
	
	/**
	 * @param string $url
	 * @return Zest_File_Helper_Image
	 */
	public function capture($url){
		$array = array(
			'url' => $url,
			'out' => $this->_file->getPathname()
		);
		
		if(strtolower(substr(PHP_OS, 0, 3)) == 'win'){
			$command = dirname(__FILE__).'/Image/CutyCapt.exe';
		}
		else{
			throw new Zest_File_Exception(sprintf('L\'OS "%s" n\'est pas implémenté.', PHP_OS));
		}
		foreach($array as $argName => $argValue){
			$command .= ' --'.$argName.'='.escapeshellarg($argValue);
		}
		
		exec($command);
		
		return $this;
	}
	
	/**
	 * @param array $options
	 * @return Zest_File_Helper_Image
	 */
	public function resize(array $options){
		$this->convert($options);
		return $this;
	}
	
	/**
	 * @param Zest_File $file
	 * @param array $options
	 * @return Zest_File_Helper_Image
	 */
	protected function _convertTo(Zest_File $file, array $options){
		if($contents = $this->_getContents($options)){
			$file->putContents($contents);
		}
		return $this;
	}
	
	/**
	 * @param array $options
	 * @return boolean
	 */
	public function canBeConverted(array $options){
		if(!$this->_file->isReadable()) return false;
		
		$mimes = array('gif', 'jpeg', 'png');
		if(isset($options['extension'])){
			list(, $mime) = explode('/', Zest_File_MimeType::getMimeType($options['extension']));
			if(!in_array($mime, $mimes)){
				return false;
			}
		}
		list(, $mime) = explode('/', $this->_file->getMimeType());
		return in_array($mime, $mimes);
	}
	
	/**
	 * @param integer $width
	 * @param integer $height
	 * @return array
	 */
	public function getSize(array $options){
		if(!$this->_file->isReadable()) return null;
		
		list($width, $height, $type, $attr) = getimagesize($this->_file->getPathname());
		if($this->canBeConverted($options)){
			return $this->_getSize($width, $height, $options);
		}
		
		return array($width, $height);
	}
	
	/**
	 * @param array $options
	 * @return string|null
	 */
	protected function _getContents(array $options){
		if(!$this->_file->isReadable()) return null;
		
		$options = array_change_key_case($options, CASE_LOWER);
		
		list($width, $height, $inputType, $attr) = getimagesize($this->_file->getPathname());
		$outputType = $inputType;
		
		list(, $mime) = explode('/', $this->_file->getMimeType());
		$funcCreate = 'imagecreatefrom'.$mime;
		$funcResize = 'imagecopyresampled';
		$funcImage = 'image'.$mime;
		
		// changement d'extension de l'image
		if(isset($options['extension'])){
			list(, $mime) = explode('/', Zest_File_MimeType::getMimeType($options['extension']));
			$imageTypes = array('gif' => IMAGETYPE_GIF, 'jpeg' => IMAGETYPE_JPEG, 'png' => IMAGETYPE_PNG);
			if(isset($imageTypes[$mime])){
				$funcImage = 'image'.$mime;
				$outputType = $imageTypes[$mime];
			}
		}
	
		// qualité du jpeg
		$quality = $outputType == IMAGETYPE_PNG ? null : 100;
		if($outputType == IMAGETYPE_JPEG && isset($options['quality'])){
			$quality = $options['quality'];
		}
		
		// couleur de background (par défaut : les GIF sont transparent et les PNG sur fond blanc)
		$backgroundColor = null;
		if(isset($options['backgroundcolor'])){
			$backgroundColor = $options['backgroundcolor'];
		}
		
		if(function_exists('imagetypes') && function_exists($funcCreate) && function_exists($funcImage)){
			list($newWidth, $newHeight) = $this->_getSize($width, $height, $options);
				
			if($newWidth == $width && $newHeight == $height){
				// FIXME
			}
								
			$imgSrc = $funcCreate($this->_file->getPathname());
			$imgDest = imagecreatetruecolor($newWidth, $newHeight);
		
			if(in_array($inputType, array(IMAGETYPE_GIF, IMAGETYPE_PNG))){
				if($backgroundColor){
					// gestion de la couleur de fond
					$red = substr($backgroundColor, 0, 2);
					$green = substr($backgroundColor, 2, 2);
					$blue = substr($backgroundColor, 4, 2);
					$color = imagecolorallocate($imgDest, hexdec($red), hexdec($green), hexdec($blue));
					imagefill($imgDest, 0, 0, $color);
				}
				else{
					switch($inputType){
						case IMAGETYPE_GIF:
							// gestion de la transparence du gif
							$transparentColor = imagecolortransparent($imgSrc);
							imagefill($imgDest, 0, 0, $transparentColor);
							imagecolortransparent($imgDest, $transparentColor);
							$funcResize = 'imagecopyresized';
							
//							$originalTransparentColor = imagecolortransparent($imgSrc);
//							if( $originalTransparentColor >= 0 && $originalTransparentColor < imagecolorstotal($imgSrc) ){
//								$transparentColor = imagecolorsforindex($imgSrc, $originalTransparentColor);
//								$newTransparentColor = imagecolorallocate($imgDest, $transparentColor['red'], $transparentColor['green'], $transparentColor['blue']);
//								imagefill($imgDest, 0, 0, $newTransparentColor);
//								imagecolortransparent($imgDest, $newTransparentColor);
//							}
							break;
						case IMAGETYPE_PNG:
							// gestion de la transparence du png
							imagealphablending($imgDest, false);
							imagesavealpha($imgDest, true);
							break;
					}
				}
			}
			
			if($funcResize($imgDest, $imgSrc, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height) !== false){
				ob_start();
				$funcImage($imgDest, null, $quality);
				return ob_get_clean();
			}
		}
		
		return null;
	}
	
}