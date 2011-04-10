<?php

/**
 * @category Zest
 * @package Zest_Controller
 */
abstract class Zest_Controller_Crud extends Zest_Controller_Action{
	
	/**
	 * @var string
	 */
	protected $_primaryAttribute = 'id';
	
	/**
	 * @var Zest_Db_Object
	 */
	protected $_object = null;
	
	/**
	 * @var Zest_Db_Object
	 */
	private $_formDb = null;
	
	/**
	 * @return void
	 */
	public function indexAction(){
		$this->view->objects = $this->_getDbMapper()->getArray();
	}

	/**
	 * @return void
	 */
	public function newAction(){
		$this->_object = $this->__getFormDb()->getDbObject();
		$this->view->form = $this->__getFormDb()
			->setAction($this->_helper->url->url(array('action' => 'create')))
			->populateFromDbObject($this->_object);
	}

	/**
	 * @return void
	 */
	public function createAction(){
		$form = $this->__getFormDb();
		
		if($this->getRequest()->isPost()){
			if($form->isValid($this->getRequest()->getPost())){
				$this->_object = $form->getValuesToDbObject()->getDbObject();
				$this->_object->save();
				$this->_gotoRouteAndExit();
			}
		}
		else{
			throw new Zest_Controller_Exception(sprintf('La méthode "%s" n\'est pas correcte.', strtolower($this->getRequest()->getMethod())));
		}
		
		$this->view->form = $form;
		$this->_helper->viewRenderer->setScriptAction('new');
	}

	/**
	 * @return void
	 */
	public function showAction(){
		$this->_object = $this->_getDbMapper()->find($this->_getParam($this->_primaryAttribute));
		if($this->_object->getData($this->_primaryAttribute)){
			$this->view->object = $this->_object; 
		}
	}

	/**
	 * @return void
	 */
	public function editAction(){
		$this->_object = $this->_getDbMapper()->find($this->_getParam($this->_primaryAttribute));
		if($this->_object->getData($this->_primaryAttribute)){
			$this->view->form = $this->__getFormDb()
				->setAction($this->_helper->url->url(array('action' => 'update')))
				->populateFromDbObject($this->_object);
		}
	}

	/**
	 * @return void
	 */
	public function updateAction(){
		$form = $this->__getFormDb();
		
		if($this->getRequest()->isPost()){
			$this->_object = $this->_getDbMapper()->find($this->_getParam($this->_primaryAttribute));
			if($this->_object->getData($this->_primaryAttribute)){
				if($form->isValid($this->getRequest()->getPost())){
					$form->getValuesToDbObject($this->_object);
					$this->_object->save();
					$this->_gotoRouteAndExit();
				}
			}
		}
		else{
			throw new Zest_Controller_Exception(sprintf('La méthode "%s" n\'est pas correcte.', strtolower($this->getRequest()->getMethod())));
		}
		
		$this->view->form = $form;
		$this->_helper->viewRenderer->setScriptAction('edit');
	}

	/**
	 * @return void
	 */
	public function deleteAction(){
		$this->_object = $this->_getDbMapper()->find($this->_getParam($this->_primaryAttribute));
		if($this->_object->getData($this->_primaryAttribute)){
			$this->_object->delete();
			$this->_helper->redirector->gotoRouteAndExit(array('action' => 'index', $this->_primaryAttribute => null));
		}
	}
	
	/**
	 * @return Zest_Form_Db
	 */
	abstract protected function _getFormDb();
	
	/**
	 * @return Zest_Form_Db
	 */
	private function __getFormDb(){
		if(is_null($this->_formDb)){
			$this->_formDb = $this->_getFormDb();
			
			$method = strtolower($this->_formDb->getMethod());
			if($method != 'post'){
				throw new Zest_Controller_Exception(sprintf('La méthode "%s" n\'est pas autorisée.', $method));
			}
		}
		return $this->_formDb;
	}
	
	/**
	 * @return void
	 */
	protected function _gotoRouteAndExit(){
		$this->_helper->redirector->gotoRouteAndExit(array('action' => 'show', $this->_primaryAttribute => $this->_object->getData($this->_primaryAttribute)));
	}
	
	/**
	 * @return Zest_Db_Object_Mapper
	 */
	protected function _getDbMapper(){
		$dbModel = $this->__getFormDb()->getDbMapper();
		if(!$dbModel instanceof Zest_Db_Object_Mapper){
			throw new Zest_Controller_Exception('Le model doit hériter de Zest_Db_Object_Mapper.');
		}
		return $dbModel;
	}
	
}