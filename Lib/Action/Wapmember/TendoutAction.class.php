<?php
// 本类由系统自动生成，仅供测试用途
class TendoutAction extends MCommonAction {
    
    
    
    
        public function index()
    {
        
        $strUrl = U("tendout/index").'?1=1';
        $map['investor_uid'] = $this->uid;
        $map['status'] = array('in', '1,4,5,6');
        $borrow_status = '';
        
        if (isset($_GET['borrow_name']) && !empty($_GET['borrow_name']) ) {
            $map['borrow_name'] =["like","%". $_GET['borrow_name']."%"];
            $strUrl .= '&type='.$_GET['type'];
        }
        if (isset($_GET['type']) && !empty($_GET['type']) && 0 != $_GET['type']) {
            $map['pid'] = $_GET['type'];
            $strUrl .= '&type='.$_GET['type'];
        }
        if(isset($_GET['borrow_status'])){
             $map['borrow_status']= $_GET['borrow_status'];
             $borrow_status = $_GET['borrow_status'];
        }
   $this->assign('pid', $_GET['type']);
        // if (isset($_GET['borrow_status']) && !empty($_GET['borrow_status']) && 1 == $_GET['borrow_status']) {
        //     $map['borrow_status'] = array('in', '0,2,6');
        // } elseif (isset($_GET['borrow_status']) && !empty($_GET['borrow_status']) && $_GET['borrow_status'] = 2) {
        //     $map['borrow_status'] = array('in', '7');
        // } elseif (isset($_GET['borrow_status']) && !empty($_GET['borrow_status']) && $_GET['borrow_status'] = 0) {
        // }
		$count = M('borrow_investor i') ->where($map)->join("lzh_borrow_info b ON b.id=i.borrow_id")->count('i.id');
        $list = getTenderList2($map, 15);
        //var_dump( $list['list']);die;
        $this->assign('count', $count);
        $this->assign('borrow_status', $borrow_status);
        $this->assign('strUrl', $strUrl);
        $this->assign('list', $list['list']);
        $this->assign('pagebar', $list['page']);
        $this->assign('total', $list['total_money']);
        $this->assign('num', $list['total_num']);
        $mdata = M('members')->find($this->uid);
        $this->assign('mdata', $mdata);
 //var_dump( $list );die;
        $this->display();
    }
  public function zidong()
    {
        
        $bespeak=M("bespeak i");
        //分页处理
        import("ORG.Util.Page");
        $map['bespeak_uid'] = $this->uid;
        $map['bespeak_point'] ='1.00';
        
        $size=10;
        $count =$bespeak ->where($map)->join("lzh_borrow_info b ON b.id=i.borrow_id")->count('i.id');
        $p = new Page($count, $size);
        $page = $p->show();
        $Lsql = "{$p->firstRow},{$p->listRows}";
        $field="i.*,b.borrow_name,b.borrow_img,b.borrow_status";
        $arr=array("智投中","已成功","智投失败");
        $list=$bespeak ->where($map)->field($field)->join("lzh_borrow_info b ON b.id=i.borrow_id")->order('i.add_time DESC')->limit($Lsql)->select();/**/
        foreach ($list as $k => $v) {
            $list[$k]["status"] =$arr[$v["bespeak_status"]];
        }
        $this->assign('list', $list);
        $this->assign('pagebar', $page);
        $this->assign('count', $count);
        $this->display();
    }
    
