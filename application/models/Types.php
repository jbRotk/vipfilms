<?php

class TypesModel extends BaseModel {
    public static function getTypes() {
        return self::db()->where('state=1')->field('type_id,type_name')->select();
    }

    public static function getTypesWithArticle() {
        if ($types = self::gethset('recommand_type')) {
            return $types;
        }
        return self::sethset('recommand_type', self::db()->alice('type')
            ->field('type.type_id, type.type_name,article.id,article.title')
            ->join('articles', 'article', 'article.type = type.type_id', 'INNER')
            ->group('type_id')
            ->where('type.state=1 and article.audit_state = 3')->select());
    }

    public static function getType($type_id='', $type_name='') {
        $type_id = filterStr($type_id);
        $type_name = filterStr($type_name);
        $where = " 1=1 ";
        $type_id ? $where .= " and type_id='{$type_id}' " : null;
        $type_name ? $where .= " and type_name='{$type_name}'" : null;
        return self::db()->where($where)->find();
    }
    public static function getArticleList($args=array(), $page=0, $size=0) {
        $where = 'article.audit_state = 3';
        $where .= isset($args['type_ids']) ? " and type.type_id != '{$args['type_ids']}' " : null;
        if (isset($args['type_names'])) {
            $names = explode(',', $args['type_names']);
            foreach ($names as &$name) {
                $name = "'$name'";
            }
            $where .= " and type.type_name in (".join(',', $names).") ";
        }
        return self::db()->alice('type')->where($where)
            ->field('article.id,article.thumb_path,article.introduction,article.title,article.create_time,substr(article.create_time,3,7) as thumb_time, article.click_amount')
            ->join('articles', 'article', 'article.type = type.type_id', 'INNER')
            ->limit(($page-1)*$size, $page*$size)
            ->order('article.create_time desc')
            ->select();
    }
}