<?php 

class Model_User extends App_Model {
	
	
	public function create()
	{
		return false;
		$this->_oDbAdapter->beginTransaction();
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
		try {
			$query = "
				INSERT INTO `client` (
					`client_name`,
					`client_street`,
					`client_city`,
					`client_postal`,
					`client_email`,
					`client_phone`
				) VALUES (
					" . $this->_oDbAdapter->quote($this->getOption('receiver_name')) . ",
					" . $this->_oDbAdapter->quote($this->getOption('receiver_street')) . ",
					" . $this->_oDbAdapter->quote($this->getOption('receiver_city')) . ",
					" . $this->_oDbAdapter->quote($this->getOption('receiver_postal')) . ",
					" . $this->_oDbAdapter->quote($this->getOption('receiver_email')) . ",
					" . $this->_oDbAdapter->quote($this->getOption('receiver_phone')) . "
				)
			";
			$this->_oDbAdapter->query($query);
			
			$iReceiverId = $this->_oDbAdapter->lastInsertId();
			
			$query = "
				INSERT INTO `client` (
					`client_name`,
					`client_street`,
					`client_city`,
					`client_postal`,
					`client_email`,
					`client_phone`
				) VALUES (
					" . $this->_oDbAdapter->quote($this->getOption('sender_name')) . ",
					" . $this->_oDbAdapter->quote($this->getOption('sender_street')) . ",
					" . $this->_oDbAdapter->quote($this->getOption('sender_city')) . ",
					" . $this->_oDbAdapter->quote($this->getOption('sender_postal')) . ",
					" . $this->_oDbAdapter->quote($this->getOption('sender_email')) . ",
					" . $this->_oDbAdapter->quote($this->getOption('sender_phone')) . "
				)
			";
			$this->_oDbAdapter->query($query);
			
			$iSenderId = $this->_oDbAdapter->lastInsertId();
			
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
	
	public function getStatement()
	{
		$query = "
			SELECT  `user`.*
			FROM    `user`
			WHERE	:where
			ORDER BY
					`user`.`user_name` ASC
		";
		
		return $query;
	}
	
	public function getList()
	{
		$where = '1';
		
		if ($this->getOption('type', false)) {
			$where .= " AND `user_type` = " . $this->_oDbAdapter->quote($this->getOption('type'), PDO::PARAM_STR);
		}
		
		try {
			$query = $this->getStatement();
		
			$query = str_replace(':where', $where, $query);
				
			$oResult = $this->_oDbAdapter->query($query)->fetchAll();
			return $oResult;
		} catch (PDOException $e) {
			$this->handlePDOException($e);
			return false;
		}
		
	}
	
}