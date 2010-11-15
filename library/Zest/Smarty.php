<?php

/**
 * @see Smarty
 */
require_once 'Smarty/Smarty.class.php';

/**
 * @see Smarty_Compiler
 */
require_once 'Smarty/Smarty_Compiler.class.php';

/**
 * @category Zest
 * @package Zest_Smarty
 */
class Zest_Smarty extends Smarty{	
	
	/**
	 * @return void
	 */
	public function __construct(){
		parent::Smarty();
		$this->security_settings['MODIFIER_FUNCS'][] = 'rand';
	}
	
	/**
	 * @param string $name
	 * @param string $cache_id
	 * @param string $compile_id
	 * @param boolean $display
	 * @return string
	 */
	public function fetch($name, $cache_id, $compile_id, $display = false){
		$forceCompile = $this->force_compile;
		$content = parent::fetch($name, $cache_id, $compile_id, $display);
		if($forceCompile){
			$this->clear_compiled_tpl($name, $compile_id);
		}
		return $content;
	}
	
	// VARS
	
	/**
	 * @return array
	 */
	public function getVars(){
		return $this->get_template_vars();
	}
	
	/**
	 * @param array $vars
	 * @return Zest_Smarty
	 */
	public function setVars($vars){
		$this->clearVars()->assign($vars);
		return $this;
	}
	
	/**
	 * @return Zest_Smarty
	 */
	public function clearVars(){
		$this->clear_all_assign();
		return $this;
	}
	
	// DOSSIERS
	
	/**
	 * @param string $cacheDir
	 * @return Zest_Smarty
	 */
	public function setCacheDir($cacheDir){
		$this->cache_dir = $cacheDir;
		return $this;
	}
	
	/**
	 * @param string $compileDir
	 * @return Zest_Smarty
	 */
	public function setCompileDir($compileDir){
		$this->compile_dir = $compileDir;
		return $this;
	}
	
	/**
	 * @param string $configDir
	 * @return Zest_Smarty
	 */
	public function setConfigDir($configDir){
		$this->config_dir = $configDir;
		return $this;
	}
	
	/**
	 * @param array $dirs
	 * @return Zest_Smarty
	 */
	public function setPluginsDir($dirs){
		$this->plugins_dir = array();
		$this->addPluginDir($dirs);
		$this->addPluginDir('plugins');
		return $this;
	}
	
	/**
	 * @param array|string $pluginDir
	 * @return Zest_Smarty
	 */
	public function addPluginDir($pluginDir){
		if(is_string($pluginDir) && $pluginDir == 'auto'){
			$frontController = Zest_Controller_Front::getInstance();
			$controllerDirectories = $frontController->getControllerDirectory();
			
			$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
			$inflector = $viewRenderer->getInflector()->setTarget($viewRenderer->getViewBasePathSpec());
			
			$pluginDir = array();
			foreach($controllerDirectories as $module => $controllerDirectoriy){
				$pluginDir[] = $inflector->filter(array('moduleDir' => $frontController->getModuleDirectory($module))).'/plugins';
			}
		}
		if(is_array($pluginDir)){
			foreach($pluginDir as $dir){
				$this->addPluginDir($dir);
			}
		}
		else{
			$this->plugins_dir[] = $pluginDir;
		}
		return $this;
	}
	
	/**
	 * @param string $module
	 * @param string $filename
	 */
	public function getPluginPathname($module, $filename){
		$frontController = Zest_Controller_Front::getInstance();
		$moduleDir = $frontController->getModuleDirectory($module);
		
		if($moduleDir){
			$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
			$inflector = $viewRenderer->getInflector()->setTarget($viewRenderer->getViewBasePathSpec());
			
			$pluginPathname = $inflector->filter(array('moduleDir' => $moduleDir)).'/plugins/'.$filename;
			if(is_readable($pluginPathname)){
				return $pluginPathname;
			}
		}
		
		return null;
	}
	
	/**
	 * @param string $templateDir
	 * @return Zest_Smarty
	 */
	public function setTemplateDir($templateDir){
		$this->template_dir = $templateDir;
		return $this;
	}
	
	/**
	 * @param string|integer $filePerms
	 * @return Zest_Smarty
	 */
	public function setFilePerms($filePerms){
		$this->_file_perms = intval($filePerms, 8);
		return $this;
	}
	
	/**
	 * @param string|integer $dirPerms
	 * @return Zest_Smarty
	 */
	public function setDirPerms($dirPerms){
		$this->_dir_perms = intval($dirPerms, 8);
		return $this;
	}
	
