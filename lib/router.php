<?php
class router
{
    // uri 转 module controller action
    static function url2ca( $uri )
    {
        $res = parse_url( $uri );
        $path = $res['path'];
        $path = router::pseudo_url( $path );

        $ca = explode( '/', $path );
        $ret = array();

        $con_index = 1;
        $act_index = 2;

        $ret['controller'] = empty($ca[$con_index]) ? 'home' : $ca[$con_index];
        $ret['action']     = empty($ca[$act_index]) ? 'index' : $ca[$act_index];

        $_SESSION[SYS_NAME]['controller'] = $ret['controller'];
        $_SESSION[SYS_NAME]['action'] = $ret['action'];

        $num = count( $ca );
        if ( $num > 2 )
        {
            for ( $i = 3; $i < $num; $i += 2 )
            {
                $key = $ca[$i];
                $val = $ca[$i+1];
                $_GET[$key] = $val;
                $_REQUEST[$key] = $val;
            }
        }

        return $ret;
    }

    static function pseudo_url( $uri )
    {
        $suffix = substr( $uri, -5 );
        if ( 0 === strcmp( $suffix, '.html' ) )
        {
            return substr( $uri, 0, -5 );
        }
        else
        {
            return $uri;
        }
    }

}