<?php
// 全局设置
class MarketAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
	
    public function index()
    {
		$field= 'id,title,art_miaoshu,art_fengge,type_id,art_jiage,art_img,art_jifen,art_set,art_writer,art_time';
		$this->_list(D('market'),$field,'','art_time','DESC');
		
        $this->display();
    }
	
	
	public function getlog(){
		//分页处理
		import("ORG.Util.Page");

		if(isset($_REQUEST['user_name'])&&!empty($_REQUEST['user_name'])){
			$map['m.user_name'] = array('like',"%".$_REQUEST['user_name']."%");
			$this->assign('user_name',$_REQUEST['user_name']);
		}

		$count = M('member_creditslog b')->join("{$this->pre}members m ON m.id=b.uid")->where($map)->count('b.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		$field= 'b.id,b.uid,b.type,b.affect_credits,b.account_credits,b.info,b.add_time,b.add_ip,m.id mid,m.user_name';
		$list = M('member_creditslog b')->field($field)->join("{$this->pre}members m ON m.id=b.uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();
		$list = $this->_listFilter($list);
		
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
		$this->assign("xaction",ACTION_NAME);
        $this->assign("query", http_build_query($search));
		
        $this->display();
	}
	
	
	public function order()
	{
	
		//分页处理
		import("ORG.Util.Page");
		$count = M('order b')->join("{$this->pre}members m ON m.id=b.uid")->join("{$this->pre}market x ON x.id=b.gid")->where($map)->count('b.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		$field= 'b.id,b.uid,b.gid,b.jifen,b.jine,b.num,b.add_time,b.add_ip,m.id mid,m.user_name,x.title';
		$list = M('order b')->field($field)->join("{$this->pre}members m ON m.id=b.uid")->join("{$this->pre}market x ON x.id=b.gid")->where($map)->limit($Lsql)->order("b.id DESC")->select();
		$list = $this->_listFilter($list);
		
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
		$this->assign("xaction",ACTION_NAME);
        $this->assign("query", http_build_query($search));
		
        $this->display();
	}
	
	
    public function _addFilter()
    {
		$typelist = get_type_leve_list('513','acategory');//分级栏目
		//var_dump($typelist);die;
		$this->assign('type_list',$typelist);
		$vo["art_time"]=time();
		$this->assign('vo',$vo);
    }
	
	public function _doAddFilter($m){
		if(!empty($_FILES['imgfile']['name'])){
			$this->saveRule = date("YmdHis",time()).rand(0,1000);
			$this->savePathNew = '/Public/upload/';
			$this->thumbMaxWidth = C('PRODUCT_UPLOAD_W');
			$this->thumbMaxHeight = C('PRODUCT_UPLOAD_H');
			$info = $this->CUpload();
			$data['art_img'] = $info[0]['savepath'].$info[0]['savename'];
		}
		if($data['art_img']) $m->art_img=$data['art_img'];
		$m->art_time=strtotime($_REQUEST["art_time"]);
		$m->type_id=$_REQUEST["type_id"];
		// $m->art_writer = session("admin_user_name");
		if($_POST['is_remote']==1) $m->art_content = get_remote_img($m->art_content);
		return $m;
	}
	public function _doEditFilter($m){
		
		if(!empty($_FILES['imgfile']['name'])){
			$this->saveRule = date("YmdHis",time()).rand(0,1000);
			$this->savePathNew = '/Public/upload/';
			$this->thumbMaxWidth = C('PRODUCT_UPLOAD_W');
			$this->thumbMaxHeight = C('PRODUCT_UPLOAD_H');
			$info = $this->CUpload();
			$data['art_img'] = $info[0]['savepath'].$info[0]['savename'];
		}
		$m->type_id=$_REQUEST["type_id"];
		$m->art_time=strtotime($_REQUEST["art_time"]);
		if($data['art_img']) $m->art_img=$data['art_img'];
		if($_POST['is_remote']==1) $m->art_content = get_remote_img($m->art_content);
		return $m;
	}
	public function _editFilter($id){
		$typelist = get_type_leve_list('513','acategory');//分级栏目
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
