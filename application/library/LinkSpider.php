<?php

use QL\QueryList;

class LinkSpider {
    private $range="#js-longvideo .js-longitem:eq(0)";
    private $rules=[];
    private $page="https://so.360kan.com/index.php?kw=";

    private static $obj=null;

    public static function get() {
        if (is_null(self::$obj)) {
            Yaf_Loader::import('QL/autoload.php');
            self::$obj = new LinkSpider();
        }
        return self::$obj;
    }

    public function getSpiderLink($spider="") {
        $this->page = $this->page.urlencode($spider);
        $rules = [
            "profile" => ['.b-mainpic img', 'src'],
            "title" => ['.cont .title b', 'text'],
            "sum" => ['.cont ul:eq(0) span', 'text'],
            "category" => ['.cont .title .playtype', 'text'],
            "subject" => ['.cont .b-description span', 'data-full']
        ];
        $result = QueryList::get($this->page)->rules($rules)->range($this->range)->query()->getData(function ($data){
            switch ($data['category']) {
                case "[电影]":
                    $data = $this->getMove($data);
                    break;
                case "[动漫]":
                    $data = $this->getComic($data);
                    break;
                case "[电视剧]":
                    $data = $this->getTVPlay($data);
                    break;
            }
            return $data;
        })->all()[0];
        return $result;
    }

    private function getMove($item) {
        $range = "#js-longvideo .js-longitem:eq(0)";
        $rules = [
            'data' => ['.cont .btn.btn-play', 'href']
        ];
        $item['src'] = QueryList::get($this->page)->rules($rules)->range($range)->query()->getData()->all();
        return $item;
    }

    private function getComic($item) {
        preg_match('/[1-9]{1,50}/', $item['sum'], $sum);
        $sum = (int)$sum[0];
        if ($sum) {
            if ($sum > 21) { //如果分页
                $range = '#js-longvideo .js-longitem:eq(0)';
                $rules = [
                    "data" => ['.b-series.js-series textarea', 'html'],
                ];
                $item['src'] = QueryList::get($this->page)->rules($rules)->range($range)->query()->getData(function ($item){
                    //$result = [];
                    preg_match_all('/href=\"([\s\S]*?)\"[\s\S]*?>/', $item['data'], $result);
                    return $result[1];
                })->all()[0];
            } else { //如果部分也
                $range = '.b-series-number-container:eq(0) a';
                $rules = [
                    "data" => ['', 'href'],
                ];
                $item['src'] = QueryList::get($this->page)->rules($rules)->range($range)->query()->getData()->all()[0];
            }
        }
        return $item;
    }

    private function getTVPlay($item) {
        preg_match('/[1-9]{1,50}/', $item['sum'], $sum);
        $sum = (int)$sum[0];
        if ($sum) {
            if ($sum > 21) { //如果分页
                $range = '#js-longvideo .js-longitem:eq(0)';
                $rules = [
                    "data" => ['.b-series.js-series textarea', 'html'],
                ];
                $item['src'] = QueryList::get($this->page)->rules($rules)->range($range)->query()->getData(function ($item){
                    //$result = [];
                    preg_match_all('/href=\"([\s\S]*?)\"[\s\S]*?>/', $item['data'], $result);
                    return $result[1];
                })->all()[0];
            } else { //如果部分也
                $range = '.b-series-number-container:eq(0) a';
                $rules = [
                    "data" => ['', 'href'],
                ];
                $item['src'] = QueryList::get($this->page)->rules($rules)->range($range)->query()->getData(function ($data){
                    return $data['data'];
                })->all();
            }
        }
        return $item;
    }

    private function getVarietyShow() {

    }

    private function getPages() {

    }
}