<?php

class ArticlesModel extends BaseModel {


    public static function addArticle($arr=array()) {
        Helper::import('image');
        $upload_res = qiniu_image_upload('file');
        $arr['crt_uid'] = 1;
        $article_id = self::db()->insert([
            'title' => $arr['title'],
            'source' => $arr['source'],
            'auther' => $arr['auther'],
            'content' => $arr['content'],
            'introduction' => $arr['introduction'],
            'keyword' => $arr['keyword'],
            'click_amount' => $arr['click_amount'],
            'type' => $arr['type'],
            'top' => $arr['top'],
            'audit_state' => $arr['audit_state'],
            'crt_uid' => $arr['crt_uid'],
            'state' => 1,
            'thumb_path' => $upload_res ? $upload_res : null,
        ]);
        if (isset($arr['tags'])) {
            $tags = explode(',',$arr['tags']);
            foreach ($tags as $index => $tag) {
                if (!$tag) {continue;}
                $tag = trim($tag);
                if ($new_tag = TagsModel::getTagID($tag)[0]['tag_id']) {
                    BaseModel::db('tag_article')->insert(array('tag_id'=>$new_tag, 'article_id'=>$article_id));
                } else {
                    if ($new_tag = TagsModel::addTag($tag)) {
                        BaseModel::db('tag_article')->insert(array('tag_id'=>$new_tag, 'article_id'=>$article_id));
                    }
                }
            }
            self::clearhset();
        }
        return $article_id ? $article_id : false;
    }

    public static function getArticle($article_id) {
        $article = self::db()->where("id = $article_id")->find();
        $article ? self::updateClickAmount($article_id) : null;
        $tags_id = BaseModel::db('tag_article')->where("article_id = $article_id")->field('tag_id')->select();
        $tags = array();
        $article['tags_id'] = array();
        if ($tags_id) {
            foreach ($tags_id as $tag_id) {
                $tag_id = $tag_id['tag_id'];
                $tag = BaseModel::db('tags')->field('tag_id,tag_name')->where("tag_id = {$tag_id}")->find();
                if ($tag) {
                    array_push($tags, $tag);
                    array_push($article['tags_id'], $tag_id);
                }
            }
            $article['create_time'] = substr($article['create_time'],0,10) ;
            $article['tags'] = $tags;
            $article['tags_id'] = implode(DELIMIT, $article['tags_id']);
        }
        return $article;
    }

    public static function updateClickAmount($aid='') {
        self::db()->query("UPDATE `aiqing110_articles` SET `real_click` = real_click+1 , click_amount = click_amount+1 WHERE id = '{$aid}'");
    }

    public static function updateArticle($update_msg =array()) {
        Helper::import('image');
        $upload_res = qiniu_image_upload('file');
        $articl = self::db()->where("id='{$update_msg['article_id']}'")->find();
        $update_inf = [
            'title' => $update_msg['title'],
            'source' => $update_msg['source'],
            'auther' => $update_msg['auther'],
            'content' => $update_msg['content'],
            'introduction' => $update_msg['introduction'],
            'keyword' => $update_msg['keyword'],
            'set_click' => $update_msg['click_amount'],
            'click_amount' => "{}+{}",
            'top' => $update_msg['top'],
            'type' => $update_msg['type'],
            'audit_state' => $update_msg['audit_state'],
            //'crt_uid' => $update_msg['crt_uid'],
            'state' => 1,
        ];
        $upload_res ? $update_inf['thumb_path'] = $upload_res : null;
        $update = self::db()->where("id='{$update_msg['article_id']}'")->update($update_inf);
        if (isset($update_msg['tags'])) {
            $tags = explode(',',$update_msg['tags']);
            $delete_tags = BaseModel::db('tag_article')->where("article_id='{$update_msg['article_id']}'")->delete();
            if ($delete_tags) {
                foreach ($tags as $index => $tag) {
                    if (!$tag) {continue;}
                    $tag = trim($tag);
                    if ($new_tag = TagsModel::getTagID($tag)[0]['tag_id']) {
                        BaseModel::db('tag_article')->insert(array('tag_id'=>$new_tag, 'article_id'=>$update_msg['article_id']));
                    } else {
                        if ($new_tag = TagsModel::addTag($tag)) {
                            BaseModel::db('tag_article')->insert(array('tag_id'=>$new_tag, 'article_id'=>$update_msg['article_id']));
                        }
                    }
                }
            }
        }
        self::clearhset();
    }

