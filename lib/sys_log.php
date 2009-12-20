<?php
class sys_log
{
    static function report( $msg )
    {
    	if( !file_exists( LOG_DIR ) )
    	{
    		mkdir(LOG_DIR, 0777, true);
    	}

        $filepath = LOG_DIR . DS . date( 'Ymd' );
        $msg = date( "Y-m-d H:i:s" ) . " => {$msg}\n";
        error_log( $msg, 3, $filepath );
    }
}