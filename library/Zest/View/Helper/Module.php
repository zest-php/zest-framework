<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage Helper
 */
class Zest_View_Helper_Module extends Zend_View_Helper_Abstract{
	
	/**
	 * @return string
	 */
	public function module($action, $controller, $module = null, array $params = array()){
		$front = Zest_Controller_Front::getInstance();
		$request = $front->getRequest();
		$router = $front->getRouter();
		$paramsPrefix = $router->getParamsPrefix();
		
		if(is_array($module)){
			$params = $module;
			$module = null;
		}
		
		if(is_null($module)){
			$module = $request->getModuleName();
		}
		
		$requestParams = $request->getParams();
		
		if(isset($params['id'])){
			$prefix = 'm_'.$params['id'].'_';
			unset($params['id']);
			
			$router->setParamsPrefix($prefix);
			
			// restauration des paramètres pour leur utilisation dans le module
			foreach($requestParams as $key => $value){
				if(strpos($key, $prefix) === 0){
					unset($requestParams[$key]);
					
					// remplace les paramètres du tableau $params par ceux de l'URL
					$key = str_replace($prefix, '', $key);
					$params[$key] = $value;
					
					switch(strtolower($key)){
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
		}
		
		$params = array_merge($requestParams, $params);
		$return = $this->view->action($action, $controller, $module, $params);
		$router->setParamsPrefix($paramsPrefix);
		return $return;
	}
	
}