<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage UnitTests
 */
class Zest_File_Transfer_Adapter_HttpTest extends PHPUnit_Framework_TestCase{

	public function testPluginLoaderLoad(){
		$http = new Zest_File_Transfer_Adapter_Http();
		$className = $http->getPluginLoader(Zest_File_Transfer_Adapter_Http::FILTER)->load('rename');
		$this->assertEquals('Zest_Filter_File_Rename', $className);
	}
	
}