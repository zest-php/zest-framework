<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage Helper
 */
class Zest_File_Helper_Psd extends Zest_File_Helper_Abstract_Convertable{
	
	/**
	 * @param array $options
	 * @return boolean
	 */
	public function canBeConverted(array $options){
		if(isset($options['extension'])){
			list(, $mime) = explode('/', Zest_File_MimeType::getMimeType($options['extension']));
			return in_array($mime, array('gif', 'jpeg', 'png'));
		}
		return true;
	}
	
	/**
	 * @param array $options
	 * @return void
	 */
	public function convert(array $options){
		throw new Zest_File_Exception('Impossible d\'écraser le fichier courant : utiliser la méthode "convertTo\'.');
	}
	
	/**
	 * @param Zest_File $file
	 * @param array $options
	 * @return Zest_File_Helper_Psd
	 */
	protected function _convertTo(Zest_File $file, array $options){
		if($contents = $this->_getImage($options)){
			$file->putContents($contents);
			
			if($options){
				$file->convert($options);
			}
		}
		
		return $this;
	}
	
	/**
	 * @param array $options
	 * @return string
	 */
	protected function _getImage(array $options = array()){
		$options = array_map('strtolower', $options);
		
		// type
		$extension = 'png';
		if(isset($options['extension'])){
			list(, $extension) = explode('/', Zest_File_MimeType::getMimeType($options['extension']));
		}
		
		// quality
		$quality = $extension == 'png' ? null : 90;
		if($extension == 'jpeg' && isset($options['quality'])){
			$quality = $options['quality'];
		}
		
		// génération de l'image
		$funcImage = 'image'.$extension;
		if(function_exists($funcImage)){
			$psdReader = new Zest_File_Helper_Psd_Reader($this->_file->getPathname());
			
			ob_start();
			$funcImage($psdReader->getImage(), null, $quality);
			return ob_get_clean();
		}
		return null;
	}
	
}