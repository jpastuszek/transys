<?php

class App_Request {
	
	private static $_instance;
	
	protected $_params;
	
	protected $_controller;
	
	protected $_action;
	
	public function __construct()
	{
		$this->_params = new ArrayObject();
		self::$_instance = $this;
		
		$aTmp = explode('?', trim($_SERVER['REQUEST_URI'], '/'));
		$sRequestUri = array_shift($aTmp);
		
		$aRequest = explode('/', $sRequestUri);
		
		if (count($aRequest) > 0 && strlen($aRequest[0]) > 0) {
			$this->setController(array_shift($aRequest));
		} else {
			$this->setController('default');
		}
		if (count($aRequest) > 0 && strlen($aRequest[0]) > 0) {
			$this->setAction(array_shift($aRequest));
		} else {
			$this->setAction('index');
		}
		
		
		// parse params
		while (count($aRequest) > 1) {
			$sParamName = array_shift($aRequest);
			$sParamValue = array_shift($aRequest);
			
			$this->setParam($sParamName, $sParamValue);
			
		}
		
		return $this;
	}
	
	/**
	 * @return $this
	 */
	public static function getInstance()
	{
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	public function setController($value)
	{
		$this->_controller = $value;
		
		return $this;
	}
	
	public function setAction($value)
	{
		$this->_action = $value;
		
		return $this;
	}
	
	public function setParam($name, $value) 
	{
		$this->_params->offsetSet($name, $value);
		
		return $this;
	}

	public function getController()
	{
		return $this->_controller;
	}
	public function getAction()
	{
		return $this->_action;
	}
	public function getParam($name, $defaultValue = null)
	{
		if ($this->_params->offsetExists($name)) {
			return $this->_params->offsetGet($name);
		}
		
		if (isset($_GET[$name])) {
			return $_GET[$name];
		}
		
		if (isset($_POST[$name])) {
			return $_POST[$name];
		}
		
		return $defaultValue;
	}
	
	public function getParams()
	{
		$aOut = (array) $this;
		unset ($aOut['controller'], $aOut['action']);
		return $aOut;
	}
	
	public function getPost()
	{
		if (func_num_args() == 0) {
			return $_POST;
		}
		$name = func_get_arg(0);
		
		if (isset($_POST[$name])) {
			return $_POST[$name];
		}
		
		if (func_num_args() == 2) {
			return func_get_arg(1);
		}
		
		trigger_error('Trying to access inexistent POST "' . $name . '" with no default value', E_USER_NOTICE);
		
		return null;
	}
	
	public static function getReferer()
	{
		return $_SERVER['HTTP_REFERER'];
	}
	
}