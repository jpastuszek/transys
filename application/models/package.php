<?php 

class Model_Package extends App_Model {

	/*
	 * 14 * 24 * 60 * 60
	 */
	const LAST_COMPLETED = 1209600;
	
	const STATE_created = 'Zlecenie utworzone w systemie TranSys';
	
	const STATE_picked = 'Przesyłka odebrana od zleceniodawcy';
	const STATE_picked_transit = 'Przesyłka jest w drodze do magazynu';
	const STATE_pick_error = 'Problem odbioru przesyłki od zleceniodawcy';
	
	const STATE_warehouse = 'Przesyłka znajduje się w magazynie i oczekuje na podjęcie przez kuriera doręczającego';
	
	const STATE_delivered = 'Przesyłka doręczona do odbiorcy';
	const STATE_deliver_transit = 'Przesyłka podjęta przez kuriera doręczającego';
	const STATE_deliver_error = 'Problem doręczenia przesyłki do odbiorcy';
	
	const STATE_other_error = 'Inny problem';
	
	const STATE_SHORT_created = 'Nowe';
	
	const STATE_SHORT_picked = 'Odebrane';
	const STATE_SHORT_picked_transit = 'W drodze do magazynu';
	const STATE_SHORT_pick_error = 'Problem z odbiorem';
	
	const STATE_SHORT_warehouse = 'Na magazynie';
	
	const STATE_SHORT_delivered = 'Doręczona';
	const STATE_SHORT_deliver_transit = 'W doręczeniu';
	const STATE_SHORT_deliver_error = 'Problem z doręczeniem';
	
	const STATE_SHORT_other_error = 'Inny problem';
	
