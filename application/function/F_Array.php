<?php

/**
 * Object转数组
 * @param $obj
 * @return array
 */
function object2Array($obj) {
    if(is_object($obj)){
        $obj = get_object_vars($obj);
    }

    return is_array($obj) ? array_map(__FUNCTION__, $obj):$obj;
}

/**
 * Array转数组
 * @param $arr
 * @return object
 */
function array2Object($arr) {
    return is_array($arr) ? (object) array_map(__FUNCTION__, $arr):$arr;
}

function array2list($arr = array(), $page = 1, $num = 10) {
    return array_slice($arr, $num*($page-1), $num);
}

/**
 * Value Find Key
 * @param $arr
 * @param $value
 * @return int|null|string
 */
function getArrayKey($arr, $value) {
    if(!is_array($arr)){
        return null;
    }

    foreach($arr as $k =>$v) {
        $return = getArrayKey($v, $value);
        if($v == $value){
            return $k;
        }

        if(!is_null($return)) {
            return $return;
        }
    }
}

/**
 * value find keys
 * @param array $arr
 * @param string $val
 * @return array|null
 */
function array_val_find_keys($arr=array(), $val='') {
    $res = array();
    if ($val && $arr) {
        while (list($k,$v) = each($arr)) {
            $v == $val ? array_push($res, $k) : null;
        }
        return $res ? $res : null;
    } else {
        return null;
    }
}

/**
 * 判断array里面的键值是否在string里面
 * @param array $arr
 * @param string $text
 * @return mixed|null
 */
function array_in_string($arr=array(), $text='') {
    if(!is_array($arr)){
        return null;
    }

    foreach($arr as $key){
        if(strstr($text, $key) != ''){
            $result = $key;
            break;
        }
    }

    if($result == ''){
        foreach($arr as $key){
            if(strstr($text, mb_substr($key, 0, 1, 'utf-8')) != ''){
                $result = $key;
                break;
            }
        }
    }
    return $result;
}

/**
 * 以字符形式保存原始数组内容
 * @param $array
 * @param bool $arrayName
 * @return mixed|string
 */
function array2string($array, $arrayName = false) {
    $data = var_export($array, true);
    if (!$arrayName) {
        $data = "<?php\n return " .$data.";\n?>";
    } else {
        $data = "<?php\n " .$arrayName . "=" .$data . ";\n?>";
    }
    return $data;
}

function array2D_unique($array2D, $stkeep = false, $ndformat = true) {
    if($stkeep){    //一级数组键可以为非数字
        $stArr = array_keys($array2D);
    }

    if($ndformat){   //二级数组键必须相同
        $ndArr = array_keys(end($array2D));
    }

    foreach ($array2D as $v){  //降维
        $v = join(',', $v);
        $temp[] = $v;
    }

    $temp = array_unique($temp);
    foreach ($temp as $k => $v){  //数组重新组合
        if($stkeep){
            $k = $stArr[$k];
        }

        if($ndformat){
            $tempArr = explode(",",$v);
            foreach($tempArr as $ndkey => $ndval){
                $output[$k][$ndArr[$ndkey]] = $ndval;
            }
        }else{
            $output[$k] = explode(",",$v);
        }
    }
    return $output;
}