	/**
	 * @param boolean $security
	 * @return Zest_Smarty
	 */
	public function setSecurity($security){
		$this->security = (bool) $security;
		return $this;
	}
	
	/**
	 * @param boolean $useSubDirs
	 * @return Zest_Smarty
	 */
	public function setUseSubDirs($useSubDirs){
		$this->use_sub_dirs = (bool) $useSubDirs;
		return $this;
	}
	
	// COMPILATION / CACHE
	
	/**
	 * @param string $compileId
	 * @return Zest_Smarty
	 */
	public function setCompileId($compileId){
		$this->compile_id = $compileId;
		return $this;
	}
	
	/**
	 * @param boolean $forceCompile
	 * @return Zest_Smarty
	 */
	public function setForceCompile($forceCompile){
		$this->force_compile = (bool) $forceCompile;
		return $this;
	}
	
	/**
	 * @param boolean $compileCheck
	 * @return Zest_Smarty
	 */
	public function setCompileCheck($compileCheck){
		$this->compile_check = (bool) $compileCheck;
		return $this;
	}
	
	/**
	 * @param integer $caching
	 * @return Zest_Smarty
	 */
	public function setCaching($caching){
		$this->caching = (int) $caching;
		return $this;
	}
	
	/**
	 * @param integer $cacheLifetime
	 * @return Zest_Smarty
	 */
	public function setCacheLifetime($cacheLifetime){
		$this->cache_lifetime = (int) $cacheLifetime;
		return $this;
	}
	
	/**
	 * @param boolean $cacheModifiedCheck
	 * @return Zest_Smarty
	 */
	public function setCacheModifiedCheck($cacheModifiedCheck){
		$this->cache_modified_check = (bool) $cacheModifiedCheck;
		return $this;
	}
	
	/**
	 * @param integer $errorReporting
	 * @return Zest_Smarty
	 */
	public function setErrorReporting($errorReporting){
		$this->error_reporting = (int) $errorReporting;
		if($this->error_reporting == 0){
			$this->error_reporting = null;
		}
		return $this;
	}
	
