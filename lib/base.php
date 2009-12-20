<?php
class base
{
    function __construct()
    {

    }

    static public function arr_urlencode( &$arr )
    {
        foreach ( $arr AS &$val )
        {
            if ( is_array( $val ) )
            {
                base_model::arr_urlencode( $val );
            }
            else
            {
                $val = urlencode( $val );
            }
        }
    }

    static public function redirect( $url, $args = array() )
    {
    	if( $args and is_array( $args ) )
    	{
            $arr_hidden = array();
	        foreach( $args AS $name => $value )
	        {
	            $input  = "<input type=\"hidden\" name=\"{$name}\"";
                $input .= " value=\"{$value}\" />";
	            $arr_hidden[] = $input;
	        }
	        if( $arr_hidden )
	        {
	            $block_hidden = join( "\n", $arr_hidden );
	        }

            $html = <<< EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Redirect</title>
<script type="text/javascript">
function load()
{
    document.getElementById("auto_form").submit();
}
</script>
</head>

    <body onLoad="load()">
        <form action="{$url}" method="post" id="auto_form" name="auto_form">
          {$block_hidden}
        </form>
    </body>
</html>
EOF;
    	}
    	else
    	{
            $html = <<< EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Redirect</title>
<script type="text/javascript">
function load()
{
    window.location = '{$url}';
}
</script>
</head>
    <body onLoad="load()">
    </body>
</html>
EOF;
    	}


        echo $html;
    }

    static public function get_client_ip ( $is_long = FALSE )
    {
        if ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"])
        {
            $ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
        }
        elseif ($HTTP_SERVER_VARS["HTTP_CLIENT_IP"])
        {
            $ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
        }
        elseif ($HTTP_SERVER_VARS["REMOTE_ADDR"])
        {
            $ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
        }
        elseif (getenv("HTTP_X_FORWARDED_FOR"))
        {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        }
        elseif (getenv("HTTP_CLIENT_IP"))
        {
            $ip = getenv("HTTP_CLIENT_IP");
        }
        elseif (getenv("REMOTE_ADDR"))
        {
            $ip = getenv("REMOTE_ADDR");
        }
        else
        {
            $ip = "Unknown";
        }

        if( $is_long )
        {
        	$ip = base::ip2long($ip);
        }

        return $ip;
    }

    static function ip2long($ip)
    {
		$ip_arr = explode('.',$ip);
		$iplong  = (16777216 * intval($ip_arr[0]));
		$iplong += (65536 * intval($ip_arr[1])) + (256 * intval($ip_arr[2]));
		$iplong += intval($ip_arr[3]);
		return $iplong;
    }

    static function arr_substr(&$arr, $start, $length, $encoding = 'UTF-8')
    {
    	foreach( $arr AS &$val )
    	{
    		if( is_array( $val ) )
    		{
    			base::arr_substr( $val, $start, $length, $encoding );
    		}
    		else
    		{
    			$val = mb_substr( $val, $start, $length, $encoding );
    		}
    	}
    }

    protected function loadModel( $model )
    {
    	$model_path  = dirname( dirname( __FILE__ ) ) . DS . 'app' . DS;
    	$model_path .= 'model' . DS . "{$model}.php";
    	if( file_exists( $model_path ) )
    	{

    		require_once( $model_path );
    	}
    	else
    	{
    		return false;
    	}

    	if ( class_exists( $model ) )
    	{
    		$this->$model = new $model;
    		return true;
    	}
  		return false;
    }

    static public function get_error_by_code( $code )
    {
    	$lang_id = $_SESSION[SYS_NAME]['lang_id'];

        $lang = $GLOBALS[SYS_NAME][LANG_LIST][$lang_id]['short'];
        $error = $GLOBALS[SYS_NAME][$code][$lang];

        sys_log::report( $error );
        return $error;
    }

}
