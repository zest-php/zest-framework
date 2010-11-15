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
	protected $_styleSheet = null;

	/**
	 * @var boolean
	 */
	protected $_formatOutput = true;
	
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
	 * @param string $styleSheet
	 * @return Zest_View_Helper_Navigation_Sitemap
	 */
	public function setStyleSheet($styleSheet){
		$this->_styleSheet = $styleSheet;
		return $this;
	}
	
	/**
	 * @return Zend_Navigation_Container
	 */
	public function getContainer(){
		if(!$this->_container){
			$this->_container = new Zend_Navigation();
			foreach($this->_data as $options){
				$this->_container->addPage($options);
			}
		}
		return $this->_container;
	}
	
	/**
	 * @param Zend_Navigation_Container $container
	 * @return DOMDocument
	 */
	public function getDomSitemapindex(Zend_Navigation_Container $container = null){
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
		
		$xml = $this->getUseXmlDeclaration() ? $dom->saveXML() : $dom->saveXML($dom->documentElement);
		$xml = rtrim($xml, PHP_EOL);
		
		if($this->_styleSheet){
			$replace = '<?xml version="1.0" encoding="UTF-8"?>';
			$xsl = '<?xml-stylesheet type="text/xsl" href="'.$this->_styleSheet.'"?>';
			$xml = str_replace($replace, $replace.PHP_EOL.$xsl, $xml);
		}
		
		return $xml;
	}
	
	/**
	 * @return void
	 */
	public function send(){
		if(headers_sent()){
			throw new Zest_Sitemap_Exception('Impossible d\'envoyer le sitemap car les headers ont déjà été envoyés.');
		}
		
		Zest_Controller_Front::getInstance()->getResponse()->setHeader('Content-Type', 'text/xml; charset=UTF-8');
		echo $this->render();
	}
	
}