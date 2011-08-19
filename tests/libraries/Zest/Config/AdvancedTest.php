<?php

/**
 * @category Zest
 * @package Zest_Config
 * @subpackage UnitTests
 */
class Zest_Config_AdvancedTest extends PHPUnit_Framework_TestCase{
	
	/**
	 * @var Zest_Config_Advanced
	 */
	protected $_config = null;
	
	/**
	 * @var string
	 */
	protected $_section = 'second_section';
	
	public static function setUpBeforeClass(){
		define('ZEST_CONFIG_ADVANCEDTEST_CONSTANT', 'constant');
		define('ZEST_CONFIG_ADVANCEDTEST_DIR', dirname(self::_getPathname()));
	}
	
	protected function setUp(){
		$this->_config = new Zest_Config_Advanced(self::_getPathname(), array('section' => $this->_section));
		
		$dir = dirname($this->_getCacheFile());
		if(file_exists($dir)){
			foreach(glob($dir.'/*') as $file){
				unlink($file);
			}
		}
		else{
			mkdir($dir);
		}
	}
	
	protected function tearDown(){
		unset($this->_config);
	}

	public function testGet(){
		$this->assertEquals('just a string value', $this->_config->getConfig('tests.get'));
	}

	public function testSection(){
		$this->assertEquals($this->_section, $this->_config->getConfig('tests.section'));
	}

	public function testConstant(){
		$this->assertEquals(ZEST_CONFIG_ADVANCEDTEST_CONSTANT, $this->_config->getConfig('tests.constant'));
	}
	
	public function testVar(){
		$this->assertEquals('just a string value', $this->_config->getConfig('tests.var'));
	}
	
	public function testRecursiveVar(){
		$this->assertEquals('just a string value', $this->_config->getConfig('tests.recursive.var'));
	}
	
	public function testChildren(){
		$this->assertEquals('child', $this->_config->getConfig('tests.inherit'));
	}
	
	public function testChildrenVarsOverride(){
		$this->assertEquals('I override you', $this->_config->getConfig('tests.children.override_var'));
	}

	public function testCacheFileWrite(){
		unset($this->_config);
		$cache = $this->_getCacheFile();
		$this->_config = new Zest_Config_Advanced(self::_getPathname(), array(
			'section' => $this->_section,
			'cache_file' => $cache
		));
		
		$children = glob(dirname($cache).'/*');
		$this->assertEquals(1, count($children));
		$this->assertEquals('cache_section_second_section.php', basename(current($children)));
	}

	public function testCacheFileLoad(){
		unset($this->_config);
		
		$cache = $this->_getCacheFile();
		$cacheFile = sprintf($cache, $this->_section).'.php';
		
		$config = new Zest_Config_Advanced(self::_getPathname(), array(
			'section' => $this->_section,
			'cache_file' => $cache
		));
		$time1 = filemtime($cacheFile);
		unset($config);
		
		sleep(1);
		
		$config = new Zest_Config_Advanced(self::_getPathname(), array(
			'section' => $this->_section,
			'cache_file' => $cache
		));
		$time2 = filemtime($cacheFile);
		
		$this->assertEquals($time1, $time2);
		$this->assertEquals('just a string value', $config->getConfig('tests.get'));
		unset($config);
	}

	public function testConfigChangeCacheFileReload(){
		unset($this->_config);
		
		$cache = $this->_getCacheFile();
		$cacheFile = sprintf($cache, $this->_section).'.php';
		
		$config = new Zest_Config_Advanced(self::_getPathname(), array(
			'section' => $this->_section,
			'cache_file' => $cache
		));
		$time1 = filemtime($cacheFile);
		unset($config);
		
		sleep(1);
		
		touch(self::_getPathname());
		$config = new Zest_Config_Advanced(self::_getPathname(), array(
			'section' => $this->_section,
			'cache_file' => $cache
		));
		$time2 = filemtime($cacheFile);
		unset($config);
		
		$this->assertNotEquals($time1, $time2);
	}

	public function testConfigChangeChildrenCacheFileReload(){
		unset($this->_config);
		
		$cache = $this->_getCacheFile();
		$cacheFile = sprintf($cache, $this->_section).'.php';
		
		$config = new Zest_Config_Advanced(self::_getPathname(), array(
			'section' => $this->_section,
			'cache_file' => $cache
		));
		$time1 = filemtime($cacheFile);
		unset($config);
		
		sleep(1);
		
		touch(dirname(self::_getPathname()).'/advanced_children.ini');
		$config = new Zest_Config_Advanced(self::_getPathname(), array(
			'section' => $this->_section,
			'cache_file' => $cache
		));
		$time2 = filemtime($cacheFile);
		unset($config);
		
		$this->assertNotEquals($time1, $time2);
	}
	
	protected function _getCacheFile(){
		return Zest_AllTests::getTempDir().'/Zest_Config_AdvancedTest/cache_section_%s';
	}
	
	protected static function _getPathname(){
		return Zest_AllTests::getDataDir().'/config/advanced.ini';
	}
	
}