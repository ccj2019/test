<?php
// 全局设置
class GoodsAction extends ACommonAction
{
	function __construct(){
		parent::__construct();
		$this->db = M('pt_goods');
		$this->dbtype = M('pt_type');
	}
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
	
    public function index()
    {
		$field='*';
		// $field= 'id,title,art_miaoshu,art_fengge,type_id,art_jiage,art_img,art_jifen,art_set,art_writer,art_time';
		$typeid = $_REQUEST['typeid']?array('typeid'=>$_REQUEST['typeid']):array();
		// $mapp["leixing"]=array("like","%|$typeid|%");
		$mapp=$typeid;
// var_dump($map);exit;
		$res=$this->dbtype->where(" type=1 ")->select();//->where(" type=1 ")
		$typelist = $res;

		$this->assign('typelist',$typelist);
		foreach ($typelist as $tk => $tv) {
			$typeids[] = $tv['id'];
		}
		$typeids = implode(",", $typeids);
		$map['typeid'] = array('in',"$typeids");
		// $this->assign("type",$typelist);
		if(isset($_REQUEST['typeid'])&&!empty($_REQUEST['typeid'])){
			$map['typeid'] = $_REQUEST['typeid'];
			$this->assign('typeid',$_REQUEST['typeid']);
		}

		$list=$this->_list_pro(M('pt_goods'),$field,$mapp,'time','DESC');
		foreach ($list as $k => $v) {
			$list[$k]["typename"]=$this->dbtype->where("id=".$v['typeid'])->getField("name");
			$list[$k]["art_img"]=unserialize($v['images'])[0]['img'];
		}

		$this->assign('list', $list);
        $this->display();
    }
	
	
	
	
    public function goodadd()
    {
		$map["type"]=1;
		$typelist = $this->dbtype->where($map)->select();
		$this->assign('type_list',$typelist);
		$map["type"]=2;
		$lxlist = $this->dbtype->where($map)->select();
		$this->assign('lxlist',$lxlist);
		
		$vo["time"]=time();
		$vo["online"]=1;
		$this->assign('info',$vo);


		$this->display("form");
    }
	public function goodedit()
    {
		$map["type"]=1;
		$typelist = $this->dbtype->where($map)->select();
		$this->assign('type_list',$typelist);
		$map["type"]=2;
		$lxlist = $this->dbtype->where($map)->select();
		$this->assign('lxlist',$lxlist);
		
		$id = intval($_REQUEST['id']);
        $info = $this->db->where("id=".$id)->find();
		$this->assign('info',$info);
		$leixing=array_filter(explode('|',$info["leixing"]));
		$this->assign('leixing',$leixing);

		$this->display("form");
    }
    public function dodell()
	{
		$id = $_REQUEST['idarr'];
		$map['id'] = array('in',explode(',',$_REQUEST['idarr']));

		$data = M('pt_goods')->where($map)->delete();

		if($data){
		  $this->success(L('删除成功'),'',$id);
        } 
        else {
          $this->error(L('删除失败'));
        }
          
	}
    public function dodel()
	{
		$map['id'] = array('in',explode(',',$_REQUEST['idarr']));

		$data = M('pt_order')->where($map)->delete();

		if($data){
			//成功提示
			$this->assign('jumpUrl', U('Goods/order'));
			$this->success(L('删除成功'));
		}else{
			$this->assign('jumpUrl', U('Goods/order'));
			$this->success(L('删除失败'));
			// $this->success('操作成功');
		}
	}

