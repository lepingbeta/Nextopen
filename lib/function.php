<?php
function go_404()
{
    redirect($GLOBALS[SYS_NAME]['uri_404']);
    exit;
}

function arr2input( $arr, $prefix = '' )
{
    $input_list = array();
    foreach ( $arr AS $name => $val )
    {
        if( !is_array( $val ) )
        {
            if( $prefix )
            {
                $input  = "<input type=\"hidden\" name=\"{$prefix}[{$name}]\"";
            }
            else
            {
                $input  = "<input type=\"hidden\" name=\"{$name}\"";
            }
            $input .= " value=\"{$val}\" />";
            $input_list[] = $input;
        }
        else
        {
            if( $prefix )
            {
                $input_list[] = arr2input( $val, $prefix . "[{$name}]" );
            }
            else
            {
                $input_list[] = arr2input( $val, $name );
            }
        }
    }
    $block_hidden = join( "\n", $input_list );
    return $block_hidden;
}

function redirect( $url, $args = array(), $exit = false )
{
    if( $args and is_array( $args ) )
    {
        $block_hidden = arr2input( $args );

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

        echo $html;
    }
    else
    {

		if (headers_sent())
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
            echo $html;
        }
        else
        {
            header('Location: ' . $url);
        }

    }

    if( $exit )
    {
    	exit();
    }
}

/*********************************************/
/* Fonction: ImageCreateFromBMP              */
/* Author:   DHKold                          */
/* Contact:  admin@dhkold.com                */
/* Date:     The 15th of June 2005           */
/* Version:  2.0B                            */
/*********************************************/

function ImageCreateFromBMP($filename)
{
    //Ouverture du fichier en mode binaire
    if (! $f1 = fopen($filename,"rb")) return FALSE;

    //1 : Chargement des ent�tes FICHIER
    $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
    if ($FILE['file_type'] != 19778) return FALSE;

    //2 : Chargement des ent�tes BM
    $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
                 '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
                 '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
    $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
    if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
    $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
    $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
    $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
    $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
    $BMP['decal'] = 4-(4*$BMP['decal']);
    if ($BMP['decal'] == 4) $BMP['decal'] = 0;

    //3 : Chargement des couleurs de la palette
    $PALETTE = array();
    if ($BMP['colors'] < 16777216)
    {
        $PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
    }

    //4 : Cr�ation de l'image
    $IMG = fread($f1,$BMP['size_bitmap']);
    $VIDE = chr(0);

    $res = imagecreatetruecolor($BMP['width'],$BMP['height']);
    $P = 0;
    $Y = $BMP['height']-1;
    while ($Y >= 0)
    {
        $X=0;
        while ($X < $BMP['width'])
        {
            if ($BMP['bits_per_pixel'] == 24)
            $COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
            elseif ($BMP['bits_per_pixel'] == 16)
            {
                $COLOR = unpack("n",substr($IMG,$P,2));
                $COLOR[1] = $PALETTE[$COLOR[1]+1];
            }
            elseif ($BMP['bits_per_pixel'] == 8)
            {
                $COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
                $COLOR[1] = $PALETTE[$COLOR[1]+1];
            }
            elseif ($BMP['bits_per_pixel'] == 4)
            {
                $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
                if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
                $COLOR[1] = $PALETTE[$COLOR[1]+1];
            }
            elseif ($BMP['bits_per_pixel'] == 1)
            {
                $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
                if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
                elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
                elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
                elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
                elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
                elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
                elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
                elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
                $COLOR[1] = $PALETTE[$COLOR[1]+1];
            }
            else
            return FALSE;
            imagesetpixel($res,$X,$Y,$COLOR[1]);
            $X++;
            $P += $BMP['bytes_per_pixel'];
        }
        $Y--;
        $P+=$BMP['decal'];
    }

    //Fermeture du fichier
    fclose($f1);

    return $res;
}

/**
* Generates an UUID
*
* @author     Anis uddin Ahmad <admin@ajaxray.com>
* @param      string  an optional prefix
* @return     string  the formatted uuid
*/
function uuid($prefix = '')
{
  $chars = md5(uniqid(mt_rand(), true));
  $uuid  = substr($chars,0,8) . '-';
  $uuid .= substr($chars,8,4) . '-';
  $uuid .= substr($chars,12,4) . '-';
  $uuid .= substr($chars,16,4) . '-';
  $uuid .= substr($chars,20,12);
  return $prefix . $uuid;
}

function get_db()
{
    return new base_model();
}

function __autoload($className)
{
	$className = strtolower($className);
    $path  = WEB_ROOT . DS . 'lib' . DS . 'class' . DS;
    $path .= "{$className}.class.php";
    require_once $path;
}
