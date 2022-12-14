<?php
// 文章分类
class ZhongzhiAction extends HCommonAction {


	public function test(){

		Vendor('Alipay.AopClient');

		// $Llpayconfig = new Llpayconfig();
		// $llpay_config=$Llpayconfig->llpay_config();
		// //开户接口
		// $llpay_kaihu = 'https://wallet.lianlianpay.com/llcomponent/openuser.htm';


		// //建立请求
		// $llpaySubmit = new LLpaySubmit($llpay_config);
		// $html_text = $llpaySubmit->buildRequestPara($data);
		
		 $c = new AopClient;
		 $c->gatewayUrl = "https://openapi.alipay.com/gateway.do";
		 $c->appId = "app_id";
		 $c->rsaPrivateKey = '请填写开发者私钥去头去尾去回车，一行字符串' ;
		 $c->format = "json";
		 $c->charset= "GBK";
		 $c->signType= "RSA2";
		 $c->alipayrsaPublicKey = '请填写支付宝公钥，一行字符串';
		 //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.open.public.template.message.industry.modify
		$request = new AlipayOpenPublicTemplateMessageIndustryModifyRequest();
		//SDK已经封装掉了公共参数，这里只需要传入业务参数
		//此次只是参数展示，未进行字符串转义，实际情况下请转义
		$request->setBizContent = "{" .
		"    \"primary_industry_name\":\"IT科技/IT软件与服务\"," .
		"    \"primary_industry_code\":\"10001/20102\"," .
		"    \"secondary_industry_code\":\"10001/20102\"," .
		"    \"secondary_industry_name\":\"IT科技/IT软件与服务\"" .
		" }";
		$response= $c->execute($request);

	}
    public function index(){
    	if(is_mobile()==1){
    		$url=str_replace("/index.php/","/wap",$_SERVER['PHP_SELF']);
	 
			echo "<script type='text/javascript'>";

     	    echo "window.location.href='".$url."';";
			echo "</script>";die;
			// echo $_SERVER['PHP_SELF']; #/PHP/XX.php
		}

		$info=M("zhongzhi")->where("id=2")->find();


        $info["gglist"]=M("zhongzhi_xx")->where("zid=".$info["id"])->select();

        $info["imglist"]=explode(",",$info["images"]);
        //var_dump($info["imglist"]);exit;
        
        $this->assign("info",$info);
		
		$nmap["tuijian"]="1";
		$norder="art_time desc";
		$rlist=M('market')->where($nmap)->limit("0,4")->order($norder)->select();
		$this->assign("rlist",$rlist);


		$this->assign("dh",'3');
    	$this->display();        
         
    }
    public function do_order(){
    	if(!$this->uid) {
           $jsons['msg']="请先登录";
           $jsons['stats']=2;
           outJson($jsons);
        }

        $data["uid"]=$this->uid;
        $data["gid"]=$_REQUEST["guige"];
        $data["type"]="2";
        $data["zid"]="2";
        $data["num"]=$_REQUEST["num"];

        $map["uid"]=$this->uid;
        $map["type"]="2";
        $resd=M("car")->where($map)->delete();

        $res=M("car")->add($data);
        if($res){
           $jsons['msg']="操作成功";
           $jsons['stats']=1;
        }else{
          $jsons['msg']="操作失败";
          $jsons['stats']=0;
        }
        outJson($jsons);
    }

    public function do_car_num(){

    	$map["uid"]=$this->uid;

        $data["num"]=$_POST["num"];
        $map["id"]=$_POST["id"];

        M("car")->where($map)->save($data);
        $cars=M("car")->where($map)->find();

        $zhongzhi=M("zhongzhi")->where("id=".$cars["zid"])->find();
        $zhongzhi_xx=M("zhongzhi_xx")->where("id=".$cars["gid"])->find();

        $zongjia=$zhongzhi_xx["price"]*$cars["num"];


        outJson($zongjia);
      	

    }
    public function is_order(){
    	if (!$this->uid) {
            $this->error('请先登录', '/member/common/login.html');
            die;
        }

        $adlist=M("member_address")->where("uid={$this->uid}")->order("'default' desc")->select();
  		if (!$adlist) {
            $this->error('请先设置收货地址', '/member/memberaddress');
            die;
        }
        $this->assign("adlist",$adlist);

        $map["uid"]=$this->uid;
        $map["type"]="2";
        
        $cars=M("car")->where($map)->find();

        if(!$cars){
            $this->error('数据错误，请重新下单', '/zhongzhi/index.html');
            die;
        }


        $zhongzhi=M("zhongzhi")->where("id=".$cars["zid"])->find();
        $zhongzhi_xx=M("zhongzhi_xx")->where("id=".$cars["gid"])->find();


        $cars["good"]["title"]=$zhongzhi["name"].$zhongzhi_xx["pinlei"];
        $imglist=explode(",",$zhongzhi["images"]);
        $cars["good"]["art_img"]=$imglist[0];
        $cars["good"]["price"]=$zhongzhi_xx["price"];
        $cars["zongjia"]=$zhongzhi_xx["price"]*$cars["num"];
        $this->assign("cars",$cars);
      	//var_dump($cars);exit();

      
        $this->display(); 
    }