    public function zhichi(){


        $half_a_year_before = time() - $this->glo["new_user_time"]*24*60*60;
        $where = null;
        $where['investor_uid'] = array('eq',"{$this->uid}");
        $where['add_time'] = array(array('egt',"{$half_a_year_before}"),array('elt',time()));
        $where['borrow_id'] = array('neq',1);
        $newUser = count(M('borrow_investor')->where($where)->find());



        $where = null;
        $where['bespeak_uid'] = array('eq',"{$this->uid}");
        $where['add_time'] = array(array('egt',"{$half_a_year_before}"),array('elt',time()));
        $where['borrow_id'] = array('neq',1);
        $bespeakUser=count(M('bespeak')->where($where)->find());
        $newUser = max($newUser,$bespeakUser);

        if ($newUser > 0) {//老用户
             $map["new_user_only"]=array("neq","1");
             $mapc["zhitou_able"]=0;
        }else{//新用户
             $mapc["zhitou_able"]=array("neq","1");
        }
        $mapc["borrow_status"]=1;
    
    
        $mapc["start_time"]=array("gt",time());
        $mapc["borrow_uid"]=array("neq",$this->uid);
        $mapc['id'] =array('neq',1);
        $zdlist=M('borrow_info')->where($mapc)->select();

        foreach ($zdlist as $k => $v) {
            //预约已投金额
            $bespeak_invest_money = M('bespeak')->where("borrow_id={$v['id']} and bespeak_status = 1")->sum('bespeak_money');
            //非预约已投金额
            $bespeak_not_invest_money = bcsub($v["has_borrow"], $bespeak_invest_money, 2);
            //非预约金额
            $not_invest_money = bcsub($v["borrow_money"], $v["bespeak_money"], 2);
            //可投金额
            $can_invest_money = bcsub($not_invest_money, $bespeak_not_invest_money, 2);

            $zdlist[$k]["shengyu"] =$can_invest_money;
        }
        //var_dump($zdlist);
        $this->assign("zdlist",$zdlist);

        $vm = getMinfo($this->uid,'m.pin_pass,mm.account_money,mm.back_money,mm.money_experience');
        $amoney = $vm['account_money']+$vm['back_money'];
        
        $pin_pass = $vm['pin_pass'];
        $has_pin  = (empty($pin_pass)) ? "no" : "yes";
        $this->assign("has_pin",$has_pin);

        $this->assign("account_money",$amoney);

        //服务端生成随机数存入session, 分配至表单页
        $from_id = $_SESSION['from_id'] = time().mt_rand(1000, 9999);
        $this->assign("from_id",$from_id);
        $this->display();
    }

