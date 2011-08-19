<?php

/**
 * @category Zest
 * @package Zest_Event
 * @subpackage UnitTests
 */
class Zest_Event_EventTest extends PHPUnit_Framework_TestCase{
	
	protected $_order = array();
	
	protected $_eventType = 'after_action';
	
	protected function setUp(){
		Zest_Event::removeEventListener($this->_eventType);
	}
	
	public function testAddEventListenerNotCallable(){
		$this->setExpectedException('Zest_Event_Exception');
		Zest_Event::addEventListener($this->_eventType, array($this, 'event_undefined'));
		Zest_Event::dispatchEvent($this->_eventType);
	}
	
	public function testAddEventListenerAppend(){
		Zest_Event::addEventListener($this->_eventType, array($this, 'event1'));
		Zest_Event::addEventListener($this->_eventType, array($this, 'event2'));
		Zest_Event::dispatchEvent($this->_eventType);
		$this->assertEquals(array(1, 2), $this->_order);
	}
	
	public function testAddEventListenerOffset(){
		Zest_Event::addEventListener($this->_eventType, array($this, 'event1'), 1);
		Zest_Event::addEventListener($this->_eventType, array($this, 'event2'), 0);
		Zest_Event::dispatchEvent($this->_eventType);
		$this->assertEquals(array(2, 1), $this->_order);
	}
	 
	public function testAddEventListenerOffsetException(){
		$this->setExpectedException('Zest_Event_Exception');
		Zest_Event::addEventListener($this->_eventType, array($this, 'event1'));
		Zest_Event::addEventListener($this->_eventType, array($this, 'event2'), 0);
	}
	
	public function testAppendEventListener(){
		Zest_Event::appendEventListener($this->_eventType, array($this, 'event2'));
		Zest_Event::appendEventListener($this->_eventType, array($this, 'event3'));
		Zest_Event::appendEventListener($this->_eventType, array($this, 'event1'));
		Zest_Event::dispatchEvent($this->_eventType);
		$this->assertEquals(array(2, 3, 1), $this->_order);
	}
	
	public function testPrependEventListener(){
		Zest_Event::prependEventListener($this->_eventType, array($this, 'event2'));
		Zest_Event::prependEventListener($this->_eventType, array($this, 'event3'));
		Zest_Event::prependEventListener($this->_eventType, array($this, 'event1'));
		Zest_Event::dispatchEvent($this->_eventType);
		$this->assertEquals(array(1, 3, 2), $this->_order);
	}
	
	public function testOffsetSetEventListener(){
		Zest_Event::offsetSetEventListener($this->_eventType, array($this, 'event2'), 2);
		Zest_Event::offsetSetEventListener($this->_eventType, array($this, 'event1'), 1);
		Zest_Event::offsetSetEventListener($this->_eventType, array($this, 'event3'), 0);
		Zest_Event::dispatchEvent($this->_eventType);
		$this->assertEquals(array(3, 1, 2), $this->_order);
	}
	
	public function testDispatchEvent(){
		Zest_Event::addEventListener($this->_eventType, array($this, 'event1'));
		Zest_Event::dispatchEvent($this->_eventType);
		$this->assertEquals(array(1), $this->_order);
	}
	
	public function testHasEventListener(){
		$this->assertFalse(Zest_Event::hasEventListener($this->_eventType));
		Zest_Event::addEventListener($this->_eventType, array($this, 'event1'));
		$this->assertTrue(Zest_Event::hasEventListener($this->_eventType));
	}
	
	public function testHasEventListenerCallback(){
		Zest_Event::addEventListener($this->_eventType, array($this, 'event1'));
		$this->assertTrue(Zest_Event::hasEventListener($this->_eventType, array($this, 'event1')));
		$this->assertFalse(Zest_Event::hasEventListener($this->_eventType, array($this, 'event2')));
	}
	
	public function testRemoveEventListener(){
		Zest_Event::addEventListener($this->_eventType, array($this, 'event1'));
		$this->assertTrue(Zest_Event::hasEventListener($this->_eventType));
		
		Zest_Event::removeEventListener($this->_eventType);
		$this->assertFalse(Zest_Event::hasEventListener($this->_eventType));
	}
	
	public function testRemoveEventListenerCallback(){
		Zest_Event::addEventListener($this->_eventType, array($this, 'event1'));
		$this->assertTrue(Zest_Event::hasEventListener($this->_eventType));
		
		Zest_Event::removeEventListener($this->_eventType, array($this, 'event2'));
		$this->assertTrue(Zest_Event::hasEventListener($this->_eventType));
		
		Zest_Event::removeEventListener($this->_eventType, array($this, 'event1'));
		$this->assertFalse(Zest_Event::hasEventListener($this->_eventType));
	}
	
	public function testStopPropagation(){
		Zest_Event::addEventListener($this->_eventType, array($this, 'event1'));
		Zest_Event::addEventListener($this->_eventType, array($this, 'event2'));
		Zest_Event::addEventListener($this->_eventType, array($this, 'event4_stopPropagation'));
		Zest_Event::addEventListener($this->_eventType, array($this, 'event3'));
		Zest_Event::dispatchEvent($this->_eventType);
		$this->assertEquals(array(1, 2, 4), $this->_order);
	}
	
	public function testStopPropagationNotStoppable(){
		$this->setExpectedException('Zest_Event_Exception');
		Zest_Event::addEventListener($this->_eventType, array($this, 'event1'));
		Zest_Event::addEventListener($this->_eventType, array($this, 'event2'));
		Zest_Event::addEventListener($this->_eventType, array($this, 'event4_stopPropagation'));
		Zest_Event::addEventListener($this->_eventType, array($this, 'event3'));
		Zest_Event::dispatchEvent($this->_eventType, array(), false);
	}
	
	public function testGetData(){
		Zest_Event::addEventListener($this->_eventType, array($this, 'event5_data'));
		Zest_Event::dispatchEvent($this->_eventType, array('name' => 'zest'));
		$this->assertEquals(array(5), $this->_order);
	}
	
	public function testGetDataUndefined(){
		$this->setExpectedException('Zest_Event_Exception');
		Zest_Event::addEventListener($this->_eventType, array($this, 'event5_data'));
		Zest_Event::dispatchEvent($this->_eventType);
	}
	
	public function event1(){
		$this->_order[] = 1;
	}
	
	public function event2(){
		$this->_order[] = 2;
	}
	
	public function event3(){
		$this->_order[] = 3;
	}
	
	public function event4_stopPropagation(Zest_Event $event){
		$this->_order[] = 4;
		$event->stopPropagation();
	}
	
	public function event5_data(Zest_Event $event){
		if($event->name === 'zest'){
			$this->_order[] = 5;
		}
	}
	
}