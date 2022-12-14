<?php
// 全局设置
class OrderAction extends ACommonAction
{

    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
	
    public function index()
    {
		$field= 'id,title,art_miaoshu,art_fengge,art_jiage,art_img,art_jifen,art_set,art_writer,art_time,carid';
		$this->_list(D('market'),$field,'','id','DESC');
        $this->display();
    }
	
	public function edit(){

		$id = intval($_GET['id']);
		if($id==0){
			$this->assign('jumpUrl', U('Order/order'));
			$this->error('参数错误');
		}
		$vo=M("order")->find($id);
		if($vo["yijian"]==""){
			$vo["yijian"]="快递名称:快递单号";	
		}
//		if($vo["carid"]){
//			$title=M("car c")->field('c.num,m.id,title,art_info,art_time,art_img,art_jiage,type_id')->where("c.id in (".$vo["carid"].")")->join("lzh_market m ON m.id=c.gid")->select();
//		}else{
//			 $title=M("market")->field('id,title,art_info,art_time,art_img,art_jiage,type_id')->where("id=".$vo["gid"])->select();
//             $title[0]["num"]=$vo["num"];
//		}

        if($vo['type']==1){
            if($vo["carid"]!=null){
                $title=M("car c")->field('c.num,m.id,title,art_info,art_time,art_img,art_jiage,type_id')->where("c.id in (".$vo["carid"].")")->join("lzh_market m ON m.id=c.gid")->select();
            }else{
                $title=M("market")->field('id,title,art_info,art_time,art_img,art_jiage,type_id')->where("id=".$vo["gid"])->select();
                $title[0]["num"]=$vo["num"];
            }
        }
        if($vo['type']==2){
            $title=M("borrow_info")->field('id,borrow_name as title,borrow_img as art_img,shoujia as art_jiage')->where("id=".$vo["gid"])->select();
            $title[0]["num"]=$vo['num'];
        }
        if ($vo["type"] == '3') {
            $zmap['z.id']=$vo['gid'];
            $zmap['zp.id']=$vo['zoid'];
            $title = M("zeng_pin z")->join('lzh_zporder zp on z.id=zp.zpid')->field('z.id,z.title,z.images as art_img,zp.zmprice as art_jiage')->where($zmap)->select();
            $title[0]["num"]=$vo['num'];
        }

		$vo["title"]=$title;

        $this->assign("zhuangtai",C("jforders"));
        $this->assign("vo", $vo);
		$this->display();
		
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

        if(isset($_REQUEST['ordernums'])&&!empty($_REQUEST['ordernums'])){
            $map['ordernums'] = array('like',"%".$_REQUEST['ordernums']."%");
            $this->assign('ordernums',$_REQUEST['ordernums']);
        }

        if(isset($_REQUEST['status'])&&!empty($_REQUEST['status'])){
            $ac= $_REQUEST['status'];
            if($ac==1){
                $ab=3;
            }
            if($ac==2){
                $ab=0;
            }
            if($ac==3){
                $ab=9;
            }
            $map['action'] =$ab;
            $this->assign('status',$_REQUEST['status']);
        }


		$count =M("order")->where($map)->order($order)->count();
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$field= 'b.zoid,b.id,gid,jifen,uid,num,action,real_name,address,jine,add_time,ordernums,carid,m.user_name,b.type,b.cell_phone';
       
		$order="add_time desc";
        $list=M("order b")->field($field)->where($map)->join("{$this->pre}members m ON m.id=b.uid")->order($order)->limit($Lsql)->select();
   
        foreach ($list as $k => $v) {
            $goods_num=0;
            if($v['type']==1){
                if($v["carid"]!=null){
                    $goods=M("car c")->field('c.num,m.id,title,art_info,art_time,art_img,art_jiage,type_id')->where("c.id in (".$v["carid"].")")->join("lzh_market m ON m.id=c.gid")->select();
                    }else{
                    $goods=M("market")->field('id,title,art_info,art_time,art_img,art_jiage,type_id')->where("id=".$v["gid"])->select();
                    $goods[0]["num"]=$v["num"];

                }
                foreach ($goods as $ke => $ve) {
                    $goods_num+=$ve["num"];
                }
            }
            if($v['type']==2){
                $goods=M("borrow_info")->field('id,borrow_name as title,borrow_img as art_img,shoujia as art_jiage')->where("id=".$v["gid"])->select();
                $goods[0]["num"]=$v['num'];
                $goods_num=$v['num'];

            }

            if ($v["type"] == '3') {
                $zmap['z.id']=$v['gid'];
                $zmap['zp.id']=$v['zoid'];
                $goods = M("zporder zp")->join('lzh_members m on m.id=zp.uid')->join('lzh_zeng_pin z on z.id=zp.zpid')->field('zp.add_time,m.user_name as uname,m.id as mid,zp.id as zpid,zp.mzpid,z.id,z.title,z.images as art_img,zp.zmprice as art_jiage')->where($zmap)->select();
                $goods_num=$v['num'];
                $list[$k]["zm_time"]=$goods[0]['add_time'];
            }

            $list[$k]["goods"]=$goods;
            $list[$k]["maijia"]=$maijia;
            $list[$k]["jianshu"]=$goods_num;
            $list[$k]["zhuangtai"]=C("jforders")[$v['action']];
        }
		$list = $this->_listFilter($list);
		
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
		$this->assign("xaction",ACTION_NAME);
        $this->assign("query", http_build_query($search));
		
        $this->display();
	}