	public function create()
	{
		if ('' == $this->getOption('receiver_name', '')
				|| '' == $this->getOption('receiver_street', '')
				|| '' == $this->getOption('receiver_city', '')
				|| '' == $this->getOption('receiver_postal', '')
				|| '' == $this->getOption('package_weight', '')
				|| '' == $this->getOption('package_width', '')
				|| '' == $this->getOption('package_height', '')
				|| '' == $this->getOption('package_depth', '')
				|| '' == $this->getOption('package_payment_method', '')
				|| '' == $this->getOption('sender_name', '')
				|| '' == $this->getOption('sender_street', '')
				|| '' == $this->getOption('sender_city', '')
				|| '' == $this->getOption('sender_postal', '')
				) {
			throw new Exception('Required parameters not found');
		}
		$this->_oDbAdapter->beginTransaction();
		try {
// 			$query = "
// 				INSERT INTO `client` (
// 					`client_name`,
// 					`client_street`,
// 					`client_city`,
// 					`client_postal`,
// 					`client_email`,
// 					`client_phone`
// 				) VALUES (
// 					" . $this->_oDbAdapter->quote($this->getOption('receiver_name')) . ",
// 					" . $this->_oDbAdapter->quote($this->getOption('receiver_street')) . ",
// 					" . $this->_oDbAdapter->quote($this->getOption('receiver_city')) . ",
// 					" . $this->_oDbAdapter->quote($this->getOption('receiver_postal')) . ",
// 					" . $this->_oDbAdapter->quote($this->getOption('receiver_email')) . ",
// 					" . $this->_oDbAdapter->quote($this->getOption('receiver_phone')) . "
// 				)
// 			";
// 			$this->_oDbAdapter->query($query);

// 			$iReceiverId = $this->_oDbAdapter->lastInsertId();
					
			$oClientModel = new Model_Client();
			
			$iReceiverId = $oClientModel
				->setOption('client_name', $this->getOption('receiver_name'))
				->setOption('client_street', $this->getOption('receiver_street'))
				->setOption('client_city', $this->getOption('receiver_city'))
				->setOption('client_postal', $this->getOption('receiver_postal'))
				->setOption('client_email', $this->getOption('receiver_email', ''))
				->setOption('client_phone', $this->getOption('receiver_phone', ''))
				->create();
			
			
// 			$query = "
// 				INSERT INTO `client` (
// 					`client_name`,
// 					`client_street`,
// 					`client_city`,
// 					`client_postal`,
// 					`client_email`,
// 					`client_phone`
// 				) VALUES (
// 					" . $this->_oDbAdapter->quote($this->getOption('sender_name')) . ",
// 					" . $this->_oDbAdapter->quote($this->getOption('sender_street')) . ",
// 					" . $this->_oDbAdapter->quote($this->getOption('sender_city')) . ",
// 					" . $this->_oDbAdapter->quote($this->getOption('sender_postal')) . ",
// 					" . $this->_oDbAdapter->quote($this->getOption('sender_email')) . ",
// 					" . $this->_oDbAdapter->quote($this->getOption('sender_phone')) . "
// 				)
// 			";
// 			$this->_oDbAdapter->query($query);
			
// 			$iSenderId = $this->_oDbAdapter->lastInsertId();

			$iSenderId = $oClientModel
				->setOption('client_name', $this->getOption('sender_name'))
				->setOption('client_street', $this->getOption('sender_street'))
				->setOption('client_city', $this->getOption('sender_city'))
				->setOption('client_postal', $this->getOption('sender_postal'))
				->setOption('client_email', $this->getOption('sender_email', ''))
				->setOption('client_phone', $this->getOption('sender_phone', ''))
				->create();
				
				
			
			do {
				$iTrackingCode = base_convert(time(), 10, 9);
				$query = "
					INSERT INTO `package` (
						`package_tracking_code`,
						`sender_id`,
						`receiver_id`,
						`package_weight`,
						`package_width`,
						`package_height`,
						`package_depth`,
						`package_payment_method`
					) VALUES (
						" . $this->_oDbAdapter->quote($iTrackingCode) . ",
						" . $iSenderId . ",
						" . $iReceiverId . ",
						" . (int) $this->getOption('package_weight') . ",
						" . (int) $this->getOption('package_width') . ",
						" . (int) $this->getOption('package_height') . ",
						" . (int) $this->getOption('package_depth') . ",
						" . $this->_oDbAdapter->quote($this->getOption('package_payment_method')) . "
					)
				";
				
				try {
					$this->_oDbAdapter->query($query);
					break;
					
				} catch (PDOException $e) {
					
					// grab exceptions but loop only if unique constraint fails
					// Error: 1169 SQLSTATE: 23000 (ER_DUP_UNIQUE)
					// Message: Can't write, because of unique constraint, to table '%s'
					
					if (1169 == $e->getCode()) {
						continue;
					} else {
						// rethrow the exception... we want to know what crapped here
						throw $e;
						break; // just to be on a safe side (:
					}
				}
				
			} while (true); // HEAVY HACK ALERT!!!!!!!!!!!
			
			
			$iPackageId = $this->_oDbAdapter->lastInsertId();

			$query = "
				INSERT INTO `package_log` (
					`package_id`,
					`user_id`,
					`package_log_type`,
					`package_log_time`,
					`package_log_info`
				) VALUES (
					" . $iPackageId .",
					NULL,
					'created',
					" . time() . ",
					NULL
				)
			";
			
			$this->_oDbAdapter->query($query);
			
			
		} catch (PDOException $e) {
			$this->handlePDOException($e);
			$this->_oDbAdapter->rollBack();
			
			return false;
		}
		
		$this->_oDbAdapter->commit();
		
		return $iTrackingCode;
	}
	
	public function update()
	{
		
		if (0 == $this->getOption('package_id', 0)
				) {
			throw new Exception('Missing package_id');
		}
		
		
		$updates = array();
		
		if (0 != $this->getOption('courier_pick_id', 0)) {
			$updates[] = "`courier_pick_id` = " . (int) $this->getOption('courier_pick_id');
		}
		
		if (0 != $this->getOption('courier_deliver_id', 0)) {
			$updates[] = "`courier_deliver_id` = " . (int) $this->getOption('courier_deliver_id');
		}
		
		if (0 != $this->getOption('package_payment_received', 0)) {
			$updates[] = "`package_payment_received` = " . (int) $this->getOption('package_payment_received');
		}
		
		if (0 == count($updates)) {
			return true;
		}
		
		$this->_oDbAdapter->beginTransaction();
		try {
			$query = "
				UPDATE	`package`
				SET		" . join(', ', $updates) . "
				WHERE	`package_id` = " . (int) $this->getOption('package_id') ."
				LIMIT	1
			";
			$this->_oDbAdapter->query($query);
		} catch (PDOException $e) {
			$this->handlePDOException($e);
			$this->_oDbAdapter->rollBack();
			
			return false;
		}
		
		$this->_oDbAdapter->commit();

		return true;
	}
	
