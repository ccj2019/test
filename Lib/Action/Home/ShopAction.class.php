<?php
// 文章分类
class ShopAction extends HCommonAction {
     function __construct(){
        parent::__construct();
        $this->assign("dh",'4');

        if($this->uid){
            $map["uid"]=$this->uid;
            $map["type"]=1;
            $map["status"]=1;
            $map["fangshi"]=1;
            $carnum=M("car")->where($map)->select();
            $carn=0;
            foreach ($carnum as $k => $v) {
                $carn+=$v["num"];
            }
        }else{
            $carn=0;
        }
        $this->assign("carnum",$carn);

    }
    public function index(){
    	if(is_mobile()==1){
    				 $url=str_replace("/index.php/","/wap",$_SERVER['PHP_SELF']);

			echo "<script type='text/javascript'>";

     	echo "window.location.href='".$url."';";
			echo "</script>";die;
			// echo $_SERVER['PHP_SELF']; #/PHP/XX.php
		}
		$nmap["tuijian"]="1";
		$norder="art_time desc";
		$rlist=M('market')->where($nmap)->limit("0,4")->order($norder)->select();
		//var_dump($rlist);exit();
		$this->assign("rlist",$rlist);




    	$_REQUEST["pid"]=$_REQUEST["pid"]==0?"0":$_REQUEST["pid"];
		$_REQUEST["jifen"]=$_REQUEST["jifen"]==0?"0":$_REQUEST["jifen"];
		$type=$_REQUEST["type"]?$_REQUEST["type"]:'';
		$map=array();
		$map['art_set'] = 0;
		$nmap["tuijian"]=array("neq","1");
		if($_REQUEST["type_id"]!=0){
			$map["type_id"]=$_REQUEST["type_id"];
		}
		if($_REQUEST["jifen"]==500){
			$map["art_jifen"]=["lt",intval($_REQUEST["jifen"])];
		}else if($_REQUEST["jifen"]==1000){
			$map["art_jifen"]=[["lt",intval($_REQUEST["jifen"])],["egt",500]];
		}else if($_REQUEST["jifen"]==3000){
			$map["art_jifen"]=[["lt",intval($_REQUEST["jifen"])],["egt",1000]];
		}else if($_REQUEST["jifen"]==5000){
			$map["art_jifen"]=[["lt",intval($_REQUEST["jifen"])],["egt",3000]];
		}else if($_REQUEST["jifen"]==5){
			$map["art_jifen"]=["egt",5000];
		}
		$this->assign("type_id", $_REQUEST["type_id"]);
		$this->assign("jifen", $_REQUEST["jifen"]);
		//分页处理
		import("ORG.Util.Page");
		$count = M('market')->where($map)->count('id');
		$this->assign("count", $count);
		$p = new Page($count, 15);
		$page = $p->show("#more");
		$Lsql = "{$p->firstRow},{$p->listRows}";
		if($type == 'new'){
			$order =" art_time desc";
			$type='new';
		}else if($type == 'hot'){
			$order=" id desc";
			$type='hot';
		}else if($type == 'jifen'){
			$order=" art_jifen desc";
			$type='jifen';
		}else{
			$order=" art_time desc";
			$type='';
		}
		//分页处理
		$field= '*';
		$list['list'] = M('market')->field($field)->where($map)->limit($Lsql)->order($order)->select();

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
		$this->assign("type", $type);
		$this->assign("uidcount",empty($uidcount)?0:$uidcount );
		$this->assign("meinfo", $meinfo);




		$this->assign("dh",'4');
    	$this->display();

    }
    public function shop_show(){
    	if(is_mobile()==1){
    		$url=str_replace("/index.php/","/wap",$_SERVER['PHP_SELF']);
			echo "<script type='text/javascript'>";
     	    echo "window.location.href='".$url."';";
			echo "</script>";die;
			// echo $_SERVER['PHP_SELF']; #/PHP/XX.php
		}

		$id = intval($_GET['id']);
		$vo = M('market')->find($id);

		$listType = D('Acategory')->getField('id,type_name');
		$vo["type_name"]=$listType[$vo['type_id']];
		$map['art_set']=0;
		$buylist = M('market')->where($map)->limit(3)->order("id DESC")->select();
		$this->assign("buylist",$buylist);
        $vo["jiaoyi"]=1000-intval($vo["art_writer"]);//M("car")->where(array('gid'=>$vo['id'],'status'=>2))->count();
		$this->assign("vo",$vo);


		$nmap["tuijian"]="1";
		$norder="art_time desc";
		$rlist=M('market')->where($nmap)->limit("0,4")->order($norder)->select();
		$this->assign("rlist",$rlist);
		$this->assign("dh",'4');
    	$this->display();

    }
    public function add_car(){
    	if(!$this->uid) {
           echo '<script>window.location.href="/Member/common/login?url='.urlencode('/shop/shop_show?id='.$_REQUEST["gid"]).'"</script>';
           exit();
        }
        $data["uid"]=$this->uid;
        $data["gid"]=$_REQUEST["gid"];
        $data["type"]="1";

        if($_REQUEST["num"]&&intval($_REQUEST["num"]>0)){
            $data["num"]=$_REQUEST["num"];
         }else{
            $data["num"]=1;
         }


        $data["fangshi"]=1;

        $map["uid"]=$this->uid;
        $map["status"]=1;
        $map["fangshi"]=1;
        $map["gid"]=$_REQUEST["gid"];
        $map["type"]=1;
        $resd=M("car")->where($map)->find();
        if($resd){
        	$id=$resd["id"];
            $datad["num"]=$resd["num"]+$data["num"];
            $res=M("car")->where($map)->save($datad);
        }else{
            $res=M("car")->add($data);
            $id=$res;
        }
        $info=M("car c")->field('c.num,m.id,title,art_img,art_jiage')->where("c.id =".$id)->join("lzh_market m ON m.id=c.gid")->find();
        $this->assign("info",$info);

        $norder="art_time desc";
        $glist=M('market')->where($nmap)->limit("0,10")->order($norder)->select();
		$this->assign("glist",$glist);


        $this->display();
    }
     public function add_cars(){
        if(!$this->uid) {
          $jsons['status']=2;
          outJson($jsons);
        }
        $data["uid"]=$this->uid;
        $data["gid"]=$_REQUEST["gid"];
        $data["type"]="1";


        $data["num"]=1;


        $data["fangshi"]=1;

        $map["uid"]=$this->uid;
        $map["status"]=1;
        $map["fangshi"]=1;
        $map["gid"]=$_REQUEST["gid"];
        $map["type"]=1;
        $resd=M("car")->where($map)->find();
        if($resd){
            $id=$resd["id"];
            $datad["num"]=$resd["num"]+$data["num"];
            $res=M("car")->where($map)->save($datad);
        }else{
            $res=M("car")->add($data);
            $id=$res;
        }
        if($id){
            $jsons['status']=1;
            $jsons['msg']="添加成功";
        }else{
            $jsons['status']=0;
            $jsons['msg']="添加失败";
        }
        $mapd["uid"]=$this->uid;
        $mapd["type"]=1;
        $mapd["status"]=1;
        $mapd["fangshi"]=1;
        $carnum=M("car")->where($mapd)->select();
        $carn=0;
        foreach ($carnum as $k => $v) {
            $carn+=$v["num"];
        }
        $jsons['carnum']=$carn;
        outJson($jsons);

        //$this->success("加入成功!", $this->_server ( 'HTTP_REFERER'));
    }

