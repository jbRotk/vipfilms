<?php

function validate_email($email='') {
    if (!$email) {
        return false;
    }
    return preg_match('/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4}$/', $email);
}

function validate_mobile($mobile='') {
    if (!$mobile) {
        return false;
    }

    return preg_match('/^((\(d{2,3}\))|(\d{3}\-))?1(3|5|8|9)\d{9}$/', $mobile);
}

function validate_postal_code($postalCode='') {
    if (!$postalCode) {
        return false;
    }

    return preg_match("/^[1-9]\d{5}$/", $postalCode);
}

function validate_ip_address($IPAddress='') {
    if (!$IPAddress) {
        return false;
    }

    return preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])" .
        "(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", $IPAddress);
}

function validate_id_card($IDCard='') {
    if (!$IDCard) {
        return false;
    }

    return preg_match('/(^([\d]{15}|[\d]{18}|[\d]{17}x)$)/', $IDCard);
}

function validate_cn($str='') {
    if(preg_match("/[\x{4e00}-\x{9fa5}]+/u", $str)) {
        return true;
    }
    return false;
}

/**
 * 检查数字
 * @param string $str 标签字符串
 */
function validate_number($str){
    if(preg_match('/^\d+$/', $str)) {
        return true;
    }
    return false;
}

/**
 * 检查是否每位相同
 * @param string $str 标签字符串
 */
function validate_same_num($str){
    if(preg_match('/^(\w)\1+$/', $str)) {
        return true;
    }
    return false;
}

/**
 * 检查是否为空
 * @param string $str 标签字符串
 */
function validate_empty($str){
    //$str = trim($str);
    if(preg_match('/^\s*$/', $str)) {
        return true;
    }
    return false;
}

/**
 * 检测是否为合法url
 */
function validate_url($url){
    if(strpos('kkk' . $url, 'http')){
        return true;
    }
    return false;
}

function validate_phone_number($phone) {
    if (!$phone) {
        return false;
    }
    echo($phone);
    return preg_match('/^((0\d{3}[\-])?\d{7}|(0\d{2}[\-])?\d{8})?$/', $phone);
}

