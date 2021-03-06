<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage Helper
 */
class Zest_View_Helper_Action extends Zend_View_Helper_Action{
	
	/**
	 * @param string $action
	 * @param string $controller
	 * @param string $module
	 * @param array $params
	 * @return string
	 */
	public function action($action, $controller, $module = null, array $params = array()){
		if(is_array($module)){
			$params = $module;
			$module = null;
		}
		
		$front = Zest_Controller_Front::getInstance();
		$request = $front->getRequest();
		$router = $front->getRouter();
		
		$requestParams = $request->getParams();
		
		if(isset($params['uaid'])){
			$paramsPrefix = $router->getParamsPrefix();
			$post = $_POST;
			$get = $_GET;
			$files = $_FILES;
			
			$prefix = 'a_'.$params['uaid'].'_';
			$router->setParamsPrefix($prefix);
			unset($params['uaid']);
			
			// restauration des paramètres pour leur utilisation dans l'action
			foreach($requestParams as $key => $value){
				if(strpos($key, $prefix) === 0){
					$newKey = str_replace($prefix, '', $key);
					
					// ajout des nouvelles clefs sans le préfix
					$params[$newKey] = $value;
					if(isset($_POST[$key])) $_POST[$newKey] = $value;
					if(isset($_GET[$key])) $_GET[$newKey] = $value;
					
					// suppression des anciennes clefs avec le préfix
					unset($requestParams[$key], $_POST[$key], $_GET[$key]);
					
					switch(strtolower($newKey)){
						case 'action':
							$action = $value;
							break;
						case 'controller':
							$controller = $value;
							break;
						case 'module':
							$module = $value;
							break;
					}
				}
			}
			
			foreach($_FILES as $key => $value){
				if(strpos($key, $prefix) === 0){
					$newKey = str_replace($prefix, '', $key);
					$_FILES[$newKey] = $value;
					unset($_FILES[$key]);
				}
			}
		}
		
		// récupération de l'état du layout
		$layout = Zest_Layout::getMvcInstance();
		if($layout){
			$enabled = $layout->isEnabled();
			$layout->disableLayout();
		}
		
		$params = array_merge($requestParams, $params);
		$return = parent::action($action, $controller, $module, $params);
		
		// restauration de l'état du layout
		if($layout){
			$layout->{$enabled ? 'enableLayout' : 'disableLayout'}();
		}
		
		if(isset($prefix)){
			$return = str_replace('name="', 'name="'.$prefix, $return);
			
			$router->setParamsPrefix($paramsPrefix);
			$_POST = $post;
			$_GET = $get;
			$_FILES = $files;
		}
		
		return $return;
	}
	
}