	public function updateLog()
	{
		if (0 == $this->getOption('package_id', 0)
				|| '' == $this->getOption('package_log_type', '')
				|| 0 == $this->getOption('user_id', 0)
				) {
			throw new Exception('Missing parameters');
		}
		try {
			$this->_oDbAdapter->beginTransaction();
			$query = "
				INSERT INTO `package_log` (
					`package_id`,
					`user_id`,
					`package_log_type`,
					`package_log_time`,
					`package_log_info`
				) VALUES (
					" . (int) $this->getOption('package_id') .",
					" . (int) $this->getOption('user_id') .",
					" . $this->_oDbAdapter->quote($this->getOption('package_log_type'), PDO::PARAM_STR) .",
					" . time() . ",
					" . $this->_oDbAdapter->quote($this->getOption('package_log_info', ''), PDO::PARAM_STR) ."
				)
			";
				
			$this->_oDbAdapter->query($query);
				
				
		} catch (PDOException $e) {
			$this->handlePDOException($e);
			$this->_oDbAdapter->rollBack();
				
			return false;
		}
		
		$this->_oDbAdapter->commit();
				
	}
	
	public function getPackageStatement()
	{
		$query = "
			SELECT  `package`.*,
				
					`sender`.`client_name` AS `sender_name`,
					`sender`.`client_street` AS `sender_street`,
					`sender`.`client_city` AS `sender_city`,
					`sender`.`client_postal` AS `sender_postal`,
					`sender`.`client_email` AS `sender_email`,
					`sender`.`client_phone` AS `sender_phone`,
						
					`receiver`.`client_name` AS `receiver_name`,
					`receiver`.`client_street` AS `receiver_street`,
					`receiver`.`client_city` AS `receiver_city`,
					`receiver`.`client_postal` AS `receiver_postal`,
					`receiver`.`client_email` AS `receiver_email`,
					`receiver`.`client_phone` AS `receiver_phone`,
					
					(	SELECT	`package_log_type` 
						FROM	`package_log`
						WHERE	`package_log`.`package_id` = `package`.`package_id` 
							AND	`package_log`.`package_log_type` <> 'payment' 
						ORDER BY
								`package_log_time` DESC LIMIT 1
					) AS `package_state`,
					
					`courier_pick`.`user_name` AS `courier_pick_name`,
					`courier_deliver`.`user_name` AS `courier_deliver_name`,
					
					/* flagi paczki */
					
					/* paczka była/jest podjęta */
					(	SELECT	COUNT(*) 
						FROM 	`package_log` 
						WHERE 	`package_log`.`package_id` = `package`.`package_id` 
							AND `package_log`.`package_log_type` = 'picked'
					) AS `package_picked`,
					
					/* paczka była/jest w drodze na magazyn */
					(	SELECT	COUNT(*) 
						FROM 	`package_log` 
						WHERE 	`package_log`.`package_id` = `package`.`package_id` 
							AND `package_log`.`package_log_type` = 'picked_transit'
					) AS `package_picked_transit`,
					
					/* paczka była/jest na magazynie */
					(	SELECT	COUNT(*) 
						FROM 	`package_log` 
						WHERE 	`package_log`.`package_id` = `package`.`package_id` 
							AND `package_log`.`package_log_type` = 'warehouse'
					) AS `package_warehouse`,
					
					/* paczka była/jest w drodze do doręczniea */
					(	SELECT	COUNT(*) 
						FROM 	`package_log` 
						WHERE 	`package_log`.`package_id` = `package`.`package_id` 
							AND `package_log`.`package_log_type` = 'deliver_transit'
					) AS `package_deliver_transit`,
					
					/* paczka była/jest doręczona */
					(	SELECT	COUNT(*) 
						FROM 	`package_log` 
						WHERE 	`package_log`.`package_id` = `package`.`package_id` 
							AND `package_log`.`package_log_type` = 'delivered'
					) AS `package_delivered`
				
			FROM    `package`
			JOIN	`client` AS `sender`
				ON	`sender`.`client_id` = `package`.`sender_id`
			JOIN	`client` AS `receiver`
				ON	`receiver`.`client_id` = `package`.`receiver_id`
			LEFT JOIN
					`user` AS `courier_pick`
				ON	`courier_pick`.`user_id` = `package`.`courier_pick_id`
			LEFT JOIN
					`user` AS `courier_deliver`
				ON	`courier_deliver`.`user_id` = `package`.`courier_deliver_id`
			WHERE	:where
			ORDER BY
					`package`.`package_tracking_code` DESC
		";
		
		return $query;
	}
	