    public function post_order(){
    	if (!$this->uid) {
           $jsons['msg']="请先登录";
           $jsons['stats']=2;
           outJson($jsons);
        }
        $order=M("order_zz");
    	// 表单令牌验证
        // if(!$order->autoCheckToken($_POST)) {
        //     $jsons['msg']="禁止重复提交表单";
        //     $jsons['stats']=0;
        //     outJson($jsons);    
        // }

    	$id=$_POST["id"];
    	$address=$_POST["address"];
    	$uid=$this->uid;
    	$adinfo=M("member_address")->where("id=".$address)->find();

    	//$zhongzhi=M("zhongzhi")->where("id=".$cars["zid"])->find();
    	$carinfo=M("car")->where("id=".$id)->find();
        $zhongzhi_xx=M("zhongzhi_xx")->where("id=".$carinfo["gid"])->find();

    	$order->startTrans(); //rollback

    	$savedata['ordernums']=sprintf('%s-%s-%s', 'ZDD', $uid,time());

    	$savedata['uid'] = $this->uid;
		$savedata['jine'] =$zhongzhi_xx["price"]*$carinfo["num"];
		$savedata['gid'] = $carinfo["gid"];
		$savedata['add_time'] = time();
        $savedata['num'] = $carinfo["num"];
		$savedata['address'] = $adinfo["province"].$adinfo["city"].$adinfo["district"].$adinfo["address"];
		$savedata['cell_phone'] = $adinfo["main_phone"];
		$savedata['real_name'] = $adinfo["name"];
		$savedata['type'] = "2";
		$savedata['status'] = 1;
		$savedata['add_ip'] = get_client_ip();
		$savedata['pay_way'] =$_POST["pay_way"];


		$newid = $order->add($savedata);


		$cars=M("car")->where("id=".$id)->delete();
		if($newid&&$cars){
			$order->commit();
       		$jsons['msg']="操作成功";
	        $jsons['stats']=1;
	        $jsons['ordernums']=$savedata['ordernums'];
	        $jsons['pay_way']=$savedata['pay_way'];

		}else{
			$order->rollback();
			$jsons['msg']="操作失败";
            $jsons['stats']=0;
		}

		outJson($jsons);            
    }
    public function weixin(){
    	$uid=$this->uid;
        $id=$_REQUEST["id"];

        $map["uid"]=$uid;
        $map["ordernums"]=$id;
        $order=M('order_zz')->where($map)->find();
    	//var_dump($order);exit();
    	if($order["status"]!=1){
    		$this->error("订单状态错误", $this->_server('HTTP_REFERER'));
            exit();
    	}
    	if($order["pay_way"]!=2){
    		$this->error("请选择支付宝完成支付", $this->_server('HTTP_REFERER'));
            exit();
    	}


        $zhongzhi_xx=M("zhongzhi_xx")->where("id=".$order["gid"])->find();
        $zhongzhi=M("zhongzhi")->where("id=".$zhongzhi_xx["zid"])->find();
		
    	$data["name"]=$zhongzhi["name"];
    	$data["ordernums"]=$order["ordernums"];
    	$data["money"]=$order["jine"]*100;
    	$data["pid"]=$order["gid"];

    	vendor('Weixin.natives');

    	$natives = new natives();
    	$a=$natives->wxewm($data);


    	// $a=curl_post("https://houtai.rzmwzc.com/phpsdk/example/native.php",$data);
     //    $a=substr($a,1,strlen($a)-2);
        $this->assign("url2",$a);

        $this->assign("data",$data);
        $this->display();

    }
    
