<?php

class App_View {
	
	protected $_viewScript;
	
	protected $_layout = 'layout';
	
	public function __construct()
	{
	}
	
	public function __set($name, $value)
	{
		$this->$name = $value;
	}
	
	public function __get($name) 
	{
// 		trigger_error('Key "' . $name . '" does not exist', E_USER_NOTICE);
	}
	
	/**
	 * @param unknown_type $sViewScript
	 * @return App_View
	 */
	public function setView($sViewScript)
	{
		$this->_viewScript = $sViewScript;
		return $this;
	}
	
	public function getLayout()
	{
		return $this->_layout;
	}
	
	public function setLayout($layout)
	{
		$this->_layout = $layout;
		return $this;
	}
	
    /**
     * Processes a view script and returns the output.
     *
     * @param string $name The script name to process.
     * @return string The script output.
     */
    public function render()
    {
        ob_start();
        $this->_run(APPLICATION_PATH. '/views/' . $this->_viewScript);

        return ob_get_clean();
    }

    /**
     * Use to include the view script in a scope that only allows public
     * members.
     *
     * @return mixed
     */
    protected function _run()
    {
    	include func_get_arg(0);
    }
    
    public function partial($sViewScript, $mBind = null)
    {
    	$partial = new self();
    	
    	if (null === $mBind) {
    		$mBind = get_object_vars($this);
    	}
    	foreach ($mBind as $key => $val) {
    		$partial->$key = $val;
    	}
    	 
    	$partial->setView($sViewScript);
    	return $partial->render();
    }
}