    public function detail()
    {
    	
        $id      = isset($_GET['id']) ? intval($_GET['id']) : 1;
        $pre     = C('DB_PREFIX');
        $Bconfig = C("BORROW");
        /****** 防止模拟表单提交 *********/
		$cookieTime = 15*3600;
		$cookieKey=md5(MODULE_NAME."-".time());
		cookie(strtolower(MODULE_NAME)."-invest",$cookieKey,$cookieTime);
		$this->assign("cookieKey",$cookieKey);
		/****** 防止模拟表单提交 *********/


        $this->assign("Bconfig", $Bconfig);

        if (!$this->uid) {
            $guanzhu_html = "关注";
        }

        $b = M('pro_guanzhu')->where("bid=" . $id . " and uid = {$this->uid}")->count('id');
        if ($b == 1) {
            $guanzhu_html = "已关注";
        } else {
            $guanzhu_html = "关注";
        }
        $this->assign("guanzhu_html", $guanzhu_html);
        $borrowinfo = M("borrow_info")->field(true)->find($id);
		//$investinfo = M("borrow_investor") ->where('borrow_id='.$id)->count();
//		var_dump(M("borrow_investor")->getlastsql());
//		var_dump($investinfo);die;
		    //   $this->assign("investinfo",$investinfo);
       // echo M("borrow_info")->getlastsql();exit;

        //点击
        $ip        = get_client_ip();
        $now_time  = strtotime(date('Y-m-d'));
        $hit_array = M("pro_hits")->field(true)->where('add_ip="' . $ip . '" and bid=' . $id . " and add_time>" . $now_time)->find();
        if (!$hit_array) {
            $save['bid']      = $id;
            $save['add_time'] = time();
            $save['add_ip']   = $ip;
            M("pro_hits")->data($save)->add();
            M("borrow_info")->where('id=' . $id)->setField('hits', $borrowinfo["hits"] + 1);
        }

        if (bcsub($borrowinfo['borrow_money'], $borrowinfo['has_borrow'], 2) == 0 and $borrowinfo["borrow_status"] == 2) {
            //echo bcsub($borrowinfo['borrow_money'],$borrowinfo['has_borrow'],2);
            borrowfull($id, $borrowinfo["borrow_type"]);
        }
        if (!is_array($borrowinfo) || ($borrowinfo['borrow_status'] == 0 && $this->uid != $borrowinfo['borrow_uid'])) {
            $this->error("数据有误");
        }

        $borrowinfo['biao'] = $borrowinfo['borrow_times'];
        $borrowinfo['need'] = bcsub($borrowinfo['borrow_money'], $borrowinfo['has_borrow'], 2);

        $borrowinfo['leftdays'] = getLeftTime($borrowinfo['collect_time'], 2);
        $borrowinfo['lefttime'] = $borrowinfo['start_time'] - time();
        $borrowinfo['endtimes'] = $borrowinfo['start_time'] + ($borrowinfo['collect_day']*60*60*24);

        $borrowinfo['progress']       = getFloatValue($borrowinfo['has_borrow']/$borrowinfo['borrow_money']*100,0);
        if(substr($borrowinfo['progress'], -1)==".")$borrowinfo['progress']=substr($borrowinfo['progress'],0,strlen($borrowinfo['progress'])-1); 
        $borrowinfo['vouch_progress'] = getFloatValue($borrowinfo['has_vouch'] / $borrowinfo['borrow_money'] * 100, 2);
        $borrowinfo['vouch_need']     = $borrowinfo['borrow_money'] - $borrowinfo['has_vouch'];
        $borrowinfo["borrow_img"]     = str_replace("'", "", $borrowinfo["borrow_img"]);
        if ($borrowinfo['borrow_img'] == "") {
            $borrowinfo['borrow_img'] = "UF/Uploads/borrowimg/nopic.png";
        }

        $borrow_interest_rate_arr             = explode('.', $borrowinfo['borrow_interest_rate'], 2);
        $borrowinfo['borrow_interest_rate_1'] = '00';
        $borrowinfo['borrow_interest_rate_2'] = '00';
        if (isset($borrow_interest_rate_arr[0])) {
            $borrowinfo['borrow_interest_rate_1'] = $borrow_interest_rate_arr[0];
        }

        if (isset($borrow_interest_rate_arr[1])) {
            $borrowinfo['borrow_interest_rate_2'] = $borrow_interest_rate_arr[1];
        }

        $borrowinfo['vote0'] = M('borrow_vote')->where('status = 0 and borrow_id = ' . $id)->count('id');
        $borrowinfo['vote1'] = M('borrow_vote')->where('status = 1 and borrow_id = ' . $id)->count('id');

        $this->assign("binfo", $borrowinfo);
        //此标借款利息还款相关情况
        //memberinfo
        $memberinfo                 = M("members m")->field("m.id,m.customer_name,m.customer_id,m.user_name,m.reg_time,m.credits,fi.*,mi.*")->join("{$pre}member_financial_info fi ON fi.uid = m.id")->join("{$pre}member_info mi ON mi.uid = m.id")->where("m.id={$borrowinfo['borrow_uid']}")->find();
        $areaList                   = getArea();
        $memberinfo['location']     = $areaList[$memberinfo['province']] . $areaList[$memberinfo['city']];
        $memberinfo['location_now'] = $areaList[$memberinfo['province_now']] . $areaList[$memberinfo['city_now']];
        $this->assign("minfo", $memberinfo);
        //memberinfo
        //vouch_list

        //data_list
        $data_list = M("member_data_info")->field('type,add_time,data_url,count(status) as num,sum(deal_credits) as credits')->where("uid={$borrowinfo['borrow_uid']} AND status=1")->group('id')->select();
        $this->assign("data_list", $data_list);
        //data_list

        //红包信息
		$bonus_list =  M('member_bonus')->where("uid='{$this->uid}' and status = 1 and (".time()." > start_time and ".time()." < end_time )")->field('id,money_bonus,end_time,bonus_invest_min')->select();
		$this->assign("bonus_list",$bonus_list);

		//加息券信息
		$inrate_list =  M('member_interest_rate')->where("uid='{$this->uid}' and status = 1 and (".time()." > start_time and ".time()." < end_time )")->field('id,interest_rate,end_time')->select();
		$this->assign("inrate_list",$inrate_list);


		$vm = getMinfo($this->uid,'m.pin_pass,mm.account_money,mm.back_money,mm.money_experience');
		$amoney = $vm['account_money']+$vm['back_money'];
		$pin_pass = $vm['pin_pass'];
		$has_pin  = (empty($pin_pass)) ? "no" : "yes";
		$this->assign("has_pin",$has_pin);
        $this->assign("account_money",$amoney);
        $this->assign("money_experience",$vm['money_experience']);
        if($borrowinfo['need'] <= $vm['account_money']){
        	$allinvest = $borrowinfo['need'];
        }else{
        	$allinvest = $vm['account_money'];
        }
        $this->assign("allinvest",$allinvest);
		$vo = M('borrow_info')->find($id);
//		var_dump($vo);
        $this->assign("vo",$vo);

        //合同模板
        $article_category = M('article_category')->where('id=10')->find();
        $this->assign("article_category", $article_category);



        // $fieldx     = "bi.investor_capital,bi.add_time,m.user_name,bi.investor_uid,bi.is_auto,bi.investor_interest,bi.investor_way,bi.member_interest_rate_id,bi.bonus_id,bi.is_experience";
        // $investinfo = M("borrow_investor bi")->field($fieldx)->join("{$pre}members m ON bi.investor_uid = m.id")->where("bi.borrow_id={$id}")->order("bi.id DESC")->group("bi.investor_uid")->select();
       // echo M("borrow_investor bi")->getlastsql();exit;

        $fieldx = "bi.investor_capital,bi.add_time,m.user_name,bi.is_auto,bi.investor_interest,bi.investor_way,bi.member_interest_rate_id,bi.bonus_id,bi.is_experience";
        $investinfo = M("borrow_investor bi")->field($fieldx)
            ->join("{$pre}members m ON bi.investor_uid = m.id")
            ->where("bi.borrow_id={$id}")
            ->order("bi.id DESC")
            ->select();
        $this->assign("investinfo", $investinfo);

        $glo = array('web_title' => $borrowinfo['borrow_name'] . ' - 我要投资');
        $this->assign($glo);
        $this->display();
    }
	
	
    public function summary(){
		$uid = $this->uid;
		$pre = C('DB_PREFIX');
		
		$this->assign("dc",M('investor_detail')->where("investor_uid = {$this->uid}")->sum('substitute_money'));
		$this->assign("mx",getMemberBorrowScan($this->uid));
		$this->display();
    }
	
