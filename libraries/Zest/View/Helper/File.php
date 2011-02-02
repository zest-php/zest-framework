<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage Helper
 */
class Zest_View_Helper_File extends Zend_View_Helper_Abstract{
	
	/**
	 * @param string|array $pathname
	 * @param array $options
	 * @return array|stdClass
	 */
	public function file($pathname, array $options = array()){
		$isMulti = is_array($pathname);
		if($isMulti){
			foreach($pathname as $key => $value){
				if(!is_numeric($key)){
					$isMulti = false;
					break;
				}
			}
		}
	
		if(!$isMulti){
			$pathname = array($pathname);
		}
		
		$options = array_change_key_case($options, CASE_LOWER);
		
		$files = array();
		foreach($pathname as $fileInfos){
			if(!is_array($fileInfos)){
				$fileInfos = array('pathname' => $fileInfos);
			}
			
			if(isset($fileInfos['pathname'])){
				$file = $this->_file($fileInfos['pathname'], $options);
				foreach($fileInfos as $key => $value){
					$file->$key = $value;
				}
				$files[] = $file;
			}
			else{
				continue;
			}
		}
		
		if(count($files) == 1){
			list($files) = $files;
		}
		
		return $files;
	}
	
	/**
	 * @param string $pathname
	 * @param array $options
	 * @return stdClass
	 */
	protected function _file($pathname, $options){		
		$file = new Zest_File($pathname);
		$std = $file->toStdClass();
		
		$std->isAudio = $file->isAudio();
		$std->isImage = $file->isImage();
		$std->isVideo = $file->isVideo();
		$std->url = null;
		$std->displayed = new stdClass();
		
		// urlfilename
		$urlfilename = $file->getBasename();
		if(isset($options['urlfilename'])){
			$urlfilename = $options['urlfilename'];
			unset($options['urlfilename']);
		}
		$urlfilename = basename($urlfilename, '.'.$std->extension);
		
		// gestion des conversions (principalement les médias)
		$helpers = $file->getHelpers();
		foreach($helpers as $helper){
			if($helper instanceof Zest_File_Helper_Abstract_Convertable){
				$paramValue = $file->url()->getSendOptions($options);
				
				// extension
				if($helper->canBeConverted($paramValue)){
					if(isset($options['extension'])){
						$options['urlfilename'] = basename($urlfilename, '.'.$options['extension']).'.'.$options['extension'];
					}
				}
				else if(isset($options['extension'])){
					unset($options['extension']);
					$paramValue = $file->url()->getSendOptions($options);
				}
				break;
			}
			else{
				unset($helper);
			}
		}
	
		// urlfilename
		if(!isset($options['urlfilename'])){
			$options['urlfilename'] = $urlfilename.'.'.$std->extension;
		}
		
		// url
		$std->url = $file->url()->getUrl($options);
		$std->displayed->extension = pathinfo($options['urlfilename'], PATHINFO_EXTENSION);
		
		// récupération de la taille affichée
		if(isset($helper) && method_exists($helper, 'getSize')){
			list($width, $height) = $helper->getSize($paramValue);
			$std->displayed->width = $width;
			$std->displayed->height = $height;
		}
		
		return $std;
	}
	
}