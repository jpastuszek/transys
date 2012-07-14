<?php

function classAutoloader($sClassName) {
	$aClassName = explode('_', $sClassName);
	
	if (count($aClassName) == 2) {
		$sType = strtolower(array_shift($aClassName));
		$sClassFile = strtolower(array_shift($aClassName)); 
	} else {
		$sClassFile = strtolower(array_shift($aClassName));
		$sType = 'app';
	}
	
	
	switch ($sType) {
		case 'controller':
			$sClassPath = APPLICATION_PATH . '/controllers/';
			break;
			
		case 'model':
			$sClassPath = APPLICATION_PATH . '/models/';
			break;
			
		case 'helper':
			$sClassPath = APPLICATION_PATH . '/helpers/';
			break;
			
		case 'app':
		default:
			$sClassPath = APPLICATION_PATH . '/';
			break;
	}
	
	require_once $sClassPath . $sClassFile . '.php';
}

spl_autoload_register('classAutoloader');