    public function goumai(){
        if(!$this->uid) {
           echo '<script>window.location.href="/Member/common/login?url='.urlencode('/shop/shop_show?id='.$_REQUEST["gid"]).'"</script>';
           exit();
        }
        $data["uid"]=$this->uid;
        $data["gid"]=$_REQUEST["gid"];
        $data["type"]="1";
        $data["num"]=$_REQUEST["num"];
        $data["fangshi"]=2;

        $map["fangshi"]=2;
        $map["uid"]=$this->uid;
        $map["status"]=1;
        $map["gid"]=$_REQUEST["gid"];
        $map["type"]=1;
        $resd=M("car")->where($map)->delete();

        // if($resd){
        //     $id=$resd["id"];
        //     $datad["num"]=$resd["num"]+$_REQUEST["num"];
        //     $res=M("car")->where($map)->save($datad);
        // }else{
        //     $res=M("car")->add($data);
        //     $id=$res;
        // }
        $res=M("car")->add($data);
        // $info=M("car c")->field('c.num,m.id,title,art_img,art_jiage')->where("c.id =".$id)->join("lzh_market m ON m.id=c.gid")->find();
        // $this->assign("info",$info);

        // $norder="art_time desc";
        // $glist=M('market')->where($nmap)->limit("0,10")->order($norder)->select();
        // $this->assign("glist",$glist);




        $adlist=M("member_address")->where("uid={$this->uid}")->order("'default' desc")->select();
        if (!$adlist) {
            $this->error('请先设置收货地址', '/member/memberaddress');
            die;
        }
        $this->assign("adlist",$adlist);

        $carids=$res;
        $this->assign("carids",$carids);

        $mmpc["c.uid"]=$this->uid;
        $mmpc["c.id"]=$res;


        $list=M("car c")->field('c.num,m.id,title,art_img,art_jiage')->where($mmpc)->join("lzh_market m ON m.id=c.gid")->select();
        //var_dump(M("car c")->getlastsql());exit();
        if(!$list){
             $this->error("数据错误请重新下单！", $this->_server ( 'HTTP_REFERER'));
           exit();
        }
        $zongjia=0;
        foreach ($list as $k => $v) {
            //$list[$k]["good"]["type_name"]=M("article_category")->where("id=".$market["type_id"])->getField("type_name");
            $list[$k]["art_zj"]=floatval($v["art_jiage"])*intval($v["num"]);
            $zongjia+=$list[$k]["art_zj"];
        }
        $this->assign("zongjia",$zongjia);
        $this->assign("list",$list);

        $memberinfo=M('members')->find($this->uid);

        $jsons['kyjifen'] = intval($memberinfo["credits"]);

        if($jsons['kyjifen']>=20){
            $jsons['dixian'] = floor(intval($memberinfo["credits"])/20)/100;
            if($jsons['dixian']>=$zongjia){
                $jsons['dixian']=$zongjia;
            }
            $jsons["shiyong"]=$jsons['dixian']*2000;
        }else{
            $jsons['dixian'] =0;
            $jsons["shiyong"]=0;
        }
        $this->assign("jsons",$jsons);
        $this->assign("zjf",round(($zongjia-$jsons['dixian']),2));

        $this->display("do_order");
    }
    public function car_list(){
       	if(!$this->uid) {
           echo '<script>window.location.href="/Member/common/login?url='.urlencode('/shop/car_list').'"</script>';
           exit();
        }
        $uid=$this->uid;


        $map["uid"]=$uid;
        $map["type"]=1;
        $map["status"]=1;
        $map["fangshi"]=1;
        $list=M("car")->where($map)->select();
        if(!$list){
            $this->error('购物车为空，快去购物吧！', '/index/shop');
            die;
        }

        $zongjia=0;
        foreach ($list as $k => $v) {
            $market=M("market")->where("id=".$v["gid"])->find();
            $list[$k]["good"]["type_name"]=M("article_category")->where("id=".$market["type_id"])->getField("type_name");
            $list[$k]["good"]["title"]=$market["title"];
            $list[$k]["good"]["id"]=$market["id"];
            $list[$k]["good"]["art_img"]=$market["art_img"];
            $list[$k]["good"]["art_jiage"]=$market["art_jiage"];
            $list[$k]["good"]["art_zj"]=floatval($market["art_jiage"])*intval($v["num"]);
            //$zongjia+=$list[$k]["good"]["art_zj"];
        }
        //$this->assign("zongjia",$zongjia);
        $this->assign("list",$list);
        $this->display();
    }
    public function do_order(){
       	if(!$this->uid) {
           $this->error("请先登录", $this->_server ( 'HTTP_REFERER'));
           exit();
        }
        $uid=$this->uid;

        $adlist=M("member_address")->where("uid={$this->uid}")->order("'default' desc")->select();
  		if (!$adlist) {
            $this->error('请先设置收货', '/member/memberaddress');
            die;
        }
        $this->assign("adlist",$adlist);

        $carids=substr($_POST["carids"],2);
        $this->assign("carids",$carids);
        $map.="c.uid=".$uid;
        $map.=" and c.type=1";
        $map.=" and c.status=1";
        $map.=" and c.id in (".$carids.")";

        $list=M("car c")->field('c.num,m.id,title,art_img,art_jiage')->where($map)->join("lzh_market m ON m.id=c.gid")->select();
        if(!$list){
        	 $this->error("数据错误请重新下单！", $this->_server ( 'HTTP_REFERER'));
           exit();
        }
        $zongjia=0;
        foreach ($list as $k => $v) {
            //$list[$k]["good"]["type_name"]=M("article_category")->where("id=".$market["type_id"])->getField("type_name");
            $list[$k]["art_zj"]=floatval($v["art_jiage"])*intval($v["num"]);
    		$zongjia+=$list[$k]["art_zj"];
        }
        $this->assign("zongjia",$zongjia);
        $this->assign("list",$list);

        $memberinfo=M('members')->find($this->uid);

        $jsons['kyjifen'] = intval($memberinfo["credits"]);


        if($jsons['kyjifen']>=20){
        	$jsons['dixian'] = floor(intval($memberinfo["credits"])/20)/100;
            if($jsons['dixian']>=$zongjia){
                $jsons['dixian']=$zongjia;
            }
        	$jsons["shiyong"]=$jsons['dixian']*2000;
        }else{
        	$jsons['dixian'] =0;
        	$jsons["shiyong"]=0;
        }
        $this->assign("jsons",$jsons);
       	$this->assign("zjf",round(($zongjia-$jsons['dixian']),2));
        //round($num,2);
        // var_dump($zongjia);
        // var_dump($jsons['dixian']);
        // var_dump($zongjia-$jsons['dixian']);

        $this->display();
    }

