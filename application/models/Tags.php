<?php

class TagsModel extends BaseModel {
    public static function getTagID($tag_name='') {
        return self::db()->where("tag_name = '{$tag_name}'")->select();
    }

    public static function getTag($search='',$values=array()) {
        $value =  implode(',', $values);
        return self::db()->where("$search in ( $value )")->select() ;
    }

    public static function getTagName($tag_id='') {

    }

    public static function getTags($args=array(), $page=0, $size=0) {
        return self::db()->field('tag.tag_id,tag.tag_name')->alice('tag')
            ->join('tag_article', 'ta', 'ta.tag_id = tag.tag_id', 'INNER')
            ->order('tag.create_time desc')->limit(0, 10)->select();
    }

    public static function addTag($tag_name) {
        return self::db()->insert(['tag_name'=>$tag_name]);
    }

    public static function getlikeTags($tag = '', $model = 'r') {
        $where = '';
        switch ($model) {
            case 'r' :
                $where = " tag_name like '{$tag}%' ";
                break ;
            case 'l' :
                $where = " tag_name like '%{$tag}' ";
                break;
            case 'c' :
                $where = " tag_name like '%{$tag}%' ";
                break;
        }
        if ($tags = self::db()->where($where)->field('tag_name')->select()) {
            foreach ($tags as &$item) {
                $item = $item['tag_name'];
            }
        }
        return $tags;
    }

    public static function getTagArticleList($args=array(), $page=0, $size=0) {
        $where = " article.audit_state = 3 ";
        $where .= isset($args['article_id']) ? " and article.id != '{$args['article_id']}' " : null;
        $where .= isset($args['tag_ids']) ? " and  tag.tag_id in ({$args['tag_ids']}) " : null;
        if (isset($args['tag_names'])) {
            $names = explode(',', $args['tag_names']);
            foreach ($names as &$name) {
                $name = "'$name'";
            }
            $where .= " and tag.tag_name in (".join(',', $names).") ";
        }
        return self::db()->alice('tag')
            ->field('article.title, article.id')
            ->join('tag_article', 'ta', 'tag.tag_id = ta.tag_id', 'LEFT')
            ->join('articles', 'article', 'ta.article_id = article.id', 'LEFT')
            ->where($where)
            ->order('article.click_amount desc')
            ->group('article.title')
            ->limit(($page-1)*$size, $page*$size)
            ->select();
    }

    public static function getTopTags() {
        if ($tags = self::gethset('hot_tags')) {
            return $tags;
        } else {
            return self::sethset('hot_tags', self::db()->field('tag.tag_id,tag.tag_name')->alice('tag')
                ->join('tag_article', 'ta', 'ta.tag_id = tag.tag_id', 'INNER')
                ->group('tag.tag_id')
                ->order('tag.create_time desc')->limit(0, 10)->select());
        }
    }
}