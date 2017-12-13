<?php

class Bootstrap extends Yaf_Bootstrap_Abstract
{

    public function _initPlugin(Yaf_Dispatcher $dispatcher) {
        //$test = new Test();
        //$dispatcher->registerPlugin($test);
    }
    /**
     * 配置文件
     */
    public function _initConfig() {
        $arrConfig = Yaf_Application::app()->getConfig();
        Yaf_Registry::set('config', $arrConfig);
    }

    /**
     * 路由配置
     * @param Yaf_Dispatcher $dispatcher
     */
    public function _initRoute(Yaf_Dispatcher $dispatcher) {

    }

    public function _initAdminRoute(Yaf_Dispatcher $dispatcher) {
        $router = Yaf_Dispatcher::getInstance()->getRouter();
        //Admin
        $rout['taglist'] = new Yaf_Route_Regex('/^\/tag\/([1-9][0-9]{0,4})_([1-9][0-9]?)\/$/', ['module'=>'Index', 'controller'=>'Index', 'action'=>'taglist'], [1=>'tid', 2=>'pid'], []);
        while (list($key, $val) = each($rout)) {
            $router->addRoute($key, $val);
        }
    }

    /**
     * 基础文件导入
     * @param Yaf_Dispatcher $dispatcher
     */
    public function _initBasic(Yaf_Dispatcher $dispatcher) {
        Yaf_Loader::import('Lib/Basic/Basic.php');
        Yaf_Loader::import('Lib/Basic/View.php');
        Yaf_Loader::import('Lib/Basic/Helper.php');

        Helper::import('String');
        Helper::import('Array');
        Helper::import('Network');
        Yaf_Loader::import('Lib/Basic/AdminBasic.php');
        Yaf_Loader::import('Lib/Basic/WebBasic.php');
        Yaf_Loader::import('Lib/Basic/M_Basic.php');
        Yaf_Loader::import('Lib/Basic/BaseView.php');
    }

    public function _initConst(Yaf_Dispatcher $dispatcher) {
        Yaf_Loader::import('Lib/Const/Api.php');
    }

    public function _initLib(Yaf_Dispatcher $dispatcher) {
        //数据库操作类
        Yaf_Loader::import('Lib/Orm/Medoo.php');
        Yaf_Loader::import('Lib/Orm/Model.php');
        Yaf_Loader::import('Redis/Redis.php');
    }

}