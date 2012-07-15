<?php
class Controller_Admin extends App_Controller {
	public function init()
	{
		$this->view->setLayout('layout_admin');
		if (!App_Auth::getInstance()->hasIdentity()) {
			App_Request::getInstance()->setAction('login');
		}
		parent::init();
	}
	
	public function indexAction()
	{
		header('Location: /admin/packages');
		return;
	}
	
	public function loginAction()
	{
		$oRequest = App_Request::getInstance();
		
		$this->view->authError = false;
		
		if ('' != trim($oRequest->getPost('user_login'))
				&& '' != trim($oRequest->getPost('user_password'))
				) {
			$oAuth = App_Auth::getInstance();
			if ($oAuth->authenticate(trim($oRequest->getPost('user_login')), trim($oRequest->getPost('user_password')))) {
				header('Location: /admin');
			} else {
				$this->view->authError = true;
			}
		}
	}
	
	public function logoutAction()
	{
		App_Auth::getInstance()->clearIdentity();
		
		header('Location: /admin');
	}
	
	public function packagesAction()
	{
		
		$oRequest = App_Request::getInstance();
		
		// normalize package type
		
		if ('admin' == App_Auth::getInstance()->getIdentity()->user_type) {
			$oRequest->setParam('type', $oRequest->getParam('type','new'));
		} else {
			$oRequest->setParam('type', $oRequest->getParam('type','pick'));
		}
		
		$oPackage = new Model_Package();
		$oPackage->setOption('type', $oRequest->getParam('type', 'new'));
		
		if ('courier' == App_Auth::getInstance()->getIdentity()->user_type) {
			$oPackage->setOption('courier', App_Auth::getInstance()->getIdentity()->user_id);
		}
		
		$this->view->packages = $oPackage->getList();
		
		
		
		$oUser = new Model_User();
		$oUser->setOption('type', 'courier');
		$this->view->couriers = $oUser->getList();
	}
	
	public function packageAction()
	{
// 		trigger_error('In ' . __METHOD__ . '', E_USER_NOTICE);
		$oRequest = App_Request::getInstance();
		
		
		
		if (0 == $oRequest->getParam('package_id', 0)) {
			header('Location: ' . App_Request::getReferer());
			trigger_error('Missing package_id :: redirecting to: ' . App_Request::getReferer(), E_USER_NOTICE);
		}
		
		$oPackageModel = new Model_Package();
		$oPackageModel
			->setOption('package_id', $oRequest->getParam('package_id'));
		
		if ($oRequest->getParam('assign_pick', false)) {
			$oPackageModel
				->setOption('package_id', $oRequest->getParam('package_id'))
				->setOption('courier_pick_id', $oRequest->getParam('assign_pick'));
			$oPackageModel->update();
			
			header('Location: ' . App_Request::getReferer());
			trigger_error('assign_pick completed :: redirecting to: ' . App_Request::getReferer(), E_USER_NOTICE);
			return;
		}
		
		if ($oRequest->getParam('assign_deliver', false)) {
			$oPackageModel
				->setOption('package_id', $oRequest->getParam('package_id'))
				->setOption('courier_deliver_id', $oRequest->getParam('assign_deliver'));
			$oPackageModel->update();
			
			header('Location: ' . App_Request::getReferer());
			trigger_error('assign_deliver completed :: redirecting to: ' . App_Request::getReferer(), E_USER_NOTICE);
			return;
		}
		
		if ('' != $oRequest->getParam('set_status', '')) {
			$this->view->status_form = true;
			$this->view->package_log_type = $oRequest->getParam('set_status');
			$this->view->package_id = $oRequest->getParam('package_id');
			$this->view->referer = $oRequest->getPost('referer', App_Request::getReferer());
			
						
			$oPackage = $oPackageModel->getById();
			if ('pick' == $oPackage->package_payment_method
					&& 'picked' == $oRequest->getParam('set_status', '')
					) {
				$this->view->show_payment = true;
			}
				
			if ('deliver' == $oPackage->package_payment_method
					&& 'delivered' == $oRequest->getParam('set_status', '')
					) {
				$this->view->show_payment = true;
			}
				
			if (0 != $oRequest->getPost('package_id', 0)) {
				try {
					
						
					if (('pick_error' == $oRequest->getPost('package_log_type') 
							|| 'deliver_error' == $oRequest->getPost('package_log_type')
							|| 'other_error' == $oRequest->getPost('package_log_type')
							)
							&& '' == $oRequest->getPost('package_log_info', '')
							) {
						throw new Exception('Missing `package_log_info` with problem description');
					}
					if (
							('pick' == $oPackage->package_payment_method
								&& 'picked' == $oRequest->getParam('set_status', '')
							)
							|| ('deliver' == $oPackage->package_payment_method
								&& 'delivered' == $oRequest->getParam('set_status', '')
							)
							) {
						if (0 == $oRequest->getPost('package_payment', 0)) {
							throw new Exception('Payment is required for this action');
						}
						$oPackageModel
							->setOption('user_id', App_Auth::getInstance()->getIdentity()->user_id)
							->setOption('package_log_type', 'payment')
							->setOption('package_log_info', '')
							->updateLog();
						$oPackageModel
							->setOption('package_payment_received', 1)
							->update();
						
					}
						
					$oPackageModel
						->setOption('user_id', App_Auth::getInstance()->getIdentity()->user_id)
						->setOption('package_log_type', $oRequest->getPost('package_log_type'))
						->setOption('package_log_info', $oRequest->getPost('package_log_info', ''))
						->updateLog();
					header('Location: ' . $oRequest->getPost('referer'));
				} catch (Exception $e) {
					trigger_error('Error while updating package log: ' . $e->getMessage(), E_USER_WARNING);
					$this->view->update_error = true;
				}
			}
			
		}
	}
	
	public function sendersAction()
	{
		$oRequest = App_Request::getInstance();
		
		if ('admin' == App_Auth::getInstance()->getIdentity()->user_type) {
			
			$oClientModel = new Model_Client();
			
			$this->view->clients = $oClientModel->getList();
			
		} else {
			$this->view->setView('admin/noperms.phtml');
		}
	}
	
	public function couriersAction()
	{
		$oRequest = App_Request::getInstance();
		
		if ('admin' == App_Auth::getInstance()->getIdentity()->user_type) {
			
			$oUserModel = new Model_User();
			$oUserModel->setOption('type', 'courier');
			
			$this->view->couriers = $oUserModel->getList();
			
		} else {
			$this->view->setView('admin/noperms.phtml');
		}
	}
	
	public function reportsAction()
	{
			$oRequest = App_Request::getInstance();
		
		if ('admin' == App_Auth::getInstance()->getIdentity()->user_type) {
			
			$oClientModel = new Model_Client();
			$oUserModel = new Model_User();
			$oPackageModel = new Model_Package();
			
			$this->view->couriers = count($oUserModel->setOption('type', 'courier')->getList());
			$this->view->admins = count($oUserModel->setOption('type', 'admin')->getList());
			
			$this->view->clients = count($oClientModel->getList());
			
			$this->view->packages_total = count($oPackageModel->getList());
			$this->view->packages_new = count($oPackageModel->setOption('type', 'new')->getList());
			$this->view->packages_delivered = count($oPackageModel->setOption('type', 'complete')->getList());
			
			
			
		} else {
			$this->view->setView('admin/noperms.phtml');
		}
		
	}
	
}