	public function edit(){

		$id = intval($_GET['id']);
		if($id==0){
			$this->assign('jumpUrl', U('Order/order'));
			$this->error('参数错误');
		}
		$vo=M("pt_order o")->where(array('o.id'=>$id))
			->find();

		if($vo["yijian"]==""){
			$vo["yijian"]="快递名称:快递单号";
		}

		$hongbao = M('pt_hongbao')->where(array('tid'=>$vo['tid'],'uid'=>$vo['uid'],'type'=>1))->getField('hongbao');

		$vo['hongbao']  = $hongbao?$hongbao:0;
//		if($vo["carid"]){
//			$title=M("car c")
//				->field('c.num,m.id,title,art_info,art_time,art_img,art_jiage,type_id')
//				->where("c.id in (".$vo["carid"].")")
//				->join("lzh_market m ON m.id=c.gid")->select();
//		}else{
//			$title=M("market")->field('id,title,art_info,art_time,art_img,art_jiage,type_id')->where("id=".$vo["gid"])->select();
//			$title[0]["num"]=$vo["num"];
//		}
//		$vo["title"]=$title;
		$this->assign("vo", $vo);
		$this->display();

	}

	public function orerupdate(){
		$id = intval($_POST['id']);
		$data['kuaidi'] =  $_POST['yijian'];
		$data['status'] =  $_POST['action'];
		// var_dump($data);exit;
		$data['fh_time']=time();
		$newxid= M("pt_order")->where("id={$id}")->save($data);
		if($newxid==1) $this->success("处理成功",U('Goods/order',array('id'=>1)));
		else $this->error("处理失败");
	}

    public function order(){

		//分页处理
		import("ORG.Util.Page");

		$map['b.status'] = $_REQUEST['id'];
		$order="add_time desc";
		$count =M("pt_order b")->where($map)->order($order)->count();
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$field= 'b.id,add_time,ordernums,m.user_name,money,status,num';

		$list=M("pt_order b")->field($field)->where($map)->join("{$this->pre}members m ON m.id=b.uid")->order($order)->limit($Lsql)->select();

//		foreach ($list as $k => $v) {
//			if($v["carid"]!=null){
//				$goods=M("car c")->field('c.num,m.id,title,art_info,art_time,art_img,art_jiage,type_id')->where("c.id in (".$v["carid"].")")->join("lzh_market m ON m.id=c.gid")->select();
//			}else{
//				$goods=M("market")->field('id,title,art_info,art_time,art_img,art_jiage,type_id')->where("id=".$v["gid"])->select();
//				$goods[0]["num"]=$info["num"];
//			}
//			$goods_num=0;
//			foreach ($goods as $ke => $ve) {
//				$goods_num+=$ve["num"];
//			}
//			$list[$k]["goods"]=$goods;
//			$list[$k]["jianshu"]=$goods_num;
//		}


//		$list = $this->_listFilter($list);

//		$this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
		$this->assign("list", $list);
		$this->assign("pagebar", $page);
		$this->assign('id',$_REQUEST['id']);
//		$this->assign("search", $search);
//		$this->assign("xaction",ACTION_NAME);
//		$this->assign("query", http_build_query($search));

		$this->display();
	}

    public function dogoodedit(){
    	$_POST["time"]=strtotime($_REQUEST["time"]);
		foreach ( $GLOBALS['_POST']['swfimglist'] as $key => $v )
		{
				$row[$key]['img'] = substr( $v, 1 );
		}

		$_POST["images"]=serialize($row);
		// var_dump($_POST["images"]);exit;
		
		$lx="|";
		foreach ($_POST["leixing"] as $ke => $va) {
			$lx.=$va."|";
		}
		$_POST["leixing"]=$lx;
    	$isedit=0;
		if(isset($_POST['id'])&&!empty($_POST['id'])){
			$isedit=1;
			$res = $this->updateinfo($this->db);
		}else{
			$res = $this->addinfo($this->db);
		}
		if($res){
			if($isedit){
				 //成功提示
	            $this->assign('jumpUrl', __URL__."/index");
	            $this->success(L('修改成功'));
			}else{
				$this->assign('jumpUrl', __URL__."/index");
            	$this->success(L('新增成功'));
				// $this->success('操作成功');
			}
		}else{
			$this->error('操作失败');
		}
    }

