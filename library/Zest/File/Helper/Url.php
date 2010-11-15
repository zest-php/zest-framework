<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage Helper
 */
class Zest_File_Helper_Url extends Zest_File_Helper_Abstract{
	
	/*
	 * FIXME : revoir le système de hash
	 * 		remplacer par un système de correspondance (peut être avec des adapter Db, Array, ...)
	 * 			http://localhost/www/file/4/monfichier.pdf
	 * 			comment sait ton si on a le droit d'accéder au fichier ou non
	 * 		possibilité de rediréger vers une page d'erreur de connexion (CMS)
	 * 			http://localhost/www/cms/file/4/monfichier.pdf
	 * 			le CMS doit injecter un plugin qui gère les fichiers et en cas d'erreur rediriger
	 */
	
	/**
	 * @param array $params
	 * @return string
	 */
	public function getUrl(array $options){
		$options = array_change_key_case($options, CASE_LOWER);
		$request = Zest_Controller_Front::getInstance()->getRequest();
		
		// url public et aucune option : accès direct
		if(!$options && $public = $request->getServer('DOCUMENT_ROOT')){
			$web = str_replace($public, $request->getBaseUrl(), $this->_file->getPathname());
			if($web != $this->_file->getPathname()){
				return $web;
			}
		}
		
		// valeurs par défaut
		$urlOptions = array(
			'module' => $request->getModuleName(),
			'controller' => $request->getControllerName(),
			'action' => 'file',
			'urlfilename' => $this->_file->getBasename()
		);
		
		// surcharge des valeurs par défaut
		foreach($urlOptions as $key => $value){
			if(isset($options[$key])){
				$urlOptions[$key] = $options[$key];
				unset($options[$key]);
			}
		}

		// url private : routage vers le fichier
		$route = 'file_public';
		if(isset($options['private'])){
			$route = 'file_private';
			
			// clef de protection du fichier (vérification à faire dans l'action du controller)
			$urlOptions['private'] = (string) $options['private'];
			unset($options['private']);
			
			// clef de contrôle pour valider l'envoi du fichier
			$options['control'] = md5(serialize($urlOptions));
		}
		
		// http://[...]$url
		$serverUrl = false;
		if(isset($options['serverurl'])){
			$serverUrl = !empty($options['serverurl']);
			unset($options['serverurl']);
		}
		
		// crypt permettant l'affichage du fichier
		$urlOptions['hash'] = Zest_Crypt::encrypt($this->getSendOptions($options));
		
//		// vérification de l'existence de la route
//		if(!Zest_Controller_Front::getInstance()->getRouter()->hasRoute($route)){
//			$route = null;
//		}
		
		$view = Zest_View::getStaticView();
		$url = $view->url($urlOptions, $route, true);
		
		if($serverUrl){
			return $view->serverUrl($url);
		}
		return $url;
	}
	
	/**
	 * @return void
	 */
	public function send(Zend_Controller_Request_Http $request){
		$params = $request->getParams();
		if(isset($params['hash'])){
			$options = Zest_Crypt::decrypt($params['hash']);
			if(is_array($options) && isset($options['pathname'])){
				$pathname = $options['pathname'];
				$this->_file->setPathname($pathname);
				
				$valid = true;
				if(isset($options['control'])){
					$control = array(
						'module' => $request->getModuleName(),
						'controller' => $request->getControllerName(),
						'action' => $request->getActionName(),
						'private' => $request->getParam('private')
					);
					$valid = $options['control'] == md5(serialize($control));
					unset($options['control']);
				}
				if($valid){
					if($this->_file->isReadable()){
						unset($options['pathname']);
						$this->_file->send($options);
					}
				}
			}
		}
		throw new Zest_File_Exception(sprintf('Impossible de trouver le fichier à partir de l\'URL "%s".', $request->getRequestUri()));
	}
	
	/**
	 * @param array $options
	 * @return array
	 */
	public function getSendOptions(array $options){
		$options = array_change_key_case($options, CASE_LOWER);
		$options = array_merge($options, array('pathname' => $this->_file->getPathname()));
		
		// options selon le type du fichier
		list($type,) = explode('/', $this->_file->getMimeType());
		foreach($options as $key => $value){
			if(strpos($key, $type) === 0){
				unset($options[$key]);
//				$key = preg_replace('/^'.$type.'/', '', $key);
				$key = substr($key, strlen($type));
				$options[$key] = $value;
			}
		}
		
		return $options;
	}
	
}