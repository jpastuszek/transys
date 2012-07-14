<?php
class App_Db {
	/**
	 * 
	 * @var App_Db
	 */
	protected static $_instance;
	
	/**
	 * @var PDO
	 */
	protected $_oDbAdapter;
	
	public function __construct()
	{
		if (null === $this->_oDbAdapter) {
			$this->connect();
		}
	}
	
	public function handlePDOException(PDOException $e)
	{
		trigger_error('PHP PDO Error in ' . $e->getFile() . ' @' . strval($e->getLine()) . ' ['  . strval($e->getCode()) . '] :: ' . $e->getMessage(), E_USER_WARNING);
		foreach ($e->getTrace() as $a => $b) {
			foreach ($b as $c => $d) {
				if ($c == 'args') {
					foreach ($d as $e => $f) {
						trigger_error('PHP PDO Error trace: ' . strval($a) . '# args: ' . $e . ': ' . $f . '', E_USER_WARNING);
					}
				} else {
					trigger_error('PHP PDO Error trace: ' . strval($a) . '# ' . $c . ': ' . $d . '', E_USER_WARNING);
				}
			}
		}
	}
	
	/**
	 * @return PDO
	 */
	public function getAdapter()
	{
		return $this->_oDbAdapter;
	}
	
	public function connect()
	{
		// TODO kick off this to some config or whatever
		try {
			$this->_oDbAdapter = new PDO('mysql:host=localhost;dbname=transys', 'transys', 'transyspass',
					array(
							PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
							PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8",
							PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
					));
		} catch (PDOException $e) {
			$this->handlePDOException($e);
			return false;
		}
	}
	
	public static function getInstance()
	{
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}