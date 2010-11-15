<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage Engine
 */
class Zest_View_Engine_Smarty extends Zest_View_Engine_Abstract{
	
	/**
	 * @var Zest_Smarty
	 */
	protected $_smarty = null;
	
	/**
	 * @var string
	 */
	protected $_cacheId = null;
	
	/**
	 * @var string
	 */
	protected $_compileId = null;
	
	/**
	 * @param Zest_View $view
	 * @return void
	 */
	public function __construct(Zest_View $view){
		parent::__construct($view);
		$this->_smarty = new Zest_Smarty();
	}
	
	/**
	 * @param string $name
	 * @return string
	 */
	public function render($name){
		// dossier du template
		$scriptPath = $this->_view->getScriptPath($name);
		
		if(!$scriptPath){
			throw new Zest_View_Exception(sprintf('script "%s" not found in path (%s)', $name, implode(PATH_SEPARATOR, $this->_view->getScriptPaths())));
		}
		
		$scriptPath = str_replace('\\', '/', $scriptPath);
		$name = str_replace('\\', '/', $name);
		
		$templateDir = str_replace('/'.$name, '', $scriptPath);

		$this->_smarty->setTemplateDir($templateDir);
		
		// identifiant de compilaition
		$this->setCompileId(hash('md5', $name));
		
		// rendu du template
		$vars = $this->_smarty->getVars();
		
		$this->_smarty->clearVars();
		$this->_smarty->assign($this->_view->getVars());
		
		$content = $this->_smarty->fetch($name, $this->getCacheId(), $this->getCompileId());
		
		$this->_smarty->clearVars();
		$this->_smarty->assign($vars);
		
		return $content;
	}
	
	/**
	 * @param array $options
	 * @return Zest_View_Engine_Smarty
	 */
	public function setOptions(array $options){
		$options = array_change_key_case($options, CASE_LOWER);
		
		foreach($options as $key => $value){
			$key = ucwords(str_replace('_', ' ', $key));
			$method = 'set'.str_replace(' ', '', $key);
			if(method_exists($this->_smarty, $method)){
				$this->_smarty->$method($value);
			}
		}
		
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getCacheId(){
		return $this->_cacheId;
	}
	
	/**
	 * @param string $cacheId
	 * @return Zest_View_Smarty
	 */
	public function setCacheId($cacheId){
		$this->_cacheId = $cacheId;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getCompileId(){
		return $this->_compileId;
	}
	
	/**
	 * @param string $compileId
	 * @return Zest_View_Smarty
	 */
	public function setCompileId($compileId){
		$this->_compileId = $compileId;
		return $this;
	}
	
	/**
	 * @return void
	 */
	public function __clone(){
		$this->_smarty = clone $this->_smarty;
	}
	
}