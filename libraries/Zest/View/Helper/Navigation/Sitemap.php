<?php

/**
 * @category Zest
 * @package Zest_View
 * @subpackage Helper
 */
class Zest_View_Helper_Navigation_Sitemap extends Zend_View_Helper_Navigation_Sitemap{
	
	/**
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 * @var boolean
	 */
	protected $_useSitemapindex = false;
	
	/**
	 * @var boolean
	 */
	protected $_useUrlset = false;
	
	/**
	 * @var string
	 */
	protected $_stylesheet = null;

	/**
	 * @var boolean
	 */
	protected $_formatOutput = true;
	
	/**
	 * @var boolean
	 */
	protected $_recursiveAllContainerVisible = true;
	
	/**
	 * @var boolean
	 */
	protected $_uniqueHref = true;
	
	/**
	 * @var array
	 */
	protected $_uniqueHrefTest = array();
	
	/**
	 * @var string
	 */
	const SITEMAPINDEX_XSD = 'http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd';
	
	/**
	 * @param string $loc
	 * @param array $options
	 * @return Zest_Sitemap
	 */
	public function addUrl($loc, array $options = array()){
		if($this->_useSitemapindex){
			throw new Zest_Sitemap_Exception('Un sitemap ne peut pas être composé à la fois de "sitemapindex" et de "urlset".');
		}
		$this->_useUrlset = true;
		
		$options['uri'] = $loc;
		$this->_data[] = $options;
		
		return $this;
	}
	
	/**
	 * @param string $loc
	 * @param array $options
	 * @return Zest_Sitemap
	 */
	public function addSitemap($loc, array $options = array()){
		if($this->_useUrlset){
			throw new Zest_Sitemap_Exception('Un sitemap ne peut pas être composé à la fois de "sitemapindex" et de "urlset".');
		}
		$this->_useSitemapindex = true;
		
		$options['uri'] = $loc;		
		$this->_data[] = $options;
		
		return $this;
	}
	
	/**
	 * @param string $stylesheet
	 * @return Zest_View_Helper_Navigation_Sitemap
	 */
	public function setStylesheet($stylesheet){
		$this->_stylesheet = $stylesheet;
		return $this;
	}
	
	/**
	 * @param Zend_Navigation_Container $container
	 * @return Zest_View_Helper_Navigation_Sitemap
	 */
	public function setContainer(Zend_Navigation_Container $container = null){
		if($this->_recursiveAllContainerVisible && $container){
			$this->_recursiveAllContainerVisible($container);
		}
		parent::setContainer($container);
		return $this;
	}
	
	/**
	 * @return Zend_Navigation_Container
	 */
	public function getContainer(){
		if(is_null($this->_container)){
			$container = parent::getContainer();
			if($this->_data){
				foreach($this->_data as $options){
					$container->addPage($options);
				}
			}
			if($this->_recursiveAllContainerVisible){
				$this->_recursiveAllContainerVisible($container);
			}
		}
		return $this->_container;
	}
	
	/**
	 * @param Zend_Navigation_Container $container
	 * @return void
	 */
	protected function _recursiveAllContainerVisible(Zend_Navigation_Container $container){
		foreach($container as $page){
			$page->setVisible(true);
			$this->_recursiveAllContainerVisible($page);
		}
	}
	
	/**
	 * @param Zend_Navigation_Container $container
	 * @return DOMDocument
	 */
	public function getDomSitemap(Zend_Navigation_Container $container = null){
		$this->_uniqueHrefTest = array();
		return parent::getDomSitemap($container);
	}
	