	/**
	 * @param array|object $foreachable
	 * @return array
	 */
	public static function recursiveToArray($foreachable){
		if(is_array($foreachable)){
			foreach($foreachable as $key => $value){
				$foreachable[$key] = self::recursiveToArray($foreachable[$key]);
			}
		}
		else if(is_object($foreachable)){
			foreach($foreachable as $key => $value){
				$foreachable->$key = self::recursiveToArray($foreachable->$key);
			}
		}
		else{
			return $foreachable;
		}
		return (array) $foreachable;
	}
	
//	public static function parseDir($dirPath, $recursive=false, $options = array()){
//		$arrayFiles = array();
//		$dir = new DirectoryIterator($dirPath);
//		foreach($dir as $res){
//			if($res->isDot()){
//				continue;
//			}
//			if($res->isFile()){
//				$tags = self::parseFile($res->getPathname(), $options);
//				if(count($tags)){
//					$arrayFiles[$res->getFilename()] = $tags;
//				}
//			}
//			else if($recursive){
//				$arrayFiles = array_merge($arrayFiles, self::parseDir($res->getPathname(), $recursive, $options));
//				// $arrayFiles[$res->getFilename()] = self::parseDir($res->getPathname());
//			}
//		}
//		return $arrayFiles;
//	}
//	
//	public static function parseFile($filePath, $options = array()){
//		$content = file_get_contents($filePath);
//		return self::parseContent($content, $options);
//	}
//	
//	public static function parseContent($content, $options = array()){
//		if(isset($options['restrict'])){
//			return self::_parseContentRestrict($content, $options);
//		}
//		
//		$arrayParse = array();
//		
//		// récupération des tags Smarty
//		$ldq = preg_quote('{', '~');
//		$rdq = preg_quote('}', '~');
//		if(isset($options['left_delimiter']) && isset($options['right_delimiter'])){
//			$ldq = preg_quote($options['left_delimiter'], '~');
//			$rdq = preg_quote($options['right_delimiter'], '~');
//		}
//		
//		preg_match_all("~{$ldq}\s*(.*?)\s*{$rdq}~s", $content, $template_tags,  PREG_SET_ORDER);
//						
//		// récupération des attributs des tags Smarty
//		$compiler = new Smarty_Compiler();
//		$compiler->left_delimiter = $ldq;
//		$compiler->right_delimiter = $rdq;
//		
//		if(is_array($template_tags)){
//			foreach($template_tags as $tag){
//				$tag = $tag[1];
//				if(! preg_match('~^(?:(' . $compiler->_num_const_regexp . '|' . $compiler->_obj_call_regexp . '|' . $compiler->_var_regexp . '|\/?' . $compiler->_reg_obj_regexp . '|\/?' . $compiler->_func_regexp . ')(' . $compiler->_mod_regexp . '*))(?:\s+(.*))?$~xs', $tag, $match)) {
//				}
//				
//				if(count($match)==4){
//					// on récupère le nom du tag
//					$tagName = $match[1];
//					
//					// on récupère les paramètres
//					$tagArgs = isset($match[3]) ? $match[3] : null;
//					
//					if($tagName!='if' && $tagArgs){
//						$tagArgs = $compiler->_parse_attrs($tagArgs);
//						foreach($tagArgs as $arg => $value){
//							if(preg_match('/^\'|\".*\'|\"$/', $value)){
//								$value = substr($value, 1, strlen($value)-2);
//							}
//							$tplVars = '/\$this->_tpl_vars\[\'(.*)\'\]/';
//							if(preg_match($tplVars, $value)){
//								$value = preg_replace($tplVars, '$\\1', $value);
//								$value = str_replace('\'][\'', '.', $value);
//							}
//							if(is_int(strpos($value, '$this->'))){
//								unset($tagArgs[$arg]);
//								continue;
//							}
//							$tagArgs[$arg] = $value;
//						}
//					}
//					
//					if(!isset($arrayParse[$tagName])){
//						$arrayParse[$tagName] = array();
//					}
//					
//					$arrayParse[$tagName][] = $tagArgs;
//				}
//				
//			}
//		}
//		
//		return $arrayParse;
//	}
//	
//	private static function _parseContentRestrict($content, $options){
//		$arrayTagName = null;
//		$arrayTagArg = null;
//		if(isset($options['restrict'])){
//			$restrict = $options['restrict'];
//			if(isset($restrict['tag'])){
//				$arrayTagName = $restrict['tag'];
//			}
//			if(isset($restrict['arg'])){
//				$arrayTagArg = $restrict['arg'];
//			}
//			unset($options['restrict']);
//		}
//		
//		if($arrayTagName && !is_array($arrayTagName)){
//			$arrayTagName = array($arrayTagName);
//		}
//		if($arrayTagArg && !is_array($arrayTagArg)){
//			$arrayTagArg = array($arrayTagArg);
//		}
//		
//		$arrayParse = self::parseContent($content, $options);
//		
//		foreach($arrayParse as $tagName => $tags){
//			if($arrayTagName && !in_array($tagName, $arrayTagName)){
//				unset($arrayParse[$tagName]);
//				continue;
//			}
//			
//			foreach($tags as $tagClef => $tagArgs){
//				if($arrayTagArg){
//					if(is_array($tagArgs)){
//						foreach($tagArgs as $tagArgName => $tagArgValue){
//							if(!in_array($tagArgName, $arrayTagArg)){
//								unset($arrayParse[$tagName][$tagClef][$tagArgName]);
//							}
//						}
//					}
//					else{
//						unset($arrayParse[$tagName][$tagClef]);
//						continue;
//					}
//				}
//				if(count($arrayParse[$tagName][$tagClef])==0){
//					unset($arrayParse[$tagName][$tagClef]);
//				}
//			}
//			
//			if(count($arrayParse[$tagName])==0){
//				unset($arrayParse[$tagName]);
//			}
//		}
//		
//		return $arrayParse;
//	}
//	
//	public static function parseTFunctions($dirPath) {
//		$arrayFiles = self::parseDir($dirPath, true, array('restrict' => array('tag' => 't', 'arg' => 'str')));
//		
//		foreach($arrayFiles as $file => $arrayTags){
//			echo '; <b>'.$file.'</b><br/>';
//			foreach($arrayTags as $tags){
//				foreach($tags as $tagArgs){
//					$startTag = '';
//					$endTag = '';
//					
//					$text = $tagArgs['str'];
//					if(is_int(strpos($text, '=')) || is_int(strpos($text, '$'))){
//						$startTag = '<span style="color: red;">';
//						$endTag = '</span>';
//					}
//					echo $startTag.CS_Encode::encode($text).$endTag.' =&nbsp;<br/>';
//				}
//			}
//			echo '<br/>';
//		}
//		
//		exit;
//	}
	
}