    public function car_price(){
       	if(!$this->uid) {
           $jsons['msg']="请先登录";
           $jsons['stats']=2;
           outJson($jsons);
        }
        $uid=$this->uid;

        $carids=$_POST["carids"];
        $map.="c.uid=".$uid;
        $map.=" and c.type=1";
        $map.=" and c.status=1";
        $map.=" and c.id in (".$carids.")";

        $list=M("car c")->field('c.num,art_jiage')->where($map)->join("lzh_market m ON m.id=c.gid")->select();
        $zongjia=0;

        foreach ($list as $k => $v) {
            $art_zj+=floatval($v["art_jiage"])*intval($v["num"]);
            $zongjia=$art_zj;
        }

        outJson($zongjia);
    }

    public function do_car_num(){

    	$map["uid"]=$this->uid;
        $data["num"]=$_POST["num"];
        $map["id"]=$_POST["id"];

        M("car")->where($map)->save($data);


        $mapc["c.uid"]=$this->uid;
        $mapc["c.id"]=$_POST["id"];

        $cars=M("car c")->field('c.num,m.id,title,art_img,art_jiage')->where($mapc)->join("lzh_market m ON m.id=c.gid")->find();

        $zongjia=floatval($cars["art_jiage"])*intval($cars["num"]);


        outJson($zongjia);


    }
    public function car_del(){

        $mapc["uid"]=$this->uid;
        $mapc["id"]=$_REQUEST["id"];

        $res=M("car")->where($mapc)->delete();


        $uid=$this->uid;
        $map["uid"]=$uid;
        $map["type"]=1;
        $map["status"]=1;
        $map["fangshi"]=1;
        $list=M("car")->where($map)->select();

        if(!$list){
            $this->error('购物车为空，快去购物吧！', '/shop/index');
            die;
        }
        $zongjia=0;
        foreach ($list as $k => $v) {
            $market=M("market")->where("id=".$v["gid"])->find();
            $list[$k]["good"]["type_name"]=M("article_category")->where("id=".$market["type_id"])->getField("type_name");
            $list[$k]["good"]["title"]=$market["title"];
            $list[$k]["good"]["id"]=$market["id"];
            $list[$k]["good"]["art_img"]=$market["art_img"];
            $list[$k]["good"]["art_jiage"]=$market["art_jiage"];
            $list[$k]["good"]["art_zj"]=floatval($market["art_jiage"])*intval($v["num"]);
            //$zongjia+=$list[$k]["good"]["art_zj"];
        }
        //$this->assign("zongjia",$zongjia);
        $this->assign("list",$list);
        $this->display("car_list");

    }
    public function post_order(){
    	if (!$this->uid) {
           $jsons['msg']="请先登录";
           $jsons['stats']=2;
           outJson($jsons);
        }
        $uid=$this->uid;
        $order=M("order");
    	$carids=$_POST["carids"];
    	$address=$_POST["address"];

    	$adinfo=M("member_address")->where("id=".$address)->find();



    	$savedata['ordernums']=sprintf('%s-%s-%s', 'SDD', $uid,time());

    	$savedata['uid'] = $this->uid;
		$savedata['jine'] =$_POST['jine'];
		$savedata['jifen'] =$_POST['jifen'];
		$savedata['carid'] = $carids;
		$savedata['add_time'] = time();
		$savedata['address'] = $adinfo["province"].$adinfo["city"].$adinfo["district"].$adinfo["address"];
		$savedata['cell_phone'] = $adinfo["main_phone"];
		$savedata['real_name'] = $adinfo["name"];
		$savedata['liuyan'] = $_POST["liuyan"];
		$savedata['type'] = "1";
		$savedata['action'] = "3";
		$savedata['add_ip'] = get_client_ip();
		//$savedata['pay_way'] =$_POST["pay_way"];

        if($savedata["jine"]==0){
            $savedata['action'] = 0;
            $savedata['pay_time'] = time();
            $alljifen=1;
            $savedata['pay_way'] = 3;
        }else{
            $alljifen=0;
            $savedata['pay_way'] =$_POST["pay_way"];
        }


        if(empty($savedata["address"])){
            $jsons['msg'] ="地址信息未填写！";
            $jsons['status'] = '0';
            outJson($jsons);
        }

        $rjifen=true;
        if($savedata["jifen"]>0){
            $rjifen=memberCreditsLog($this->uid,8,-intval($savedata["jifen"]),"购买商品减去积分".$jifen);
        }
        M()->startTrans(); //rollback
        if($rjifen){


    		$newid = $order->add($savedata);

    		$cdata["status"]=2;

        	$map.="uid=".$uid;
            $map.=" and id in (".$carids.")";
    		$cars=M("car")->where($map)->save($cdata);


            $mk=true;
            $glist=M("car")->where($map)->select();
            foreach ($glist as $k => $v) {
                $mk=M('market')->where("id=".$v["gid"])->setDec('art_writer',$v['num']);

                $mkinfo=M('market')->where("id=".$v["gid"])->find();
                if($mkinfo["isyhq"]==1&&$savedata['pay_way']==3){
                    //$addsign['money_type']=4;
                    for ($i=0; $i < $v['num']; $i++) {
                         $pubData = array(

                         'uid' => $v["uid"],
                         'add_time'=> strtotime(date('Y-m-d 00:00:00')),

                         'money_bonus' =>$mkinfo["jine"],
                         'type' =>4,

                         'start_time' => strtotime(date('Y-m-d 00:00:00')),

                         'end_time'   => strtotime(date("Y-m-d 23:59:59", strtotime("+".$mkinfo["youxiaoqi"]." months", strtotime(date("Y-m-d"))))),

                         'bonus_invest_min' =>$mkinfo["bonus_invest_min"],

                         );
                         $rs = M('member_bonus')->add($pubData);
                    }
                    $order->where("id=".$newid)->save(array("action"=>1));
                }
            }

    		if($newid&&$cars&&$mk){
    			M()->commit();
           		$jsons['msg']="操作成功";
    	        $jsons['status']=1;
    	        $jsons['ordernums']=$savedata['ordernums'];
    	        $jsons['pay_way']=$savedata['pay_way'];

    		}else{
    			M()->rollback();
    			$jsons['msg']="操作失败";
                $jsons['status']=0;
    		}

    		outJson($jsons);

        }else{
             $jsons['msg'] ="抵现积分不足！";
             $jsons['status'] = '0';
             outJson($jsons);
        }
    }

