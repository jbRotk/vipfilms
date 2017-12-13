<?php
class SystemController extends AdminController {
    public function loginAction() {
        if ($this->isGet) {
            $this->render_view();
        } elseif ($this->isPost) {
            if ($this->validateCsrf() && $this->checkLogin($this->getParam('user'), $this->getParam('pwd'))) {
                $this->redirect('/admin_Mmggqbj1X6yPTg6w/list');
            } else {
                $this->vparam['error_msg'] = '(账号或密码错误)';
                $this->render_view();
            }
        }
    }

    public function indexAction() {
        $this->render_view();
    }

    public function logoutAction() {
        $cookie_inf['uid'] = '';
        $cookie_inf['name'] = '';
        $cookie_inf['login_time'] = '';
        $cookie_inf['key'] = '';
        ssetcookie($cookie_inf, COOKIE_EXPIRE_TIME);
        $this->redirect('/admin_Mmggqbj1X6yPTg6w/login');
    }
}