	public function getById()
	{
		if (0 == $this->getOption('package_id', 0)) {
			throw new Exception('No package_id given');
		}
		
		try {
			$query = $this->getPackageStatement();
			$where = "`package_id` = " . (int) $this->getOption('package_id') . "";

			$query = str_replace(':where', $where, $query);
			
			$oResult = $this->_oDbAdapter->query($query)->fetch();
			return $oResult; 
		} catch (PDOException $e) {
			$this->handlePDOException($e);
			return false;
		}
		
	}
	
	public function getByTrackingCode()
	{
		if (0 == $this->getOption('package_tracking_code', 0)) {
			throw new Exception('No package_tracking_code given');
		}
		
		try {
			$query = $this->getPackageStatement();
			$where = "`package_tracking_code` = '" . $this->getOption('package_tracking_code') . "'";
			
			$query = str_replace(':where', $where, $query);

			$oResult = $this->_oDbAdapter->query($query)->fetch();
			return $oResult;
		} catch (PDOException $e) {
			$this->handlePDOException($e);
			return false;
		}
	}
	
	public function getList()
	{
		$where = '1';
		
		if (0 != $this->getOption('courier', 0)) {
			$where .= " AND (`courier_pick_id` = " . (int) $this->getOption('courier') . " OR `courier_deliver_id` = " . (int) $this->getOption('courier') . ")"; 
			$where .= " AND `courier_pick_id` IS NOT NULL";
			$where .= " AND `courier_deliver_id` IS NOT NULL"; 
		}
		
		try {
			$query = $this->getPackageStatement();
			
			if ($this->hasOption('type')) {
				if ('new' == $this->getOption('type')) {
					$where .= " AND (`package`.`courier_deliver_id` IS NULL OR `package`.`courier_pick_id` IS NULL)";
				}
				/*
				 * paczka oczekująca na odbiór gdy nie ma w logu `picked` 
				 */
				if ('pick' == $this->getOption('type')) {
					$where .= " AND `courier_pick_id` IS NOT NULL";
					$where .= " AND `courier_deliver_id` IS NOT NULL"; 
					$where .= " AND (
						SELECT	COUNT(*) 
						FROM 	`package_log` 
						WHERE 	`package_log`.`package_id` = `package`.`package_id` 
							AND (	`package_log`.`package_log_type` = 'picked'
								OR	`package_log`.`package_log_type` = 'picked_transit'
								)
					) = 0"; 
				}
				
				/*
				 * paczka odebrana gdy ma w logu `picked` lub `picked_transit`
				 * ale nie ma `warehouse` (nie była na magazynie)
				 */
				if ('picked' == $this->getOption('type')) {
					$where .= " AND `courier_pick_id` IS NOT NULL";
					$where .= " AND `courier_deliver_id` IS NOT NULL"; 
					$where .= " AND (
						SELECT	COUNT(*) 
						FROM 	`package_log` 
						WHERE 	`package_log`.`package_id` = `package`.`package_id` 
							AND (	`package_log`.`package_log_type` = 'picked'
								OR	`package_log`.`package_log_type` = 'picked_transit'
								)
					) > 0";
					$where .= " AND (
						SELECT	COUNT(*) 
						FROM 	`package_log` 
						WHERE 	`package_log`.`package_id` = `package`.`package_id` 
							AND (	`package_log`.`package_log_type` = 'warehouse'
								)
					) = 0";
				}
				
