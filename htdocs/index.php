<?php

date_default_timezone_set('Europe/Warsaw');
ini_set('display_errors', 0);

try {

	defined('APPLICATION_PATH')
    	|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
	
	require_once APPLICATION_PATH . '/bootstrap.php';
	
	if ('' == session_id()) {
		session_start();
	}
	
	$oRequest = App_Request::getInstance();
	

	try {
		$sControllerClass = 'Controller_' . ucfirst($oRequest->getController()) . '';
		
		$oController = new $sControllerClass();
		
		$oController->init();
	
		$sActionMethod = strtolower($oRequest->getAction()) . 'Action';
		$oController->$sActionMethod();
		
		$content = $oController->view->render();
		
		include APPLICATION_PATH . '/views/' . $oController->view->getLayout() . '.phtml';
	} catch (Exception $e) {
		trigger_error('General error occured: ' . $e->getMessage(), E_USER_WARNING);
		$oController = new Controller_Default();
		$oController->init();
		$oController->errorAction();
		$oController->view->setView('default/error.phtml');
		$content = $oController->view->render();
		
		include APPLICATION_PATH . '/views/' . $oController->view->getLayout() . '.phtml';
		
	}
	session_write_close();
	
} catch (Exception $e) {
	
}