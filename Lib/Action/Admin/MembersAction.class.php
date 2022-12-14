<?php
// 全局设置
class MembersAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		
		
		$map=array();
		$recommend_id = intval(@$_REQUEST['recommend_id']);
    	if($recommend_id>0){
    		$map['m.recommend_id'] = $recommend_id;	
			$search['recommend_id'] = $recommend_id;	
    	}
		if($_REQUEST['uname']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		if($_REQUEST['realname']){
			$map['mi.real_name'] = urldecode($_REQUEST['realname']);
			$search['realname'] = $map['mi.real_name'];	
		}
		if($_REQUEST['user_phone']){
			$map['m.user_phone'] = array("like","%".urldecode($_REQUEST['user_phone'])."%");
			$search['user_phone'] =urldecode($_REQUEST['user_phone']);	
		}
		if($_REQUEST['is_vip']=='yes'){
			$map['m.user_leve'] = 1;
			$map['m.time_limit'] = array('gt',time());
			$search['is_vip'] = 'yes';	
		}elseif($_REQUEST['is_vip']=='no'){
			$map['_string'] = 'm.user_leve=0 ';
			$search['is_vip'] = 'no';	
		}
		if($_REQUEST['is_transfer']=='yes'){
			$map['m.is_transfer'] = 1;
		}elseif($_REQUEST['is_transfer']=='no'){
			$map['m.is_transfer'] = 0;
		}
			if($_REQUEST['customer_name']){
				$map['m.customer_id'] = $_REQUEST['customer_id'];
				$search['customer_id'] = $map['m.customer_id'];	
				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	
			}
			
			if($_REQUEST['customer_name']){
				$cusname = urldecode($_REQUEST['customer_name']);
				$kfid = M('ausers')->getFieldByUserName($cusname,'id');
				$map['m.customer_id'] = $kfid;
				$search['customer_name'] = $cusname;	
				$search['customer_id'] = $kfid;	
			}
		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['lx']) && !empty($_REQUEST['money'])){
			if($_REQUEST['lx']=='mm.money_freeze'){
                $map['_string'] = '(mm.money_freeze+mm.yubi_freeze)>'.$_REQUEST['money'];
            }else{
                $map[$_REQUEST['lx']] = array($_REQUEST['bj'],$_REQUEST['money']);
            }
			$search['bj'] = $_REQUEST['bj'];	
			$search['lx'] = $_REQUEST['lx'];	
			$search['money'] = $_REQUEST['money'];	
		}
		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['m.reg_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['m.reg_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['m.reg_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		//分页处理
		import("ORG.Util.Page");
		$pre = C("DB_PREFIX");
		$count = M('members m')->join("{$pre}member_info mi on m.id=mi.uid")->join("{$pre}member_money mm on mm.uid = m.id")->where($map)->count('m.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
//		$field= 'm.id,m.user_phone,m.reg_time,m.reg_ip,m.credits,m.user_name,m.customer_name,m.user_leve,m.time_limit,m.user_email,m.user_leve';
		$list = M('members m')->field($field)->join("{$pre}member_info mi on m.id=mi.uid")->join("{$pre}member_money mm on mm.uid = m.id")->where($map)->limit($Lsql)->order('m.id DESC')->select();
		
		$list=$this->_listFilter($list);
		foreach($list as $key=>$val){
			$vx=array();
			$vx = M('member_info')->where("uid=".$val["id"])->find();
			$val["real_name"]=$vx["real_name"];
			$vx=array();
			$vx = M('member_money')->where("uid=".$val["id"])->find();
			$val["member_address"] = M('member_address')->where("uid=".$val["id"]." and lzh_member_address.default=1")->find();
			$vxx=array();
			$vxx = M('member_login')->where("uid=".$val["id"])->order("id desc")->find();
			$val["last_time"]=$vxx["add_time"];
			$val["last_ip"]=$vxx["ip"];
// 		     print_r($val);
// 		 echo "<br>";
			//累计充值				
			$val["leijichongzhi"] = M('member_payonline')->where("uid=".$val["uid"]." and status=1") ->sum("money");
			//投资项目个数 *
			$val["tzxmgs"] = M('borrow_investor')->where("investor_uid=".$val["uid"]) ->count("id");
		    //投资项目总额
			$ysmoney = M('ys_order')->where("buid=".$val["uid"]) ->sum("money");
			$val["tzxmze"] = M('borrow_investor')->where("investor_uid=".$val["uid"]) ->sum("investor_capital")+$ysmoney;


			//项目分红个数
			$val["xmfxgs"] = M('investor_detail')->where("investor_uid=".$val["uid"]) ->count("id");
			
			//项目分红总额
			$val["xmfhze"] = M('investor_detail')->where("investor_uid=".$val["uid"]) ->sum("receive_interest");
			
			   
			$val["account_money"]=$vx["account_money"]+$vx["back_money"];
			$val["money_freeze"]=$vx["money_freeze"];
			$val["money_collect"]=$vx["money_collect"];
			$val["money_experience"]=$vx["money_experience"];
                        $val["all_money"] = getFloatvalue($val['account_money']+$val['money_collect']+$val['money_freeze'],2);
			$lists[]=$val;
//			var_dump(M('member_address')->getlastsql());die;
		}
		//print_r($lists);
		//exit;
		
                $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
                $this->assign("lx", array("mm.account_money"=>'充值可用余额',"mm.back_money"=>'回款可用余额',"mm.money_freeze"=>'冻结金额',"mm.money_collect"=>'待收金额'));
                $this->assign("list", $lists);
                $this->assign("pagebar", $page);
                $this->assign("search", $search);
                $this->assign("query", http_build_query($search));
                $this->display();
    }
	    public function reclist(){
    	$map=array();
    		
    			
		if($_REQUEST['uname']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		if($_REQUEST['realname']){
			$map['mi.real_name'] = urldecode($_REQUEST['realname']);
			$search['realname'] = $map['mi.real_name'];	
		}
		if($_REQUEST['is_vip']=='yes'){
			$map['m.user_leve'] = 1;
			//$map['m.time_limit'] = array('gt',time());
			$search['is_vip'] = 'yes';	
		}elseif($_REQUEST['is_vip']=='no'){
			$map['_string'] = 'm.user_leve=0 ';
			$search['is_vip'] = 'no';	
		}
		if($_REQUEST['is_transfer']=='yes'){
			$map['m.is_transfer'] = 1;
		}elseif($_REQUEST['is_transfer']=='no'){
			$map['m.is_transfer'] = 0;
		}
			if($_REQUEST['customer_name']){
				$map['m.customer_id'] = $_REQUEST['customer_id'];
				$search['customer_id'] = $map['m.customer_id'];	
				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	
			}
			
			if($_REQUEST['customer_name']){
				$cusname = urldecode($_REQUEST['customer_name']);
				$kfid = M('ausers')->getFieldByUserName($cusname,'id');
				$map['m.customer_id'] = $kfid;
				$search['customer_name'] = $cusname;	
				$search['customer_id'] = $kfid;	
			}
		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['lx']) && !empty($_REQUEST['money'])){
			$map[$_REQUEST['lx']] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['lx'] = $_REQUEST['lx'];	
			$search['money'] = $_REQUEST['money'];	
		}
		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['m.reg_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['m.reg_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['m.reg_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		$recList = M('members m')->field('m.recommend_id as r_id')->group('m.recommend_id')->having('count(m.id)>0')->where('m.recommend_id >0')->order('count(m.id) desc')->select(false);
		$map['id'] = array('exp',"in($recList)");
		$search['isrec'] = 1;
		//分页处理
		import("ORG.Util.Page");
		$count = M('members m')->where($map)->count('m.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		
		$field= 'm.id,m.user_phone,m.reg_time,m.credits,m.user_name,m.customer_name,m.user_leve,m.time_limit,m.user_email,m.user_leve';
		
		$list = M('members m')->field($field)->where($map)->limit($Lsql)->order('m.id DESC')->select();
		$list=$this->_listFilter($list);
		foreach($list as $key=>$val){
			$vx=array();
			$vx = M('member_info')->where("uid=".$val["id"])->find();
			$val["real_name"]=$vx["real_name"];
			$vx=array();
			$vx = M('member_money')->where("uid=".$val["id"])->find();
			//print_r($vx);
			//echo "<br>";
			$val["account_money"]=$vx["account_money"]+$vx["back_money"];
			$val["money_freeze"]=$vx["money_freeze"];
			$val["money_collect"]=$vx["money_collect"];
			$lists[]=$val;
		}
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("lx", array("mm.account_money"=>'充值可用余额',"mm.back_money"=>'回款可用余额',"mm.money_freeze"=>'冻结金额',"mm.money_collect"=>'待收金额'));
        $this->assign("list", $lists);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
        $this->assign("query", http_build_query($search));
        $this->display();
    }
	public function auto(){
		$pre = c("DB_PREFIX");
		$this->assign("all",M('Auto_borrow mm')->join("{$pre}member_money m ON mm.uid=m.uid")->where("mm.status=1 and m.account_money>=50")->count());
		$list = M('auto_borrow')->field(true)->order('yongdao asc,id asc')->select();
		$list=$this->_listFilter($list);
		$timelimit_status=array('不限制','按天到期还款','按月等额本息','按季分期还款','每月还息到期还本','一次付清');
		foreach($list as $key=>$val){
			$vx=array();
			$vx = M('member_info')->where("uid=".$val["uid"])->find();
			
			$val["real_name"]=$vx["real_name"];
			$vx=array();
			$vx = M('members')->where("id=".$val["uid"])->find();
			$val["user_name"]=$vx["user_name"];
			$vx=array();
			$vx = M('member_money')->where("uid=".$val["uid"])->find();
			//print_r($vx);
			$val["account_money"]=$vx["account_money"];
			
			$val["repayment_type"]=$timelimit_status[$val["repayment_type"]];
			$lists[]=$val;
		}
		  $this->assign("list", $lists);
		  $this->display();	
	}
	
	
	public function export(){
		import("ORG.Io.Excel");
		$map=array();
		$recommend_id = intval(@$_REQUEST['recommend_id']);
    	if($recommend_id>0){
    		$map['m.recommend_id'] = $recommend_id;	
			$search['recommend_id'] = $recommend_id;	
    	}
		if($_REQUEST['uname']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		if($_REQUEST['realname']){
			$map['mi.real_name'] = urldecode($_REQUEST['realname']);
			$search['realname'] = $map['mi.real_name'];	
		}
		if($_REQUEST['is_vip']=='yes'){
			$map['m.user_leve'] = 1;
			//$map['m.time_limit'] = array('gt',time());
			$search['is_vip'] = 'yes';	
		}elseif($_REQUEST['is_vip']=='no'){
			$map['_string'] = 'm.user_leve=0 ';
			$search['is_vip'] = 'no';	
		}
		if($_REQUEST['is_transfer']=='yes'){
			$map['m.is_transfer'] = 1;
		}elseif($_REQUEST['is_transfer']=='no'){
			$map['m.is_transfer'] = 0;
		}
		
			if($_REQUEST['customer_name']){
				$map['m.customer_id'] = $_REQUEST['customer_id'];
				$search['customer_id'] = $map['m.customer_id'];	
				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	
			}
			
			if($_REQUEST['customer_name']){
				$cusname = urldecode($_REQUEST['customer_name']);
				$kfid = M('ausers')->getFieldByUserName($cusname,'id');
				$map['m.customer_id'] = $kfid;
				$search['customer_name'] = $cusname;	
				$search['customer_id'] = $kfid;	
			}
		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['lx']) && !empty($_REQUEST['money'])){
            if($_REQUEST['lx']=='mm.money_freeze'){
                $map['_string'] = '(mm.money_freeze+mm.yubi_freeze)>'.$_REQUEST['money'];
            }else{
                $map[$_REQUEST['lx']] = array($_REQUEST['bj'],$_REQUEST['money']);
            }

//			$map[$_REQUEST['lx']] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['lx'] = $_REQUEST['lx'];	
			$search['money'] = $_REQUEST['money'];	
		}
		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['m.reg_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['m.reg_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['m.reg_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		$field= 'm.id,m.user_phone,m.reg_time,m.credits,m.user_name,m.customer_name,m.user_leve,m.time_limit,mi.real_name,mm.money_freeze,mm.money_collect,mm.yubi_freeze,mm.yubi,(mm.account_money+mm.back_money) account_money,m.user_email,m.user_leve';
		$list = M('members m')->field($field)->join("{$this->pre}member_money mm ON mm.uid=m.id")->join("{$this->pre}member_info mi ON mi.uid=m.id")->where($map)->order('m.id DESC')->select();
		
		$row=array();
		$row[0]=array('序号','用户ID','用户名','用户电话','真实姓名','会员类型','可用余额','冻结金额','鱼币','冻结鱼币','待收金额','注册时间');
		$i=1;
		foreach($list as $v){
				$row[$i]['i'] = $i;
				$row[$i]['uid'] = $v['id'];
				$row[$i]['card_num'] = $v['user_name'];
				$row[$i]['card_num2'] = $v['user_phone'];
				$row[$i]['card_pass'] = $v['real_name'];
				$row[$i]['card_mianfei'] = getLeveName($v['credits'],2);
				$row[$i]['card_second_fee'] = $v['account_money'];
				$row[$i]['card_mianfei1'] =$v['money_freeze'];
				$row[$i]['card_mianfei3'] =$v['yubi'];
				$row[$i]['card_mianfei4'] =$v['yubi_freeze'];
				$row[$i]['card_mianfei2'] = $v["money_collect"];
				$row[$i]['card_timelimit'] = date("Y-m-d",$v['reg_time']);
				$i++;
		}
		//print_r($row);
		//exit;
		$xls = new Excel_XML('UTF-8', false, 'datalist');
		$xls->addArray($row);
		$xls->generateXML("datalistcard");
	}
    public function edit() {
        $model = D(ucfirst($this->getActionName()));
		setBackUrl();
        $id = intval($_REQUEST['id']);
        $vo = $model->find($id);

        ///////////////////////
        $vb = M('member_banks')->where("uid={$id}")->order("is_default desc")->find();
        if(!is_array($vb)){
//			M('member_banks')->add(array("uid"=>$id));
        }else{
            foreach($vb as $key=>$vbe){
                $vo[$key]=$vbe;
            }
        }
        $vo["bk_name"]=$vo["bank_name"];

		$vx = M('member_info')->join("lzh_members on lzh_member_info.uid=lzh_members.id")->where("uid={$id}")->find();
		//	var_dump(M('member_info')->getlastsql());die;
		if(!is_array($vx)){
			M('member_info')->add(array("uid"=>$id));
		}else{
			foreach($vx as $key=>$vxe){
				$vo[$key]=$vxe;
			}
		}
		
	
		$pre = c("DB_PREFIX");
    	$renzheng = m("members m")->field("m.id,m.user_leve,m.time_limit,s.id_status,s.phone_status,s.email_status,s.video_status,s.face_status")->join("{$pre}members_status s ON s.uid=m.id")->where("m.id={$id}")->find();
    	
		$this->assign('renzheng', $renzheng);

		
		//////////////////////
        $this->assign('vo', $vo);
		$this->assign("utype", C('XMEMBER_TYPE'));
		$this->assign("bank_list",C('BANK_NAME'));
        $this->display();
    }
	//获取会员资金列表
	  public function member_capital(){
      $map=array();
      $id = intval($_REQUEST['id']);
  		if($id){
  			$map['l.uid'] = $id;
  			$search['uid'] = $map['l.uid'];
  		}
  		if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');
  		//分页处理
  		import("ORG.Util.Page");
  		$count = M('member_moneylog l')->join("{$this->pre}members m ON m.id=l.uid")->join("{$this->pre}member_info n ON n.uid=l.uid")->where($map)->count('l.id');
  		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
  		$page = $p->show();
  		$Lsql = "{$p->firstRow},{$p->listRows}";
  		//分页处理

  		$field= 'l.yubi,l.freeze_yubi,l.id,l.add_time,m.user_name,l.affect_money,l.freeze_money,l.collect_money,(l.account_money+l.back_money) account_money,l.experience_money,l.target_uname,l.type,l.info,n.real_name';
  		$order = "l.id DESC";
  		$list = M('member_moneylog l')->field($field)->join("{$this->pre}members m ON m.id=l.uid")->join("{$this->pre}member_info n ON n.uid=l.uid")->where($map)->limit($Lsql)->order($order)->select();
          $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
          $this->assign("type", C('MONEY_LOG'));
          $this->assign("list", $list);
          $this->assign("pagebar", $page);
          $this->assign("search", $search);
          $this->assign("query", http_build_query($search));
          $this->display();
    }
	//添加数据
    public function doEdit() {
        $uid=$_POST["uid"];
       // var_dump($uid);die;
        $model = D(ucfirst($this->getActionName()));
        $model2 = M("member_info");
		$model3 = M("member_banks");
		$model4 = M("members_status");
//		if(!empty($_POST['riskint'])){
//			
//		}
//		$minfo=$model->field('recommend_id')->find($uid);

		$MM = $model4->field(true)->find($_POST["uid"]);
		if (!is_array($MM)) {
			$model4->add(array(
				"uid" => $_POST["uid"]
			));
		}

        $name   = C('TOKEN_NAME');
//		if($_POST['borrow_uids']&&empty($minfo['recommend_id'])){
//			$rid=explode(',',$_POST['borrow_uids'])[1];
//			if($rid!=$uid){
//				$_POST['recommend_id']=$rid;
//			}
//		}
        $code = $_SESSION[$name];
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        $_SESSION[$name] = $code;
        if (false === $model2->create()) {
            $this->error($model2->getError());
        }
        $_SESSION[$name] = $code;
		if (false === $model3->create()) {
            $this->error($model3->getError());
        }
        $_SESSION[$name] = $code;
		if (false === $model4->create()) {
            $this->error($model4->getError());
        }
        $_SESSION[$name] = $code;
		$model4->create();
		//echo $model4->getlastsql();
		$model->startTrans();
        if(!empty($model->user_pass)){
			$model->user_pass=md5($model->user_pass);
		}else{
			unset($model->user_pass);
		}
        if(!empty($model->pin_pass)){
			$model->pin_pass=md5($model->pin_pass);
		}else{
			unset($model->pin_pass);
		}
		
		$model->user_phone = $model2->cell_phone = text($_POST['user_phone']);

		$model3->add_ip = get_client_ip();
		$model3->add_time = time();
		$model3->uid=$uid;
	//	var_dump($uid);die;
		$aUser = get_admin_name();
		$kfid = $model->customer_id;
		$model->customer_name = $aUser[$kfid];
        $model->id = $_POST["uid"];

        $result = $model->save();
		$result2 = $model2->save();
        $result3 = 1;
        $bankcard = get_object_vars($model3);
        $member_banks = M("member_banks")->find($model3->id);
        if(!empty($member_banks)){
            $bankcardid = $model3->uid;
            $model3->create($bankcard);
            if($bankcardid == $model3->uid){
                $result3 = $model3->save();
            }
        }else{
            if(!(empty($_POST["bank_num"]) && empty($_POST["bank_name"]) && empty($_POST["bank_province"]) && empty($_POST["bank_city"]) && empty($_POST["bank_address"]))){
                M("member_banks")->add(array("bank_num"=>$_POST["bank_num"],"bank_name"=>$_POST["bank_name"],"bank_province"=>$_POST["bank_province"],"bank_city"=>$_POST["bank_city"],"bank_address"=>$_POST["bank_address"],"add_ip"=>get_client_ip(),"add_time"=>time(),"uid"=>$uid));
            }
        }
	//	var_dump(M("member_banks")->getlastsql());
		$result4 = $model4->save();
	
        if ($result !== false && $result2 !== false && $result3 !== false) { //保存成功
			$model->commit();
            //成功提示
            $this->assign('jumpUrl', __URL__."/".session('listaction'));

            $infos=$model2->where("uid=".$_POST["uid"])->find();
            $title="修改用户信息";
			$mes=json_encode($_POST);
			//$this->do_log($title,$mes);

            $this->success(L('修改成功'));
        } else {
			$model->rollback();
            //失败提示
            $this->error(L('修改失败'));
        }
    }
	
    public function info()
    {	
		if($_GET['user_name']) $search['m.user_name'] = text($_GET['user_name']);
		else $search=array();
		$list = getMemberInfoList($search,10);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
        $this->assign("search", $search);
        $this->display();
    }
	
    public function infowait()
    {	
		$Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";
		if($_GET['user_name']) $search['m.user_name'] = text($_GET['user_name']);
		else $search=array();
		$list = getMemberApplyList($search,10);
		$this->assign("aType",$Bconfig['APPLY_TYPE']);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
        $this->display();
    }
	
    public function viewinfo()
    {	
		$Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";
		$this->assign("aType",$Bconfig['APPLY_TYPE']);
		setBackUrl();
		$id = intval($_GET['id']);
		$vx = M('member_apply')->field(true)->find($id);
		$uid = $vx['uid'];
		$vo = getMemberInfoDetail($uid);
		$this->assign("vx",$vx);
		$this->assign("vo",$vo);
		$this->assign("id",$id);
        $this->display();
    }
	
	 public function initpwd()
    {	
	
		$id = intval($_GET['id']);
		$pwd['user_pass'] = md5("123456");
		$infos=M('members')->where("id={$id}")->find();
		if($infos["user_pass"]!=$pwd['user_pass']){
			$newid = M('members')->where("id={$id}")->save($pwd);
			//echo M('members')->getlastsql();
			//exit;
	        $this->assign('jumpUrl', __URL__."/index.html");
			if($newid){
				$title="重置用户密码";
				$mes="重置了用户".$infos["user_name"]."的登录密码！";
				//$this->do_log($title,$mes);
				$this->success("操作成功");
			}else{
				$this->error("操作失败");
			}
		}else{
			$title="重置用户密码";
			$mes="重置了用户".$infos["user_name"]."的登录密码！";
			//$this->do_log($title,$mes);
			$this->success("操作成功!");
		}
    }
	
	
    public function viewinfom()
    {	
		$id = intval($_GET['id']);
		$vo = getMemberInfoDetail($id);
		$this->assign("vo",$vo);
        $this->display();
    }
	public function vipedit(){
		$id = intval($_GET['id']);
		$viptype = intval($_GET['viptype']);
		$credits['user_leve'] = $viptype;
		$newid = M('members')->where("id={$id}")->save($credits);
        $this->assign('jumpUrl', __URL__."/index.html");
		if($newid) $this->success("操作成功");
		else  $this->error("操作失败");
	}
	public function doEditCredit(){
		$id = intval($_POST['id']);
		$uid = intval($_POST['uid']);
		$data['id'] = $id;
		$data['deal_info'] = text($_POST['deal_info']);
		$data['apply_status'] = intval($_POST['apply_status']);
		$data['credit_money'] = floatval($_POST['credit_money']);
		$newid = M('member_apply')->save($data);
		
		if($newid){
			//审核通过后资金授信改动
			if($data['apply_status']==1){
				$vx = M('member_apply')->field(true)->find($id);
				$umoney = M('member_money')->field(true)->find($vx['uid']);
				
				$moneyLog['uid'] = $vx['uid'];
				if($vx['apply_type']==1){
					$moneyLog['credit_limit'] = floatval($umoney['credit_limit']) + $data['credit_money'];
					$moneyLog['credit_cuse'] = floatval($umoney['credit_cuse']) + $data['credit_money'];
				}elseif($vx['apply_type']==2){
					$moneyLog['borrow_vouch_limit'] = floatval($umoney['borrow_vouch_limit']) + $data['credit_money'];
					$moneyLog['borrow_vouch_cuse'] = floatval($umoney['borrow_vouch_cuse']) + $data['credit_money'];
				}elseif($vx['apply_type']==3){
					$moneyLog['invest_vouch_limit'] = floatval($umoney['invest_vouch_limit']) + $data['credit_money'];
					$moneyLog['invest_vouch_cuse'] = floatval($umoney['invest_vouch_cuse']) + $data['credit_money'];
				}
				
				if(!is_array($umoney))	M('member_money')->add($moneyLog);
				else M('member_money')->where("uid={$vx['uid']}")->save($moneyLog);
			}//审核通过后资金授信改动
			$this->success("审核成功",__URL__."/infowait".session('listaction'));
		}else{
			$this->error("审核失败");
		}
	}
	
    public function moneyedit()
    {
		setBackUrl();
		$this->assign("money",M('member_money')->field(true)->find($_GET['id']));
		$this->assign("id",intval($_GET['id']));
		$this->display();
    }
	
    public function doMoneyEdit()
    {
		$id = intval($_POST['id']);
		$uid = $id;
		$info = text($_POST['info']);
		$done=false;
		$members=M("members")->where("id=".$uid)->find();
		if(floatval($_POST['account_money'])!=0){
			$done=memberMoneyLog($uid,71,floatval($_POST['account_money']),$info);
			if($done){
				$title="修改余额";
				$mes="把客户{".$members["user_name"]."}的可用余额变动了".$_POST["account_money"];
				//$this->do_log($title,$mes);
			}
		}
//		if(floatval($_POST['money_freeze'])!=0){
//			$done=false;
//			$done=memberMoneyLog($uid,72,floatval($_POST['money_freeze']),$info);
//		}
//		if(floatval($_POST['money_collect'])!=0){
//			$done=false;
//			$done=memberMoneyLog($uid,73,floatval($_POST['money_collect']),$info);
//		}
		if(floatval($_POST['yubi'])!=0){
			$done=false;
			$done=memberMoneyLog($uid,733,floatval($_POST['yubi']),$info);
			if($done){
				$title="修改鱼币";
				$mes="把客户{".$members["user_name"]."}的可用鱼币变动了".$_POST["yubi"];
				//$this->do_log($title,$mes);
			}
		}

		if(intval($_POST['credits'])!=0){
			$done=false;
			
			$members=M("members")->where("id=".$uid)->find();
//			var_dump($members);die;
			$credits=intval($members['credits'])+intval($_POST['credits']);
			$done=M("members")->where("id=".$uid)->save(["credits"=>$credits]);
			if($done){ 
				$zdata["uid"]=$uid;
				$zdata["type"]=10;
				$zdata["affect_credits"]=intval($_POST['credits']);
				$zdata["account_credits"]=$credits;
				$zdata["info"]="后台调整";
				$zdata["add_time"]=time();
				$zdata["add_ip"]="";
				M("member_creditslog")->add($zdata);
				if($done){
					$title="修改积分";
					$mes="把客户{".$members["user_name"]."}的积分变动了".$_POST["credits"];
					//$this->do_log($title,$mes);
				}
			}
		}
		//记录

        $this->assign('jumpUrl', __URL__."/index".session('listaction'));
		if($done) {
			if(floatval($_POST['account_money']) > 0) {
				inAccountMoney($uid,floatval($_POST['account_money']),0);	
			}elseif(floatval($_POST['account_money']) < 0){				
				useAccountMoney($uid,abs(floatval($_POST['account_money'])));
			}
			$this->success("操作成功");
		}else{
			$this->error("操作失败");
		}
    }
	public function leveedit(){
		$minfo =getMinfo(intval($_GET['id']),true);
		$this->assign("id",intval($_GET['id']));
		$this->assign("minfo",$minfo);
		$this->display();	
	}
	
	 public function doLeveEdit()
    {
		$id = intval($_POST['id']);
		$credits['credits'] = floatval($_POST['credits']);
		$newid = M('members')->where("id={$id}")->save($credits);
        $this->assign('jumpUrl', __URL__."/index".session('listaction'));
		if($newid) $this->success("操作成功");
		else  $this->error("操作失败");
    }
	
    public function creditedit()
    {
		setBackUrl();
		$this->assign("id",intval($_GET['id']));
		$this->display();
    }
	
	
	
    public function doCreditEdit()
    {
		$id = intval($_POST['id']);
		
		$umoney = M('member_money')->field(true)->find($id);
		if(intval($_POST['credit_limit'])!=0){
			$moneyLog['uid'] = $id;
			$moneyLog['credit_limit'] = floatval($umoney['credit_limit']) + floatval($_POST['credit_limit']);
			$moneyLog['credit_cuse'] = floatval($umoney['credit_cuse']) + floatval($_POST['credit_limit']);
			if(!is_array($umoney))	$newid = M('member_money')->add($moneyLog);
			else $newid = M('member_money')->where("uid={$id}")->save($moneyLog);
		}
		if(intval($_POST['borrow_vouch_limit'])!=0){
			$moneyLog=array();
			$moneyLog['uid'] = $id;
			$moneyLog['borrow_vouch_limit'] = floatval($umoney['borrow_vouch_limit']) + floatval($_POST['borrow_vouch_limit']);
			$moneyLog['borrow_vouch_cuse'] = floatval($umoney['borrow_vouch_cuse']) + floatval($_POST['borrow_vouch_limit']);
			if(!is_array($umoney) && !$newid)	$newid = M('member_money')->add($moneyLog);
			else $newid = M('member_money')->where("uid={$id}")->save($moneyLog);
		}
		if(intval($_POST['invest_vouch_limit'])!=0){
			$moneyLog=array();
			$moneyLog['uid'] = $id;
			$moneyLog['invest_vouch_limit'] = floatval($umoney['invest_vouch_limit']) + floatval($_POST['invest_vouch_limit']);
			$moneyLog['invest_vouch_cuse'] = floatval($umoney['invest_vouch_cuse']) + floatval($_POST['invest_vouch_limit']);
			if(!is_array($umoney) && !$newid)	$newid = M('member_money')->add($moneyLog);
			else $newid = M('member_money')->where("uid={$id}")->save($moneyLog);
		}
        $this->assign('jumpUrl', __URL__."/index".session('listaction'));
		if($newid) $this->success("操作成功");
		else  $this->error("操作失败");
    }
	
	
	public function _listFilter($list){
		$row=array();
		foreach($list as $key=>$v){
			($v['user_leve']==1 && $v['time_limit']>time())?$v['user_type'] = "<span style='color:red'>VIP会员</span>":$v['user_type'] = "普通会员";
			$row[$key]=$v;
		}
		return $row;
	}
	
	public function getusername(){
		$uname = M("members")->getFieldById(intval($_POST['uid']),"user_name");
		if($uname) exit(json_encode(array("uname"=>"<span style='color:green'>".$uname."</span>")));
		else exit(json_encode(array("uname"=>"<span style='color:orange'>不存在此会员</span>")));
	}
	
	 public function idcardedit() {
        $model = D(ucfirst($this->getActionName()));
		setBackUrl();
        $id = intval($_REQUEST['id']);
        $vo = $model->find($id);
		$vx = M('member_info')->where("uid={$id}")->find();
		if(!is_array($vx)){
			M('member_info')->add(array("uid"=>$id));
		}else{
			foreach($vx as $key=>$vxe){
				$vo[$key]=$vxe;
			}
		}
        $this->assign('vo', $vo);
		$this->assign("utype", C('XMEMBER_TYPE'));
        $this->display();
    }
	
	//添加身份证信息
    public function doIdcardEdit() {
        $model = D(ucfirst($this->getActionName()));
        $model2 = M("member_info");
		
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        if (false === $model2->create()) {
            $this->error($model->getError());
        }
		
		$model->startTrans();
		/////////////////////////////
		if(!empty($_FILES['imgfile']['name'])){
			$this->fix = false;
			//设置上传文件规则
			$this->saveRule = 'uniqid';
			//$this->saveRule = date("YmdHis",time()).rand(0,1000)."_".$model->id;
			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Idcard/';
			$this->thumbMaxWidth = C('IDCARD_UPLOAD_H');
			$this->thumbMaxHeight = C('IDCARD_UPLOAD_W');
			$info = $this->CUpload();
			$data['card_img'] = $info[0]['savepath'].$info[0]['savename'];
			$data['card_back_img'] = $info[1]['savepath'].$info[1]['savename'];
			
			if($data['card_img']&&$data['card_back_img']){ 
				$model2->card_img=$data['card_img'];
				$model2->card_back_img=$data['card_back_img'];
			}
		}
		///////////////////////////
		$result = $model->save();
		$result2 = $model2->save();
        //保存当前数据对象
        if ($result || $result2) { //保存成功
			$model->commit();
            //成功提示
            $this->assign('jumpUrl', __URL__."/".session('listaction'));
            $this->success(L('修改成功'));
        } else {
			$model->rollback();
            //失败提示
            $this->error(L('修改失败'));
        }
    }
	///////////////////////////////////	
		///////////////////////////////////	
	public function ajaxsearch(){
		$map = array();
		$user_name = isset($_REQUEST['user_name']) ? $_REQUEST['user_name'] : '';
		$real_name = isset($_REQUEST['real_name']) ? $_REQUEST['real_name'] : '';
		$user_phone = isset($_REQUEST['user_phone']) ? $_REQUEST['user_phone'] : '';
		
		$user_name and $map['user_name'] = $user_name;
		$real_name and $map['mi.real_name'] = urldecode($_REQUEST['real_name']);
		$user_phone and $map['user_phone'] = $user_phone;
		
		//分页处理
		import("ORG.Util.Page");
		$count = M('members m')->where($map)->count('m.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		
		$field= 'm.id,m.user_phone,m.reg_time,m.reg_ip,m.credits,m.user_name,m.customer_name,m.user_leve,m.time_limit,m.user_email,m.user_leve';
		$list = M('members m')->field($field)->join("{$this->pre}member_money mm ON mm.uid=m.id")->join("{$this->pre}member_info mi ON mi.uid=m.id")->where($map)->limit($Lsql)->order('m.id DESC')->select();
		$list=$this->_listFilter($list);
		foreach($list as $key=>$val){
			$vx=array();
			$vx = M('member_info')->where("uid=".$val["id"])->find();
			$val["real_name"]=$vx["real_name"];
			$vx=array();
			$vx = M('member_money')->where("uid=".$val["id"])->find();
			$vxx=array();
			$vxx = M('member_login')->where("uid=".$val["id"])->order("id desc")->find();
			$val["last_time"]=$vxx["add_time"];
			$val["last_ip"]=$vxx["ip"];
			//print_r($vx);
			//echo "<br>";
			$val["account_money"]=$vx["account_money"]+$vx["back_money"];
			$val["money_freeze"]=$vx["money_freeze"];
			$val["money_collect"]=$vx["money_collect"];
			$lists[]=$val;
		}
		$this->assign("list", $lists);
		$this->assign("pagebar", $page);
		$this->display();
	}
}
?>
