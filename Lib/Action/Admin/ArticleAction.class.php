<?php
// 全局设置
class ArticleAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		$field= 'id,title,type_id,art_writer,art_time';
		$map = array();
		$search = array();
		if(isset($_GET['title'])) { $map['title'] = array('like','%'.$_GET['title'].'%');$search['title'] = $_GET['title']; }
		if(isset($_GET['type_id'])) { $map['type_id'] = $_GET['type_id'];$search['type_id'] = $_GET['type_id'];	}	
		$this->_list(D('Article'),$field,$map,'id','DESC');
		$this->assign('search',$search);
		$catlist = M('article_category')->select();
		$catlist = getCatTree($catlist);
		$this->assign('catlist',$catlist);
        $this->display();
    }
    public function control(){
		$list = M('article_category')->where(array("parent_id"=>0))->order('sort_order')->select();
		$this->assign('list',$list);
        $this->display();
    }
	
    public function _addFilter()
    {
		$typelist = get_type_leve_list('0','acategory');//分级栏目
		$this->assign('type_list',$typelist);
		$vo["art_time"]=time();
		$this->assign('vo',$vo);
    }
	public function _doAddFilter($m){
		if(!empty($_FILES['imgfile']['name'])){
			$this->saveRule = uniqid;
			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Article/' ;
// 			$this->thumbMaxWidth = C('ARTICLE_UPLOAD_W');
// 			$this->thumbMaxHeight = C('ARTICLE_UPLOAD_H');
			$info = $this->CUpload();
			$data['art_img'] = $info[0]['savepath'].$info[0]['savename'];
		}
        if(!empty($_FILES['imgfiles']['name'])){
            $this->saveRule = uniqid;
            $this->savePathNew = C('ADMIN_UPLOAD_DIR').'Article/' ;
// 			$this->thumbMaxWidth = C('ARTICLE_UPLOAD_W');
// 			$this->thumbMaxHeight = C('ARTICLE_UPLOAD_H');
            $info = $this->CUpload();
            $data['art_imgs'] = $info[0]['savepath'].$info[0]['savename'];
        }

		if($data['art_img']) $m->art_img=$data['art_img'];
        if($data['art_imgs']) $m->art_imgs=$data['art_imgs'];
		$m->art_time=strtotime($_REQUEST["art_time"]);
		$m->art_writer = session("admin_user_name");
		if($_POST['is_remote']==1) $m->art_content = get_remote_img($m->art_content);
		return $m;
	}
	public function _doEditFilter($m){
		if(!empty($_FILES['imgfile']['name'])){
			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Article/' ;
// 			$this->thumbMaxWidth = C('ARTICLE_UPLOAD_W');
// 			$this->thumbMaxHeight = C('ARTICLE_UPLOAD_H');
			$info = $this->CUpload();
			$data['art_img'] = $info[0]['savepath'].$info[0]['savename'];
		}
        if(!empty($_FILES['imgfiles']['name'])){
            $this->saveRule = uniqid;
            $this->savePathNew = C('ADMIN_UPLOAD_DIR').'Article/' ;
// 			$this->thumbMaxWidth = C('ARTICLE_UPLOAD_W');
// 			$this->thumbMaxHeight = C('ARTICLE_UPLOAD_H');
            $info = $this->CUpload();
            $data['art_imgs'] = $info[0]['savepath'].$info[0]['savename'];
        }
		if($data['art_img']) $m->art_img=$data['art_img'];
        if($data['art_imgs']) $m->art_imgs=$data['art_imgs'];
		$m->art_time=strtotime($_REQUEST["art_time"]);
		if($_POST['is_remote']==1) $m->art_content = get_remote_img($m->art_content);
		//var_dump($m);exit;
		return $m;
	}
	public function _editFilter($id){
		$typelist = get_type_leve_list('0','acategory');//分级栏目
		$this->assign('type_list',$typelist);
	}
	
	public function _listFilter($list){
	 	$listType = D('Acategory')->getField('id,type_name');
		$row=array();
		foreach($list as $key=>$v){
			$v['type_name'] = $listType[$v['type_id']];
			$row[$key]=$v;
		}
		return $row;
	}
	
}
?>