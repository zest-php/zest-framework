<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage Helper
 */
class Zest_View_Helper_Head extends Zend_View_Helper_Abstract{
	
	/**
	 * @return Zest_View_Helper_Head
	 */
	public function head($type = null, $content = null){
		if(is_null($type)){
			return $this;
		}
		$type = strtolower($type);
		if(method_exists($this, $type)){
			return $this->$type($content);
		}
		throw new Zest_View_Exception(sprintf('Le type "%s" n\'existe pas.', $type));
	}
	
	/**
	 * @param string $title
	 * @param string $placement
	 * @return Zest_View_Helper_Head|string
	 */
	public function title($title = null, $placement = Zend_View_Helper_Placeholder_Container_Abstract::APPEND){
		if(is_null($title)){
			return strip_tags($this->view->headTitle());
		}
		$this->view->headTitle($title, $placement);
		return $this;
	}
	
	/**
	 * @param string $keywords
	 * @param string $placement
	 * @return Zest_View_Helper_Head|array
	 */
	public function keywords($keywords = null, $placement = Zend_View_Helper_Placeholder_Container_Abstract::APPEND){
		if(is_null($keywords)){
			$keywords = array();
			foreach($this->view->headMeta() as $meta){
				if(isset($meta->name) && $meta->name == 'keywords' && $meta->content){
					$keywords[] = $meta->content;
				}
			}
			return $keywords;
		}
		$this->view->headMeta($keywords, 'keywords', 'name', array(), $placement);
		return $this;
	}
	
	/**
	 * @param string $description
	 * @param string $placement
	 * @return Zest_View_Helper_Head|array
	 */
	public function description($description = null, $placement = Zend_View_Helper_Placeholder_Container_Abstract::APPEND){
		if(is_null($description)){
			$description = array();
			foreach($this->view->headMeta() as $meta){
				if(isset($meta->name) && $meta->name == 'description' && $meta->content){
					$description[] = $meta->content;
				}
			}
			return $description;
		}
		$this->view->headMeta($description, 'description', 'name', array(), $placement);
		return $this;
	}
	
	/**
	 * @param string $href
	 * @param string|array $media
	 * @param boolean $prepend
	 * @return Zest_View_Helper_Head|array
	 */
	public function css($href = null, $media = null, $prepend = false){
		if(is_null($href)){
			$href = array();
			foreach($this->view->headLink() as $link){
				if(isset($link->type) && $link->type == 'text/css' && !empty($link->href)){
					$href[] = $link->href;
				}
			}
			return $href;
		}
		if(is_bool($media)){
			$prepend = $media;
			$media = null;
		}
		if(is_null($media)){
			$media = 'all';
		}
		if($prepend){
			$this->view->headLink()->prependStylesheet($href, $media);
		}
		else{
			$this->view->headLink()->appendStylesheet($href, $media);
		}
		return $this;
	}
	
	/**
	 * @param string $src
	 * @param boolean $prepend
	 * @return Zest_View_Helper_Head|array
	 */
	public function js($src = null, $prepend = false){
		if(is_null($src)){
			$src = array();
			foreach($this->view->headScript() as $script){
				if(isset($script->type) && $script->type == 'text/javascript' && !empty($script->attributes['src'])){
					$src[] = $script->attributes['src'];
				}
			}
			return $src;
		}
		if($prepend){
			$this->view->headScript()->prependFile($src);
		}
		else{
			$this->view->headScript()->appendFile($src);
		}
		return $this;
	}
	
	/**
	 * @param string $href
	 * @param string $title
	 * @param boolean $prepend
	 * @return Zest_View_Helper_Head|array
	 */
	public function rss($href = null, $title = '', $prepend = false){
		if(is_null($href)){
			$href = array();
			foreach($this->view->headLink() as $link){
				if(isset($link->type) && $link->type == 'application/rss+xml' && !empty($link->href)){
					$href[] = $link->href;
				}
			}
			return $href;
		}
		if($prepend){
			$this->view->headLink()->prependAlternate($href, 'application/rss+xml', $title);
		}
		else{
			$this->view->headLink()->appendAlternate($href, 'application/rss+xml', $title);
		}
		return $this;
	}
	
	/**
	 * @param string $href
	 * @return Zest_View_Helper_Head
	 */
	public function favicon($href){
		$this->view->headLink(array(
//			'rel' => 'favicon',
			'rel' => 'shortcut icon',
			'type' => 'image/x-icon',
			'href' => $href
		));
		return $this;
	}
	
	/**
	 * @param string $lang
	 * @param string $placement
	 * @return Zest_View_Helper_Head
	 */
	public function lang($lang, $placement = Zend_View_Helper_Placeholder_Container_Abstract::SET){
		$this->view->headMeta($lang, 'language', 'name', array(), $placement);
		$this->view->headMeta($lang, 'language', 'http-equiv', array(), $placement);
		return $this;
	}
	
	/**
	 * @param string $robots
	 * @param string $placement
	 * @return Zest_View_Helper_Head
	 */
	public function robots($robots, $placement = Zend_View_Helper_Placeholder_Container_Abstract::SET){
		$this->view->headMeta($robots, 'robots', 'name', array(), $placement);
		$this->view->headMeta($robots, 'robots', 'http-equiv', array(), $placement);
		return $this;
	}
	
	/**
	 * @param string $href
	 * @return Zest_View_Helper_Head
	 */
	public function canonical($href){
		$this->view->headLink(array(
			'rel' => 'canonical',
			'href' => $href
		));
		return $this;
	}
	
}