<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage Transfer
 */
class Zest_File_Transfer_Adapter_Http extends Zend_File_Transfer_Adapter_Http{
	
	/**
	 * @param string $type
	 * @return Zend_Loader_PluginLoader_Interface
	 */
	public function getPluginLoader($type){
		$loader = parent::getPluginLoader($type);
		
		$prefixSegment = ucfirst(strtolower($type));
		$prefix = 'Zest_' . $prefixSegment . '_File';
		if(!$loader->getPaths($prefix)){
			$loader->addPrefixPath($prefix, str_replace('_', '/', $prefix));
		}
		
		return $loader;
	}
	
	/**
	 * @param string|array $files
	 * @return boolean
	 */
	public function receive($files = null){
		$rename = $this->getFilter('Rename');
		
		if(!is_null($rename)){
			$overwrite = false;
			foreach($rename->getFile() as $file){
				if($file['source'] == '*' && $file['target'] == '*'){
					$overwrite = $file['overwrite'];
					break;
				}
			}
			
			$check = $this->_getFiles($files);
			foreach($check as $file => $content){
				if(!$content['received']){
					$directory   = '';
					$destination = $this->getDestination($file);
					if($destination !== null){
						$directory = $destination . DIRECTORY_SEPARATOR;
					}
					$pathname = $directory . $content['name'];
					$rename->addFile(array(
						'source' => $content['tmp_name'],
						'target' => $pathname,
						'overwrite' => $overwrite
					));
				}
			}
		}
		return parent::receive($files);
	}
	
}