	/**
	 * 合伙人审核列表
	 */
	public function partner()
	{
		import("ORG.Util.Page");

		$status = $_REQUEST['id'];

		$order="createtime asc";
		$count =M("pt_audit")->where(array('status'=>$status))->order($order)->count();
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$field= 'a.id,m.user_name,a.uid,a.status,a.createtime';

		$list=M("pt_audit a")->field($field)
			->where(array('status'=>$status))
			->join("{$this->pre}members m ON a.uid = m.id")
			->order($order)->limit($Lsql)->select();
//var_dump($list);exit();
		$this->assign("list", $list);
		$this->assign("pagebar", $page);

		$this->display();
	}

	public function audit()
	{
		$id = $_REQUEST['id'];
		$type = $_REQUEST['type'];
//		var_dump($id);exit();
		$find = M('pt_audit')->where(array('id'=>$id))->find();
		if($find){
			$save = M('pt_audit')->where(array('id'=>$id))->save(array('status'=>$type));
			if($type==1){
				$audit = M('members_status')->where(array('uid'=>$find['uid']))->save(array('partner'=>1));
			}
			if($save!==false&&$audit!==false){
				$this->success(L('操作成功'),U('Goods/partner'));
			}else{
				$this->error(L('操作失败'));
			}
		}else{

			$this->error(L('用户请求异常'));
		}
	}


	/**
	 * 申请退款列表
	 */
	public function tuikuan()
	{
		//分页处理
		import("ORG.Util.Page");

		$map['b.status'] = 7;
		$map['r.type'] = array("in",array(0,3));
		$order = 'id desc';
		$count =M("pt_order b")
			->where($map)->order($order)
			->join("{$this->pre}pt_refund r ON b.id=r.oid")
			->count();
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$field= 'b.id,r.add_time,ordernums,m.user_name,money,status,num';
		$order="r.add_time desc";
		$list=M("pt_order b")
			->field($field)->where($map)
			->join("{$this->pre}members m ON m.id=b.uid")
			->join("{$this->pre}pt_refund r ON r.oid=b.id")
			->order($order)
			->limit($Lsql)->select();
//var_dump(M("pt_order b")->getLastSql());exit();
		$this->assign("list", $list);
		$this->assign("pagebar", $page);
		$this->display();
	}


	/**
	 * 同意退款列表
	 */
	public function tongyi()
	{
		//分页处理
		import("ORG.Util.Page");

		$map['b.status'] = 7;
		$map['r.type'] = array("in",array(1,2));
		$order = 'id desc';
		$count =M("pt_order b")
			->where($map)->order($order)
			->join("{$this->pre}pt_refund r ON b.id=r.oid")
			->count();
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$field= 'b.id,b.ordernums,m.user_name,b.money,b.status,b.num,r.add_time,r.type';
		$order="r.add_time desc";
		$list=M("pt_order b")
			->field($field)->where($map)
			->join("{$this->pre}members m ON m.id=b.uid")
			->join("{$this->pre}pt_refund r ON r.oid=b.id")
			->order($order)
			->limit($Lsql)->select();
		foreach ($list as $k=>$v){
			$rmap['oid']=$v['id'];
			$list[$k]['tk']=M("pt_refunds")->where($rmap)->find();
			//var_dump(M("pt_refunds")->getLastSql());
		}

 //var_dump(M("pt_order b")->getLastSql());exit();
		$this->assign("list", $list);
		$this->assign("pagebar", $page);
		$this->display();
	}


	/**
	 * 正在退款列表
	 */
	public function tuikuandoing()
	{
		//分页处理
		import("ORG.Util.Page");

		$map['b.status'] = 7;
		$map['r.type'] = 1;
		$map['rs.type'] = 0;
		$order = 'id desc';
		$count =M("pt_order b")
			->where($map)->order($order)
			->join("{$this->pre}pt_refund r ON b.id=r.oid")
			->count();
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$field= 'b.id,r.add_time,ordernums,m.user_name,money,status,num';
		$order="r.add_time desc";
		$list=M("pt_order b")
			->field($field)->where($map)
			->join("{$this->pre}members m ON m.id=b.uid")
			->join("{$this->pre}pt_refund r ON r.oid=b.id")
			->join("{$this->pre}pt_refunds rs ON rs.oid=b.id")
			->order($order)
			->limit($Lsql)->select();
//var_dump($list);exit();
		$this->assign("list", $list);
		$this->assign("pagebar", $page);
		$this->display();
	}

