<?php
// 本类由系统自动生成，仅供测试用途
class MaketAction extends HCommonAction {

    public function index(){
		$_REQUEST["pid"]=$_REQUEST["pid"]==0?"0":$_REQUEST["pid"];
		$_REQUEST["jifen"]=$_REQUEST["jifen"]==0?"0":$_REQUEST["jifen"];
		$map=array();
		$map['art_set'] = 0;
		if($_REQUEST["pid"]!=0){
			$map["type_id"]=$_REQUEST["pid"];
		}
		if($_REQUEST["jifen"]==1){
			$map["art_jifen"]=array('between',array('0','1000'));
		}elseif($_REQUEST['jifen']==2){
			$map["art_jifen"]=array('between',array('1001','5000'));
		}elseif($_REQUEST['jifen']==3){
			$map["art_jifen"]=array('between',array('5001','10000'));
		}elseif($_REQUEST['jifen']==4){
			$map["art_jifen"]=array('between',array('10001','50000'));
		}elseif($_REQUEST['jifen']==5){
			$map["art_jifen"]=array('between',array('50001','100000'));
		}elseif($_REQUEST['jifen']==6){
			$map["art_jifen"]=array('between',array('100001','10000000'));
		}

		//分页处理
		import("ORG.Util.Page");
		$count = M('market')->where($map)->count('id');
		$this->assign("count", $count);
		$p = new Page($count, 20);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		$field= '*';
		$list = M('market')->field($field)->where($map)->limit($Lsql)->order("id DESC")->select();		
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
		unset($map);
		$map['parent_id'] = 490;
		$menuList= M('article_category')->where($map)->order("sort_order desc")->select();
		$this->assign("menuList",$menuList);
		$this->assign("jifen",$_REQUEST["jifen"]==0?"0":$_REQUEST["jifen"]);
		$this->assign("pid",$_REQUEST["pid"]==0?"0":$_REQUEST["pid"]);				
        $this->display();
	}

	public function detail(){
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

	public function order(){
		if(!$this->uid) $this->error("请先登陆",__APP__."/member/common/login");
		$id = intval($_GET['id']);
		$vo = M('market')->find($id);  //积分商品信息
		$vminfo =getMinfo($this->uid,true);
		$model=M('member_info');
		$vou = $model->find($this->uid);
		if(!is_array($vou)) $model->add(array('uid'=>$this->uid));
		else $this->assign('vou',$vou);
		$this->assign("memberinfo", M('members')->find($this->uid));
		//if(($vminfo["account_money"]+$vminfo["back_money"])<$vo["art_jiage"]) $this->error("您的余额不足，请充值！",__APP__."/maket/detail?id=".$id);
		if($vminfo["credits"]<$vo["art_jifen"]) $this->error("您的积分不足，请继续努力！",__APP__."/maket/detail?id=".$id);
		$this->assign("vminfo",$vminfo);  // 会员信息
		$this->assign("vo",$vo);
		$this->display();
	}

	public function post_order(){
		$savedata = textPost($_POST);
		$vo = M('market')->find($savedata["gid"]);  //积分商品信息
		$vminfo =getMinfo($this->uid,true);
		$jine=$savedata["num"]*$vo["art_jiage"];
		$jifen=intval($savedata["num"])*intval($vo["art_jifen"]);
		//if(($vminfo["account_money"]+$vminfo["back_money"])<$jine) $this->error("您的余额不足，请选择其他商品或充值！",__APP__."/maket/detail?id=".$savedata["gid"]);
		if($vminfo["credits"]<$jifen) $this->error("您的积分不足，请选择其他商品！",__APP__."/maket/detail?id=".$savedata["gid"]);
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
		$this->success('订单提交成功！',__APP__."/maket/detail?id=".$savedata["gid"]);
		//$this->display();
	}


	public function staging(){
		$this->display();
	}
	public function getmoney(){
		$price  = isset($_POST['txt_price']) ? $_POST['txt_price'] : 0.00;
		$shoufu = isset($_POST['txt_shoufu']) ? $_POST['txt_shoufu'] : 0.00;
		$yueshu = isset($_POST['txt_yueshu']) ? $_POST['txt_yueshu'] : 0;
		$rate = 10;

        $monthData['money'] = bcsub($price, $shoufu,2);
        $monthData['year_apr'] = $rate;
        $monthData['duration'] = intval($yueshu);
        $repay_detail = equalmonth($monthData);
        $monthData['type'] = "all";
        $repay_all = equalmonth($monthData);
        $rsArr['repayment_money'] = $repay_detail[0]['repayment_money'];
        $rsArr['price'] = $price;
        $rsArr['fuwumoney'] = $repay_all['repayment_money'] - $price;
        $rsArr['yueshu'] = $yueshu ;
        if(isset($_POST['action']) && $_POST['action'] == 'get') {echo json_encode($rsArr);die;}

		if(!$this->uid) ajaxmsg("请先登陆",0);
		$link = isset($_POST['txt_link']) ? $_POST['txt_link'] : '';
		$desc = isset($_POST['txt_desc']) ? $_POST['txt_desc'] : '';
		$msg = '商品链接：<a hfef="'.$link.'" target="_blank">'.$link.'</a><br>';
		$msg .= '商品价格：'.$price.'<br>';
		$msg .= '首付金额：'.$shoufu.'<br>';
		$msg .= '分期月数：'.$yueshu.'<br>';
		$msg .= '商品备注：'.$desc.'<br>';
		$msg .= '分期利率：'.$rate.'%<br>';
		$msg .= '月付金额：'.$rsArr['repayment_money'].'%<br>';

		if(!filter_var($link,FILTER_VALIDATE_URL)) ajaxmsg('请正确填写商品链接！',0);
		if($price<=0) ajaxmsg('请正确填写商品价格！',0);
		if($shoufu<0) ajaxmsg('请正确填写首付金额！',0);
		if($yueshu<=0) ajaxmsg('请正确填写分期月数！',0);
		if(strlen($desc)<4) ajaxmsg('请正确填写商品备注，备注必须超过4个字符！',0);

		$uInfo = M('members')->find($this->uid);
        $addData = array(
			'type'     => 3,
			'name'     => $uInfo['user_name'],
			'contact'  => !empty($uInfo['user_phone']) ? $uInfo['user_phone'] : '&nbsp;',
			'msg'      => $msg,
			'ip'       => get_client_ip(),
			'add_time' => time(),
			'status'   => 0
        	);
        $rs = M('feedback')->add($addData);
        if($rs){
        	ajaxmsg("提交成功，网站工作人员将会及时联系您！",1);
        }else{
        	ajaxmsg("提交失败，请稍后重试！",0);
        }
	}
}
