<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage Helper
 */
class Zest_File_Helper_Url extends Zest_File_Helper_Abstract{
	
	/**
	 * @var array
	 */
	protected static $_files = null;
	
	/**
	 * @var string
	 */
	protected static $_cacheFile = null;
	
	/**
	 * @var Zest_File
	 */
	protected static $_cache = null;
	
	/**
	 * @param string $cache
	 * @return void
	 */
	public static function setCacheFile($cacheFile){
		self::$_cacheFile = $cacheFile;
	}
	
	/**
	 * @return Zest_File
	 */
	protected static function _getCache(){
		if(is_null(self::$_cache)){
			if(is_null(self::$_cacheFile)){
				self::$_cacheFile = rtrim(sys_get_temp_dir(), '/\\').'/zest-file-url-files.php';
			}
			self::$_cache = new Zest_File(self::$_cacheFile);
		}
		return self::$_cache;
	}
	
	/**
	 * @param array $params
	 * @return string
	 */
	public function getUrl(array $options){
		$cache = self::_getCache();
		
		if(is_null(self::$_files)){
			if($cache->fileExists()){
				// chargement des informations sur les fichiers
				include $cache->getPathname();
			}
			else{
				self::$_files = array();
			}
		}
		
		$fileId = md5(serialize($this->_getFileArray($options)));
		
		// utilisation de l'url déjà générée si les informations sur le fichier existent
		if(isset(self::$_files[$fileId])){
			return self::$_files[$fileId]['url'];
		}
		
		$options = array_change_key_case($options, CASE_LOWER);
		
		// paramètres utilisés par la route, valeurs par défaut
		$request = Zest_Controller_Front::getInstance()->getRequest();
		$urlOptions = array(
			'module' => $request->getModuleName(),
			'controller' => $request->getControllerName(),
			'action' => 'file',
			'urlfilename' => $this->_file->getBasename(),
			'control' => null
		);
		
		// surcharge des valeurs par défaut
		foreach($urlOptions as $key => $value){
			if(isset($options[$key])){
				$urlOptions[$key] = $options[$key];
				unset($options[$key]);
			}
		}
		$urlOptions['id'] = $fileId;
		$urlOptions['filename'] = $urlOptions['urlfilename'];
		unset($urlOptions['urlfilename']);
		
		// serverUrl : http://[...]$url
		$serverUrl = false;
		if(isset($options['serverurl'])){
			$serverUrl = !empty($options['serverurl']);
			unset($options['serverurl']);
		}

		// nom de la route
		$route = 'zest-file';
		if(isset($options['route'])){
			$route = $options['route'];
			unset($options['route']);
		}
		
		// vérification de l'existence de la route
		if(!Zest_Controller_Front::getInstance()->getRouter()->hasRoute($route)){
			throw new Zest_File_Exception(sprintf('La route "%s" n\'existe pas.', $route));
		}
		
		// création de l'url
		$view = Zest_View::getStaticView();
		$url = $control = $view->url($urlOptions, $route, true, true, false);
		if($serverUrl){
			$url = $view->serverUrl($url);
		}
		
		// écriture des informations sur le fichier si elles ne sont pas déjà présentes
		$sendOptions = $this->getSendOptions($options);
		$fileArray = $this->_getFileArray($sendOptions);
		$fileArray['url'] = $url;
		$fileArray['control'] = $control;
		self::$_files[$fileId] = $fileArray;
		$cache->putContents('<?php self::$_files = '.var_export(self::$_files, true).';');
		
		return $url;
	}
	
	/**
	 * @return void
	 */
	public function send(Zend_Controller_Request_Http $request){
		if(self::_getCache()->isReadable()){
			include self::_getCache()->getPathname();
		}
		
		$params = $request->getParams();
		if(isset($params['id']) && isset(self::$_files[$params['id']])){
			extract(self::$_files[$params['id']]);
			
			if($request->getRequestUri() == $control){
				$this->_file->setPathname($pathname);
				if($this->_file->isReadable()){
					$this->_file->send($options);
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
		
		// options selon le type du fichier
		list($type,) = explode('/', $this->_file->getMimeType());
		foreach($options as $key => $value){
			if(strpos($key, $type) === 0){
				$key = substr($key, strlen($type));
				$options[$key] = $value;
			}
		}
		
		return $options;
	}
	
	/**
	 * @param array $options
	 * @return array
	 */
	protected function _getFileArray(array $options){
		return array(
			'pathname' => $this->_file->getPathname(),
			'options' => $options
		);
	}
	
}