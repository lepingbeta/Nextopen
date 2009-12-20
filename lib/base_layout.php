<?php
require_once( 'base.php' );
class base_layout extends base
{
    static function output( $content, $controller )
    {
        if ( $_SESSION[SYS_NAME]['controller_' . $controller]['sys_layout_display'] )
        {
            return layout::display( $content );
        }
        else
        {
            echo $content;
            return true;
        }
    }

    static function display( $content )
    {
        $path = WEB_ROOT . DS . 'app' . DS . 'layout' . DS . 'layout.php';
        if( file_exists( $path ) )
        {
            require_once( $path );
            return true;
        }
        else
        {
            $msg = "layout文件不存在！路径：{$path}。";
            sys_log::report( $msg );
            return false;
        }
    }
}