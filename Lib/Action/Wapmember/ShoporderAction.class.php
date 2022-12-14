<?php
// 全局设置 
class ShoporderAction extends MCommonAction {
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
	public function index1(){			var_dump("1");die;		}
    public function index(){
     		import("ORG.Util.Page");
$map['uid']=$this->uid;
		$count = M('order b')->join("{$this->pre}members m ON m.id=b.uid")->join("{$this->pre}market x ON x.id=b.gid")->where($map)->count('b.id');

		$p = new Page($count, C('ADMIN_PAGE_SIZE'));

		$page = $p->show();

		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		$field= 'b.uid,b.gid,b.jifen,b.jine,b.num,b.add_time,b.add_ip,b.action,m.id mid,m.user_name,x.*,x.id ma_id,b.id id';
		$list = M('order b')->field($field)->join("lzh_members m ON m.id=b.uid")->join("lzh_market x ON x.id=b.gid")->where($map)->limit($Lsql)->order("b.id DESC")->select();
//	
//var_dump($list)	;
// var_dump(M('order b')->getlastsql());die;
		//$list = $this->listFilter($list);
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
		$this->assign("xaction",ACTION_NAME);
        $this->assign("query", http_build_query($search));
		//var_dump($expression)
		$this->display();
 
    }
	
		
 public function edit() {
    	
		import("ORG.Util.Page");
$map['b.uid']=$this->uid;
$map['b.id']=$_GET['id'];	 



		$field= 'm.id mid,m.user_name,x.*,b.*';

		$list = M('order b')->field($field)->join("lzh_members m ON m.id=b.uid")->join("lzh_market x ON x.id=b.gid")->where($map) ->find();
//		
 // var_dump(M('order b')->getlastsql());
  //die;
		//$list = $this->listFilter($list);


        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));

        $this->assign("list", $list);

        $this->assign("pagebar", $page);

        $this->assign("search", $search);

		$this->assign("xaction",ACTION_NAME);

        $this->assign("query", http_build_query($search));

		
//var_dump($list) ;die;
	 $this->display();
		
//		$field= 'id,title,art_miaoshu,art_fengge,type_id,art_jiage,art_img,art_jifen,art_set,art_writer,art_time';
//		$this->_list(D('market'),$field,'','id','DESC');
//		
//      $this->display();
    }
	public function listFilter($list){

	 	$listType = D('Acategory')->getField('id,type_name');
// var_dump(D('Acategory')->getlastsql());die;

		$row=array();

		foreach($list as $key=>$v){
//var_dump($v['type_name']);die;
//			var_dump($listType[$v['type_id']]);die;
			$v['type_name'] = $listType[$v['type_id']];

			$row[$key]=$v;

		}
//var_dump($row);die;
		return $row;

	}
	
	public function orerupdate(){

  		$id = intval($_POST['id']);

		$data['yijian'] =  $_POST['yijian'];

   	    $data['action'] =  $_POST['action'];

   	    $newxid= M("order")->where("id={$id}")->save($data);

   		if($newxid==1) $this->success("处理成功");

		else $this->error("处理失败");

	}
	
	public function getlog(){
		//分页处理
		import("ORG.Util.Page");
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
    }
	
	public function _doAddFilter($m){
		if(!empty($_FILES['imgfile']['name'])){
			$this->saveRule = date("YmdHis",time()).rand(0,1000);
			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Article/' ;
			$this->thumbMaxWidth = C('ARTICLE_UPLOAD_W');
			$this->thumbMaxHeight = C('ARTICLE_UPLOAD_H');
			$info = $this->CUpload();
			$data['art_img'] = $info[0]['savepath'].$info[0]['savename'];
		}
		if($data['art_img']) $m->art_img=$data['art_img'];
		$m->art_time=time();
		$m->type_id=$_REQUEST["type_id"];
		// $m->art_writer = session("admin_user_name");
		if($_POST['is_remote']==1) $m->art_content = get_remote_img($m->art_content);
		return $m;
	}
	public function _doEditFilter($m){
		
		if(!empty($_FILES['imgfile']['name'])){
			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Article/' ;
			$this->thumbMaxWidth = C('ARTICLE_UPLOAD_W');
			$this->thumbMaxHeight = C('ARTICLE_UPLOAD_H');
			$info = $this->CUpload();
			$data['art_img'] = $info[0]['savepath'].$info[0]['savename'];
		}
		$m->type_id=$_REQUEST["type_id"];
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