				/*
				 * paczka na magazynie gdy ma w logu `warehouse`
				 * ale nie ma `delivered` lub `deliver_transit`
				 */
				if ('warehouse' == $this->getOption('type')) {
					$where .= " AND `courier_pick_id` IS NOT NULL";
					$where .= " AND `courier_deliver_id` IS NOT NULL"; 
					$where .= " AND (
						SELECT	COUNT(*) 
						FROM 	`package_log` 
						WHERE 	`package_log`.`package_id` = `package`.`package_id` 
							AND (	`package_log`.`package_log_type` = 'warehouse'
								)
					) > 0";
					$where .= " AND (
						SELECT	COUNT(*) 
						FROM 	`package_log` 
						WHERE 	`package_log`.`package_id` = `package`.`package_id` 
							AND (	`package_log`.`package_log_type` = 'deliver_transit'
								OR  `package_log`.`package_log_type` = 'delivered'
								)
					) = 0";
					 
				}
				
				/*
				 * paczka w doręczeniu gdy ma w logu `deliver_transit`
				 * ale nie ma `delivered`
				 */
				if ('deliver' == $this->getOption('type')) {
					$where .= " AND `courier_pick_id` IS NOT NULL";
					$where .= " AND `courier_deliver_id` IS NOT NULL"; 
					$where .= " AND (
						SELECT	COUNT(*) 
						FROM 	`package_log` 
						WHERE 	`package_log`.`package_id` = `package`.`package_id` 
							AND (	`package_log`.`package_log_type` = 'deliver_transit'
								)
					) > 0";
					$where .= " AND (
						SELECT	COUNT(*) 
						FROM 	`package_log` 
						WHERE 	`package_log`.`package_id` = `package`.`package_id` 
							AND (	`package_log`.`package_log_type` = 'delivered'
								)
					) = 0";
				}
				
				/*
				 * paczka doręczona gdy ma w logu `delivered`
				 */
				if ('complete' == $this->getOption('type')) {
					$where .= " AND `courier_pick_id` IS NOT NULL";
					$where .= " AND `courier_deliver_id` IS NOT NULL"; 
					$where .= " AND (
						SELECT	COUNT(*) 
						FROM 	`package_log` 
						WHERE 	`package_log`.`package_id` = `package`.`package_id` 
							AND (	`package_log`.`package_log_type` = 'delivered'
								)
							AND `package_log`.`package_log_time` > " . (time() - Model_Package::LAST_COMPLETED) . "
					) > 0";
				}
				if ('archive' == $this->getOption('type')) {
					$where .= " AND `courier_pick_id` IS NOT NULL";
					$where .= " AND `courier_deliver_id` IS NOT NULL"; 
					$where .= " AND (
						SELECT	COUNT(*) 
						FROM 	`package_log` 
						WHERE 	`package_log`.`package_id` = `package`.`package_id` 
							AND (	`package_log`.`package_log_type` = 'delivered'
								)
							AND `package_log`.`package_log_time` < " . (time() - Model_Package::LAST_COMPLETED) . "
					) > 0";
				}
				
				if ('payment' == $this->getOption('type')) {
					$where .= " AND (`package`.`package_payment_method` = 'instant' AND `package`.`package_payment_received` = 0)";
					$where .= " AND `courier_pick_id` IS NOT NULL";
					$where .= " AND `courier_deliver_id` IS NOT NULL"; 
				}
			}
			$query = str_replace(':where', $where, $query);
				
			$oResult = $this->_oDbAdapter->query($query)->fetchAll();
			return $oResult;
		} catch (PDOException $e) {
			$this->handlePDOException($e);
			return false;
		}
		
	}
	
	public function getLog()
	{
		if (0 == $this->getOption('package_id', 0)) {
			throw new Exception('No package_id given');
		}
		
		$query = "
			SELECT  `package_log`.*,
					`user`.`user_name`
			FROM	`package_log`
			LEFT JOIN
					`user`
				ON	`user`.`user_id` = `package_log`.`user_id`
			WHERE	`package_id` = " . (int) $this->getOption('package_id') . "
			ORDER BY
					`package_log_time` DESC
		";
		try {
			
			$oResult = $this->_oDbAdapter->query($query)->fetchAll();
			return $oResult;
		} catch (PDOException $e) {
			$this->handlePDOException($e);
			return false;
		}
	}
}