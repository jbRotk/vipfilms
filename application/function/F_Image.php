<?php

/**
 * 创建缩略图
 * @param $source
 * @param $destination
 * @param $saveName
 * @param $targetWidth
 * @param $targetHeight
 * @return string
 */
function createThumb($source, $destination, $saveName, $targetWidth, $targetHeight){
    // Get image size
    $originalSize = getimagesize($source);

    // Set thumb image size
    $targetSize = setWidthHeight($originalSize[0], $originalSize[1], $targetWidth, $targetHeight);

    // Get image extension
    $ext = getExtension($source);

    // Determine source image type
    if($ext == 'gif'){
        $src = imagecreatefromgif($source);
    }elseif($ext == 'png'){
        $src = imagecreatefrompng($source);
    }elseif ($ext == 'jpg' || $ext == 'jpeg'){
        $src = imagecreatefromjpeg($source);
    }else{
        return 'Unknow image type !';
    }

    // Copy image
    $dst = imagecreatetruecolor($targetSize[0], $targetSize[1]);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $targetSize[0], $targetSize[1],$originalSize[0], $originalSize[1]);

    if(!file_exists($destination)){
        if(!createRDir($destination)){
            return 'Unabled to create destination folder !';
        }
    }

    // destination + fileName
    $thumbName = $destination.'/'.$saveName.'.'.$ext;

    if($ext == 'gif'){
        imagegif($dst, $thumbName);
    }else if($ext == 'png'){
        imagepng($dst, $thumbName);
    }else if($ext == 'jpg' || $ext == 'jpeg'){
        imagejpeg($dst, $thumbName, 100);
    }else{
        return 'Fail to create thumb !';
    }

    imagedestroy($dst);
    imagedestroy($src);
    return $thumbName;
}

/**
 * 加水印
 * @param $source
 * @param $destination
 * @param $watermarkPath
 */
function addWatermark($source, $destination, $watermarkPath){
    list($owidth,$oheight) = getimagesize($source);
    $width = $height = 300;
    $im = imagecreatetruecolor($width, $height);
    $img_src = imagecreatefromjpeg($source);
    imagecopyresampled($im, $img_src, 0, 0, 0, 0, $width, $height, $owidth, $oheight);
    $watermark = imagecreatefrompng($watermarkPath);
    list($w_width, $w_height) = getimagesize($watermarkPath);
    $pos_x = $width - $w_width;
    $pos_y = $height - $w_height;
    imagecopy($im, $watermark, $pos_x, $pos_y, 0, 0, $w_width, $w_height);
    imagejpeg($im, $destination, 100);
    imagedestroy($im);
}

function qiniu_image_upload($filename='') {
    Yaf_Loader::import('Qiniu/autoload.php');
    $file_name = explode(DT, $_FILES[$filename]["name"]);
    $file_name[0] = uniqid();
    $file_name = implode(DT, $file_name);
    $tmp_path = PUBLIC_PAHT. DS. 'tmp'. DS. $file_name;
    $tmp = move_uploaded_file($_FILES[$filename]["tmp_name"], $tmp_path);
    if ($tmp) {
        $auth = new \Qiniu\Auth(QINIU_ACCESS_KEY, QINIU_SECRET_KEY);
        $token = $auth->uploadToken(QINIU_BUKET);
        $uploadManager = new \Qiniu\Storage\UploadManager();
        list($ret, $err) = $upload = $uploadManager->putFile($token, null, $tmp_path);
        if($ret != null) {
            unlink($tmp_path);
            return isset($ret['hash']) ? QINIU_IMAGE_PATH.DS.$ret['hash'] : false;
        }
    }
    return false;

}