    public function zhifubao(){
        vendor('Alipay.pagepay.pagepay');

        $uid=$this->uid;
        $id=$_REQUEST["id"];

        $map["uid"]=$uid;
        $map["ordernums"]=$id;
        $order=M('order_zz')->where($map)->find();
        //var_dump($order);exit();
        if($order["status"]!=1){
            $this->error("订单状态错误", $this->_server('HTTP_REFERER'));
            exit();
        }
        if($order["pay_way"]!=1){
            $this->error("请选择微信完成支付", $this->_server('HTTP_REFERER'));
            exit();
        }

        $goods=M("zhongzhi_xx x")->field('x.*,z.images,z.name')->where("x.id=".$order["gid"])->join("lzh_zhongzhi z ON z.id=x.zid")->find();
        
        $data['WIDout_trade_no']=$order["ordernums"];
        //订单名称，必填
        $data['WIDsubject']=$goods["name"];
        //付款金额，必填
        $data['WIDtotal_amount']=$order["jine"];
        //商品描述，可空
        $data['WIDbody']=$goods["pinlei"];
        $pagepay = new pagepay();
        $pagepay->post_order($data);
    }

    public function gxorder(){
    	$data = file_get_contents("php://input");
    	$ordernums=$_POST["ordernums"];
    	$dat["status"]=1;
    	M('order_zz')->where("ordernums='".$ordernums."'")->save($dat);
    }

	public function post_order1(){

        // 表单令牌验证
        if(!M("members")->autoCheckToken($_POST)) {
            $this->error("禁止重复提交表单", $this->_server ( 'HTTP_REFERER' ));
            exit();
        }

		$savedata = textPost($_POST);
		if($savedata["num"]<=0){
		      $this->error("请输入正确的数值");die;
		    
		}
		$vo = M('market')->find($savedata["gid"]);  //积分商品信息
        if($vo["art_writer"] <= 0){
            $this->error("商品库存不足，请选择其他商品！",__APP__."/shop/shop_show?id=".$savedata["gid"]);
        }
		$vminfo =getMinfo($this->uid,true);
		$jine=$savedata["num"]*$vo["art_jiage"];
		$jifen=intval($savedata["num"])*intval($vo["art_jifen"]);
		//if(($vminfo["account_money"]+$vminfo["back_money"])<$jine) $this->error("您的余额不足，请选择其他商品或充值！",__APP__."/maket/detail?id=".$savedata["gid"]);
		if($vminfo["credits"]<$jifen) $this->error("您的积分不足，请选择其他商品！",__APP__."/shop/shop_show?id=".$savedata["gid"]);
		$savedata['uid'] = $this->uid;
		$savedata['jine'] = $jine;
		$savedata['jifen'] = $jifen;
		$savedata['add_time'] = time();
		$savedata['add_ip'] = get_client_ip();

		$newid = M('order')->add($savedata);
		if(!$newid>0){
			$this->error('提交失败，请确认填入数据正确');
			exit;
		}
		//memberMoneyLog($this->uid,37,-($jine),"购买商品减去金额".$jine,'0','');  //  商品购买  人民币消费记录
		memberCreditsLog($this->uid,8,-intval($jifen),"购买商品减去积分".$jifen);

		//减少库存
        $vo["art_writer"] -= $savedata["nums"];
        $Market = M("market");
        $Market->where("id={$vo['id']}")->save($vo);

///shop/shop_show?id=".$savedata["gid"]
		$this->success('兑换成功！',__APP__."/member/shoporder");
	
	}
	

	public function get_points(){
    	$this->display();        
         
    }
   public function madd(){

		$va=M("member_address as a")->where("uid={$this->uid}")->select();
		$this->assign("va",$va);
		$this->assign("gt",$_GET);

		$this->display();
	}
 	public function add(){
		$this->display();
	}
	
 	public function save(){
 		$va=M("member_address as a")->where("id={$_GET['id']}")->find();
		$this->assign("va",$va);
		$this->display();
	}
	public function save_do(){
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
	 	echo  '<script type="text/javascript">alert("成功");window.location.href="'.__APP__.'/member/memberaddress";</script>';
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
 	var_dump(M("member_address")->getlastsql());
 	die;
	 	echo  '<script type="text/javascript">alert("成功");window.location.href="'.__APP__.'/member/memberaddress";</script>';
	}
   
    
}
