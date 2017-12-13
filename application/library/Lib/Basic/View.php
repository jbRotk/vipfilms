<?php

/**
 * JS加载
 * @param $files
 */
function JSLoader($files, $plugin='') {
    $arrFiles = explode("|", $files);
    foreach ($arrFiles as $file) {
        $file = trim($file);
        echo $plugin ? ($file ? '<script src="' . PLUGIN_PATH . $plugin . '/js/' . $file . '"></script>' : null) : ($file ? '<script src="' . JS_PATH . $file . '"></script>' : null) ;
    }
}

/**
 * CSS加载
 * @param $files
 */
function CSSLoader($files, $plugin='', $autoload = true) {
    $arrFiles = explode("|", $files);
    foreach ($arrFiles as $file) {
        $file = trim($file);
        echo $plugin ? ($file ? '<link href="' . PLUGIN_PATH . $plugin . '/css/' . $file . '" rel="stylesheet">' : null) : ($file ? '<link href="' . CSS_PATH . $file . '" rel="stylesheet">' : null);
    }
}

function IMGLoader($files, $plugin='') {
    $arrFiles = explode("|", $files);
    foreach ($arrFiles as $file) {
        $file = trim($file);
        echo $plugin ? ($file ? PLUGIN_PATH . $plugin . '/img/' . $file : null) : ($file ? JS_PATH . $file : null) ;
    }
}


/**
 * 通用模板加载
 * @param $file
 */
function TMPLoader($file) {
    include COMMON_PATH . $file;
}

/**
 * 前台提示框输出
 * @param string $msg
 */
function jsAlert($msg='') {
    echo $msg ? "<script type='text/javascript'>alert(\"$msg\")</script>" : null;
}

/**
 * 前台跳转
 * @param string $msg
 * @param string $URL
 */
function gotoURL($msg='', $URL='') {
    jsAlert($msg);
    $URL = $URL ? $URL : $_SERVER['HTTP_REFERER'];
    echo "<script type='text/javascript'>window.location.href='$URL'</script>";
}

/**
 * 输出错误信息后，退出
 * @param string $msg
 */
function dieEcho($msg='')
{
    echo $msg;
    die;
}


/**
 * 输出隐藏信息
 * @param $msg
 */
function echoHiddenDiv($msg){
    $html = '<div style="display:none">'.$msg.'</div>';
    echo $html;
}

/**
 * 输出高亮字符
 * @param $str
 * @param $find
 * @param $color
 * @return mixed
 */
function highlight($str, $find, $color){
    return str_replace($find, '<font color="'.$color.'">'.$find.'</font>', $str);
}

/**
 * 判断字符是否相等
 * @param $str_1
 * @param $str_2
 * @param $msg
 * @param $default
 */
function str_eq($str_1='', $str_2='', $msg='', $default='') {
    echo $str_1 == $str_2 ? $msg : $default;
}

/**
 * 判断值是否在数组内
 * @param $arr
 * @param $val
 * @param $msg
 * @param $default
 */
function inarray($arr, $val, $msg, $default) {
    echo in_array($val, $arr) ? $msg : $default;
}

function out($msg='',$default='') {
    echo isset($msg) ? $msg : $default;
}