    public function weixin(){
    	$uid=$this->uid;
        $id=$_REQUEST["id"];

        $map["uid"]=$uid;
        $map["ordernums"]=$id;
        $order=M('order')->where($map)->find();

    	//var_dump($order);exit();
    	if($order["action"]!=3){
    		$this->error("订单状态错误", $this->_server('HTTP_REFERER'));
            exit();
    	}
    	if($order["pay_way"]!=2){
    		$this->error("请选择支付宝完成支付", $this->_server('HTTP_REFERER'));
            exit();
    	}


        $cars=M("car")->field('num,gid')->where("id in (".$order["carid"].")")->select();
        $goods="";
        foreach ($cars as $k => $v) {
            $markets=M("market")->where("id=".$v["gid"])->find();
            //$goods.=$markets["title"]."x".$v["num"]."/".$markets["art_jiage"];
            if($k<2){
                $goods.=$markets["title"]."x".$v["num"]."/".$markets["art_jiage"];
            }
        }

    	$data["name"]=$goods;
    	$data["ordernums"]=$order["ordernums"];
    	$data["money"]=$order["jine"]*100;
    	//$data["pid"]=$order["gid"];

    	vendor('Weixin.natives');

        $natives = new natives();
        $a=$natives->wxewm($data);


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
        $order=M('order')->where($map)->find();

        //var_dump($order);exit();
        if($order["action"]!=3){
            $this->error("订单状态错误", $this->_server('HTTP_REFERER'));
            exit();
        }
        if($order["pay_way"]!=1){
            $this->error("请选择微信完成支付", $this->_server('HTTP_REFERER'));
            exit();
        }

        $cars=M("car")->field('num,gid')->where("id in (".$order["carid"].")")->select();
        $goods="";
        foreach ($cars as $k => $v) {
        	$markets=M("market")->where("id=".$v["gid"])->find();
        	$goods.=$markets["title"]."x".$v["num"]."/".$markets["art_jiage"];
        }
        $data['WIDout_trade_no']=$order["ordernums"];
        //订单名称，必填
        $data['WIDsubject']="海鲜商城购买";
        //付款金额，必填
        $data['WIDtotal_amount']=$order["jine"];
        //商品描述，可空
        $data['WIDbody']=$goods;

        $pagepay = new pagepay();
        $pagepay->post_order($data);
    }

