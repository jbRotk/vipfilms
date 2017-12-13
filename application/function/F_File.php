<?php

function getExtension($file) {
    $info = pathinfo($file);
    return $info['extension'];
}

function deleteExtensionFiles($dir, $extension) {
    $dirs = scandir($dir);
    // Do not scan current and parent dir:
    $exceptDirs = array('.',  '..');
    foreach ($dirs as $key => $value) {
        if (!in_array($value, $exceptDirs)) {
            if (is_dir($dir . DS . $value)) {
                deleteExtensionFiles($dir . DS . $value, $extension);
            } else {
                $ext = getExtension($value);
                if ($ext == $extension) {
                    @unlink($value);
                }
            }
        }
    }
}

/**
 *  Create folder recursively
 */
function createRDir($folder) {
    $reval = false;
    if (!file_exists($folder)) {
        @umask(0);
        preg_match_all('/([^\/]*)\/?/i', $folder, $atmp);
        $base = ($atmp[0][0] == '/') ? '/' : '';

        foreach ($atmp[1] AS $val) {
            if ('' != $val) {
                $base .= $val;
                if ('..' == $val || '.' == $val) {
                    $base .= '/';
                    continue;
                }
            } else {
                continue;
            }

            $base .= '/';

            if (!file_exists($base)) {
                if (mkdir($base, 0777)) {
                    chmod($base, 0777);
                    $reval = true;
                }
            }
        }
    } else {
        $reval = is_dir($folder);
    }

    clearstatcache();
    return $reval;
}

/**
 * 获取某个目录下的所有文件名，不包含子文件
 * @param $dir
 * @return array|null
 */
function dir_files_name($dir) {
    if (!is_dir($dir)) {
        return null;
    }

    $fileArr = scandir($dir);

    if (empty($fileArr)) {
        return null;
    }

    $files = array();
    foreach ($fileArr as $key => $value) {
        if (!is_dir($value)) {
            $files[] = $value;
        }
    }

    clearstatcache();
    return $files;
}

/**
 * 文件拷贝
 * @param $source
 * @param $destination
 * @param $child
 * @return int
 */
function file_copy($source, $destination, $child) {
    if (!is_dir($source)) {
        return 0;
    }

    if (!is_dir($destination)){
        mkdir($destination, 0777);
    }

    $handle = dir($source);

    while ($entry = $handle->read()) {
        if (($entry != ".") && ($entry != "..")) {
            if (is_dir($source . "/" . $entry)) {
                if ($child){
                    xCopy($source . "/" . $entry, $destination . "/" . $entry, $child);
                }else{
                    copy($source . "/" . $entry, $destination . "/" . $entry);
                }
            }
        }
    }
    return 1;
}

/**
 * 下载远程图片到本地
 * @param $url
 * @param string $save_dir
 * @param string $filename
 * @param int $type
 * @return array
 */
function image_download($url, $save_dir='', $filename='', $type=0){
    if(trim($url) == ''){
        return array('file_name'=>'', 'save_path'=>'', 'error'=>1);
    }

    if(trim($save_dir) == ''){
        $save_dir = './';
    }

    //保存文件名
    if(trim($filename) == ''){
        $ext = strrchr($url, '.');
        if($ext != '.gif' && $ext != '.jpg'){
            return array('file_name'=>'', 'save_path'=>'', 'error'=>3);
        }
        $filename=time().$ext;
    }

    if(0 !== strrpos($save_dir, '/')){
        $save_dir.='/';
    }

    //创建保存目录
    if(!file_exists($save_dir) && !mkdir($save_dir, 0777, true)){
        return array('file_name' => '', 'save_path' => '', 'error' => 5);
    }

    //获取远程文件所采用的方法
    if($type){
        $ch = curl_init();
        $timeout = 1005;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $img = curl_exec($ch);
        curl_close($ch);
    }else{
        ob_start();
        readfile($url);
        $img=ob_get_contents();
        ob_end_clean();
    }

    //文件大小
    $fp2 = @fopen($save_dir.$filename, 'a');
    fwrite($fp2, $img);
    fclose($fp2);
    unset($img, $url);
    return array('file_name' => $filename, 'save_path' => $save_dir.$filename, 'error' => 0);
}

