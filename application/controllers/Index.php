<?php

use QL\QueryList;

class IndexController extends WebController
{

    public function indexAction() {
        //print_r(LinkSpider::get()->getSpiderLink('蜘蛛侠'));
        $this->render_view();
    }

    public function loadAction() {
        $this->render_view();
    }
    public function ajax_dataAction() {
        $field = $this->get('field');
        echo json_encode(['status'=>200, 'result'=>LinkSpider::get()->getSpiderLink($field)], JSON_UNESCAPED_UNICODE);
        return false;
    }
}