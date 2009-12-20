<?php
if( !defined( 'DS' ) )
{
    define( 'DS', DIRECTORY_SEPARATOR );
}
if( !defined( 'DS' ) )
{
    define( 'WEB_ROOT', dirname( dirname( dirname( __FILE__ ) ) ) );
}
if( !defined( 'SYS_NAME' ) )
{
    define( 'SYS_NAME', basename( WEB_ROOT ) );
}

define( 'STATUS_CODE', 'status_code' );
define( 'MSG', 'msg' );
define( 'SYS_MSG', 'sys_msg' );
$GLOBALS[SYS_NAME]['uri_404'] = '/page/404.htm';
require_once( 'code.php' );