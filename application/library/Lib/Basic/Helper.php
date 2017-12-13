<?php


abstract class Helper
{
    public static function import($function='')
    {
        $function = 'F_'.ucfirst($function);
        $function_path = FUNCTION_PATH. $function. DT. 'php';
        if (file_exists($function_path)) {
            try {
                Yaf_Loader::import($function_path);
            } catch (Exception $exception) {
                echo $exception->getMessage();
            }
            unset($function, $function_path);
        } else {
            trace("Import File:: $function Faild!", ERROR);
            //error trace
        }
    }
}