<?php

function response( $status,  $code,  $msg, array $result = [],  $quit = false) {
    $response = [
        'status' => $status,
        'code' => $code,
        'msg' => $msg,
        'result' => $result,
    ];
    if ($quit === true) {
        exit(json_encode($response, JSON_UNESCAPED_UNICODE));
    } else {
        return $response;
    }
}

function success_response(array $result = [], $quit = false) {
    return response(true, API_NO_SUCCESS, API_MSG_SUCCESS, $result, $quit);
}

function error_response( $code,  $msg, array $result = [],  $quit = false,  $header_status = StatusOK) {
    http_response_code($header_status);
    return response(false, $code, $msg, $result, $quit);
}

function validate_status(array $response,  $quit = false) {
    if ($quit === true) {
        exit(json_encode($response, JSON_UNESCAPED_UNICODE));
    } else {
        return $response['status'];
    }
}

function rcp_error_response(array $result,  $quit = false) {
    return error_response((int) CODE_FORUM . $result["code"], (string) $result["msg"], [], $quit);
}

/**
 * 日志打印
 * @param string $msg
 * @param string $type
 * @return bool
 */
function trace( $msg='',  $type=NORMAL) {
    $type = str_pad($type, 10, SP, STR_PAD_LEFT);
    $str = "[". date('Y-m-d H:i:s'). "]". "[$type]：". $msg. LN;
    return file_put_contents(LOG_PATH, $str, FILE_APPEND) ? true : false;
}

function abort($msg='', $code=404) {
    throw new Exception($msg, $code);
}

/**
 * 获取当前毫秒
 * @return float
 */
function calculateTime() {
    list($usec, $sec) = explode(' ', microtime());
    return ((float) $usec + (float) $sec);
}

/**
 * cookie设置
 * @param $var 设置的cookie名
 * @param $value 设置的cookie值
 * @param $life 设置的过期时间：为整型，单位秒 如60表示60秒后过期
 * @param $path 设置的cookie作用路径
 * @param $domain 设置的cookie作用域名
 */
 function ssetcookie($array, $life = 0, $path = '/', $domain = '') {
    global $_SERVER;
    $_cookName_ary = array_keys ( $array );
    for($i = 0; $i < count ( $array ); $i ++) {
        setcookie ( $_cookName_ary [$i], $array [$_cookName_ary [$i]], $life ? (time () + $life) : 0, $path, $domain, $_SERVER ['SERVER_PORT'] == 443 ? 1 : 0 );
    }
}