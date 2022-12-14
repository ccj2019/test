<?php
// 文章分类
class ShopAction extends HCommonAction {
    public function index(){
    	$_REQUEST["pid"]=$_REQUEST["pid"]==0?"0":$_REQUEST["pid"];
		$_REQUEST["jifen"]=$_REQUEST["jifen"]==0?"0":$_REQUEST["jifen"];


		$tmap["tuijian"]=1;
		//分页处理
		$field= '*';
		$list['tlist'] = M('market')->field($field)->where($tmap)->order("id DESC")->select();
		$tjsp=0;
		foreach ($list['tlist'] as $key => $value) {
			$tjsp=$tjsp.','.$value["id"];
		}
		//$tjsp=

		$map=array();
		$map['art_set'] = 0;
		if($_REQUEST["type_id"]!=0){
				$map["type_id"]=$_REQUEST["type_id"];	
			//$map["type_id"]=$_REQUEST["type_id"];
		} 
		$map["id"]=array("not in",$tjsp);
		//分页处理
		import("ORG.Util.Page");
		$count = M('market')->where($map)->count('id');
		$this->assign("count", $count);
		$p = new Page($count, 20);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		$field= '*';
		$list['list'] = M('market')->field($field)->where($map)->limit($Lsql)->order("art_time DESC")->select();
		$list['page']	=$page;	
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));



        $this->assign("list", $list);
        $this->assign("pagebar", $page);
		unset($map);
		$map['parent_id'] = 490;
		$menuList= M('article_category')->where($map)->order("sort_order desc")->select();
		$this->assign("menuList",$menuList);
		$this->assign("jifen",$_REQUEST["jifen"]==0?"0":$_REQUEST["jifen"]);
		$this->assign("pid",$_REQUEST["pid"]==0?"0":$_REQUEST["pid"]);	
		
			$uidcount = M('order')->where("uid={$this->uid}")->count();
		 
		$meinfo= M('members')->where("id={$this->uid}") ->find();
		
		  $this->assign("uidcount",empty($uidcount)?0:$uidcount );
		  $this->assign("meinfo", $meinfo); 
		  $this->assign("type_id",$_REQUEST["type_id"]);
		  //$type_id


		

    	$this->display();        
         
    }
    public function shop_show(){
    	
		$id = intval($_GET['id']);
		$vo = M('market')->find($id);
		$listType = D('Acategory')->getField('id,type_name');
		$vo["type_name"]=$listType[$vo['type_id']];
		$vo["buy"]=M("order")->where("gid=".$id)->count("id");
		$vo["buy"]=$vo["buy"]==""?0:$vo["buy"];
		$buylist= M('order o')->field("o.*,m.user_name")->join("lzh_members m on m.id=o.uid")->where("o.gid=".$id)->order("o.id desc")->select();
		$this->assign("buylist",$buylist);
		$this->assign("vo",$vo);
    	$this->display();        
         
    }
    public function shop_is_order(){
    	if(!$this->uid){
    		$url="/wapmember/common/login";
echo "<script language='javascript'type='text/javascript'>"; 
echo "alert('请先登陆');"; 
echo "window.location.href='$url'"; 
echo "</script>"; die;

    	}
    	
//  	sun
	if($_GET['sun']<0 ){
		
				echo "<script type='text/javascript'>";
			 	echo "alert('没选择产品！');";
        		echo "window.history.go(-1);";
			    echo "</script>";die;
	}
			if(!isset($_GET['addid'])|| empty($_GET['addid'])){
					$va=M("member_address as a")
//		->join(' member_info as i ON i.uid = a.uid')
//		->field('member_address.*')
		->where("a.uid={$this->uid} and a.default=1") ->find();
			}else{
						$va=M("member_address as a")
//		->join(' member_info as i ON i.uid = a.uid')
//		->field('member_address.*')
		->where("a.id={$_GET['addid']}") ->find();
			}	 
		
//		var_dump(M("member_address as a")->getlastsql());
		$this->assign("va",$va);
//		var_dump($va);
//		die;

		$id = intval($_GET['id']);
		$vo = M('market')->find($id);  //积分商品信息
		$vminfo =getMinfo($this->uid,true);
		$model=M('member_info');
		$vou = $model->find($this->uid);
	
		if(!is_array($vou)) $model->add(array('uid'=>$this->uid));
		else $this->assign('vou',$vou);
		$memberinfo=M('members')->find($this->uid);

		$this->assign("memberinfo",$memberinfo);


		$unm=$_GET['sun'] ;
		$jifen=$vo['art_jifen']*$unm;


		$yue=0;
		if($jifen>$memberinfo["credits"]){
			$yue=($jifen-$memberinfo["credits"])/$this->glo["market_bl"];
		}
		$this->assign("yue",round($yue,2));


	
		//if(($vminfo["account_money"]+$vminfo["back_money"])<$vo["art_jiage"]) $this->error("您的余额不足，请充值！",__APP__."/maket/detail?id=".$id);
//		if($vminfo["credits"]<$vo["art_jifen"]) $this->error("您的积分不足，请继续努力！",_"/wap/shop/detail?id=".$id);
		$this->assign("vminfo",$vminfo);  // 会员信息
		$this->assign("vo",$vo); 
		$this->assign("unm",$_GET['sun']);
	
    	$this->display();        
         
    }
	public function post_order(){

        // 表单令牌验证
        if(!M("members")->autoCheckToken($_POST)) {
            echo "<script type='text/javascript'>";
            echo "alert('禁止重复提交表单');";
            echo "window.location.href='" . getenv("HTTP_REFERER") . "';";
            echo "</script>";
            exit();
        }

		$savedata = textPost($_POST);

		$vminfo =getMinfo($this->uid,true);

        if ($vminfo['pin_pass'] != md5($savedata["zhifu"])) {
            echo "<script type='text/javascript'>";
            echo "alert('支付密码错误！');";
            echo "window.history.go(-1);";
            echo "</script>";die;
        }

		$vo = M('market')->find($savedata["gid"]);  //积分商品信息
        if($vo["art_writer"] <= 0){
            echo "<script type='text/javascript'>";
            echo "alert('商品库存不足，请选择其他商品！');";
            echo "window.history.go(-1);";
            echo "</script>";die;
        }
		if($savedata["num"] <= 0){
            echo "<script type='text/javascript'>";
            echo "alert('选择商品数量不正确！');";
            echo "window.history.go(-1);";
            echo "</script>";die;
        }

		$jine=$savedata["num"]*$vo["art_jiage"];
		
		$jifen=intval($savedata["num"])*intval($vo["art_jifen"]);
	
		//if(($vminfo["account_money"]+$vminfo["back_money"])<$jine) $this->error("您的余额不足，请选择其他商品或充值！",__APP__."/maket/detail?id=".$savedata["gid"]);


		if($vminfo["credits"]<$jifen){
			$jine=round(($jifen-$vminfo["credits"])/$this->glo["market_bl"],2);
			$jifen=$vminfo["credits"];

			if(($vminfo["account_money"]+$vminfo["back_money"])<$jine){
				echo "<script type='text/javascript'>";
			 	echo "alert('您的余额不足，请选择其他商品或充值！');";
        		echo "window.history.go(-1);";
			    echo "</script>";die;
			}
		} 
		$savedata['uid'] = $this->uid;
		$savedata['jine'] = $jine;
		$savedata['jifen'] = $jifen;
		$savedata['add_time'] = time();
		$savedata['add_ip'] = get_client_ip();
	
		if(empty($savedata["address"])){
		
				echo "<script type='text/javascript'>";
			 	echo "alert('地址信息未填写！');";
        		echo "window.history.go(-1);";
			    echo "</script>";die;
		} 
		$newid = M('order')->add($savedata);
		
		if(!$newid>0){
//			$this->error('提交失败，请确认填入数据正确');
            echo "<script type='text/javascript'>";
            echo "alert('提交失败，请确认填入数据正确！');";
            echo "window.history.go(-1);";
            echo "</script>";die;
            exit;
		}
		if($jine>0){
			memberMoneyLog($this->uid,37,-($jine),"购买商品减去金额".$jine,'0','');  //  商品购买  人民币消费记录
		}
		if($jifen>0){
			memberCreditsLog($this->uid,8,-intval($jifen),"购买商品减去积分".$jifen);
		}

        //减少库存
        $vo["art_writer"] -= $savedata["num"];
        $Market = M("market");
        $Market->where("id={$vo['id']}")->save($vo);

		echo "<script type='text/javascript'>";
        echo "alert('兑换成功！');";
        echo "window.location.href='/Wapmember/shoporder'";
        echo "</script>";die;
//		$this->success('订单提交成功！',__APP__."/maket/detail?id=".$savedata["gid"]);
		//$this->display();
	}

	public function get_points(){
    	$this->display();        
         
    }
   public function madd(){
// 	var_dump($_GET);
 	$str=getenv("HTTP_REFERER");
 	//setcookie('surl',$str);
	//var_dump($_COOKIE['surl']);
    if(eregi("/Shop/shop_is_order","$str")){ 
		session('surl',$str);
		$this->assign("shangyiyeurl",$str ); 
		$this->assign("xuze",1 ); 
	}else{
		$this->assign("shangyiyeurl",session('surl') ); 
		
//		echo "0"; die;
	}
//	var_dump($_COOKIE['surl']);
//	die;
		$va=M("member_address as a")
//		->join(' member_info as i ON i.uid = a.uid')
//		->field('member_address.*')
		->where("uid={$this->uid}")->select();
		if(empty($va)){
				echo "<script type='text/javascript'>";
			 	//echo "alert('您还没有添加地址！');";
//			 	var_dump($_COOKIE['surl']);die;
        		echo "window.location.href='/Wapmember/memberaddress/add?id=".$_GET['id']."&sum=".$_GET['sum']."';";
			    echo "</script>";die;
		}
		$this->assign("va",$va);
		$this->assign("gt",$_GET);
// var_dump($va);die;
		$this->display();
	}
 	public function add(){
		$this->display();
	}
	
 	public function save(){
 		$va=M("member_address as a")
//		->join(' member_info as i ON i.uid = a.uid')
//		->field('member_address.*')
		->where("id={$_GET['id']}")->find();
		$this->assign("va",$va);
		$this->display();
	}
		public function save_do(){
//				var_dump($_POST);
//		die;
if($_POST['default']==1){
	
 		$va=M("member_address")
//		->join(' member_info as i ON i.uid = a.uid')
//		->field('member_address.*')
		->where("id={$this->uid}")->save(["default"=>0]); 
	
}
 		$va=M("member_address")
//		->join(' member_info as i ON i.uid = a.uid')
//		->field('member_address.*')
		->where("id={$_POST['id']}")->save($_POST); 
//		var_dump(M("member_address")->getlastsql());
//		die;
	 	echo  '<script type="text/javascript">alert("成功");window.location.href="'.__APP__.'/Wapmember/memberaddress";</script>';
	}
 public function add_do(){
//				var_dump($_POST);
//		die;
if($_POST['default']==1){
	
 		$va=M("member_address")
//		->join(' member_info as i ON i.uid = a.uid')
//		->field('member_address.*')
		->where("id={$this->uid}")->save(["default"=>0]); 
	
}
$_POST['uid']=$this->uid;
 		$va=M("member_address")
//		->join(' member_info as i ON i.uid = a.uid')
//		->field('member_address.*') 
		->add($_POST); 

	 	echo  '<script type="text/javascript">alert("成功");window.location.href="'.__APP__.'/Wapmember/memberaddress";</script>';
	}
   
    
}