    public static function getList($args = array(), $page=0, $size=0, $tag = '') {
        if ($tag && $lists = self::gethset($tag)) {
            return $lists;
        }
        $field = isset($args['field']) ? $args['field'] : 'title,article.id,thumb_path,sum(click_amount+real_click) as click_amount,substr(article.create_time,1,10) as create_time,article.introduction';
        $where = isset($args['audit_state']) ? $args['audit_state'] : " article.audit_state = 3 ";
        $where .= isset($args['where']) ? $args['where'] : null;
        $order = isset($args['order']) ? $args['order'] : 'article.create_time desc';
        /*if (isset($args['is_page']) && $args['is_page']) {
            return $total = self::db()->field('article.id')
                ->alice('article')
                ->where($where)
                ->join('tag_article', 'ta', 'ta.article_id = article.id', 'INNER')
                ->join('tags', 'tag', 'tag.tag_id = ta.tag_id', 'INNER' )
                ->join('types', 'type', 'type.type_id = article.type', 'INNER')
                ->group('article.id')
                ->total()->select();
        }*/
        /*echo self::db()->field($field)
            ->alice('article')
            ->where($where)
            ->join('tag_article', 'ta', 'ta.article_id = article.id', 'INNER')
            ->join('tags', 'tag', 'tag.tag_id = ta.tag_id', 'INNER' )
            ->join('types', 'type', 'type.type_id = article.type', 'INNER')
            ->limit(($page-1)*$size, $page*$size)
            ->all(isset($args['all']) ? $args['all'] : false)
            ->group('article.id')
            ->order($order)->fetchSql(true)
            ->total(isset($args['is_page']) ? $args['is_page'] : false)
            ->select();*/
        return self::sethset($tag, self::db()->field($field)
            ->alice('article')
            ->where($where)
            ->join('tag_article', 'ta', 'ta.article_id = article.id', 'INNER')
            ->join('tags', 'tag', 'tag.tag_id = ta.tag_id', 'INNER' )
            ->join('types', 'type', 'type.type_id = article.type', 'INNER')
            ->limit(($page-1)*$size, $page*$size)
            ->all(isset($args['all']) ? $args['all'] : false)
            ->group('article.id')
            ->order($order)//->fetchSql(true)
            ->total(isset($args['is_page']) ? $args['is_page'] : false)
            ->select());
    }

    public static function getHotsArticles($page=0, $size=0) {
        return array_slice(ArticlesModel::getList(array('order'=>'sum(click_amount+real_click) desc', 'field'=>'article.id,article.title,article.thumb_path,article.introduction,click_amount,substr(article.create_time,1,10) create_time'), 1, 10, 'hot_articles'), 0, $size);
    }

    public static function getTopArticles() {
        if ($top = self::gethset('top_banner')) {
            return $top;
        }
        return self::sethset('top_banner', self::getList(array('where'=> ' and article.top=1 '), 1, 3));
    }


    /*public static function getJingxuanList($type_id, $page, $size, $is_page = false) {
        $filed['where'] = "and article.type = '{$type_id}'";
        $filed['is_page'] = $is_page;
       return self::getList( $filed, $page, $size);
    }*/

    public static function getJingxuanList($limit=3) {
        if ($ret = self::gethset('jingxuan')) {
            return $ret;
        }
        $result = array();
        $articles = self::db()->alice('article')
            ->field('article.id,article.title,article.click_amount,article.create_time,article.thumb_path,article.introduction,type.type_id,type.type_name')
            ->join('articles', 'article1', 'article.type = article1.type and article.id < article1.id', 'LEFT')
            ->join('types', 'type', 'article.type = type.type_id', "INNER")
            ->group('article.id having count(article1.id) < '.$limit)
            ->order('type.type_id asc')->select();
        foreach ($articles as $index=>$article) {
            if (!$result){
                $result[$article['type_id']] = array();
                array_push($result[$article['type_id']],$article);
            } elseif (array_keys($result)[sizeof($result)-1] == $article['type_id']) {
                array_push($result[$article['type_id']],$article);
            } else {
                $result[$article['type_id']] = array();
                array_push($result[$article['type_id']],$article);
            }
        }
        return self::sethset('jingxuan', $result);
    }

}