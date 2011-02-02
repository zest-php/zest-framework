<?php

/**
 * @category Zest
 * @package Zest_Controller
 * @subpackage Plugin
 */
class Zest_Controller_Plugin_Post extends Zend_Controller_Plugin_Abstract{
	
	/**
	 * @param Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function routeStartup(Zend_Controller_Request_Abstract $request){
		// si la session n'est pas démarée, impossible de sauvegarder les données
		if(!class_exists('Zend_Session', false) || !Zend_Session::isStarted()) return;

		// exclusion des requêtes ajax et flash
		if($request->isXmlHttpRequest() || $request->isFlashRequest()) return;
		
		// request_uri
		$requestUri = $request->getScheme().'://'.$request->getHttpHost().$request->getRequestUri();
		$getPost = !empty($_GET['post']);
		if($getPost){
			unset($_GET['post']);
			$requestUri = preg_replace('/(\?|\&)post=1/', '', $requestUri);
		}
		
		$session = new Zend_Session_Namespace('postData');
		
		if($request->isPost()){
			$gotoRedirect = true;
			
			/*
			 * durée de vie de $_FILES = execution du script
			 * une fois la réponse envoyée au navigateur, les fichiers sont supprimés
			 */
			foreach($_FILES as $file){
				$errors = (array) $file['error'];
				foreach($errors as $error){
					if($error != UPLOAD_ERR_NO_FILE){
						$gotoRedirect = false;
					}
				}
			}
			
			if(!$gotoRedirect) return;
			
			$session->$requestUri = array(
				// sauvegarde de l'URL suivante (1)
				'_next' => null,

				// sauvegarde des données du $_POST et $_FILES
				'_post' => $_POST,
				'_files' => $_FILES
			);
			
			// sauvegarde de la session
			Zend_Session::writeClose();

			// redirection
			$requestUri .= $_GET ? '&post=1' : '?post=1';
			header('Location: '.$requestUri);
			exit;
		}
		
		// referer
		$referer = $request->getHeader('referer');
		if(!$referer) return;

		// sauvegarde de l'URL suivante (2)
		if(isset($session->$referer)){
			$session->$referer['_next'] = $requestUri;
		}

		if(isset($session->$requestUri) && $getPost){
			extract($session->$requestUri);

			if(!$_next || $_next == $referer){
				// restauration des données
				$_POST = $_post;
				$_REQUEST = array_merge($_REQUEST, $_POST);
				$_FILES = $_files;
				$_SERVER['REQUEST_METHOD'] = 'POST';
			}
		}
	}
	
}