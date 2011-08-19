<?php

/**
 * @category Zest
 * @package Zest_Data
 * @subpackage UnitTests
 */
class Zest_Data_DataTest extends PHPUnit_Framework_TestCase{
	
	/**
	 * @var Zest_Data
	 */
	protected $_data = null;
	
	protected function setUp(){
		$this->_data = new Zest_Data();
	}
	
	protected function tearDown(){
		unset($this->_data);
	}
	
	public function testConstruct(){
		$data = new Zest_Data(array('test' => 'zest'));
		$this->assertEquals('zest', $data->test);
	}
	
	public function testGet(){
		$this->_data->test = 'zest';
		$this->assertEquals('zest', $this->_data->test);
		$this->assertEquals('zest', $this->_data->getData('test'));
	}
	
	public function testSet(){
		$this->_data->setData('test1', 'zest1');
		$this->_data->test2 = 'zest2';
		$this->assertEquals('zest1', $this->_data->test1);
		$this->assertEquals('zest2', $this->_data->test2);
	}
	
	public function testSetArray(){
		$this->_data->setData(array('test' => 'zest'));
		$this->assertEquals('zest', $this->_data->test);
	}
	
	public function testHas(){
		$this->_data->test = 'zest';
		$this->assertTrue($this->_data->hasData('test'));
		$this->assertFalse($this->_data->hasData('zest'));
	}
	
	public function testIsset(){
		$this->_data->test = 'zest';
		$this->assertTrue(isset($this->_data->test));
		$this->assertFalse(isset($this->_data->zest));
	}
	
	public function testRemove(){
		$this->_data->test = 'zest';
		$this->_data->removeData('test');
		$this->assertFalse(isset($this->_data->test));
	}
	
	public function testUnset(){
		$this->_data->test = 'zest';
		unset($this->_data->test);
		$this->assertFalse(isset($this->_data->test));
	}
	
	public function testAppendData(){
		$this->_data->appendData('test', 'zest1');
		$this->_data->appendData('test', 'zest2');
		$this->assertEquals($this->_data->test, array('zest1', 'zest2'));
	}
	
	public function testPrependData(){
		$this->_data->prependData('test', 'zest1');
		$this->_data->prependData('test', 'zest2');
		$this->assertEquals($this->_data->test, array('zest2', 'zest1'));
	}
	
	public function testIterator(){
		$this->_data->test1 = 'zest1';
		$this->_data->test2 = 'zest2';
		
		$i = 1;
		foreach($this->_data as $key => $value){
			$this->assertEquals('test'.$i, $key);
			$this->assertEquals('zest'.$i, $value);
			$i++;
		}
	}
	
	public function testTo(){
		$this->_data->test = 'zest';
		$this->assertEquals($this->_data->toArray(), array('test' => 'zest'));
		
		$std = $this->_data->toStdClass();
		$this->assertInstanceOf('stdClass', $std);
		$this->assertEquals((array) $std, array('test' => 'zest'));
	}
	
}