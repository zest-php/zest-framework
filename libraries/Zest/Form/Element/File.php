<?php

/**
 * @category Zest
 * @package Zest_Form
 * @subpackage Element
 */
class Zest_Form_Element_File extends Zend_Form_Element_File{
	
	/**
	 * @param string $type
	 * @return Zend_Loader_PluginLoader_Interface
	 */
	public function getPluginLoader($type){
		$type = strtoupper($type);
		if($type == self::TRANSFER_ADAPTER){
			$exists = array_key_exists($type, $this->_loaders);
			$loader = parent::getPluginLoader($type);
			if(!$exists){
				$loader->addPrefixPath('Zest_File_Transfer_Adapter', 'Zest/File/Transfer/Adapter/');
			}
			return $loader;
		}
		return parent::getPluginLoader($type);
	}
	
	/**
	 * @param string $name
	 * @return Zest_Form_Element_File
	 */
	public function setName($name){
		if($this->_adapter){
			$destination = $this->getDestination();
			$this->_adapter = null;
			parent::setName($name);
			$this->setDestination($destination);
		}
		else{
			parent::setName($name);
		}
		return $this;
	}
	
	/**
	 * @param Zend_View_Interface $view
	 * @return string
	 */
	public function render(Zend_View_Interface $view = null){
		$viewHelperDecorator = $this->getDecorator('viewHelper');
		$fileDecorator = $this->getDecorator('file');
		
		// sauvegarde des décorateurs
		$decorators = $this->getDecorators();
		$this->clearDecorators();
		
		// restauration des décorateurs
		foreach($decorators as $name => $decorator){
			if($decorator === $viewHelperDecorator || !$viewHelperDecorator){
				
				// ajout du décorateur propre au file
				if(!$fileDecorator){
					$this->addDecorator('file');
				}
				
				if(!$viewHelperDecorator){
					$this->addDecorator($decorator);
				}
				
				$viewHelperDecorator = true;
			}
			else{
				$this->addDecorator($decorator);
			}
		}
		
		return parent::render($view);
	}
	
	/**
	 * @param string $method
	 * @param array $args
	 * @return string
	 */
	public function __call($method, $args){
		if(strtolower($method) == 'renderviewhelper'){
			$method = 'renderFile';
		} 
		return parent::__call($method, $args);
	}
	
}