	/**
	 * @param Zend_Navigation_Container $container
	 * @return DOMDocument
	 */
	public function getDomSitemapindex(Zend_Navigation_Container $container = null){
		$this->_uniqueHrefTest = array();
		
		if(is_null($container)){
			$container = $this->getContainer();
		}

		// check if we should validate using our own validators
		if($this->getUseSitemapValidators()){
			// create validators
			$locValidator = new Zend_Validate_Sitemap_Loc();
			$lastmodValidator = new Zend_Validate_Sitemap_Lastmod();
		}

		// create document
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->formatOutput = $this->getFormatOutput();

		// ...and sitemapindex (root) element
		$sitemapIndex = $dom->createElementNS(self::SITEMAP_NS, 'sitemapindex');
		$dom->appendChild($sitemapIndex);

		// create iterator
		$iterator = new RecursiveIteratorIterator($container, RecursiveIteratorIterator::SELF_FIRST);

		$maxDepth = $this->getMaxDepth();
		if(is_int($maxDepth)){
			$iterator->setMaxDepth($maxDepth);
		}
		$minDepth = $this->getMinDepth();
		if(!is_int($minDepth) || $minDepth < 0){
			$minDepth = 0;
		}

		// iterate container
		foreach($iterator as $page){
			if($iterator->getDepth() < $minDepth || !$this->accept($page)){
				// page should not be included
				continue;
			}

			// get absolute url from page
			if(!$url = $this->url($page)){
				// skip page if it has no url (rare case)
				continue;
			}

			// create url node for this page
			$sitemapNode = $dom->createElementNS(self::SITEMAP_NS, 'sitemap');
			$sitemapIndex->appendChild($sitemapNode);

			if($this->getUseSitemapValidators() && !$locValidator->isValid($url)){
				throw new Zest_Sitemap_Exception(sprintf('L\'url "%s" n\'est pas valide.', $url));
			}

			// put url in 'loc' element
			$element = $dom->createElementNS(self::SITEMAP_NS, 'loc', $url);
			$sitemapNode->appendChild($element);

			// add 'lastmod' element if a valid lastmod is set in page
			if(isset($page->lastmod)){
				$lastmod = strtotime((string) $page->lastmod);

				// prevent 1970-01-01...
				if($lastmod !== false){
					$lastmod = date('c', $lastmod);
				}

				if(!$this->getUseSitemapValidators() || $lastmodValidator->isValid($lastmod)){
					$element = $dom->createElementNS(self::SITEMAP_NS, 'lastmod', $lastmod);
					$sitemapNode->appendChild($element);
				}
			}
		}

		// validate using schema if specified
		if($this->getUseSchemaValidation()){
			if(!@$dom->schemaValidate(self::SITEMAPINDEX_XSD)){
				throw new Zest_Sitemap_Exception(sprintf('En se basant sur le XSD "%s", le sitemap n\'est pas valide.', self::SITEMAPINDEX_XSD));
			}
		}

		return $dom;
	}
	
	/**
	 * @param Zend_Navigation_Container $container
	 * @return string
	 */
	public function render(Zend_Navigation_Container $container = null){
		if($this->_useSitemapindex){
			$dom = $this->getDomSitemapindex($container);
		}
		else{
			$dom = $this->getDomSitemap($container);
		}
		
		if($this->_stylesheet){
			$stylesheet = new DOMProcessingInstruction('xml-stylesheet', 'href="'.$this->_stylesheet.'" type="text/xsl"');
			if($this->_useSitemapindex){
				$sitemapindex = $dom->getElementsByTagName('sitemapindex')->item(0);
				$sitemapindex->parentNode->insertBefore($stylesheet, $sitemapindex);
			}
			else{
				$urlset = $dom->getElementsByTagName('urlset')->item(0);
				$urlset->parentNode->insertBefore($stylesheet, $urlset);
			}
		}
		
		$xml = $this->getUseXmlDeclaration() ? $dom->saveXML() : $dom->saveXML($dom->documentElement);
		
		return $xml;
	}
	
	/**
	 * @param Zend_Navigation_Page $page
	 * @return string
	 */
	public function url(Zend_Navigation_Page $page){
		$href = parent::url($page);
		if($this->_uniqueHref && in_array($href, $this->_uniqueHrefTest)){
			return;
		}
		$this->_uniqueHrefTest[] = $href;
		return $href;
	}
	
	/**
	 * @return void
	 */
	public function send(Zend_Controller_Response_Abstract $response){
		if(headers_sent()){
			throw new Zest_Sitemap_Exception('Impossible d\'envoyer le sitemap car les headers ont déjà été envoyés.');
		}
		
		$response
			->setHeader('content-type', 'text/xml; charset=utf-8')
			->setBody($this->render())
			->sendResponse();
			
		exit;
	}
	
}