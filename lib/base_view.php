<?php
require_once( 'base.php' );
require_once ('sys_log.php');
class base_view extends base
{
    static function display ($tpl, $args = array())
    {
    	$c = $tpl['c'];
    	$a = $tpl['a'];

        $path = WEB_ROOT . DS . 'app' . DS;
        $path .= 'view' . DS;
        $path .= $c . DS;
        $path .= $a . '.php';

        base_view::show_tpl ( $path, $c, $a, $args );
    }

    static private function show_tpl ( $tpl_path, $c, $a, $args = array() )
    {
        if (! empty($args))
        {
            while (list ($key, $val) = each($args))
            {
                $$key = $val;
            }
        }
        unset( $args );

        $lang_id = $_SESSION[SYS_NAME]['lang_id'];
        $lang = $GLOBALS[SYS_NAME][LANG_LIST][$lang_id]['short'];

        // 读取语言包
        $sys_lang = $GLOBALS[SYS_NAME][$c][$a][$lang];
        if ( $GLOBALS[SYS_NAME][$c]['common'][$lang] )
        {
            $sys_lang += $GLOBALS[SYS_NAME][$c]['common'][$lang];
        }

        if ( $sys_lang )
        {
            $sys_lang += $GLOBALS[SYS_NAME]['common'][$lang];
        }
        else
        {
        	$sys_lang = $GLOBALS[SYS_NAME]['common'][$lang];
        }

        $sys_lang['lang_id'] = $lang_id;
        $sys_lang['lang_list'] = $GLOBALS[SYS_NAME]['lang_list'];
        unset( $lang );

        if (file_exists($tpl_path))
        {
            require_once($tpl_path);
        }
        else
        {
            $msg = "请求的模板（路径：{$tpl_path}）不存在";
            sys_log::report($msg);
        }
    }

    static function load_partial($name, $args = array(), $is_curr_mod = false)
    {
        if (! empty($args))
        {
            while (list ($key, $val) = each($args))
            {
                $$key = $val;
            }
        }
        unset( $args );
        if( $is_curr_mod )
        {
            $path  = WEB_ROOT . DS . 'app' . DS . 'view';
            $path .= DS . $_SESSION[SYS_NAME]['controller'];
            $path .= DS . $name . '.php';
        }
        else
        {
            $path  = WEB_ROOT . DS . 'app' . DS . 'partial';
            $path .= DS . $name . '.php';
        }
        require_once( $path );
    }

    static function set_title( $title = '' )
    {
    	if( empty( $title ) )
    	{
    		$_SESSION[SYS_NAME]['layout']['title'] = SYS_NAME;
    		return ;
    	}
        $_SESSION[SYS_NAME]['layout']['title'] = $title;
    }

    static function get_title()
    {
        return $_SESSION[SYS_NAME]['layout']['title'];
    }
}
