<?php
define( 'DS', DIRECTORY_SEPARATOR );
define( 'WEB_ROOT', dirname( dirname( __FILE__ ) ) );
require_once( WEB_ROOT . DS . 'lib' . DS . 'conf' . DS . 'conf.php' );

// 定义系统文件名
if( !defined( 'SYS_NAME' ) )
{
    define( 'SYS_NAME', basename( WEB_ROOT ) );
}


/*
 * 开发环境
 */
define( 'SYS_DEBUG',  '1' );     // 调试版用1，产品版用0。
define( 'LOG_DIR',    dirname( dirname( __FILE__ ) ) . DS . 'log' );
define( 'SYS_DB_HOST', 'mysql.lepingbeta.com' );
define( 'SYS_DB_NAME', 'sns_sync_test' );
define( 'SYS_DB_USER', 'sns_sync' );
define( 'SYS_DB_PASSWD', 'sns_sync@pwd' );

/*
 * 运营环境
 */
//define( 'SYS_DEBUG',  '0' );     // 调试版用1，产品版用0。
//define( 'LOG_DIR',    dirname( dirname( __FILE__ ) ) . DS . 'log' );
//define( 'SYS_DB_HOST', 'mysql.lepingbeta.com' );
//define( 'SYS_DB_NAME', 'sns_sync_test' );
//define( 'SYS_DB_USER', 'sns_sync' );
//define( 'SYS_DB_PASSWD', 'sns_sync@pwd' );