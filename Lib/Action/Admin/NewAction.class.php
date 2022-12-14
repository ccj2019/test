<?php
// 全局设置
class NewAction extends ACommonAction
{

    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
	public function guoqi(){
		$map["b.end_time"]=array("lt",time()+86400);
        $map["b.tixing"]='1';
        $map["b.status"]="1";

        $members = M("members");
        $members->startTrans();
        $gqlist = M('members m')->lock(true)->field('m.id,m.user_name,m.user_phone,b.end_time,b.money_bonus')->join('lzh_member_bonus b on m.id=b.uid')->where($map)->select();
        foreach ($gqlist as $k => $v) {
            $data["time"]=date("Y-m-d H:i:s",$v["end_time"]);
            $data["money"]=$v["money_bonus"];
            notice1("12",$v["id"],$data);
        }
        $members->commit();
	}

    public function index()
    {
		
       

        //分页处理
		import("ORG.Util.Page");
		$count = m('order_zz b')->where($map)->count('b.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		// $field= 'b.id,b.uid,b.gid,b.jifen,b.jine,b.num,b.add_time,b.add_ip,b.action,m.id mid,m.user_name,x.title';
		// $list = M('order b')->field($field)->join("{$this->pre}members m ON m.id=b.uid")->join("{$this->pre}market x ON x.id=b.gid")->where($map)->limit($Lsql)->order("b.id DESC")->select(); 
		
		$order="add_time desc";
		$map["status"]=array("neq","1");
		$list = m('order_zz o')->field('o.ordernums,o.add_time,o.id as oid,o.status,o.pay_way,o.jine,x.*,o.num,z.name,z.images,m.user_name')->where($map)->join("{$this->pre}members m ON m.id=o.uid")->join("lzh_zhongzhi_xx x ON x.id=o.gid")->join("lzh_zhongzhi z ON z.id=x.zid")->order($order)->limit($Lsql)->select();
  
        $arr=array("","未付款","已付款","已发货","已签收");
        foreach ($list as $k => $v) {  
            $list[$k]["images"]=explode(",",$v["images"])[0];
            $list[$k]["zhuangtai"]=$arr[$v["status"]];

            $list[$k]["title"]=$v["pinlei"]."<br>配送".$v["cishu"]."次/".$v["guige"]."/".$v["zhouqi"]."个月";
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
	
	public function edit(){

		$id = intval($_GET['id']);
		if($id==0){
			$this->assign('jumpUrl', U('Order/order'));
			$this->error('参数错误');
		}
		// $vo=M("order")->find($id);
		// if($vo["yijian"]==""){
		// 	$vo["yijian"]="订单号：\n快递名称：";	
		// }
		
		// $vo["title"]=M("market")->find($vo["gid"]);
		// //print_r($vo["title"]);

		
		$map["id"]=$id;
		

        $order=M('order_zz')->field('id,gid,jifen,uid,num,cell_phone,action,real_name,address,jine,add_time,pay_time,ordernums,status,yijian')->where($map)->find();

        $goods=M("zhongzhi_xx x")->field('x.*,z.images,z.name')->where("x.id=".$order["gid"])->join("lzh_zhongzhi z ON z.id=x.zid")->find();
      
        $goods["imglist"]=explode(",",$goods["images"]);

        $goods["title"]=$goods["pinlei"]."<br>配送".$goods["cishu"]."次/".$goods["guige"]."/".$goods["zhouqi"]."个月";

        if($goods["yijian"]==""){
			$goods["yijian"]="快递名称:快递单号";	
		}


        $this->assign("goods", $goods);	

		$this->assign("order", $order);	
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
		$count = M('order b')->join("{$this->pre}members m ON m.id=b.uid")->join("{$this->pre}market x ON x.id=b.gid")->where($map)->count('b.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$field= 'b.id,b.uid,b.gid,b.jifen,b.jine,b.num,b.add_time,b.add_ip,b.action,m.id mid,m.user_name,x.title';
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
	
	public function _listFilter($list){
	 	$listType = D('Acategory')->getField('id,type_name'); 
		$row=array();
		foreach($list as $key=>$v){ 
			$v['type_name'] = $listType[$v['type_id']];
			$row[$key]=$v;
		} 
		return $row;
	}
	
	public function doedit(){
  		$id = intval($_POST['id']);
		$data['yijian'] =  $_POST['yijian'];
   	    $data['status'] =  $_POST['status'];
   	    $data['fh_time']=time();
   	    $newxid= M("order_zz")->where("id={$id}")->save($data);
   		if($newxid==1) $this->success("处理成功");
		else $this->error("处理失败");
	}

}
?>