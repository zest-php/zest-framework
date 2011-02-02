<?php

/**
 * @category Zest
 * @package Zest_Controller
 * @subpackage Action
 */
class Zest_Controller_Action_Helper_ViewRenderer extends Zend_Controller_Action_Helper_ViewRenderer{
	
	/**
	 * @return Zest_View
	 */
	public function getView(){
		if(!$this->view){
			$this->setView(new Zest_View());
		}
		return $this->view;
	}
	
	/**
	 * @param string $path
	 * @param string $prefix
	 * @param array $options
	 * @return void
	 */
	public function initView($path = null, $prefix = null, array $options = array()){
		// on s'assure que la vue soit créée
		$this->getView();
		parent::initView($path, $prefix, $options);
	}
	
	/**
	 * @return void
	 */
	public function postDispatch(){
		parent::postDispatch();
		
		$scriptPaths = $this->view->getScriptPaths();
		reset($scriptPaths);
		
		// configuration du Zend_Layout
		$layout = Zend_Layout::getMvcInstance();
		if($layout && $layout->isEnabled()){
			// LayoutPath
			if(!$layout->getLayoutPath()){
				// layoutPath de Zest
				$layoutPath = dirname(current($scriptPaths)).'/layouts';
				if(!is_dir($layoutPath)){
					// layoutPath de Zend
					$layoutPath = dirname(dirname(current($scriptPaths))).'/layouts';
				}
				$layout->setLayoutPath($layoutPath);
			}
			
			// Layout
			if(!$layout->getLayout()){
				$layout->setLayout($this->getRequest()->getControllerName());
			}
		}
		
//		// pour éviter une erreur : création des templates s'ils n'existent pas
//		$viewScriptPath = current($scriptPaths).$this->getViewScript($this->getRequest()->getActionName());
//		Zest_File::factory($viewScriptPath)->touch();
//		
//		$layoutScriptPath = $layout->getLayoutPath().'/'.$layout->getInflector()->filter(array('script' => $layout->getLayout()));
//		Zest_File::factory($layoutScriptPath)->touch();
	}
	
}