<?php

class Controller_Package extends App_Controller {
	public function indexAction()
	{
		$this->view->name = 'Default::indexAction';
	}
	
	public function newAction()
	{
		$oRequest = App_Request::getInstance();
		
		/*
		 * 1: dane odbiorcy
		 * 2: dane przesyłki (rozmiar, waga, etc?)
		 * 3: dane nadawcy
		 * 4: weryfikacja + pseudo koszty
		 * 5: zapis w bazie
		 */
		
		$iStep = $oRequest->getParam('step', 1);
		
		if (0 == $iStep && isset($_SESSION['package_data'])) {
			unset($_SESSION['package_data']);
			header('Location: /package/new');
			return;
		}

		$aPackageData = array();
		if (isset($_SESSION['package_data'])) {
			$aPackageData = array_merge($aPackageData, $_SESSION['package_data']);
		}
		
		$bHasErrors = false;
		
		if (1 == $oRequest->getPost('current_step', 0)) {
			$aPackageData['receiver_name'] = trim($oRequest->getPost('receiver_name', ''));
			if (strlen($aPackageData['receiver_name']) == 0) {
				$this->view->receiver_name_error = true;
				$bHasErrors = true;
			}
			
			$aPackageData['receiver_street'] = trim($oRequest->getPost('receiver_street', ''));
			if (strlen($aPackageData['receiver_street']) == 0) {
				$this->view->receiver_street_error = true;
				$bHasErrors = true;
			}
			
			$aPackageData['receiver_city'] = trim($oRequest->getPost('receiver_city', ''));
			if (strlen($aPackageData['receiver_city']) == 0) {
				$this->view->receiver_city_error = true;
				$bHasErrors = true;
			}
			
			$aPackageData['receiver_postal'] = trim($oRequest->getPost('receiver_postal', ''));
			if (strlen($aPackageData['receiver_postal']) == 0) {
				$this->view->receiver_postal_error = true;
				$bHasErrors = true;
			}
			$aPackageData['receiver_email'] = trim($oRequest->getPost('receiver_email'));
			$aPackageData['receiver_phone'] = trim($oRequest->getPost('receiver_phone'));
		}
		
		if (2 == $oRequest->getPost('current_step', 0)) {
			$aPackageData['package_weight'] = trim($oRequest->getPost('package_weight', ''));
			if (!is_numeric($aPackageData['package_weight'])) {
				$this->view->package_weight_error = true;
				$bHasErrors = true;
			}
			$aPackageData['package_width'] = trim($oRequest->getPost('package_width', ''));
			if (!is_numeric($aPackageData['package_width'])) {
				$this->view->package_width_error = true;
				$bHasErrors = true;
			}
			$aPackageData['package_height'] = trim($oRequest->getPost('package_height', ''));
			if (!is_numeric($aPackageData['package_height'])) {
				$this->view->package_height_error = true;
				$bHasErrors = true;
			}
			$aPackageData['package_depth'] = trim($oRequest->getPost('package_depth', ''));
			if (!is_numeric($aPackageData['package_depth'])) {
				$this->view->package_depth_error = true;
				$bHasErrors = true;
			}
		}

		if (3 == $oRequest->getPost('current_step', 0)) {
			$aPackageData['sender_name'] = trim($oRequest->getPost('sender_name', ''));
			if (strlen($aPackageData['sender_name']) == 0) {
				$this->view->sender_name_error = true;
				$bHasErrors = true;
			}
				
			$aPackageData['sender_street'] = trim($oRequest->getPost('sender_street', ''));
			if (strlen($aPackageData['sender_street']) == 0) {
				$this->view->sender_street_error = true;
				$bHasErrors = true;
			}
				
			$aPackageData['sender_city'] = trim($oRequest->getPost('sender_city', ''));
			if (strlen($aPackageData['sender_city']) == 0) {
				$this->view->sender_city_error = true;
				$bHasErrors = true;
			}
				
			$aPackageData['sender_postal'] = trim($oRequest->getPost('sender_postal', ''));
			if (strlen($aPackageData['sender_postal']) == 0) {
				$this->view->sender_postal_error = true;
				$bHasErrors = true;
			}
			$aPackageData['sender_email'] = trim($oRequest->getPost('sender_email'));
			$aPackageData['sender_phone'] = trim($oRequest->getPost('sender_phone'));
		}
		
		if ($bHasErrors) {
			$iStep = $oRequest->getPost('current_step', 1);
		}
		

		if (4 == $iStep || 5 == $iStep) {
			$this->view->package_price = Helper_PackagePrice::calculate($aPackageData['package_weight'], $aPackageData['package_width'], $aPackageData['package_height'], $aPackageData['package_depth']);
		}

		if (5 == $iStep) {
			// package_payment_method
			$aPackageData['package_payment_method'] = $oRequest->getPost('package_payment_method', 'instant');
		}
		
		$_SESSION['package_data'] = $aPackageData;
		
		if (5 == $iStep) {
			
			try {
				$oPackageModel = new Model_Package();
				$oPackageModel->setOptions($aPackageData);
				
				$mResult = $oPackageModel->create();
				
				if (false !== $mResult) {
					$this->view->success = true;
					$this->view->package_tracking_code = $mResult;
					
					unset($_SESSION['package_data']);
				} else {
					$this->view->success = false;
				}
				
				
			} catch (Exception $e) {
				trigger_error('Error while creating new package: ' . $e->getCode() . ' :: ' . $e->getMessage(), E_USER_WARNING);
				$this->view->success = false;
			}
			
		}
		
		
		
		
		foreach ($aPackageData as $key => $value) {
			$this->view->$key = $value;
		}
		
		
		$this->view->step = $iStep;
		
	}
	
	public function trackAction() 
	{
		/* http://www.siodemka.com/tracking/7030003783481/ */
		
		$oRequest = App_Request::getInstance();
		
		if (0 != $oRequest->getParam('tracking_code', 0)) {
			try {
				$oPackageModel = new Model_Package();
				$oPackageModel->setOption('package_tracking_code', (int) $oRequest->getParam('tracking_code'));
				
				$oPackage = $oPackageModel->getByTrackingCode();
				if ($oPackage) {
					$oPackageModel->setOption('package_id', $oPackage->package_id);
					$aPackageLog = $oPackageModel->getLog(); 
					
					$this->view->package = $oPackage;
					$this->view->package_log = $aPackageLog;
				}
				
			} catch (Exception $e) {
				trigger_error('Error while searching for package: ' . $e->getCode() . ' :: ' . $e->getMessage(), E_USER_WARNING);
			}
		}
		
	}
	
	public function payAction()
	{
		$oRequest = App_Request::getInstance();
		
		
		
		if (0 != $oRequest->getParam('tracking_code', 0)) {
			$oPackageModel = new Model_Package();
			$oPackageModel->setOption('package_tracking_code', (int) $oRequest->getParam('tracking_code'));
			$oPackage = $oPackageModel->getByTrackingCode();
			if ('instant' == $oPackage->package_payment_method) {
				$oPackageModel->setOption('package_id', $oPackage->package_id);
				$oPackageModel->setOption('package_payment_received', 1);
				try {
					if ($oPackageModel->update()) {
						$this->view->success = true;
					} else {
						$this->view->success = false;
					}
				} catch (Exception $e) {
					$this->view->success = false;
					trigger_error('Error while paying: ' . $e->getMessage(), E_USER_WARNING);
				}
				$this->view->payment_type_mismatch = false;
			} else {
				$this->view->payment_type_mismatch = true;
			}
		}
	}
}