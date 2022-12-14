<?php
// 全局设置
 
class ShoporderAction extends MCommonAction { 
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
	
    public function index()
    {
 		
 		$strUrl = U("shoporder/index");

       	import('ORG.Util.Page');
        $map = null;
        //分页处理
        $field= 'id,gid,jifen,uid,num,action,real_name,address,cell_phone,jine,add_time,ordernums,carid,yijian,pay_way';
        $page=intval($_REQUEST["page"]);
        $size=intval($_REQUEST["size"]);

        $limit=$page*$size.','.$size;
        $order="id DESC";

        $map['uid']=$this->uid;

        $map['action'] = array('in', '1,2,3,0');

        if(isset($_GET['action'])){
             $map['action']= $_GET['action'];
             $action = $_GET['action'];
        }
        $map['gid']=array('exp','is null');
        $this->assign('action',$action);
        $this->assign('strUrl',$strUrl);

        $count = M("order")->field($field)->where($map)->count();
        $num = 6;
        $page = new Page($count,$num);
        foreach($getval as $key=>$val) {
            $page->parameter .= "$key=".urlencode($val).'&';
        }
        $order='id desc';
        $show = $page->show();
        $limit = $page->firstRow.','.$page->listRows;
        //$list=M("order o")->field($field)->where($map)->join("lzh_car c ON c.id in (o.carid)")->join("lzh_market m ON m.id =c.gid")->order($order)->limit($limit)->select();
        $list=M("order")->field($field)->where($map)->order($order)->limit($limit)->select();
        foreach ($list as $k => $v) {
            if($v["carid"]!=null){
                $goods=M("car c")->field('c.num,m.id,title,art_info,art_time,art_img,art_jiage,type_id')->where("c.id in (".$v["carid"].")")->join("lzh_market m ON m.id=c.gid")->select();
            }else{
                $goods=M("market")->field('id,title,art_info,art_time,art_img,art_jiage,type_id')->where("id=".$v["gid"])->select();
                $goods[0]["num"]=$info["num"];
            }
            $goods_num=0;
            foreach ($goods as $ke => $ve) {
                $goods_num+=$ve["num"];
                $goods[$ke]["goods_jiage"]=$ve["num"]*$ve["art_jiage"];
            }
            $list[$k]["goods"]=$goods;
            $list[$k]["jianshu"]=$goods_num;
        }
       
        $this->assign('page',$show);
        $this->assign('list',$list);


		$this->display();
		
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
	public function quxiao(){
		$id = $_REQUEST['id'];

		$Model = M();
		$Model->startTrans();

		$oinfo= M("order")->where("ordernums='{$id}'")->find();
		if($oinfo["action"]!='3'){
			$this->error("订单状态错误");
		}else{
			$res=memberCreditsLog($this->uid, 8, intval($oinfo['jifen']), $info = "取消商品购买积分退回");

			$glist=M("car")->where("id in ('".$oinfo['carid']."')")->select();
			$mk=true;

			foreach ($glist as $k => $v) {
				$mk=M('market')->where("id=".$v["gid"])->setInc('art_writer',$v['num']);
			}

			$data["action"]="4";
			$newxid= M("order")->where("ordernums='{$id}'")->save($data);
			if ($res && $mk && $newxid){
				$Model->commit();

		   		$this->success("处理成功");

			}else{
				$Model->rollback();
				$this->error("处理失败");
			}
		}
		
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
