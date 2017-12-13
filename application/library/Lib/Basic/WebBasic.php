<?php
class WebController extends Yaf_Controller_Abstract
{
    public $req;
    public $isPost = false;
    public $isGet = false;
    public $isPC=false;

    public $view;
    public $default_view;
    public $vparam = array();

    public function init() {
        Yaf_Dispatcher::getInstance()->autoRender(false); //关闭自动渲染
        $this->req = $this->getRequest();
        $this->isPost = $this->req->isPost() ? true : false;
        $this->isGet = $this->req->isGet() ? true : false;
        $this->view = new BaseView(PUBLIC_PAHT. 'resources');
        $this->default_view = strtolower($this->req->getModuleName().DS.$this->req->getControllerName().DS.$this->req->getActionName().DT. Yaf_Registry::get('config')->application->view->ext);
    }

    public function render_view($path = '') {
        $module = $this->req->getModuleName();
        $controller = $this->req->getControllerName();
        $action = $this->req->getActionName();
        if ($path) {
            $path_arr = explode('/', strtolower($path));
            if (sizeof($path_arr) == 3) {
                $module = $path_arr[0];
                $controller = $path_arr[1];
                $action = $path_arr[2];
            } else if (sizeof($path_arr) == 2) {
                $controller = $path_arr[0];
                $action = $path_arr[1];
            } else if (sizeof($path_arr) == 1) {
                $action = $path_arr[0];
            } else {
                //trace faild
            }
        }
        $view_path =  $module. DS. $controller. DS. $action .DT. Yaf_Registry::get('config')->application->view->ext;
        $view_path = strtolower($view_path);
        $this->view->display($view_path, $this->vparam);
    }

    public function abort($msg='', $code=404) {
        throw new Exception($msg, $code);
    }

    /**
     * 获取GET参数
     * @param null $key
     * @param bool $filter
     * @return array|null|string
     */
    public function get($key=null, $filter=true) {
        if ($key) {
            $str = $this->req->get($key);
            return $filter ? filter($str) : $str;
        } else {
            return $filter ? filter($_GET) : $_GET;
        }
    }

    /**
     * 获取POST参数
     * @param string $key
     * @param bool $fileter
     * @return array|null|string
     */
    public function getPost( $key='',  $fileter=true) {
        if ($key) {
            $str = $this->req->getPost($key);
            return $fileter ? filter($str) : $str;
        } else {
            return $fileter ? filter($_POST) : $_POST;
        }
    }

    /**
     * 获取请求参数
     * @param string $key
     * @param bool $filter
     * @return array|null|string
     */
    public function getParam( $key='',  $filter=true) {
        return $this->isGet ? $this->get($key, $filter) : ($this->isPost ? $this->getPost($key, $filter) : null);
    }

    /**
     * 页面跳转
     * @param string $URL
     * @param int $second
     */
    function redirect($URL = '', $second = 0) {
        if (!isset($URL)) {
            $URL = $_SERVER['HTTP_REFERER'];
        }
        ob_start();
        ob_end_clean();
        header("Location: ".$URL, TRUE, 302);
        ob_flush();
        exit;
    }


    function page($page=1, $size=0, $count=0) {
        $result = array();
        $pagelen = (ceil($count/$size));
        $result['len'] = ($pagelen > 10) ? 10 : $pagelen;
        $result['first_page'] = 1;
        $result['privious_page'] = ($page == 1) ? null : ($page - 1);
        $result['next_page'] = ($page == $pagelen) ? null : ($page + 1);
        $result['curren_page'] = $page;
        return $result;
    }

}