<?php

class ArticleController extends AdminController {

    public function editAction() {
        $article_id = $this->getParam('aid');
        $article = ArticlesModel::getArticle($article_id);
        $article ? $this->vparam = array_merge($this->vparam, $article) : null;
        $this->vparam['types'] = TypesModel::getTypes();
        $this->vparam['action'] = 'edit';
        $this->render_view();
    }

    public function addAction() {
        $this->vparam['types'] = TypesModel::getTypes();
        $this->vparam['action'] = 'add';
        $this->render_view('edit');
    }

    public function saveAction() {
        $this->validateCsrf();
        $posts = $this->getParam('',false);
        if($posts['action'] == 'add') {
            $aticle_id = ArticlesModel::addArticle($posts);
        } else {
            ArticlesModel::updateArticle($posts);
        }
        $this->redirect('/admin_Mmggqbj1X6yPTg6w/list');
    }

    public function listAction() {
        $this->render_view();
    }

    public function tagslistAction() {
        ($tags = TagsModel::getlikeTags($this->getParam('tag'))) ? null : $tags = array();
        echo json_encode($tags, JSON_UNESCAPED_UNICODE);
    }

    public function ajaxlistAction() {
        if ($this->isPost) {
            $response = array();
            $data = $this->getParam('',false);
            $response['sEcho'] = $data['sEcho'];
            $start = $data['iDisplayStart'];
            $len = $data['iDisplayLength'];
            $sort_index = $data['iSortCol_0'];
            $sort_type = $data['sSortDir_0'];
            $sort_column = $data["mDataProp_{$sort_index}"];

            $response['iTotalRecords'] =  $response['iTotalDisplayRecords'] = ArticlesModel::db()->alice('article')
                ->field('id')
                ->join('types', 'type', 'article.type = type.type_id','LEFT')->total()->select();
            $response['aaData'] = ArticlesModel::db()->alice('article')
                ->field('thumb_path, article.top, type.type_name, audit_state, article.create_time, id, title')
                ->join('types', 'type', 'article.type = type.type_id','LEFT')
                ->limit($start,$start+$len)
                ->order(" {$sort_column} {$sort_type}")
                ->select();
            foreach ($response['aaData'] as &$adata) {
                switch ($adata['audit_state']) {
                    case 1 :
                        $adata['audit_state'] = '未审核';
                        break;
                    case 2:
                        $adata['audit_state'] = '审核中';
                        break;
                    case 3:
                        $adata['audit_state'] = '已审核';
                        break;
                }
                $adata['top'] = "{$adata['top']},{$adata['title']}";
                unset($adata['title']);
            }
            echo json_encode($response,JSON_UNESCAPED_UNICODE);
        }
        return false;
    }

}