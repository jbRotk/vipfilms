<?php

class AdminController extends Yaf_Controller_Abstract
{
    public $req;
    public $isPost = false;
    public $isGet = false;

    public $view;
    public $default_view;

    public $vparam = array();

    public function init() {
        echo $this->validateKey();
        Yaf_Dispatcher::getInstance()->autoRender(false); //关闭自动渲染
        $this->req = $this->getRequest();
        strtolower($this->req->getActionName()) == 'login' ? null : ($this->isLogin() ? null : $this->redirect('/Mmggqbj1X6yPTg6w_admin/login'));
        $this->isPost = $this->req->isPost() ? true : false;
        $this->isGet = $this->req->isGet() ? true : false;
        $this->view = new BaseView(PUBLIC_PAHT. 'resources'. DS);
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

    public function isLogin() {
        $cookie = $_COOKIE;
        if (isset($cookie['key'])) {
            if ($cookie['key'] === sha1($cookie['name']. $cookie['uid']. $cookie['login_time']. COOKIE_KEY)) {
                $cookie_inf['login_time'] = date('Y-m-d H:i:s');
                $cookie_inf['key'] = sha1($cookie['name']. $cookie['uid']. $cookie_inf['login_time']. COOKIE_KEY);
                ssetcookie($cookie_inf, 7200);
                return true;
            }
        }
        $cookie_inf['uid'] = '';
        $cookie_inf['name'] = '';
        $cookie_inf['login_time'] = '';
        $cookie_inf['key'] = '';
        ssetcookie($cookie_inf, 7200);
        return false;
    }

    public function checkLogin($user='', $pwd='', $timeout = 7200) {
        $user = filterStr($user);
        $pwd = filterStr($pwd);
        $user = BaseModel::db('users')->where("name='{$user}' and MD5(concat('{$pwd}',tag)) = pwd")->find();
        if ($user) {
            $cookie_inf['uid'] = $user['uid'];
            $cookie_inf['name'] = $user['name'];
            $cookie_inf['login_time'] = date('Y-m-d H:i:s');
            $cookie_inf['key'] = sha1($cookie_inf['name']. $cookie_inf['uid']. $cookie_inf['login_time']. COOKIE_KEY);
            ssetcookie($cookie_inf, $timeout);
            return true;
        }
        $cookie_inf['uid'] = '';
        $cookie_inf['name'] = '';
        $cookie_inf['login_time'] = '';
        $cookie_inf['key'] = '';
        ssetcookie($cookie_inf, $timeout);
        return false;
    }

    public function validateCsrf() {
        @session_start();
        if ($this->getParam('csrf_token') === $_SESSION['csrf_token']) {
            return true;
        }
        @header("HTTP/1.0 404 Not Found");
        exit("token expire");
    }

    public function validateKey() {
        @session_start();
        if (isset($_SESSION['k'])) {
            if ($_SESSION['k'] != ADMIN_KEY) {
                @header("HTTP/1.0 404 Not Found");
                exit("无权使用");
            }
        } else {
            if(isset($_GET['k']) && $_GET['k'] == ADMIN_KEY) {
                $_SESSION['k'] = $_GET['k'];
            } else {
                @header("HTTP/1.0 404 Not Found");
                exit("无权使用");
            }
        }
    }

}