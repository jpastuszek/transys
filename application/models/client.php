<?php

class Model_Client extends App_Model {
	public function create()
	{
		if ('' == $this->getOption('client_name', '')
				|| '' == $this->getOption('client_street', '')
				|| '' == $this->getOption('client_city', '')
				|| '' == $this->getOption('client_postal', '')
				) {
			throw new Exception('Required parameters not found');
		}
		$bCommit = true;
		if (!$this->_oDbAdapter->inTransaction()) {
			$this->_oDbAdapter->beginTransaction();
		} else {
			$bCommit = false;
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
				" . $this->_oDbAdapter->quote($this->getOption('client_name')) . ",
				" . $this->_oDbAdapter->quote($this->getOption('client_street')) . ",
				" . $this->_oDbAdapter->quote($this->getOption('client_city')) . ",
				" . $this->_oDbAdapter->quote($this->getOption('client_postal')) . ",
				" . $this->_oDbAdapter->quote($this->getOption('client_email')) . ",
				" . $this->_oDbAdapter->quote($this->getOption('client_phone')) . "
			)
			";
			$this->_oDbAdapter->query($query);
				
			$iClientId = $this->_oDbAdapter->lastInsertId();
		} catch (PDOException $e) {
			$this->handlePDOException($e);
			$this->_oDbAdapter->rollBack();
				
			return false;
		}
		
		if ($bCommit) {
			$this->_oDbAdapter->commit();
		}
		
		return $iClientId;
	}
	
	public function getListStatement()
	{
		$query = "
			SELECT	`client`.*,
					COUNT(`client_name`) AS `client_use_count`
			FROM	`client`
			WHERE	:where
			GROUP BY
					`client_name`,
					`client_street`,
					`client_city`,
					`client_postal`,
					`client_email`,
					`client_phone`
			ORDER BY
					`client_name` ASC
		";
		
		return $query;
	}
	
	public function getList()
	{
		$where = '1';
		
		try {
			$query = $this->getListStatement();
			$query = str_replace(':where', $where, $query);
			
			$oResult = $this->_oDbAdapter->query($query)->fetchAll();
			return $oResult;
		} catch (PDOException $e) {
			$this->handlePDOException($e);
			return false;
		}
				
	}
}