	public function tending(){
		//$map['i.investor_uid'] = $this->uid;
		// $map['i.status'] = 1;
		$map['investor_uid'] = $this->uid;
		$map['status'] = 1;
		//$map['start_time'] = array('gt',time());
		$list = getTenderList($map,15);

		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
		$this->assign("total",$list['total_money']);
		$this->assign("num",$list['total_num']);
		$this->display();
	}
	
	public function myyuyuelist(){
	    $map['investor_uid'] = $this->uid;
		$map['status'] = 1;
		$list = getTenderList($map,15);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
		$this->assign("total",$list['total_money']);
		$this->assign("num",$list['total_num']);
		$data['html'] = $this->fetch();
		//$this->display("Public:_footer");
		exit(json_encode($data));
	}
	
	public function guanzhulist(){
	
		$map['investor_uid'] = $this->uid;
		$list = M('pro_guanzhu i')->field("b.*")->join("lzh_borrow_info b ON b.id=i.bid")->where("i.uid=".$this->uid)->select();
		$this->assign("list",$list);
		$this->assign("pagebar",$list['page']);
		$this->assign("total",$list['total_money']);
		$this->assign("num",$list['total_num']);
		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}
	
	public function pinglunlist(){
		$list = M('comment i')->field("i.*,b.borrow_name")->join("lzh_borrow_info b ON b.id=i.tid")->where("i.uid=".$this->uid)->select();
		$this->assign("list",$list);
		$this->assign("pagebar",$list['page']);
		$this->assign("total",$list['total_money']);
		$this->assign("num",$list['total_num']);
		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}
	