	public function refund()
    {
        $id = intval($_GET['id']);
        if($id==0){
            $this->assign('jumpUrl', U('Goods/order'));
            $this->error('参数错误');
        }
        $vo=M("pt_order o")->where(array('o.id'=>$id))
            ->find();

        // if($vo["yijian"]==""){
        //     $vo["yijian"]="快递名称:快递单号";
        // }

        $hongbao = M('pt_hongbao')->where(array('tid'=>$vo['tid'],'uid'=>$vo['uid'],'type'=>1))->getField('hongbao');

        $cause = M('pt_refund')->where(array('oid'=>$id))->find();
		$vo['hongbao']  = $hongbao?$hongbao:0;
		$vo['cause']  = $cause['cause'];
		$vo['type_cause']  = $cause['type_cause'];
		$vo['addr']  = $cause['address'];
		$vo['name']  = $cause['name'];
		$vo['phone']  = $cause['phone'];

        $this->assign("vo", $vo);
        $this->display();
    }

	public function refunds()
    {
        $id = intval($_GET['id']);
        if($id==0){
            $this->assign('jumpUrl', U('Goods/order'));
            $this->error('参数错误');
        }
        $vo=M("pt_order o")->where(array('o.id'=>$id))
            ->find();

        // if($vo["yijian"]==""){
        //     $vo["yijian"]="快递名称:快递单号";
        // }

        $hongbao = M('pt_hongbao')->where(array('tid'=>$vo['tid'],'uid'=>$vo['uid'],'type'=>1))->getField('hongbao');

        $cause = M('pt_refund')->where(array('oid'=>$id))->find();
		$vo['hongbao']  = $hongbao?$hongbao:0;
		$vo['cause']  = $cause['cause'];
		$vo['type_cause']  = $cause['type_cause'];
		$vo['addr']  = $cause['address'];
		$vo['name']  = $cause['name'];
		$vo['phone']  = $cause['phone'];

        $this->assign("vo", $vo);
        $this->display();
	}
	
