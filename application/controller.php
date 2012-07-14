<?php

class App_Controller {
	public $view;
	
	/**
	 * 
	 * @var App_Request
	 */
	protected $_request;
	
	public function __construct()
	{
		$this->view = new App_View();
		$this->_request = App_Request::getInstance();
		
		$this->init();
	}
	
	public function setRequest($oRequest)
	{
		$this->_request = $oRequest;
		return $this;
	}
	
	public function getRequest($o)
	{
		return $this->_request;
	}
	
	public function init() 
	{
		$viewScript = strtolower(str_replace('Controller_', '', $this->_request->getController())) . '/' . strtolower($this->_request->getAction()) . '.phtml';
		$this->view->setView($viewScript);
	}

	
	public function __call($methodName, $arguments)
	{
		throw new Exception('Action "' . $methodName . '" is not defined!');
	}
}