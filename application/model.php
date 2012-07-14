<?php

class App_Model extends App_Db {
	/**
	 * 
	 * @var PDO
	 */
	protected $_oDbAdapter;

	protected $__options = array();
	
	const HAS_ANY = 1;
	const HAS_ALL = 2;
	
	
	public function __construct() 
	{
		parent::connect();
		return $this;
	}
	
	/**
	 * Sets single option
	 * @param string $sOptionName
	 * @param mixed $mOptionValue
	 * @return $this
	 */
	public function setOption($sOptionName, $mOptionValue) {
		if (0 == strlen($sOptionName)) {
			$this->__vommit('Option name should be a non-zero-length string.');
		}
		$this->__options[$sOptionName] = $mOptionValue;
		return $this;
	}
	
	/**
	 * Sets multiple options at once
	 * @return $this
	 */
	public function setOptions($aOptions) {
		foreach ($aOptions as $sOptionName => $mOptionValue) {
			$this->setOption($sOptionName, $mOptionValue);
		}
		return $this;
	}
	
	public function getOption($sOptionName, $mDefaultValue = null) {
		if (!array_key_exists($sOptionName, $this->__options)) {
			if (func_num_args() == 2) {
				return func_get_arg(1);
			} else {
				$this->__vommit('Requested option `' . $sOptionName . '` was not found and no default value was provided');
			}
		}
	
		return $this->__options[$sOptionName];
	}
	
	public function getOptions($aOptions)
	{
		$options = array();
		foreach ($aOptions as $sOptionName => $mDefaultValue) {
			$options[$sOptionName] = $this->getOption($sOptionName, $mDefaultValue);
		}
		return $options;
	}
	
	public function hasOption($sOptionName)
	{
		return array_key_exists($sOptionName, $this->__options);
	}
	
	public function hasOptions($aOptions, $iFlag = self::HAS_ALL)
	{
		$iIntersection = count(array_intersect(array_keys($this->__options), $aOptions));
		if ($iFlag == self::HAS_ALL) {
			return count($aOptions) == $iIntersection;
		} elseif ($iFlag == self::HAS_ANY) {
			return $iIntersection > 0;
		}
	}
	
	private function __vommit($message) {
		trigger_error($message, E_USER_NOTICE);
	}
	
	
}