	public function thorder()
	{
		//分页处理
		import("ORG.Util.Page");

        if(isset($_REQUEST['ordernums'])&&!empty($_REQUEST['ordernums'])){
            $map['b.ordernums'] = array('like',"%".$_REQUEST['ordernums']."%");
            $this->assign('ordernums',$_REQUEST['ordernums']);
        }

		$map['b.action']=array("in",array(4,5,6,7,8));

		$count =M("order b")->where($map)->order($order)->count();
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$field= 'b.type,b.id,gid,jifen,uid,num,action,real_name,address,jine,add_time,ordernums,carid,m.user_name,b.zoid';
       
		$order="add_time desc";
        //$list=M("order b")->field($field)->where($map)->join("{$this->pre}members m ON m.id=b.uid")->join("{$this->pre}order_refund f ON f.ordernums=b.ordernums")->order($order)->limit($Lsql)->select();
        $list=M("order b")->field($field)->where($map)->join("{$this->pre}members m ON m.id=b.uid")->order($order)->limit($Lsql)->select();
       //var_dump(M("order b")->getlastsql());
   
        foreach ($list as $k => $v) {
            $goods_num=0;
            if($v['type']==1){
                if($v["carid"]!=null){
                    $goods=M("car c")->field('c.num,m.id,title,art_info,art_time,art_img,art_jiage,type_id')->where("c.id in (".$v["carid"].")")->join("lzh_market m ON m.id=c.gid")->select();
                }else{
                    $goods=M("market")->field('id,title,art_info,art_time,art_img,art_jiage,type_id')->where("id=".$v["gid"])->select();
                    $goods[0]["num"]=$v["num"];

                }
                foreach ($goods as $ke => $ve) {
                    $goods_num+=$ve["num"];
                }
            }
            if($v['type']==2){
                $goods=M("borrow_info")->field('id,borrow_name as title,borrow_img as art_img,shoujia as art_jiage')->where("id=".$v["gid"])->select();
                $goods[0]["num"]=$v['num'];
                $goods_num=$v['num'];

            }

            if ($v["type"] == '3') {
                $zmap['z.id']=$v['gid'];
                $zmap['zp.id']=$v['zoid'];
                $goods = M("zeng_pin z")->join('lzh_zporder zp on z.id=zp.zpid')->field('z.id,z.title,z.images as art_img,zp.zmprice as art_jiage')->where($zmap)->select();
//                foreach ($goods as $gk => $gv) {
//                    $goods[$gk]['art_img']= unserialize($gv['art_img'])[0]['img']?unserialize($gv['art_img'])[0]['img']:'';
//                    $goods[$gk]['art_time']=$v['add_time'];
//                    $goods[$gk]['num']=$v['num'];
//                    $goods[$gk]['art_jiage']=bcdiv($v['jine'],$v['num'],2);
//                }
                $goods_num = $v['num'];
             //   var_dump( M("zeng_pin z")->getLastSql());
            }

            $list[$k]["goods"]=$goods;
            $list[$k]["jianshu"]=$goods_num;
            $list[$k]["zhuangtai"]=C("jforders")[$v['action']];
            $list[$k]["refund"]=M("order_refund")->where("ordernums='".$v['ordernums']."'")->find();
        }

		$list = $this->_listFilter($list);
		
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
		$this->assign("xaction",ACTION_NAME);
        $this->assign("query", http_build_query($search));
		
        $this->display();
	}


