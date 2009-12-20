<?php
class init
{
	static function boot()
	{
	    require_once( WEB_ROOT . DS . 'lib' . DS . 'conf' . DS . 'conf.php' );
	    require_once( WEB_ROOT . DS . 'lib' . DS . 'conf' . DS . 'lang.php' );
	    require_once( WEB_ROOT . DS . 'lib' . DS . 'function.php' );
		require_once( WEB_ROOT . DS . 'app' . DS . 'controller.php' );
		require_once( WEB_ROOT . DS . 'app' . DS . 'model.php' );
		require_once( WEB_ROOT . DS . 'app' . DS . 'view.php' );
		require_once( WEB_ROOT . DS . 'app' . DS . 'layout.php' );
		require_once( WEB_ROOT . DS . 'lib' . DS . 'sys_log.php' );
		require_once( WEB_ROOT . DS . 'lib' . DS . 'router.php' );
		require_once( WEB_ROOT . DS . 'lib' . DS . 'dispatcher.php' );
		init::load_lang();
	}

	static function load_lang()
	{
        // 已登录，使用设定语言
        if( !empty( $_SESSION[SYS_NAME]['user']['lang_id'] ) )
        {
        	$lang_id = $_SESSION[SYS_NAME]['user']['lang_id'];
            $_SESSION[SYS_NAME]['lang_id'] = $lang_id;
            return ;
        }

        switch (substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,5))
        {
        	case $GLOBALS[SYS_NAME][LANG_LIST][2]['short']:
                $_SESSION[SYS_NAME]['lang_id']
                             = $GLOBALS[SYS_NAME][LANG_LIST][2]['code'];
        		break;
        	default: // 未登录，使用默认语言
                $_SESSION[SYS_NAME]['lang_id'] = SYS_DEFAULT_LANG_ID;
        }
	}
}