	public function refundscause(){
		include './WechattestAction.class.php';

		$id = $_REQUEST['id'];

		$order = M('pt_order')->where(array('id'=>$id))->find();

		$tuan = M('pt_tuan')->where(array('id'=>$order['tid']))->find();

		$hongbao = M('pt_hongbao')->where(array('id'=>$order['hbid']))->find();

		$status = 0;
		if($tuan['cnum']>($tuan['num']-1)){
			$status = 1;
		}

		//成团人数不足  退还成团奖励
		if($status==1){
			//这个成团的奖励全部扣除
			$stop = M('pt_stop')->where(array('tid'=>$tuan['id'],'status'=>1))->select();
			foreach($stop as $k=>$v){
				memberMoneyLog($v['tuid'], 304, $v['num'], $info = "团员退货奖励扣除", $target_uid = 0, $target_uname = "",$shiwu=true,$yubi=0);
			}

			//其他人的改回冻结状态
			M('pt_stop')->where(array('tid'=>$tuan['id']))->save(array('status'=>0));
			//因为此人的 改成失效的
			M('pt_stop')->where(array('oid'=>$id,'tid'=>$tuan['id']))->save(array('status'=>2));
			//成团奖励改成失效的
			M('pt_stop')->where(array('tid'=>$tuan['id'],'type'=>0))->save(array('status'=>2));

			//判断这个团是否已经结束
			if(strtotime($tuan['end_time'])<time()){
				M('pt_tuan')->where(array('id'=>$order['tid']))->save(array('status'=>3));
			}else{
				// M('pt_tuan')->where(array('id'=>$order['tid']))->save(array('status'=>1));
			}
			
		}else{
			//不算此人也能成团 扣款

			//扣除这个人所创造的奖励
			$stop = M('pt_stop')->where(array('oid'=>$id,'tid'=>$tuan['id'],'status'=>1,'type'=>1))->select();
			foreach($stop as $k=>$v){
				memberMoneyLog($v['tuid'], 304, $v['num'], $info = "团员退货扣除奖励", $target_uid = 0, $target_uname = "",$shiwu=true);
			}
			//因为此人的 改成失效的
			M('pt_stop')->where(array('oid'=>$id,'tid'=>$tuan['id'],'status'=>1,'type'=>1))->save(array('status'=>2));
		}

		//如果是混合支付 退还佣金支付部分
		if($order['pay_yongjin']>0){
			$res=memberMoneyLog($order['uid'], 303, $order['pay_yongjin'], $info = "退货退还佣金支付部分", $target_uid = 0, $target_uname = "",$shiwu=true);
		}
		if($order['zftype']!=1) {
			//退还微信支付部分
			$data['out_trade_no'] = $order['ordernums'];
			$data['out_refund_no'] = $order['ordernums'];

			$total_fee_one = $order['money'] + $order['yunfei'] - $hongbao['hongbao'];

			if ($order['yongjin'] == 0) {
				if ($total_fee_one <= 0) {
					$total_fee_one = 0.01;
					$refund_fee_one = 0.01;
				} else {
					$refund_fee_one = ($order['money'] - $hongbao['hongbao']) <= 0 ? 0.01 : $order['money'] - $hongbao['hongbao'];
				}
				$total_fee = $total_fee_one * 100;//支付金额
				$refund_fee = $refund_fee_one * 100;//退款金额
			} else {
				$total_fee = ($total_fee_one - $order['yongjin']) * 100;
				$refund_fee = ($total_fee_one - $order['yongjin']) * 100;
			}
			$data['total_fee'] = $total_fee;//支付金额
			$data['refund_fee'] = $refund_fee;//退款金额
			// $data['refund_fee'] = 1;//$order['money'] - $hongbao['hongbao'];
			// $data['notify_url'] = '';
			$wechat = new WechattestAction();
			$res = $wechat->ttt($data);
		}

		$ress = M('pt_refunds')->where(array('oid'=>$id,'uid'=>$order['uid']))->save(array('type'=>1));
//		var_dump($order);
//		var_dump(M('pt_refunds')->getLastSql());exit;
		if($res !=false && $ress !=false){
			$this->success('退款成功',U('Goods/tongyi'));
		}else{
			$this->error('退款失败');
		}
	}
	//申请退款 退款审核
    public function cause()
	{
//		var_dump($_REQUEST);exit();
//		array( ["id"]=> string(1) "1" ["action"]=> string(1) "2" ["yijian"]=> string(9) "123456789" ["type_cause"]=> string(6) "123456");
		$type = $_REQUEST['action'];
		$id = $_REQUEST['id'];
		$yijian = $_REQUEST['yijian'];
		$type_cause = $_REQUEST['type_cause'];

		$addr = $_REQUEST['address'];
		$phone = $_REQUEST['phone'];
		$name = $_REQUEST['name'];

		// if($type==1){
		// 	//调用退款方法  暂未完成
		// 	$this->orders($id);
		// }

		$order = M('pt_order')->where(array('id'=>$id))->save(array('yijian'=>$type_cause));

		$refund = M('pt_refund')->where(array('oid'=>$id))->save(array(
			'type'=>$type,
			'type_cause'=>$type_cause,
			'address'=>$addr,
			'name'=>$name,
			'phone'=>$phone
		));

		if($order!==false && $refund!==false){
			$this->success('审核成功',U('Admin/Goods/tuikuan'));
		}else{
			$this->error('审核失败');
		}
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
		
		// if(!empty($_FILES['imgfile']['name'])){
		// 	$this->savePathNew = '/Public/upload/';
		// 	$this->thumbMaxWidth = C('PRODUCT_UPLOAD_W');
		// 	$this->thumbMaxHeight = C('PRODUCT_UPLOAD_H');
		// 	$info = $this->CUpload();
		// 	$data['art_img'] = $info[0]['savepath'].$info[0]['savename'];
		// }

		// $m->type_id=$_REQUEST["type_id"];
		$m->time=strtotime($_REQUEST["time"]);
		//if($data['art_img']) $m->art_img=$data['art_img'];
		foreach ( $GLOBALS['_POST']['swfimglist'] as $key => $v )
		{
				$row[$key]['img'] = substr( $v, 1 );
				$row[$key]['info'] = $_POST['picinfo'][$key];
				$row[$key]['url'] = $_POST['urlinfo'][$key];
		}
		$m->images = serialize($row);
		$lx="|";
		foreach ($_POST["leixing"] as $ke => $va) {
			$lx.=$va."|";
		}
		$m->leixing=$lx;

		// if($_POST['is_remote']==1) $m->art_content = get_remote_img($m->art_content);
		return $m;
	}
	public function _editFilter($id){
		$map["type"]=1;
		$typelist = $this->dbtype->where($map)->select();
		$this->assign('type_list',$typelist);
		$map["type"]=2;
		$lxlist = $this->dbtype->where($map)->select();
		$this->assign('lxlist',$lxlist);

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

	public function typelist()
    {
		$field= '*';
		$this->_list(D('pt_type'),$field,'','id','asc');
		
        $this->display();
    }
    public function typeadd(){
    	$info["status"]=1;
    	$info["type"]=1;
    	$this->assign("vo",$info);
    	$this->display("typeedit");
    }
    public function typeedit(){
    	$id=$_GET["id"];
    	$map["id"]=$id;
    	$info=M("pt_type")->where($map)->find();
    	$this->assign("vo",$info);
    	$this->display();
    }
    public function dotypeedit(){
    	$isedit=0;
		if(isset($_POST['id'])&&!empty($_POST['id'])){
			$isedit=1;
			$res = $this->updateinfo($this->dbtype);
		}else{
			$res = $this->addinfo($this->dbtype);
		}
		//var_dump($this->dbtype);exit;
		if($res){
			if($isedit){
				 //成功提示
	            $this->assign('jumpUrl', __URL__."/typelist");
	            $this->success(L('修改成功'));
			}else{
				$this->assign('jumpUrl', __URL__."/typelist");
            	$this->success(L('新增成功'));
				// $this->success('操作成功');
			}
		}else{
			$this->error('操作失败');
		}
	}
	

	public function swfUpload( )
	{
		if ( $_POST['picpath'] )
		{
			$imgpath = substr( $_POST['picpath'], 1 );
			if ( in_array( $imgpath, $_SESSION['imgfiles'] ) )
			{
				unlink( C( "WEB_ROOT" ).$imgpath );
				$thumb = get_thumb_pic( $imgpath );
				$res = unlink(C("WEB_ROOT").$thumb );
				if ( $res )
				{
					$this->success("删除成功", "", $_POST['oid'] );
				}
				else
				{
					$this->error( "删除失败", "", $_POST['oid'] );
				}
			}
			else
			{
				$this->error( "图片不存在", "", $_POST['oid'] );
			}
		}
		else
		{
			//$_REQUEST["PHPSESSID"]=
			$this->savePathNew = C( "ADMIN_UPLOAD_DIR" )."Ad/";
			$this->thumbMaxWidth = "100";
			$this->thumbMaxHeight = "100";
			$this->saveRule = date( "YmdHis", time()).rand(0,1000);
			$info = $this->CUpload();
			$data['product_thumb'] = $info[0]['savepath'].$info[0]['savename'];
			if ( !isset( $_SESSION['count_file'] ) )
			{
				$_SESSION['count_file'] = 1;
			}
			else
			{
				++$_SESSION['count_file'];
			}
			$_SESSION['imgfiles'][$_SESSION['count_file']] = $data['product_thumb'];
			echo "{$_SESSION['count_file']}:".__ROOT__."/".$data['product_thumb'];
		}
	}
	
}
?>