	public function yuetanlist(){
		$list = M('comment_yuetan i')->field("i.*,b.borrow_name")->join("lzh_borrow_info b ON b.id=i.tid")->where("i.uid=".$this->uid)->select();
		$this->assign("list",$list);
		$this->assign("pagebar",$list['page']);
		$this->assign("total",$list['total_money']);
		$this->assign("num",$list['total_num']);
		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}
	
	public function tendbacking(){
		//$map['i.investor_uid'] = $this->uid;
		// $map['i.status'] = 4;
		$map['investor_uid'] = $this->uid;
		$map['status'] = 4;
		if($_GET['start_time2']&&$_GET['end_time2']){
			$_GET['start_time2'] = strtotime($_GET['start_time2']." 00:00:00");
			$_GET['end_time2'] = strtotime($_GET['end_time2']." 23:59:59");
			
			if($_GET['start_time2']<$_GET['end_time2']){
				$map['add_time']=array("between","{$_GET['start_time2']},{$_GET['end_time2']}");
				$search['start_time2'] = $_GET['start_time2'];
				$search['end_time2'] = $_GET['end_time2'];
			}
		}
		$list = getTenderList($map,15);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
		$this->assign("total",$list['total_money']);
		$this->assign("alltotal",$list['total_money']+$list['lixi']);
		$this->assign("num",$list['total_num']);
		$mdata = M('members')->find($this->uid);
        $this->assign('mdata',$mdata);
		$this->display();
	}
	public function tenddone(){
		//$map['i.investor_uid'] = $this->uid;
		// $map['i.status'] = array("in","5,6");
		$map['investor_uid'] = $this->uid;
		$map['status'] = array("in","5,6");
		$list = getTenderList($map,15);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
		$this->assign("total",$list['total_money']);
		$this->assign("num",$list['total_num']);
		$this->assign("uid",$this->uid);
		$mdata = M('members')->find($this->uid);
        $this->assign('mdata',$mdata);
		$this->display();
	}
	public function tendbreak(){
		$map['d.status'] = array('neq',0);
		$map['d.repayment_time'] = array('eq',"0");
		$map['d.deadline'] = array('lt',time());
		$map['d.investor_uid'] = $this->uid;
		
		$list = getMBreakInvestList($map,15);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
		$this->assign("total",$list['total_money']);
		$this->assign("num",$list['total_num']);
		//$this->display("Public:_footer");
	
		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}
    public function tendoutdetail(){
		$pre = C('DB_PREFIX');
		$status_arr =array('还未还','已还完','已提前还款','迟还','网站代还本金','逾期还款','','等待还款');
		$investor_id = intval($_GET['id']);
		$vo = M("borrow_investor i")->field("b.borrow_name")->join("{$pre}borrow_info b ON b.id=i.borrow_id")->where("i.investor_uid={$this->uid} AND i.id={$investor_id}")->find();
		if(!is_array($vo)) $this->error("数据有误");
		$map['invest_id'] = $investor_id;
		$list = M('investor_detail')->field(true)->where($map)->select();
		$this->assign("status_arr",$status_arr);
		$this->assign("list",$list);
		$this->assign("name",$vo['borrow_name'].$investor_id);
		$data['html'] = $this->fetch();
		exit(json_encode($data));
		//$this->display();
    }
}