    public function shop_is_order1(){
		if(!isset($_GET['addid'])|| empty($_GET['addid'])){
			$va=M("member_address as a")->where("a.uid={$this->uid} and a.default=1") ->find();
		}else{
			$va=M("member_address as a")->where("a.id={$_GET['addid']}") ->find();
		}
		$this->assign("va",$va);
		$id = intval($_GET['id']);
		$vo = M('market')->find($id);  //积分商品信息
		$vminfo =getMinfo($this->uid,true);
		$model=M('member_info');
		$vou = $model->find($this->uid);

		if(!is_array($vou)) $model->add(array('uid'=>$this->uid));
		else $this->assign('vou',$vou);

		$this->assign("memberinfo", M('members')->find($this->uid));
		$this->assign("vminfo",$vminfo);  // 会员信息
		$this->assign("vo",$vo);
		$this->assign("unm",$_GET['sun']);

    	$this->display();

    }
    public function shop_is_order(){
		if(!isset($_GET['addid'])|| empty($_GET['addid'])){
			$va=M("member_address as a")->where("a.uid={$this->uid} and a.default=1") ->find();
		}else{
			$va=M("member_address as a")->where("a.id={$_GET['addid']}") ->find();
		}
		$this->assign("va",$va);
		$id = intval($_GET['id']);
		$vo = M('market')->find($id);  //积分商品信息
		$vminfo =getMinfo($this->uid,true);
		$model=M('member_info');
		$vou = $model->find($this->uid);
		$memberinfo=M('members')->find($this->uid);

		if(!is_array($vou)) $model->add(array('uid'=>$this->uid));
		else $this->assign('vou',$vou);

		$unm=$_GET['sun'] ;
		$jifen=$vo['art_jifen']*$unm;

		$yue=0;
		if($jifen>$memberinfo["credits"]){
			$yue=($jifen-$memberinfo["credits"])/$this->glo["market_bl"];
		}
		$this->assign("yue",round($yue,2));
		$this->assign("memberinfo",$memberinfo);
		$this->assign("vminfo",$vminfo);  // 会员信息
		$this->assign("vo",$vo);
		$this->assign("unm",$unm);

    	$this->display();

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
// 	public function post_order(){

//         // 表单令牌验证
//         if(!M("members")->autoCheckToken($_POST)) {
//             $this->error("禁止重复提交表单", $this->_server ( 'HTTP_REFERER' ));
//             exit();
//         }

// 		$savedata = textPost($_POST);
// 		if($savedata["num"]<=0){
// 		      $this->error("请输入正确的数值");die;

// 		}
// 		$vminfo =getMinfo($this->uid,true);

//         if ($vminfo['pin_pass'] != md5($savedata["zhifu"])) {
//            $this->error("支付密码错误");die;
//         }


// 		$vo = M('market')->find($savedata["gid"]);  //积分商品信息
//         if($vo["art_writer"] <= 0){
//             $this->error("商品库存不足，请选择其他商品！",__APP__."/shop/shop_show?id=".$savedata["gid"]);
//         }


// 		$jine=$savedata["num"]*$vo["art_jiage"];
// 		$jifen=intval($savedata["num"])*intval($vo["art_jifen"]);

// 		if($vminfo["credits"]<$jifen){
// 			$jine=round(($jifen-$vminfo["credits"])/$this->glo["market_bl"],2);
// 			$jifen=$vminfo["credits"];

// 			if(($vminfo["account_money"]+$vminfo["back_money"])<$jine) $this->error("您的余额不足，请选择其他商品或充值！",__APP__."/maket/detail?id=".$savedata["gid"]);

// 			//$this->error("您的积分不足，请选择其他商品！",__APP__."/shop/shop_show?id=".$savedata["gid"]);
// 		} 
// 		$savedata['uid'] = $this->uid;
// 		$savedata['jine'] = $jine;
// 		$savedata['jifen'] = $jifen;
// 		$savedata['add_time'] = time();
// 		$savedata['add_ip'] = get_client_ip();

// 		$newid = M('order')->add($savedata);
// 		if(!$newid>0){
// 			$this->error('提交失败，请确认填入数据正确');
// 			exit;
// 		}
// 		if($jine>0){
// 			memberMoneyLog($this->uid,37,-($jine),"购买商品减去金额".$jine,'0','');  //  商品购买  人民币消费记录
// 		}
// 		if($jifen>0){
// 			memberCreditsLog($this->uid,8,-intval($jifen),"购买商品减去积分".$jifen);
// 		}
// 		//减少库存
//         $vo["art_writer"] -= $savedata["nums"];
//         $Market = M("market");
//         $Market->where("id={$vo['id']}")->save($vo);

// ///shop/shop_show?id=".$savedata["gid"]
// 		$this->success('兑换成功！',__APP__."/member/shoporder");

// 	}

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
