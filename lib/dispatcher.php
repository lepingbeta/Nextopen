<?php
require_once ('router.php');

class dispatcher
{
    function exec ($ca)
    {
        ob_start();
        $controller = $ca['controller'];
        $action = $ca['action'];
        $filepath  = WEB_ROOT . DS . 'app' . DS . 'controller';
        $filepath .= DS . $controller . '.php';

        if ( file_exists( $filepath ) )
        {
            require_once( $filepath );
            $oController = new $controller();

            // 判断对应的model存在就加载model
            $model_name = strtolower( "{$controller}_model" );
	        $model_path  = WEB_ROOT . DS . 'app' . DS . 'model';
	        $model_path .= DS . $model_name . '.php';
	        if( file_exists( $model_path ) )
	        {
	        	include_once( $model_path );
	        	$oController->$model_name = new $model_name;
	        }

            if ( method_exists( $oController, $action ) )
            {
                $oController->$action();
                $content = ob_get_contents();
            }
            else
            {
            	$error  = "错误！：请求不存在的方法：{$ca['controller']} =>";
            	$error .= " {$action}";
                sys_log::report( $error );
                // 然后跳转到404页面。
                $content = false;
            }
        }
        else
        {
            $content = false;
        }
        ob_end_clean();
        if( !$content )
        {
            go_404();
        }
        return $content;
    }
}
