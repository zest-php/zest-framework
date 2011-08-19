<?php

class IndexController extends Zest_Controller_Action{
	
	public function indexAction(){
		$this->view->variable = $this->_getParam('variable');
	}

}