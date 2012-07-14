<?php
class App_Auth {
	/**
	 * 
	 * @var App_Auth
	 */
	private static $_instance = null;
	
	private $_identity;
	
	/**
	 * 
	 * @var PDO
	 */
	protected $_oDbAdapter;
	
	/**
	 *
	 * @return App_auth
	 */
	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::init();
		}
	
		return self::$_instance;
	}
	
	/**
	 * Set the default registry instance to a specified instance.
	 *
	 * @param Registry $registry An object instance of type Registry,
	 *   or a subclass.
	 * @return void
	 * @throws App_Exception if registry is already initialized.
	 */
	public static function setInstance(App_Auth $oAuth)
	{
		if (self::$_instance !== null) {
			throw new App_Exception('Auth is already initialized');
		}
	
		self::$_instance = $oAuth;
	}
	
	/**
	 * Initialize the default registry instance.
	 *
	 * @return void
	 */
	protected static function init()
	{
		self::setInstance(new self());
		
	}

	public function __construct()
	{
		$this->_oDbAdapter = App_Db::getInstance()->getAdapter();
	}
	
	public function authenticate($login, $password)
	{
		$query = "
			SELECT  *
			FROM	`user`
			WHERE	`user_login` = " . $this->_oDbAdapter->quote($login) . "
				AND `user_password` = " . $this->_oDbAdapter->quote(sha1($password)) . "
		";
		$mResult = $this->_oDbAdapter->query($query)->fetch();
		if ($mResult) {
			trigger_error('User #' . $mResult->user_id . ' authenticated', E_USER_NOTICE);
			$this->setIdentity($mResult);
			return true;
		}
		trigger_error('Could not authenticate user `' . $login . '` authenticated', E_USER_NOTICE);
		return false;
	}
	
	public function setIdentity($oUser)
	{
		$_SESSION['__TRU'] = array();
		$_SESSION['__TRU']['user_id'] = $oUser->user_id;
		return $this;
	}
	
	public function getIdentity()
	{
		if (!$this->hasIdentity()) {
			trigger_error('User not authenticated.', E_USER_WARNING);
			return false;
		}
		
		if (is_null($this->_identity)) {
			$query = "
				SELECT  *
				FROM	`user`
				WHERE	`user_id` = " . (int) $_SESSION['__TRU']['user_id'] . "
			";
			$mResult = $this->_oDbAdapter->query($query)->fetch();
			$this->_identity = $mResult;
		}
		
		return $this->_identity;
	}
	
	public function clearIdentity()
	{
		unset($this->_identity);
		if (isset($_SESSION['__TRU'])
				&& isset($_SESSION['__TRU']['user_id'])
				) {
			unset($_SESSION['__TRU']['user_id']);
		}
		return $this;
	}
	
	public function hasIdentity()
	{
		if (isset($_SESSION)
				&& isset($_SESSION['__TRU'])
				&& isset($_SESSION['__TRU']['user_id'])
				&& is_numeric($_SESSION['__TRU']['user_id'])
				) {
			return true;
		}
		return false;
	}
}