	public function thedit(){

		$id = intval($_GET['id']);
		if($id==0){
			$this->assign('jumpUrl', U('Order/order'));
			$this->error('参数错误');
		}
		$vo=M("order")->find($id);
		if($vo["yijian"]==""){
			$vo["yijian"]="快递名称:快递单号";	
		}

        $goods_num=0;
        if($vo['type']==1){
            if($vo["carid"]!=null){
                $title=M("car c")->field('c.num,m.id,title,art_info,art_time,art_img,art_jiage,type_id')->where("c.id in (".$vo["carid"].")")->join("lzh_market m ON m.id=c.gid")->select();
            }else{
                $title=M("market")->field('id,title,art_info,art_time,art_img,art_jiage,type_id')->where("id=".$vo["gid"])->select();
                $title[0]["num"]=$v["num"];

            }
        }
        if($vo['type']==2){
            $title=M("borrow_info")->field('id,borrow_name as title,borrow_img as art_img,shoujia as art_jiage')->where("id=".$vo["gid"])->select();
            $title[0]["num"]=$vo['num'];
        }
        $vo["refund"]=M("order_refund")->where("ordernums='".$vo['ordernums']."'")->find();

        $vo["refund"]['images']=explode(',',$vo["refund"]['images']);
        $vo["fslist"]=c('shouhoufs');
//        var_dump($vo["refund"]['images']);
        $vo["goods"]=$title;

        $this->assign("action", $vo["action"]);
		$this->assign("vo", $vo);
        $this->assign("shxx",  $vo["refund"]);
        $this->assign("info", $vo);
        $this->display();
	}


	
	public function _listFilter($list){
	 	$listType = D('Acategory')->getField('id,type_name'); 
		$row=array();
		foreach($list as $key=>$v){ 
			$v['type_name'] = $listType[$v['type_id']];
			$row[$key]=$v;
		}
//		var_dump($list);exit;
		return $row;
	}
	
	public function orerupdate(){
  		$id = intval($_POST['id']);
		$data['yijian'] =  $_POST['yijian'];
   	    $data['action'] =  $_POST['action'];
        if(empty($_POST['fh_time'])&&$data['action']=='1') {
            $data['fh_time'] = time();
        }
   	    $newxid= M("order")->where("id={$id}")->save($data);
   		if($newxid==1){
            if(empty($_POST['fh_time'])&&$data['action']=='1'){
                $data['fh_time']=time();
                $dt['name']=$_POST['sp_name'];
                $dt['phone']=$_POST['sp_phone'];
                $dt['danhao']=explode(':',$data['yijian'])[1];
                notice1("13", $_POST['uid'],$dt);
            }
            $this->success("处理成功");
        }else{
            $this->error("处理失败");
        }
	}
	public function thorerupdate(){
  		$id = intval($_POST['id']);
   	    $gdata['action'] =  $_POST['action'];
        $order=M("order")->where("id={$id}")->find();

        $refund=M("order_refund")->where("ordernums='".$order['ordernums']."'")->find();
        if($gdata['action']==6){
            $redata["status"]=2;
            $redata["ty_time"]=time();
        }
        if($gdata['action']==8){
            $gdata["wc_time"]=time();
            $redata["status"]=5;
            $redata["bhyuanyin"]=$_POST["bhyuanyin"];
        }
        if($gdata['action']==4){
            $gdata["wc_time"]=time();
            $redata["status"]=4;
            $redata["tk_time"]=time();
            if($order['type']=="1"){
                $glist=M("car")->where("id in ('".$order['carid']."')")->select();
                $mk=true;
                foreach ($glist as $k => $v) {
                    $mk=M('market')->where("id=".$v["gid"])->setInc('art_writer',$v['num']);
                }
            }
            if($order['type']=="2"){
                if(!empty($order["investor_id"])){
                    $num=$order["num"];
                    $mk=M("borrow_investor")->where("id=" . $order["investor_id"])->setDec('xsfenshu', $num);
                }else{
                    $mk=M('borrow_info')->where("id=".$order["gid"])->setInc('art_writer',$order['num']);
                }
            }
        }

        if($gdata['action']==10){
            if($_POST['fangshi']!=3){
                M()->rollback();
                $this->error("处理方式错误");
            }
            $gdata["wc_time"]=time();
            $redata["status"]=6;
        }

        $newxid= M("order")->where("id={$id}")->save($gdata);
        $res=M("order_refund")->where("ordernums='".$order['ordernums']."'")->save($redata);
        if($gdata['action']==4&&$order["jine"]!=0){
            $rdata=tuikuanapi($order["pay_way"],$order['ordernums'],$refund["money"]);
            if($rdata['status']==0){
                M()->rollback();
                $this->error($rdata['msg']);
            }
        }
        if($newxid&&$res) {
            M()->commit();
            if(!empty($orderinfo["jifen"])){
                membercreditslog($orderinfo['uid'],20,$orderinfo["jifen"], "退货退款积分退回！");
            }
            $this->success("处理成功");
        }else{
            M()->rollback();
            $this->error("处理失败");
        }
	}

}
?>