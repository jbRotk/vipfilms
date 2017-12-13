<?php

function random_strings($length = 10, $type = null) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    switch ($type) {
        case "lowercase":
            $characters = 'abcdefghijklmnopqrstuvwxyz';
            break;
        case "uppercase":
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case "letter":
            $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case "number":
            $characters = '0123456789';
            break;
        default:
            break;
    }

    $rand_strings = '';

    for ($i = 0; $i < $length; $i++) {
        $rand_strings .= $characters[rand(0, strlen($characters))];
    }

    return $rand_strings;
}

function converCharset($from, $to, $content){
    if(empty($from) || empty($to) || empty($content)) return '';
    if(function_exists('iconv')){
        $content = iconv($from, $to, $content);
    }else if(function_exists('mb_convert_encoding')){
        $content = mb_convert_encoding($content, $to, $from);
    }
    return $content;
}

//对字符串等进行过滤
/**
 * 过滤字符串或者数组
 * @param $arr
 * @return array|null|string
 */
function filter($arr) {
    if (!isset($arr)) {
        return null;
    }

    if (is_array($arr)) {
        foreach ($arr as $k => $v) {
            $arr[$k] = filterStr(stripSQLChars(stripHTML(trim($v), true)));
        }
    } else {
        $arr = filterStr(stripSQLChars(stripHTML(trim($arr), true)));
    }

    return $arr;
}

/**
 * 在预定义字符之前添加反斜杠
 * @param $content
 * @return string
 */
function filterStr($content) {
    if (!get_magic_quotes_gpc()) {
        return addslashes($content);
    } else {
        return $content;
    }
}

/**
 * 过滤HTML脚本代码
 * @param $content
 * @param bool $xss
 * @return string
 */
function stripHTML($content, $xss = true) {
    $search = array("@<script(.*?)</script>@is",
        "@<iframe(.*?)</iframe>@is",
        "@<style(.*?)</style>@is",
        "@<(.*?)>@is"
    );

    $content = preg_replace($search, '', $content);

    if($xss){
        $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link',
            'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer',
            'layer', 'bgsound', 'title', 'base');

        $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy',      'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        $ra = array_merge($ra1, $ra2);

        $content = str_ireplace($ra, '', $content);
    }
    return strip_tags($content);
}

/**
 * 防止XSS攻击
 * @param $val
 * @return mixed
 */
function removeXSS($val) {
    // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
    // this prevents some character re-spacing such as <javaΘscript>
    // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
    $val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val);

    // straight replacements, the user should never need these since they're normal characters
    // this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); $i++) {
        // ;? matches the ;, which is optional
        // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

        // &#x0040 @ search for the hex values
        $val = preg_replace('/(&#[x|X]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
        // @ @ 0{0,7} matches '0' zero to seven times
        $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
    }

    // now the only remaining whitespace attacks are \t, \n, and \r
    $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link',
        'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer',
        'layer', 'bgsound', 'title', 'base');

    $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy',      'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $ra = array_merge($ra1, $ra2);

    $found = true; // keep replacing as long as the previous round replaced something
    while ($found == true) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?';
                    $pattern .= '|(&#0{0,8}([9][10][13]);?)?';
                    $pattern .= ')?';
                }
                $pattern .= $ra[$i][$j];
            }
            $pattern .= '/i';
            $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
            $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
            if ($val_before == $val) {
                // no replacements were made, so exit the loop
                $found = false;
            }
        }
    }

    return $val;
}

/**
 * 防止SQL注入攻击
 * @param $str
 * @return mixed
 */
function stripSQLChars($str) {
    $replace = array('SELECT', 'INSERT', 'DELETE', 'UPDATE', 'CREATE', 'DROP', 'VERSION', 'DATABASES',
        'TRUNCATE', 'HEX', 'UNHEX', 'CAST', 'DECLARE', 'EXEC', 'SHOW', 'CONCAT', 'TABLES', 'CHAR', 'FILE',
        'SCHEMA', 'DESCRIBE', 'UNION', 'JOIN', 'ALTER', 'RENAME', 'LOAD', 'FROM', 'SOURCE', 'INTO', 'LIKE', 'PING', 'PASSWD');
    return str_ireplace($replace, '', $str);
}

function csrfToken() {
    @session_start();
    $key = sha1(random(32));
    $_SESSION['csrf_token'] = $key;
    echo "<div  hidden><input type='text' name='csrf_token' value='{$key}'></div>";
}

function random($length, $type = 0, $hash = '') {
    if ($type == 0) {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
    } else if ($type == 1) {
        $chars = '0123456789';
    } else if ($type == 2) {
        $chars = 'abcdefghijklmnopqrstuvwxyz';
    } else if ($type == 3) {
        $chars = '0123456789abcdef';
    }
    $max = strlen ( $chars ) - 1;
    mt_srand ( ( double ) microtime () * 1000000 );
    for($i = 0; $i < $length; $i ++) {
        $hash .= $chars [mt_rand ( 0, $max )];
    }
    return strtoupper($hash);
}

function string_to_array($string) {
    $length = strlen($string);

    $arr = array();
    for ($i = 0; $i < $length; $i++) {
        $arr[] = $string[$i];
    }

    return $arr;
}

function array_to_string($arr=array()) {
    return implode(',', $arr);
}

/**
 * 把手机号的中间四位换成*号
 */
function convertMobile($mobile) {
    $pattern = "/(1\d{1,2})\d\d(\d{0,3})/";
    $replacement = "\$1****\$3";

    $mobile = preg_replace($pattern, $replacement, $mobile);
    return $mobile;
}

function is_serialized( $data, $strict = true ) {
    // if it isn't a string, it isn't serialized
    if ( ! is_string( $data ) )
        return false;
    $data = trim( $data );
    if ( 'N;' == $data )
        return true;
    $length = strlen( $data );
    if ( $length < 4 )
        return false;
    if ( ':' !== $data[1] )
        return false;
    if ( $strict ) {
        $lastc = $data[ $length - 1 ];
        if ( ';' !== $lastc && '}' !== $lastc )
            return false;
    } else {
        $semicolon = strpos( $data, ';' );
        $brace     = strpos( $data, '}' );
        // Either ; or } must exist.
        if ( false === $semicolon && false === $brace )
            return false;
        // But neither must be in the first X characters.
        if ( false !== $semicolon && $semicolon < 3 )
            return false;
        if ( false !== $brace && $brace < 4 )
            return false;
    }
    $token = $data[0];
    switch ( $token ) {
        case 's' :
            if ( $strict ) {
                if ( '"' !== $data[ $length - 2 ] )
                    return false;
            } elseif ( false === strpos( $data, '"' ) ) {
                return false;
            }
        // or else fall through
        case 'a' :
        case 'O' :
            return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
        case 'b' :
        case 'i' :
        case 'd' :
            $end = $strict ? '$' : '';
            return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
    }
    return false;
}
