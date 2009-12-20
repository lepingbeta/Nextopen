<?php
require_once( 'base.php' );
class base_controller extends base
{
	protected $tpl;

    function __construct()
    {
    	parent::__construct();
        $this->tpl = array();
        $this->tpl['c'] = $_SESSION[SYS_NAME]['controller'];
        $this->tpl['a'] = $_SESSION[SYS_NAME]['action'];
    }
}