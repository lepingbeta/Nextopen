<?php
session_start();
require_once( dirname( dirname( dirname( __FILE__ ) ) ) . DIRECTORY_SEPARATOR
             . 'conf' . DIRECTORY_SEPARATOR . 'conf.php' );

if( !SYS_DEBUG )
{
    error_reporting(0);
}
else
{
	error_reporting(E_ALL ^ E_NOTICE);
}

require_once( WEB_ROOT . DS . 'lib' . DS . 'init.php' );
init::boot();
sys_log::report( '请求的路径：' . $_SERVER['REQUEST_URI'] );
$ca = router::url2ca( $_SERVER['REQUEST_URI'] );

// 默认为使用layout
$_SESSION[SYS_NAME]['controller_' . $ca['controller']]['sys_layout_display'] = TRUE;

$dispatcher = new dispatcher();
$content = $dispatcher->exec( $ca );
$res = layout::output( $content, $ca['controller'] );