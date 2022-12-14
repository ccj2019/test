<?php

// 本类由系统自动生成，仅供测试用途
class InvestAction extends HCommonAction
{
    function __construct(){
        parent::__construct();
        $this->assign("dh",'2');
        if(is_mobile()==1){
            $id = isset($_GET['id']) ? (int) ($_GET['id']) : 1;
            echo "<script type='text/javascript'>";
            echo "window.location.href='".$url."/m/project_detail/".$id."';";
            echo "</script>";die;
            // echo $_SERVER['PHP_SELF']; #/PHP/XX.php
        }
        if(!$this->uid) {
            echo '<script>window.location.href="/Member/common/login?url='.urlencode('/invest/index').'"</script>';
        }

    }
    public function is_order()
    {
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 1;
   
        $pre = C('DB_PREFIX');
        $Bconfig = C('BORROW');
        /****** 防止模拟表单提交 *********/
        $cookieTime = 15 * 3600;
        $cookieKey = md5(MODULE_NAME.'-'.time());
        cookie(strtolower(MODULE_NAME).'-invest', $cookieKey, $cookieTime);
        $this->assign('cookieKey', $cookieKey);
        /****** 防止模拟表单提交 *********/

        $borrowinfo = M('borrow_info')->field(true)->find($id);


        $ytmoney=M('borrow_investor')->where(array("borrow_id"=>$id,"investor_uid"=>$this->uid))->sum('investor_capital');
        $borrowinfo["yt_money"]=$ytmoney%$borrowinfo['huodongnum'];

        $borrowinfo["yt_ms"]=$borrowinfo["huodongnum"]-$borrowinfo["yt_money"];
        $zengpin=M("zeng_pin")->where("id=".$borrowinfo["zpid"])->find();
        $this->assign("zengpin",$zengpin);

        $invnums= isset($_GET['money']) ? (int) ($_GET['money'])/$borrowinfo['borrow_min']:1; 
        $this->assign('binfo', $borrowinfo);
        $this->assign('invnums', $invnums);
        if($borrowinfo['borrow_uid'] == $this->uid){
            $this->error("自己无法对自己进行投资");
            exit();
        }
        if($this->uid) {
            $new_user_time = time() - $this->glo['new_user_time'] * 24 * 60 * 60;
            $where = null;
            $where['investor_uid'] = array('eq', "{$this->uid}");
            if($this->glo['new_user_time'] > 0) {
                $where['add_time'] = array(array('egt', "{$new_user_time}"), array('elt', time()));
            }
            $where['borrow_id'] = array('neq', 1);
            $newUser = count(M('borrow_investor')->where($where)->find());
            $where = null;
            $where['bespeak_uid'] = array('eq', "{$this->uid}");
            if($this->glo['new_user_time'] > 0) {
                $where['add_time'] = array(array('egt', "{$new_user_time}"), array('elt', time()));
            }
            $where['borrow_id'] = array('neq', 1);
            $newUser = max($newUser, count(M('bespeak')->where($where)->find()));
            $this->assign('newUser', $newUser);

            $has_bespeak = count(M('bespeak')->where("bespeak_uid={$this->uid} and borrow_id={$id}")->find());
            $this->assign('has_bespeak', $has_bespeak);
            if($borrowinfo['bespeak_able'] == 1 && $borrowinfo['start_time'] + $borrowinfo["bespeak_days"]*60*60*24 > time()) {
                $bespeak = M('bespeak')->field("bespeak_money,bespeak_point")->where("borrow_id={$id} AND bespeak_uid={$this->uid} AND bespeak_status=0")->find();
                if($bespeak && $bespeak["bespeak_money"] != 0) {
                    echo "<script>alert('您已预约此标{$bespeak["bespeak_money"]}元，请交余款')</script>";
                    $this->assign('money', $bespeak["bespeak_money"]);
                    $this->assign('invnums', (int)$bespeak["bespeak_money"] / $borrowinfo["borrow_min"]);
                    $this->assign('bonus_id', 0);
                    $this->assign('invest_nums', (int)$bespeak["bespeak_money"] / $borrowinfo["borrow_min"]);
                    $this->assign('bespeak', $bespeak);
                    $this->assign('point', $bespeak["bespeak_point"]);
                }
            }
        }

        //红包信息
        $bonus_list = M('member_bonus')->where("uid='{$this->uid}' and status = 1 and (".time().' > start_time and '.time().' < end_time )')->field('id,money_bonus,end_time,bonus_invest_min')->order("money_bonus asc,end_time desc")->select();
        if(empty($bonus_list)){
            $bonus_list=array();
        }
        $this->assign('bonus_list', $bonus_list);
        //var_dump(M('member_bonus')->getLastSql());exit;
        $shcut = explode(',', $borrowinfo['shortcut']);
        $this->assign('shcut', $shcut);


        $vm = getMinfo($this->uid, 'm.pin_pass,mm.account_money,mm.yubi,mm.back_money,mm.money_experience');
        $amoney = $vm['account_money'];
        $yubi = $vm['yubi'];

        $pin_pass = $vm['pin_pass'];
        $has_pin = (empty($pin_pass)) ? 'no' : 'yes';
        $this->assign('has_pin', $has_pin);
        $this->assign('account_money', $amoney);
        $this->assign('yubi', $yubi);

        $glo = array('web_title' => $borrowinfo['borrow_name'].' - 我要投资');
        $this->assign($glo);
        $this->display();
    }
	public function is_order_confirm()
	{
	    $id = isset($_GET['id']) ? (int) ($_GET['id']) : 1;
	   
	    $pre = C('DB_PREFIX');
	    $Bconfig = C('BORROW');
	    /****** 防止模拟表单提交 *********/
	    $cookieTime = 15 * 3600;
	    $cookieKey = md5(MODULE_NAME.'-'.time());
	    cookie(strtolower(MODULE_NAME).'-invest', $cookieKey, $cookieTime);
	    $this->assign('cookieKey', $cookieKey);
	    /****** 防止模拟表单提交 *********/
	
	    $borrowinfo = M('borrow_info')->field(true)->find($id);
	    $invnums= isset($_GET['money']) ? (int) ($_GET['money'])/$borrowinfo['borrow_min']:1; 
	    $this->assign('binfo', $borrowinfo);
	    $this->assign('invnums', $invnums);
	    if($borrowinfo['borrow_uid'] == $this->uid){
	        $this->error("自己无法对自己进行投资");
	        exit();
	    }
	    if($this->uid) {
	        $new_user_time = time() - $this->glo['new_user_time'] * 24 * 60 * 60;
	        $where = null;
	        $where['investor_uid'] = array('eq', "{$this->uid}");
	        if($this->glo['new_user_time'] > 0) {
	            $where['add_time'] = array(array('egt', "{$new_user_time}"), array('elt', time()));
	        }
	        $where['borrow_id'] = array('neq', 1);
	        $newUser = count(M('borrow_investor')->where($where)->find());
	        $where = null;
	        $where['bespeak_uid'] = array('eq', "{$this->uid}");
	        if($this->glo['new_user_time'] > 0) {
	            $where['add_time'] = array(array('egt', "{$new_user_time}"), array('elt', time()));
	        }
	        $where['borrow_id'] = array('neq', 1);
	        $newUser = max($newUser, count(M('bespeak')->where($where)->find()));
	        $this->assign('newUser', $newUser);
	
	        $has_bespeak = count(M('bespeak')->where("bespeak_uid={$this->uid} and borrow_id={$id}")->find());
	        $this->assign('has_bespeak', $has_bespeak);
	        if($borrowinfo['bespeak_able'] == 1 && $borrowinfo['start_time'] + $borrowinfo["bespeak_days"]*60*60*24 > time()) {
	            $bespeak = M('bespeak')->field("bespeak_money,bespeak_point")->where("borrow_id={$id} AND bespeak_uid={$this->uid} AND bespeak_status=0")->find();
	            if($bespeak && $bespeak["bespeak_money"] != 0) {
	                echo "<script>alert('您已预约此标{$bespeak["bespeak_money"]}元，请交余款')</script>";
	                $this->assign('money', $bespeak["bespeak_money"]);
	                $this->assign('invnums', (int)$bespeak["bespeak_money"] / $borrowinfo["borrow_min"]);
	                $this->assign('bonus_id', 0);
	                $this->assign('invest_nums', (int)$bespeak["bespeak_money"] / $borrowinfo["borrow_min"]);
	                $this->assign('bespeak', $bespeak);
	                $this->assign('point', $bespeak["bespeak_point"]);
	            }
	        }
	    }
	
	    //红包信息
	    $bonus_list = M('member_bonus')->where("uid='{$this->uid}' and status = 1 and (".time().' > start_time and '.time().' < end_time )')->field('id,money_bonus,end_time,bonus_invest_min')->select();
	    $this->assign('bonus_list', $bonus_list);
	    $shcut = explode(',', $borrowinfo['shortcut']);
	    $this->assign('shcut', $shcut);
	
	
	    $vm = getMinfo($this->uid, 'm.pin_pass,mm.account_money,mm.yubi,mm.back_money,mm.money_experience');
	    $amoney = $vm['account_money'];
	    $yubi = $vm['yubi'];
	
	    $pin_pass = $vm['pin_pass'];
	    $has_pin = (empty($pin_pass)) ? 'no' : 'yes';
	    $this->assign('has_pin', $has_pin);
	    $this->assign('account_money', $amoney);
	    $this->assign('yubi', $yubi);
	
	    $glo = array('web_title' => $borrowinfo['borrow_name'].' - 我要投资');
	    $this->assign($glo);
	    $this->display();
	}

    public function bespeak_order()
    {
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 1;

        $pre = C('DB_PREFIX');
        $Bconfig = C('BORROW');
        /****** 防止模拟表单提交 *********/
        $cookieTime = 15 * 3600;
        $cookieKey = md5(MODULE_NAME.'-'.time());
        cookie(strtolower(MODULE_NAME).'-invest', $cookieKey, $cookieTime);
        $this->assign('cookieKey', $cookieKey);
        /****** 防止模拟表单提交 *********/

        $borrowinfo = M('borrow_info')->field(true)->find($id);
        if ($borrowinfo['bespeak_able'] != 1) {
            $this->error('此标不支持预约');
        }
        if($borrowinfo["start_time"] < time()){
            $this->error('此标不在预热期');
        }
        $invnums= isset($_GET['money']) ? (int) ($_GET['money'])/$borrowinfo['borrow_min']:1;
        $this->assign('binfo', $borrowinfo);
        $this->assign('invnums', $invnums);
        if($borrowinfo['borrow_uid'] == $this->uid){
            $this->error("自己无法对自己进行预约");
            exit();
        }

        if (bcsub($borrowinfo['borrow_money'], $borrowinfo['bespeak_money'], 2) == 0 and $borrowinfo["borrow_status"] == 1) {
            $this->error("预约已满");
            exit();
        }

        if($this->uid) {
            $new_user_time = time() - $this->glo['new_user_time'] * 24 * 60 * 60;
            $where = null;
            $where['investor_uid'] = array('eq', "{$this->uid}");
            if($this->glo['new_user_time'] > 0) {
                $where['add_time'] = array(array('egt', "{$new_user_time}"), array('elt', time()));
            }
            $where['borrow_id'] = array('neq', 1);
            $newUser = count(M('borrow_investor')->where($where)->find());
            $where = null;
            $where['bespeak_uid'] = array('eq', "{$this->uid}");
            if($this->glo['new_user_time'] > 0) {
                $where['add_time'] = array(array('egt', "{$new_user_time}"), array('elt', time()));
            }
            $where['borrow_id'] = array('neq', 1);
            $newUser = max($newUser, count(M('bespeak')->where($where)->find()));
            $this->assign('newUser', $newUser);

            $has_bespeak = count(M('bespeak')->where("bespeak_uid={$this->uid} and borrow_id={$id}")->find());
            $this->assign('has_bespeak', $has_bespeak);
        }

        $vm = getMinfo($this->uid, 'm.pin_pass,mm.account_money,mm.back_money,mm.money_experience');
        $amoney = $vm['account_money'];
        $pin_pass = $vm['pin_pass'];
        $has_pin = (empty($pin_pass)) ? 'no' : 'yes';
        $this->assign('has_pin', $has_pin);
        $this->assign('account_money', $amoney);

        $glo = array('web_title' => $borrowinfo['borrow_name'].' - 预约投资');
        $this->assign($glo);
        $this->display();
    }
    public function index()
    {
    	if(is_mobile()==1){
    		$url=str_replace("/index.php/","/wap/",$_SERVER['PHP_SELF']);
			echo "<script type='text/javascript'>";
     	    echo "window.location.href='".$url."';";
			echo "</script>";die;
		}		
        $strUrl = U('invest/index').'?1=1';
        static $newpars;
        $Bconfig = C('BORROW');
        $per = C('DB_PREFIX');
        $curl = $_SERVER['REQUEST_URI'];
        $urlarr = parse_url($curl);
        parse_str($urlarr['query'], $surl); //array获取当前链接参数，2.

        //$yzmap["pid"]=array('in',array('1,2,3,4'));
        $yzmap['leixing']=array('neq',2);
        $yzmap['id']=array('neq',1);
        $yzmap["borrow_status"]=array("in",array(1,2));
        $order='borrow_status ASC,id DESC';
        $yzlist = m('borrow_info')->where($yzmap)->order($order)->select();
        foreach ($yzlist as $k => $v) {
            $yzlist[$k]['progress']       = getFloatValue($v['has_borrow']/$v['borrow_money'],2)*100;
            $yzlist[$k]['fenshu']       = ($v['borrow_money']-$v['has_borrow'])/$v["borrow_min"];
            $yzlist[$k]['endtimes'] = $v['start_time'] + ($v['collect_day'] * 60 * 60 * 24);
        }
        $this->assign('yzlist', $yzlist);

//        $xsmap["pid"]=3;
//        $xsmap["borrow_status"]=array("in",array(1,2));
//        $xslist = m('borrow_info')->where($xsmap)->order($order)->select();
//        foreach ($xslist as $k => $v) {
//            $xslist[$k]['progress']       = getFloatValue($v['has_borrow']/$v['borrow_money'],2)*100;
//            $xslist[$k]['fenshu']       = ($v['borrow_money']-$v['has_borrow'])/$v["borrow_min"];
//            $xslist[$k]['endtimes'] = $v['start_time'] + ($v['collect_day'] * 60 * 60 * 24);
//        }
//        $this->assign('xslist', $xslist);

        

        $horder="art_time desc";
        $hlist=M("article")->field('title,art_keyword,art_img')->where("type_id=531")->order($horder)->select();
        //var_dump($hlist);
        $this->assign('hlist', $hlist);

        $this->assign("dh",'2');

        $this->display();
    }

    public function xmlist()
    {
        //新添加
        $tjgoods=M("market")->where("tuijian=1")->limit('0,4')->select();
        $this->assign('tjgoods',$tjgoods);

        if(is_mobile()==1){
            $url=str_replace("/index.php/","/wap/",$_SERVER['PHP_SELF']);
            echo "<script type='text/javascript'>";
              echo "window.location.href='".$url."';";
            echo "</script>";die;
        }
        import('ORG.Util.Page');
        $map = null;
        
        if($_GET['type']){
            $map['pid'] =$_GET["type"];
            $this->assign('type',$_GET["type"]);
        }


        if($_GET["name"]){
            $map['borrow_name'] = array('like',"%".$_GET["name"]."%");
        }

        $this->assign('name',$_GET["name"]);

        $status=$_GET["type_id"];
        if($status==0){
            $map["borrow_status"]=array("in",array(1,2,6,7));
        }else{
            if($status==1){
                $map["borrow_status"] = 2;
            }
            if($status==2){
                $map['borrow_status'] = 6;
                //$map['xs_time'] = array("gt",time());
                $map['_complex']=array('shoujia'=>"0","xs_time"=>array("gt",time()),'_logic'=>'or');
            }
            if($status==3){
                $map['borrow_status'] = 6;
                $map['xs_time'] = array("lt",time());
                $map['shoujia'] = array("neq",0);
                //$map['_complex']=array('shoujia'=>"0","xs_time"=>array("gt",time()),'_logic'=>'or');
            }
            if($status==4){
                $map['borrow_status'] = 7;
            }
        }
        $map['id']=array('neq',1);
        $map['leixing']=array('neq',2);
        $this->assign('status',$status);

        $count = m('borrow_info')->where($map)->count();

        $num = 15;
        $page = new Page($count,$num);
        foreach($getval as $key=>$val) {
            $page->parameter .= "$key=".urlencode($val).'&';
        }
        $order='borrow_status ASC,id DESC';
        $show = $page->show();
        $limit = $page->firstRow.','.$page->listRows;

        $list = m('borrow_info')->where($map)->order($order)->limit($limit)->select();
        foreach ($list as $k => $v) {
            $list[$k]['progress']       = getFloatValue($v['has_borrow']/$v['borrow_money'],2)*100;
            $status="";
            if($v['borrow_status']==2){
                $status="销售中";
            }
            if($v['borrow_status']==6){
                if(!empty($v["shoujia"])){
                    if($v["xs_time"]>time()){
                        $status="养殖中";
                    }
                    if($v["xs_time"]<time()){
                        $status="售卖中";
                    }
                }else{
                    $status="养殖中";
                }
            }
            if($v['borrow_status']==7){
                $status="已完成";
            }
            $list[$k]['zhuangtai']=$status;
            $list[$k]['fenshu']       = ($v['borrow_money']-$v['has_borrow'])/$v["borrow_min"];
        }
        $this->assign('page',$show);
        $this->assign('list',$list);




        // $horder="art_time desc";
        // $hlist=M("article")->field('title,art_keyword,art_img')->where("type_id=527")->order($horder)->select();
        //var_dump($list);
        // $this->assign('hlist', $hlist);

       

        $this->display();
    }

    /////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////
    private function _tendlist($list)
    {
        $areaList = getArea();
        $row = array();
        foreach ($list as $key => $v) {
            $v['location'] = $areaList[$v['province']].$areaList[$v['city']].$areaList[$v['area']];
            $v['breakday'] = getExpiredDays($v['deadline']);
            $v['expired_money'] = getExpiredMoney($v['breakday'], $v['total_expired'], $v['interest']);
            $v['call_fee'] = getExpiredCallFee($v['breakday'], $v['total_expired'], $v['interest']);
            $row[$key] = $v;
        }

        return $row;
    }
    public function Verifymoney()
    {
          //红包信息
            $verifymoney=$_POST['verifymoney'] ;
            
          //$ Verify_money verify_money
         $bonus_list = M('member_bonus')->where("uid='{$this->uid}' and status = 1 and (".time().' > start_time and '.time().' < end_time )')->field('id,money_bonus,end_time,bonus_invest_min')->select();
        foreach($bonus_list as $k=>$v){
            $bonus_list[$k]["verify_money"]=$_POST['verifymoney'];
            if($v["bonus_invest_min"] <= $verifymoney){
                    $bonus_list[$k]["is_Verify_money"]=1;
            }else{
                    $bonus_list[$k]["is_Verify_money"]=2;
            }
        
        }
  
     $this->ajaxReturn($bonus_list,"成功",1);      die;
    }
    public function agreement()
    {
        $id = (int) ($_GET['id']);
        $binfo = M('borrow_info')->field('agreement')->find($id);
        $agreement = $binfo['agreement'];
        $this->assign('agreement', $agreement);
        $this->display();
    }
    public function idetail()
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
		if($id != 1){
        if (!is_array($borrowinfo) || ($borrowinfo['borrow_status'] == 0 && $this->uid != $borrowinfo['borrow_uid'])) {
            $this->error("数据有误");
        }
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
        $memberinfo                 = M("members m")->field("m.id,m.customer_name,m.customer_id,m.pin_pass,m.user_name,m.reg_time,m.credits,fi.*,mi.*")->join("{$pre}member_financial_info fi ON fi.uid = m.id")->join("{$pre}member_info mi ON mi.uid = m.id")->where("m.id={$borrowinfo['borrow_uid']}")->find();
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
        $this->assign("vo",$vo);
 
        //合同模板
          $article_category = M('article')->where('type_id = 525 AND title  LIKE "%'.$vo['templateid'].'%"')->find();
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
		if($this->uid){
			
		}
		if( !$this->uid){
				$this->assign('uid',0);
		}  else{$this->assign('uid',$this->uid);
				
		}
	   $this->display();
    }
    ////////////////////////////////////////////////////////////////////////////////////
    public function detail()
    {
  		
        $id = isset($_GET['id']) ? (int) ($_GET['id']) : 1;
            if($id==1){
                        
        		echo "<script type='text/javascript'>";
             	echo "window.location.href='".$url."/invest/idetail/id/".$id.".html';";
        		echo "</script>";die;
            }
    	if(is_mobile()==1){

			echo "<script type='text/javascript'>";
     	    echo "window.location.href='".$url."/app.html#/project_detail/".$id."';";
			echo "</script>";die;
			// echo $_SERVER['PHP_SELF']; #/PHP/XX.php
		}
        $pre = C('DB_PREFIX');
        $Bconfig = C('BORROW');
        /****** 防止模拟表单提交 *********/
        $cookieTime = 15 * 3600;
        $cookieKey = md5(MODULE_NAME.'-'.time());
        cookie(strtolower(MODULE_NAME).'-invest', $cookieKey, $cookieTime);
        $this->assign('cookieKey', $cookieKey);
        /****** 防止模拟表单提交 *********/

        $this->assign('Bconfig', $Bconfig);

        if($this->uid){
            $new_user_time = time() - $this->glo['new_user_time']*24*60*60;
            $where = null;
            $where['investor_uid'] = array('eq',"{$this->uid}");
            if($this->glo['new_user_time'] > 0) {
                $where['add_time'] = array(array('egt', "{$new_user_time}"), array('elt', time()));
            }
            $where['borrow_id'] = array('neq',1);
            $newUser = count(M('borrow_investor')->where($where)->find());
            $where = null;
            $where['bespeak_uid'] = array('eq',"{$this->uid}");
            if($this->glo['new_user_time'] > 0) {
                $where['add_time'] = array(array('egt', "{$new_user_time}"), array('elt', time()));
            }
            $where['borrow_id'] = array('neq',1);
            $newUser = max($newUser,count(M('bespeak')->where($where)->find()));
            $this->assign('newUser', $newUser);

            $has_bespeak = count(M('bespeak')->where("bespeak_uid={$this->uid} and borrow_id={$id}")->find());
            $this->assign('has_bespeak', $has_bespeak);
        }

        if (!$this->uid) {
            $guanzhu_html = '关注';
        }

        $b = M('pro_guanzhu')->where('bid='.$id." and uid = {$this->uid}")->count('id');
        

        
        if (1 == $b) {
            $guanzhu_html = '已关注';
        } else {
            $guanzhu_html = '关注';
        }
        $this->assign('guanzhu_html', $guanzhu_html);
        $borrowinfo = M('borrow_info')->field(true)->find($id);
        $shortcut_c = explode(",", $borrowinfo['shortcut']);
        $this->assign("shortcut_c",$shortcut_c);
        //  var_dump($binfo['shortcut_c']);die;
        // $this->assign('guanzhu_html', $guanzhu_html);
        // echo M("borrow_info")->getlastsql();exit;
        $logintime='';
  
            $logint = m("member_login")->where("uid={$borrowinfo['borrow_uid']}")->order("id desc")->find();

            $logintime=$logint['add_time'];
 
        $this->assign("logintime", $logintime);
        //点击
        $ip = get_client_ip();
        $now_time = strtotime(date('Y-m-d'));
        $hit_array = M('pro_hits')->field(true)->where('add_ip="'.$ip.'" and bid='.$id.' and add_time>'.$now_time)->find();
        if (!$hit_array) {
            $save['bid'] = $id;
            $save['add_time'] = time();
            $save['add_ip'] = $ip;
            M('pro_hits')->data($save)->add();
            M('borrow_info')->where('id='.$id)->setField('hits', $borrowinfo['hits'] + 1);
        }

        if (0 == bcsub($borrowinfo['borrow_money'], $borrowinfo['has_borrow'], 2) and 2 == $borrowinfo['borrow_status']) {
            //echo bcsub($borrowinfo['borrow_money'],$borrowinfo['has_borrow'],2);
            borrowfull($id, $borrowinfo['borrow_type']);
        }
        if (!is_array($borrowinfo) || (0 == $borrowinfo['borrow_status'] && $this->uid != $borrowinfo['borrow_uid'])) {
            $this->error('数据有误');
        }

        $borrowinfo['biao'] = $borrowinfo['borrow_times'];
        $borrowinfo['need'] = bcsub($borrowinfo['borrow_money'], $borrowinfo['has_borrow'], 2);

        $borrowinfo['leftdays'] = getLeftTime($borrowinfo['collect_time'], 2);
        $borrowinfo['lefttime'] = $borrowinfo['start_time'] - time();
        $borrowinfo['endtimes'] = $borrowinfo['start_time'] + ($borrowinfo['collect_day'] * 60 * 60 * 24);

        $borrowinfo['progress'] = getFloatValue($borrowinfo['has_borrow'] / $borrowinfo['borrow_money'] * 100, 0);
        if($borrowinfo['bespeak_able'] == 1) {
            $borrowinfo['bespeak_progress'] = getFloatValue($borrowinfo['bespeak_money'] / $borrowinfo['borrow_money'] * 100, 0);
        }
        if ('.' == substr($borrowinfo['progress'], -1)) {
            $borrowinfo['progress'] = substr($borrowinfo['progress'], 0, strlen($borrowinfo['progress']) - 1);
        }
        $borrowinfo['vouch_progress'] = getFloatValue($borrowinfo['has_vouch'] / $borrowinfo['borrow_money'] * 100, 2);
        $borrowinfo['vouch_need'] = $borrowinfo['borrow_money'] - $borrowinfo['has_vouch'];
        $borrowinfo['borrow_img'] = str_replace("'", '', $borrowinfo['borrow_img']);
        if ('' == $borrowinfo['borrow_img']) {
            $borrowinfo['borrow_img'] = 'UF/Uploads/borrowimg/nopic.png';
        }
        $imgarr = array();
        if($borrowinfo['content_img'] != ''){
            $imgarr = explode(',', $borrowinfo['content_img']);
        }

        $borrow_interest_rate_arr = explode('.', $borrowinfo['borrow_interest_rate'], 2);
        $borrowinfo['borrow_interest_rate_1'] = '00';
        $borrowinfo['borrow_interest_rate_2'] = '00';
        if (isset($borrow_interest_rate_arr[0])) {
            $borrowinfo['borrow_interest_rate_1'] = $borrow_interest_rate_arr[0];
        }

        if (isset($borrow_interest_rate_arr[1])) {
            $borrowinfo['borrow_interest_rate_2'] = $borrow_interest_rate_arr[1];
        }

        $borrowinfo['vote0'] = M('borrow_vote')->where('status = 0 and borrow_id = '.$id)->count('id');
        $borrowinfo['vote1'] = M('borrow_vote')->where('status = 1 and borrow_id = '.$id)->count('id');

        $borrowinfo['zongfenshu'] =$borrowinfo['borrow_money']/$borrowinfo['borrow_min'];
        $borrowinfo['fenshu'] =($borrowinfo['borrow_money']-$borrowinfo['has_borrow'])/$borrowinfo['borrow_min'];

        if($borrowinfo['borrow_status']==1){
            $status="预热中";
        }

        if($borrowinfo['borrow_status']==6){
            if($v["xs_time"]>time()){
                $status="养殖中";
            }else{
                $status="售卖中";
            }
        }
        if($borrowinfo['borrow_status']==7){
            $status="已完成";
        }
        $borrowinfo['zhuangtai']=$status;
        if(!empty($borrowinfo['sg_time'])){
            $a=$borrowinfo['sg_time'];
        }else{
            if ($borrowinfo["total"] > 1) {
                if (!empty($borrowinfo["hkday"])) {
                    $yue = $borrowinfo["hkday"];
                    $dt = date('Y-n-j', $borrowinfo['lead_time']);
                    $a = strtotime("$dt+" . $yue * ($borrowinfo["total"] - 1) . "days");
                } else {
                    $dt = date('Y-n-j', $borrowinfo['lead_time']);
                    $yus=$borrowinfo["borrow_duration"]/$borrowinfo["total"]; 
                    $a = strtotime("$dt+" . ($yus*($borrowinfo["total"]-1)) . "month");
                }
            } else {
                $a = $borrowinfo['lead_time'];
            }
        }
        $borrowinfo['sg_time']=$a;

        $borrowinfo["zpname"]=M("zeng_pin")->where("id=".$borrowinfo['zpid'])->getField('title');
        $this->assign('imgarr', $imgarr);
        $this->assign('binfo', $borrowinfo);
        //此标借款利息还款相关情况
        //memberinfo
        $memberinfo = M('member_info')->where("uid={$borrowinfo['borrow_uid']}")->find();
      //  var_dump($memberinfo );die;
        $location = M('member_address')->where("uid={$borrowinfo['borrow_uid']} and lzh_member_address.DEFAULT =1")->find();
        $membersinfo = M('members')->where("id={$borrowinfo['borrow_uid']}")->find();
        
        $areaList = getArea();
        $memberinfo['location'] =$location["province"].$location["city"];
        $memberinfo['user_name'] =$membersinfo["user_name"];
     //   var_dump( M('member_address')->getlastsql() ); 
        //$areaList[$membersinfo['province']].$areaList[$membersinfo['city']];
     //var_dump(  $areaList[$membersinfo['province']]); 
       //var_dump($memberinfo );die;
        $this->assign('borrowminfo', $memberinfo);
        //memberinfo
        //vouch_list

        //data_list
        $data_list = M('member_data_info')->field('type,add_time,data_url,count(status) as num,sum(deal_credits) as credits')->where("uid={$borrowinfo['borrow_uid']} AND status=1")->group('id')->select();
        $this->assign('data_list', $data_list);
        //data_list

        //红包信息
        $bonus_list = M('member_bonus')->where("uid='{$this->uid}' and status = 1 and (".time().' > start_time and '.time().' < end_time )')->field('id,money_bonus,end_time,bonus_invest_min')->select();
        $this->assign('bonus_list', $bonus_list);

        //加息券信息
        $inrate_list = M('member_interest_rate')->where("uid='{$this->uid}' and status = 1 and (".time().' > start_time and '.time().' < end_time )')->field('id,interest_rate,end_time')->select();
        $this->assign('inrate_list', $inrate_list);

        $vm = getMinfo($this->uid, 'm.pin_pass,mm.account_money,mm.back_money,mm.money_experience');
        $amoney = $vm['account_money'] + $vm['back_money'];
        $pin_pass = $vm['pin_pass'];
        $has_pin = (empty($pin_pass)) ? 'no' : 'yes';
        $this->assign('has_pin', $has_pin);
        $this->assign('account_money', $amoney);
        $this->assign('money_experience', $vm['money_experience']);
        if ($borrowinfo['need'] <= $vm['account_money']) {
            $allinvest = $borrowinfo['need'];
        } else {
            $allinvest = $vm['account_money'];
        }
        $this->assign('allinvest', $allinvest);
        $vo = M('borrow_info')->find($id);
        $this->assign('vo', $vo);

// var_dump($vo['templateid']);
        //合同模板
            $article_category = M('article')->where('type_id = 525 AND title  LIKE "'.$vo['templateid'].'"')->find();
            //var_dump($article_category,M('article')->getlastsql());
            // var_dump(M('article')->getlastsql());
           //  var_dump(M('article')->getlastsql(),1);
            // die;`title` LIKE 'TEM1005289' AND `type_id` =525
          //  var_dump( $article_category);
        $this->assign('article_category', $article_category);

        // $fieldx     = "bi.investor_capital,bi.add_time,m.user_name,bi.investor_uid,bi.is_auto,bi.investor_interest,bi.investor_way,bi.member_interest_rate_id,bi.bonus_id,bi.is_experience";
        // $investinfo = M("borrow_investor bi")->field($fieldx)->join("{$pre}members m ON bi.investor_uid = m.id")->where("bi.borrow_id={$id}")->order("bi.id DESC")->group("bi.investor_uid")->select();
        // echo M("borrow_investor bi")->getlastsql();exit;

        $fieldx = 'bi.investor_capital,bi.add_time,m.user_name,bi.is_auto,bi.investor_interest,bi.investor_way,bi.member_interest_rate_id,bi.bonus_id,bi.is_experience,mi.user_img';
        $investinfo = M('borrow_investor bi')->field($fieldx)
            ->join("{$pre}members m ON bi.investor_uid = m.id")
			  ->join("{$pre}member_info mi ON bi.investor_uid = mi.uid")
            ->where("bi.borrow_id={$id}")
            ->order('bi.id DESC')
            ->select();
        $this->assign('investinfo', $investinfo);

        //
        //评论
        $commentlist = m("comment")->where("tid={$id}")->order("id desc")->select();
        $this->assign('commentlist', $commentlist);
         $dynamiclist = m("dynamic")->where("tid={$id}")->order("add_time desc")->select();
        $this->assign('dynamiclist', $dynamiclist);

        $glo = array('web_title' => $borrowinfo['borrow_name'].' - 我要投资');
        $this->assign($glo);
        $this->display();
    }

    public function investcheck()
    {
        $pre = C('DB_PREFIX');
        if (!$this->uid) {
            ajaxmsg('', 0);
        }
        $pin = md5($_POST['pin']);
        $borrow_id = (int) ($_POST['borrow_id']);
        $money = (float) ($_POST['money']);

        $is_experience = isset($_POST['is_experience']) && 1 == $_POST['is_experience'] ? 1 : 0;
        $member_interest_rate_id = 0;
        $bonus_id = isset($_POST['bonus_id']) ? (int) ($_POST['bonus_id']) : 0;
        if ($bonus_id > 0) {
            $bs = M('member_bonus')->where("id='{$bonus_id}'")->find();
            $canInvestMoneys = canInvestMoney($this->uid, $borrow_id, $money+$bs['money_bonus'], 0, $is_experience, '0', $bonus_id, text($_POST['borrow_pass']));

            if (0 == $canInvestMoneys['status']) {
                ajaxmsg($canInvestMoneys['tips'], $canInvestMoneys['status']);
            }
            $money_bonuss = $canInvestMoneys['money_bonus'];
            $money = (float) ($money + $money_bonuss);
        }

        $memberinterest_id = isset($_POST['memberinterest_id']) ? (int) ($_POST['memberinterest_id']) : 0;

        $vm = getMinfo($this->uid, 'm.pin_pass,mm.account_money,mm.back_money,mm.money_experience');
        $pin_pass = $vm['pin_pass'];
        if ($pin != $pin_pass) {
            ajaxmsg('支付密码错误，请重试！', 0);
            exit;
        }
        //ajaxmsg("支付密码错误，请重试",0);

        $amoney = $vm['account_money'] + $vm['back_money'];
        $uname = session('u_user_name');
        $amoney = (float) $amoney;

        $binfo = M('borrow_info')->field('borrow_money,borrow_status,has_borrow,has_vouch,borrow_min,borrow_type,password,pause,max_limit,new_user_only')->find($borrow_id);
        $binfo['borrow_max'] = $binfo['borrow_min']*$binfo['max_limit'];
        $minfo = getMinfo($this->uid, true);
        $levename = getLeveId($minfo['credits']);
   
 
        $ids = M('members_status')->getFieldByUid($this->uid, 'id_status');
        if($ids!=1){
        	ajaxmsg("请先通过实名认证后再进行投标。",3);
        }
        $phones = M('members_status')->getFieldByUid($this->uid,'phone_status');
        if($phones!=1){
        	ajaxmsg("请先通过手机认证后再进行投标。",3);
        }
        // $emails = M('members_status')->getFieldByUid($this->uid,'email_status');
        // if($emails!=1){
        // 	//ajaxmsg("请先通过邮箱认证后再进行投标。",3);
        // }
        if($binfo['pause']==1){
        	ajaxmsg("此标当前已经截标，请等待管理员开启。",0);
        }

        // 50 > 10
        if ($money < $binfo['borrow_min']) {
            ajaxmsg('此标最小投标金额为'.$binfo['borrow_min'].'元', 3);
            exit();
        }
        if ($money > $binfo['borrow_max'] and 0 != $binfo['borrow_max']) {
  
            // exit(json_encode(array('status' => 0, 'message' => '此标最大投标金额为'.$binfo['borrow_max'].'元', 'invest_money' => $binfo['borrow_max'])));
            ajaxmsg("此标最大投标金额为".$binfo['borrow_max']."元",3);
            exit();
        }

        if ($binfo['has_vouch'] < $binfo['borrow_money'] && 2 == $binfo['borrow_type']) {
            ajaxmsg("此标担保还未完成，您可以担保此标或者等担保完成再投标",3);
            exit();
            
        }
        //ajaxmsg("此标担保还未完成，您可以担保此标或者等担保完成再投标",3);

        if ($binfo["new_user_only"] == 1) {
            $new_user_time = time() - $this->glo['new_user_time']*24*60*60;
            $where = null;
            $where['investor_uid'] = array('eq',"{$this->uid}");
            if($this->glo['new_user_time'] > 0) {
                $where['add_time'] = array(array('egt', "{$new_user_time}"), array('elt', time()));
            }
            $where['borrow_id'] = array('neq',1);
            $newUser = count(M('borrow_investor')->where($where)->find());
            $where = null;
            $where['bespeak_uid'] = array('eq',"{$this->uid}");
            if($this->glo['new_user_time'] > 0) {
                $where['add_time'] = array(array('egt', "{$new_user_time}"), array('elt', time()));
            }
            $where['borrow_id'] = array('neq',1);
            $newUser = max($newUser,count(M('bespeak')->where($where)->find()));
            if($newUser > 0) {
                $this->error('此标是新手专享标');
            }
        }

        //投标总数检测
        $capital = M('borrow_investor')->where("borrow_id={$borrow_id} AND investor_uid={$this->uid}")->sum('investor_capital');

        if (($capital + $money) > $binfo['borrow_max'] && $binfo['borrow_max'] > 0) {
            $xtee = $binfo['borrow_max'] - $capital;
            ajaxmsg("您已投标{$capital}元，上限为{$binfo['borrow_max']}元，你最多只能再投{$xtee}元", 3);
            exit();
        }

        $need = bcsub($binfo['borrow_money'], $binfo['has_borrow'], 2);
        $caninvest = bcsub($need, $binfo['borrow_min'], 2);
        if ($money > $caninvest && 0 != ($need - $money)) {
            if (0 == (int) $need or '0.00' == $need) {
                ajaxmsg("尊敬的{$uname}，该项目已经投资完成，请选择其他下项目！",0);
                exit();
                // ajaxmsg("尊敬的{$uname}，此标已经投满",0);
            }
            if ($money > $need) {
                ajaxmsg("尊敬的{$uname}，此标还差{$need}元满标,您最多只能再投{$need}元",0);
                exit();
                // ajaxmsg("尊敬的{$uname}，此标还差{$need}元满标,您最多只能再投{$need}元",0);
            }

            $msg = "尊敬的{$uname}，此标还差{$need}元满标,如果您投标{$money}元，将导致最后一次投标最多只能投".(bcsub($need, $money, 2))."元，小于最小投标金额{$binfo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满标</font>或者投标金额必须<font color='#FF0000'>小于等于{$caninvest}元</font>";
            if ($caninvest < $binfo['borrow_min']) {
                $msg = "尊敬的{$uname}，此标还差{$need}元满标,如果您投标{$money}元，将导致最后一次投标最多只能投".(bcsub($need, $money, 2))."元，小于最小投标金额{$binfo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满标</font>即投标金额必须<font color='#FF0000'>等于{$need}元</font>";
            }
            ajaxmsg($msg,0);
            exit();
            exit(json_encode(array('status' => 0, 'message' => $msg, 'invest_money' => $need)));
            // ajaxmsg($msg,0);
        }

        if (($need - $money) < 0) {
            $money=$need;
//            ajaxmsg("尊敬的{$uname}，此标还差{$need}元满标,您最多只能再投{$need}元",0);
//            exit();
            // ajaxmsg("尊敬的{$uname}，此标还差{$need}元满标,您最多只能再投{$need}元",0);
        }

        $canInvestMoney = canInvestMoney($this->uid, $borrow_id, $money, 0, $is_experience, '0', $bonus_id, text($_POST['borrow_pass']));
        if (0 == $canInvestMoney['status']) {

            ajaxmsg($canInvestMoney['tips'], $canInvestMoney['tips_type']);
            exit();
        }
        $money_bonus = $canInvestMoney['money_bonus'];
        //if($pin<>$pin_pass) ajaxmsg("支付密码错误，请重试",0);
        //		if($memberinterest_id > 0){
        //				//加息券校验
        //			$canUseInterest = canUseInterest($this->uid,$memberinterest_id);
        //			writeLog($canUseInterest);
        //			if($canUseInterest['status'] == 0){
        //				$this->error('加息券不可用');
        //			}
        //			$interest_rate = $canUseInterest['interest_rate'];
        //		}else{
        //			$interest_rate=0;
        //		}
        if (2 == $canInvestMoney['money_type']) {
            if ($memberinterest_id > 0) {
                $msg = "尊敬的{$uname}，您的体验金账户余额为{$vm['money_experience']}元，您确认投标{$money}元？";
            } else {
                $msg = "尊敬的{$uname}，您的体验金账户余额为{$vm['money_experience']}元，您确认投标{$money}元吗？";
            }
            ajaxmsg($msg, 1);
            exit();
        } elseif (3 == $canInvestMoney['money_type']) {
            if ($memberinterest_id > 0) {
                $msg = "尊敬的{$uname}，您确认投标{$money}元（使用{$money_bonus}元红包），投资吗？";
            } else {
                $msg = "尊敬的{$uname}，您确认投标{$money}元（使用{$money_bonus}元红包），投资吗？";
            }
            ajaxmsg($msg, 1);
            exit();
        } elseif ($money <= $amoney) {
            if ($memberinterest_id > 0) {
                $msg = "尊敬的{$uname}，您的账户可用余额为{$amoney}元，您确认投标{$money}元吗？";
            } else {
                $msg = "尊敬的{$uname}，您的账户可用余额为{$amoney}元，您确认投标{$money}元吗？";
            }
            ajaxmsg($msg, 1);
            exit();
        } else {
            ajaxmsg($msg, 2);
            exit();
        }
    }

    public function investmoney()
    {
        if (!$this->uid) {
            $this->error('请先登录', '/member/common/login.html');
        }
        /****** 防止模拟表单提交 *********/
        $cookieKeyS = cookie(strtolower(MODULE_NAME).'-invest');
        //echo cookie(strtolower(MODULE_NAME)."-invest");
        //echo $_REQUEST['cookieKey'];
        //exit;
        if ($cookieKeyS != $_REQUEST['cookieKey']) {
            //$this->error("数据校验有误");
        }

        // 表单令牌验证
        if(!M("members")->autoCheckToken($_POST)) {
            $this->error("禁止重复提交表单", $this->_server ( 'HTTP_REFERER' ));
            exit();
        }
     
        /****** 防止模拟表单提交 *********/
        //$money = $_POST['money'];

        writeLog($_POST);
        $borrow_id = (int) ($_POST['borrow_id']);
        $binfo = M('borrow_info')->field('shoujia,borrow_money,has_borrow,has_vouch,borrow_max,borrow_min,borrow_type,password,pause,max_limit,start_time,new_user_only,bespeak_able,bespeak_money,bespeak_days')->find($borrow_id);

        $zs=$_POST["sg"]+$_POST["zt"];

        $is_experience = isset($_POST['is_experience']) && 1 == $_POST['is_experience'] ? 1 : 0;

        $bonus_id = isset($_POST['bonus_id']) ? (int) ($_POST['bonus_id']) : 0;

        if (!empty($_POST["bonus_id"])) {
            $borrow = M("member_bonus")->where("id=" . $_POST["bonus_id"])->find();
            $_POST['money']= $money=$zs*$binfo["borrow_min"]- $borrow["money_bonus"];
        }else{
            $_POST['money']= $money=$zs*$binfo["borrow_min"];
        }
        $memberinterest_id = isset($_POST['memberinterest_id']) ? (int) ($_POST['memberinterest_id']) : 0;
        if ($bonus_id > 0) {
            $bs = M('member_bonus')->where("id='{$bonus_id}'")->find();
            $canInvestMoneys = canInvestMoney($this->uid, $borrow_id, $money + $bs['money_bonus'], 0, $is_experience, '0', $bonus_id, text($_POST['borrow_pass']));
            if (0 == $canInvestMoneys['status']) {
                //ajaxmsg($canInvestMoneys['tips'], $canInvestMoneys['status']);
                 $this->error($canInvestMoneys['tips']);
                 die;
            }
            $money_bonuss = $canInvestMoneys['money_bonus'];
            $money = (float) ($money + $money_bonuss);
        }

        $m = M('member_money')->field('account_money,back_money,yubi')->find($this->uid);
        $amoney = $m['account_money'] + $m['back_money']+$m["yubi"];
        $uname = session('u_user_name');

        $vm = getMinfo($this->uid, 'm.pin_pass,mm.account_money,mm.back_money,mm.money_experience');
        $pin_pass = $vm['pin_pass'];
        $pin = md5($_POST['pin']);
        if ($pin_pass == "") {
            $this->error('支付密码为空，请您设置密码',U("/member/user/pinpass", array('type' => 1)));
            exit();
        } elseif ($pin != $pin_pass) {
            $this->error('支付密码错误，请重试');  //   暂时取消支付密码
            exit();
        }
        //$binfo = M('borrow_info')->field('borrow_money,has_borrow,has_vouch,borrow_max,borrow_min,borrow_type,password,pause,max_limit,start_time,new_user_only,bespeak_able,bespeak_money,bespeak_days')->find($borrow_id);

           //invnums
        if(($_POST['money']/$binfo['borrow_min'])<=0){
             var_dump($_POST['invnums']);die;
             $this->error("请输入正确的数值");die;
        }
        
        $binfo['borrow_max'] = $binfo['borrow_min']*$binfo['max_limit'];
        $minfo = getMinfo($this->uid, true);
        /*$can_invest = unserialize($binfo['can_invest']);
        $levename=getLeveId($minfo["credits"]);
        if(!in_array($levename, $can_invest)){
             // ajaxmsg("您没有权限投此项目！",0);
             $this->error("您没有权限投此项目！");die;
        }*/

        $ids = M('members_status')->getFieldByUid($this->uid, 'id_status');
        if($ids!=1){
        	$this->error("请先通过实名认证后再进行投标。");
        }
        $phones = M('members_status')->getFieldByUid($this->uid,'phone_status');
        if($phones!=1){
        	$this->error("请先通过手机认证后再进行投标。");
        }
        // $emails = M('members_status')->getFieldByUid($this->uid,'email_status');
        // if($emails!=1){
        // 	//$this->error("请先通过邮箱认证后再进行投标。",3);
        // }
        if($binfo['pause']==1){
        	$this->error("此标当前已经截止，请等待管理员开启。");
        }
        // 50 > 10
        if ($money < $binfo['borrow_min']) {
            $this->error('此项目最小投资金额为'.$binfo['borrow_min'].'元');
        }
        if ($money > $binfo['borrow_max'] and 0 != $binfo['borrow_max']) {
            $this->error('此项目最大投资金额为'.$binfo['borrow_max'].'元');
        }

        if ($binfo['has_vouch'] < $binfo['borrow_money'] && 2 == $binfo['borrow_type']) {
            $this->error('此标担保还未完成，您可以担保此标或者等担保完成再投资');
        }
        if (!empty($binfo['password'])) {
            if (empty($_POST['borrow_pass'])) {
                $this->error('此标是定向项目，必须验证项目密码');
            } elseif ($binfo['password'] != $_POST['borrow_pass']) {
                $this->error('投标密码不正确');
            }
        }

        if ($binfo["new_user_only"] == 1) {
            $new_user_time = time() - $this->glo['new_user_time']*24*60*60;
            $where = null;
            $where['investor_uid'] = array('eq',"{$this->uid}");
            if($this->glo['new_user_time'] > 0) {
                $where['add_time'] = array(array('egt', "{$new_user_time}"), array('elt', time()));
            }
            $where['borrow_id'] = array('neq',1);
            $newUser = count(M('borrow_investor')->where($where)->find());
            $where = null;
            $where['bespeak_uid'] = array('eq',"{$this->uid}");
            if($this->glo['new_user_time'] > 0) {
                $where['add_time'] = array(array('egt', "{$new_user_time}"), array('elt', time()));
            }
            $where['borrow_id'] = array('neq',1);
            $newUser = max($newUser,count(M('bespeak')->where($where)->find()));
            if($newUser > 0) {
                $this->error('此标是新手专享标');
            }
        }

        //投标总数检测
        $capital = M('borrow_investor')->where("borrow_id={$borrow_id} AND investor_uid={$this->uid}")->sum('investor_capital');
        if (($capital + $money) > $binfo['borrow_max'] && $binfo['borrow_max'] > 0) {
            $xtee = $binfo['borrow_max'] - $capital;
            $this->error("您已投标{$capital}元，上限为{$binfo['borrow_max']}元，你最多只能再投{$xtee}元");
        }

        if($binfo['bespeak_able'] == 1 && $binfo['start_time'] + $binfo["bespeak_days"]*60*60*24 > time()){
            $bespeak_money = M('bespeak')->where("borrow_id={$borrow_id} AND bespeak_uid={$this->uid} AND bespeak_status=0")->sum('bespeak_money');
            if(!$bespeak_money || $bespeak_money == 0){
                //预约已投金额
                $bespeak_invest_money = M('bespeak')->where("borrow_id={$borrow_id} and bespeak_status = 1")->sum('bespeak_money');
                //非预约已投金额
                $bespeak_not_invest_money = bcsub($binfo["has_borrow"], $bespeak_invest_money, 2);
                //非预约金额
                $not_invest_money = bcsub($binfo["borrow_money"], $binfo["bespeak_money"], 2);
                //可投金额
                $can_invest_money = bcsub($not_invest_money, $bespeak_not_invest_money, 2);
                if($can_invest_money == 0){
                    $this->error("此标为预约标，当前预约已满，您未参与预约，请等待");
                } elseif($money > $can_invest_money){
                    $this->error("此标为预约标，当前最多可投{$can_invest_money}元");
                } elseif(bcsub($can_invest_money, $binfo['borrow_min'], 2) > 0 && bcsub($can_invest_money, $binfo['borrow_min'], 2) < $binfo["borrow_min"]){
                    $this->error("此标为预约标，如果您投{$money}元，将导致最后一次投标最多只能投".bcsub($can_invest_money, $binfo['borrow_min'], 2)."元，小于最小投标金额{$binfo['borrow_min']}元,请重新选择投标金额");
                }
            }elseif($bespeak_money != $money){
                $this->error("此标为预约标，您已参与预约，此次支持金额必须与预约金额相等");
            }
            if($borrow_id == 1 || $is_experience == 1){
                $this->error("新手标不支持预约");
            }
            if($bonus_id > 0){
                $this->error("预约标不支持使用红包");
            }
        }

        //$need = $binfo['borrow_money'] - $binfo['has_borrow'];
        // $caninvest = $need - $binfo['borrow_min'];
        //$need = getfloatvalue($binfo['borrow_money'] - $binfo['has_borrow'],2);
        //$caninvest = getfloatvalue($need - $binfo['borrow_min'],2);
        $need = bcsub($binfo['borrow_money'], $binfo['has_borrow'], 2);
        $caninvest = bcsub($need, $binfo['borrow_min'], 2);
        //exit;
        if ($money > $caninvest && 0 != ($need - $money)) {
            if (0 == (int) $need or '0.00' == $need) {
                $this->error("尊敬的{$uname}，此项目已经投满");
            }
            if ($money > $need) {
                $this->error("尊敬的{$uname}，此项目还差{$need}元满标,您最多只能再投{$need}元");
                $money = $need;
            }

//            $msg = "尊敬的{$uname}，此项目还差{$need}元满标,如果您投标{$money}元，将导致最后一次投标最多只能投".(bcsub($need, $money, 2))."元，小于最小投标金额{$binfo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满标</font>或者投标金额必须<font color='#FF0000'>小于等于{$caninvest}元</font>";
//            if ($caninvest < $binfo['borrow_min']) {
//                $msg = "尊敬的{$uname}，此标还差{$need}元满标,如果您投标{$money}元，将导致最后一次投标最多只能投".(bcsub($need, $money, 2))."元，小于最小投标金额{$binfo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满标</font>即投标金额必须<font color='#FF0000'>等于{$need}元</font>";
//            }
//            $this->error($msg);
        }

        if (($need - $money) < 0) {
            $this->error("尊敬的{$uname}，此项目还差{$need}元满标,您最多只能再投{$need}元");
//            $jsons['msg'] ='尊敬的{$uname}，此项目的剩余额度是：{$money}元，请重新选择购买！';//'投资金额错误.';
//            $jsons['status'] = '0';
//            outJson($jsons);
//
		     $money = $need;
        } else {
            $capital = M('borrow_investor')->where("borrow_id={$borrow_id}")->sum('investor_capital');
            if ($capital > $binfo['borrow_money']) {
                $this->error('投资金额错误4');
            }

            $capital = M('investor_detail')->where("borrow_id={$borrow_id}")->sum('capital');
            if ($capital > $binfo['borrow_money']) {
                $this->error('投资金额错误5');
            }
            // $done = investMoney($this->uid,$borrow_id,$money);
            writeLog($memberinterest_id);
            if ($memberinterest_id > 0) {
                //加息券校验
                $canUseInterest = canUseInterest($this->uid, $memberinterest_id);
                writeLog($canUseInterest);
                if (0 == $canUseInterest['status']) {
                    $this->error('加息券不可用');
                }
                $interest_rate = $canUseInterest['interest_rate'];
            } else {
                $interest_rate = 0;
            }
            //体验金校验
            $canInvestMoney = canInvestMoney($this->uid, $borrow_id, $money, 0, $is_experience, '0', $bonus_id, text(@$_POST['borrow_pass']));
            if (0 == $canInvestMoney['status']) {
                $this->error($canInvestMoney['tips']);
            }
            $money_bonus = $canInvestMoney['money_bonus'];

            if (1 == $canInvestMoney['money_type'] && $amoney < $money) {
                $this->error("尊敬的{$uname}，您准备投标{$money}元，但您的账户鱼币+可用余额为{$amoney}元，请先去充值再投项目.");
            }

            //$done = investMoney($this->uid, $borrow_id, $money, '0', '1', $is_experience, $memberinterest_id, $bonus_id, $money_bonus, text(@$_POST['borrow_pass']));
            $zt=$_POST["zt"];
            $sg=$_POST["sg"];

            $ktfs=$money/$binfo["borrow_min"];
            $zfs=$zt+$sg;

//            if($ktfs<$zfs){
//                if($sg>$zfs){
//                    $sg=$zfs;
//                }else{
//                    $zt=$ktfs-$sg;
//                }
//            }

            $ktfs=$money/$binfo["borrow_min"];
            $zfs=$zt+$sg;
            if($binfo["shoujia"]==0){
                if($ktfs>=$zfs){
                    $sg=$zfs;
                }else{
                    $sg=$ktfs;
                }
                $zt=0;
            }else{
                if($ktfs<$zfs){
                    if($sg>=$ktfs){
                        $sg=$ktfs;
                        $zt=0;
                    }else{
                        $zt=$ktfs-$sg;
                    }
                }
            }

            $xsinvestor_capital=$sg*$binfo["borrow_min"];
            if(($zt+$sg)<=0){
                $this->error('项目份额错误!');
            }


            if($is_experience==1){
                    $money_experience = M("member_money")->where("uid=".$this->uid)->find();

            // 	var_dump($money_experience);die;
                    $done = investMoney($this->uid,$borrow_id,$money,'0','1', 1,$memberinterest_id,$bonus_id,$money_bonus,text(@$_POST['borrow_pass']),$zt,$sg,$xsinvestor_capital);
                    if($done===true) {
                        $zrm=$binfo["borrow_money"]+$money;
                        M('borrow_info')->where("id={$borrow_id}")->save(['borrow_money'=>$zrm]);
                    }
             }else{
                    $done = investMoney($this->uid,$borrow_id,$money,'0','1', 0,$memberinterest_id,$bonus_id,$money_bonus,text(@$_POST['borrow_pass']),$zt,$sg,$xsinvestor_capital);
             }
        }

        //$this->display("Public:_footer");
        //$this->assign("waitSecond",1000);

        if (true === $done) {
        	    $this->success("恭喜成功投资{$money}元（其中使用红包{$money_bonus}元）!", U('/member/tendout/index', array('type' => 1)));
         // $this->success("恭喜成功投资{$money}元（其中使用红包{$money_bonus}元）!", U('invest/detail', array('id' => $borrow_id)));
        } elseif ($done) {
            $this->error($done);
        } else {
            $this->error('对不起，投资失败，请重试!');
        }
    }
    public function bespeakmoney()
    {
        if (!$this->uid) {
            $this->error('请先登录', '/member/common/login.html');
        }
        /****** 防止模拟表单提交 *********/
        $cookieKeyS = cookie(strtolower(MODULE_NAME).'-invest');
        if ($cookieKeyS != $_REQUEST['cookieKey']) {
        }

        /****** 防止模拟表单提交 *********/
        $money = $_POST['money'];
        writeLog($_POST);
        $borrow_id = (int) ($_POST['borrow_id']);

        $m = M('member_money')->field('account_money,back_money')->find($this->uid);
        $amoney = $m['account_money'] + $m['back_money'];
        $uname = session('u_user_name');

        $vm = getMinfo($this->uid, 'm.pin_pass,mm.account_money,mm.back_money,mm.money_experience');
        $pin_pass = $vm['pin_pass'];
        $pin = md5($_POST['pin']);
        if($pin_pass==''){
            $this->error('支付密码为空，请您设置密码',U("/member/user/password", array('type' => 1)));
            exit();
        }
        if ($pin != $pin_pass) {
            $this->error('支付密码错误，请重试');
            exit();
        }
        $binfo = M('borrow_info')->field('borrow_money,has_borrow,has_vouch,borrow_max,borrow_min,borrow_type,password,pause,max_limit,start_time,new_user_only,bespeak_able,bespeak_money,bespeak_days')->find($borrow_id);

        if(($_POST['money']/$binfo['borrow_min'])<=0){
            var_dump($_POST['invnums']);die;
            $this->error("请输入正确的数值");die;
        }

        $binfo['borrow_max'] = $binfo['borrow_min']*$binfo['max_limit'];

        $ids = M('members_status')->getFieldByUid($this->uid, 'id_status');
        if($ids!=1){
            $this->error("请先通过实名认证后再进行预约。",3);
        }
        $phones = M('members_status')->getFieldByUid($this->uid,'phone_status');
        if($phones!=1){
            $this->error("请先通过手机认证后再进行预约。",3);
        }
        if($binfo['pause']==1){
            $this->error("此标当前已经截止，请等待管理员开启。",3);
        }
        if ($money < $binfo['borrow_min']) {
            $this->error('此项目最小投资金额为'.$binfo['borrow_min'].'元');
        }
        if ($money > $binfo['borrow_max'] and 0 != $binfo['borrow_max']) {
            $this->error('此项目最大投资金额为'.$binfo['borrow_max'].'元');
        }

        if ($binfo['has_vouch'] < $binfo['borrow_money'] && 2 == $binfo['borrow_type']) {
            $this->error('此标担保还未完成，您可以担保此标或者等担保完成再预约');
        }
        if (!empty($binfo['password'])) {
            if (empty($_POST['borrow_pass'])) {
                $this->error('此标是定向项目，必须验证项目密码');
            } elseif ($binfo['password'] != $_POST['borrow_pass']) {
                $this->error('投标密码不正确');
            }
        }
        if ($money < 1) {
            $this->error('最低预约金额为1元');
        }
        if ($binfo['bespeak_able'] != 1) {
            $this->error('此标不支持预约');
        }
	if($binfo["start_time"] < time()){
            $this->error('此标不在预热期');
        }

        if ($binfo["new_user_only"] == 1) {
            $new_user_time = time() - $this->glo['new_user_time']*24*60*60;
            $where = null;
            $where['investor_uid'] = array('eq',"{$this->uid}");
            if($this->glo['new_user_time'] > 0) {
                $where['add_time'] = array(array('egt', "{$new_user_time}"), array('elt', time()));
            }
            $where['borrow_id'] = array('neq',1);
            $newUser = count(M('borrow_investor')->where($where)->find());
            $where = null;
            $where['bespeak_uid'] = array('eq',"{$this->uid}");
            if($this->glo['new_user_time'] > 0) {
                $where['add_time'] = array(array('egt', "{$new_user_time}"), array('elt', time()));
            }
            $where['borrow_id'] = array('neq',1);
            $newUser = max($newUser,count(M('bespeak')->where($where)->find()));
            if ($newUser > 0) {
                $this->error('此标是新手专享标');
            }
        }

        if($borrow_id == 1){
            $this->error('新手标不支持预约');
        }

        $capital = M('bespeak')->where("borrow_id={$borrow_id} AND bespeak_uid={$this->uid}")->find();
        if($capital){
            $this->error("您已参与过预约");
        }

        if ($binfo['has_vouch'] < $binfo['borrow_money'] && 2 == $binfo['borrow_type']) {
            $this->error('此标担保还未完成，您可以担保此标或者等担保完成再预约');
        }


        //预约已投金额
        $bespeak_invest_money = M('bespeak')->where("borrow_id={$borrow_id} and bespeak_status = 1")->sum('bespeak_money');
        //非预约已投金额
        $bespeak_not_invest_money = bcsub($binfo["has_borrow"], $bespeak_invest_money, 2);
        //非预约金额
        $not_invest_money = bcsub($binfo["borrow_money"], $binfo["bespeak_money"], 2);
        //可投金额
        $need = bcsub($not_invest_money, $bespeak_not_invest_money, 2);

        $caninvest = bcsub($need, $binfo['borrow_min'], 2);
        if (0 == (int) $need or '0.00' == $need) {
            $this->error("尊敬的{$uname}，此项目预约已满", 3);
        }
        if ($money > $need) {
            $this->error("尊敬的{$uname}，此项目还差{$need}元满预约,您最多只能再预约{$need}元");
        }
        $capital = M('bespeak')->where("borrow_id={$borrow_id}")->sum('bespeak_money');
        if ($capital > $binfo['borrow_money']) {
            $this->error('投资金额错误4');
        }
        $capital = M('investor_detail')->where("borrow_id={$borrow_id}")->sum('capital');
        if ($capital > $binfo['borrow_money']) {
            $this->error('投资金额错误5');
        }
        if ($money > $caninvest && 0 != ($need - $money)) {
            $msg = "尊敬的{$uname}，此项目还差{$need}元满预约,如果您预约{$money}元，将导致最后一次预约最多只能预约".(bcsub($need, $money, 2))."元，小于最小预约金额{$binfo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满预约</font>或者预约金额必须<font color='#FF0000'>小于等于{$caninvest}元</font>";
            if ($caninvest < $binfo['borrow_min']) {
                $msg = "尊敬的{$uname}，此标还差{$need}元满预约,如果您预约{$money}元，将导致最后一次预约最多只能预约".(bcsub($need, $money, 2))."元，小于最小预约金额{$binfo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满预约</font>即预约金额必须<font color='#FF0000'>等于{$need}元</font>";
            }
            $this->error($msg);
        }

        if ($amoney < $money*$this->glo["bespeak_point"]) {
            $this->error("尊敬的{$uname}，您准备预约{$money}元，需要支付".$money*$this->glo["bespeak_point"]."元，但您的账户可用余额为{$amoney}元，请先去充值再预约项目.");
        }
        $done = bespeakMoney($this->uid,$borrow_id,$money);

        if (true === $done) {
            $this->success("成功预约{$money}元,请于开标后".Sec2Time(floatval($binfo["bespeak_days"]) * 24 * 60 * 60)."天内交清余款，完成投标，逾期预约作废", U('/member/tendout/index', array('type' => 1)));
        } elseif ($done) {
            $this->error($done);
        } else {
            $this->error('对不起，预约失败，请重试!');
        }
    }

    public function addvote()
    {
        if (!$this->uid) {
            ajaxmsg('请先登陆', 0);
        }

        $borrow_id = (int) (@$_POST['borrow_id']);
        $status = (int) (@$_POST['status']) ? 1 : 0;
        $can_vote = M('borrow_info')->getFieldById($borrow_id, 'can_vote');
        if (1 != $can_vote) {
            ajaxmsg('投票未开放！', 0);
        }
        $canVote = M('borrow_investor')->where('borrow_id='.$borrow_id.' and investor_uid = '.$this->uid)->count('id');
        if (empty($canVote)) {
            ajaxmsg('未投资此项目不可投票！', 0);
        }
        $voteCount = M('borrow_vote')->where('borrow_id='.$borrow_id." and vote_uid = '{$this->uid}'")->count('id');
        if ($voteCount > 0) {
            ajaxmsg('你已经投过票！', 0);
        } else {
            $data['add_time'] = time();
            $data['vote_uid'] = $this->uid;
            $data['borrow_id'] = $borrow_id;
            $data['status'] = $status;
            $newid = M('borrow_vote')->add($data);
            if ($newid) {
                $user_phone = M('members')->getFieldById($this->uid, 'user_phone');
                if (preg_match("/^1[34578]\d{9}$/", $user_phone)) {
                    $borrow_name = M('borrow_info')->getFieldById($borrow_id, 'borrow_name');
                    $content = "尊敬的用户，您好！你对“{$borrow_name}”项目投票成功，感谢您的参与！";
                    // $sendRs = sendsms($user_phone, $content,1);
                }
                ajaxmsg('投票成功', 1);
            } else {
                ajaxmsg('投票失败', 0);
            }
        }
    }

    public function addcomment()
    {
        $data['comment'] = text($_POST['comment']);
        if (!$this->uid) {
            ajaxmsg('请先登陆', 0);
        }
        if (empty($data['comment'])) {
            ajaxmsg('评论内容不能为空', 0);
        }
        $bid =  $_POST['tid'];
        $inv = m("borrow_investor")->where("borrow_id={$bid} and investor_uid={$this->uid}")->count();
        if(empty($inv)){
            ajaxmsg('没有投资无法发表意见', 0);
            exit();
        }

        $data['type'] = 1;
        $data['add_time'] = time();
        $data['uid'] = $this->uid;        
        $data['uname'] = session('u_user_name');
        $data['tid'] = (int) ($_POST['tid']);
        $data['name'] = M('borrow_info')->getFieldById($data['tid'], 'borrow_name');
        $data['stars'] = (int) ($_POST['stars']);
        $newid = M('comment')->add($data);
        if ($newid) {
            ajaxmsg();
        } else {
            ajaxmsg('评论失败，请重试', 0);
        }
    }
    public function adddynamic()
    {
        $data['dycomment'] = text($_POST['dycomment']);
        if (!$this->uid) {
            ajaxmsg('请先登陆', 0);
        }
        if (empty($data['dycomment'])) {
            ajaxmsg('动态内容不能为空', 0);
        }
        $bid =  $_POST['tid'];
        $binf = m("borrow_info")->where("id={$bid}")->find();
        if($binf['borrow_uid']!=$this->uid){
            ajaxmsg('不是发起方人无法更新动态', 0);
            exit();
        }
		$data['imgs'] = $_POST['imgs'];
        		$data['typename'] = $_POST['typename'];
        $data['type'] = 1;
        $data['add_time'] = time();
        $data['uid'] = $this->uid;
        $data['uname'] = session('u_user_name');
        $data['tid'] = (int) ($_POST['tid']);
        $data['name'] = M('borrow_info')->getFieldById($data['tid'], 'borrow_name');
        $data['stars'] = (int) ($_POST['stars']);		
        $newid = M('dynamic')->add($data);		//var_dump(M('dynamic')->getlastsql());die;
        if ($newid) {
            ajaxmsg();
        } else {
            ajaxmsg('更新动态失败，请重试', 0);
        }
    }

    public function addyuetan()
    {
        $data['comment'] = text($_POST['comment']);
        if (!$this->uid) {
            ajaxmsg('请先登陆', 0);
        }
        if (empty($data['comment'])) {
            ajaxmsg('内容不能为空', 0);
        }
        $data['type'] = 1;
        $data['add_time'] = time();
        $data['uid'] = $this->uid;
        $data['tid'] = (int) ($_POST['tid']);
        $data['touid'] = $_POST['touid'];
        $newid = M('comment_yuetan')->add($data);
        if ($newid) {
            ajaxmsg();
        } else {
            ajaxmsg('发送失败，请重试', 0);
        }
    }

    public function addguanzhu()
    {
        $jsons['status'] = '0';
        $jsons['message'] = '关注失败';
        if (!$this->uid) {
            $jsons['message'] = '请先登录';
            exit(json_encode($jsons));
        } else {
           // setInc
           
            $b = M('pro_guanzhu')->where('bid='.$_POST['tid']." and uid = {$this->uid}")->count('id');
            if ($b>0) {
                // M('borrow_info')->where("id =".$_POST['tid'])->setDec("gz_num",1);
                 $Model = new Model(); // 实例化一个model对象 没有对应任何数据表

                    $sql="update lzh_borrow_info set  gz_num=gz_num-1  where id =".$_POST['tid'];
                     //echo $sql;die;
                    $Model->query($sql);


                M('pro_guanzhu')->where('bid='.$_POST['tid']." and uid = {$this->uid}")->delete();
                $jsons['message'] = '取消关注成功';
                $jsons['status'] = '1';
            } else {
                $data['add_time'] = time();
                $data['uid'] = $this->uid;
                $data['bid'] = (int) ($_POST['tid']);
                $newid = M('pro_guanzhu')->add($data);
                if ($newid) {
                     $Model = new Model(); // 实例化一个model对象 没有对应任何数据表
                    $sql="update lzh_borrow_info set  gz_num=gz_num+1  where id =".$_POST['tid'];
                   //  echo $sql;die;
                    $Model->query($sql);
                   
                    // M('borrow_info')->where("id =".$_POST['tid'])->setInc("gz_num",1);
                    $jsons['message'] = '关注成功';
                    $jsons['status'] = '1';
                }
            }
        }
        $jsons['has_guanzhu'] = get_pro_guanzhu((int) ($_POST['tid']));
        exit(json_encode($jsons));
    }

    public function jubao()
    {
        if ($_POST['checkedvalue']) {
            $data['reason'] = text($_POST['checkedvalue']);
            $data['text'] = text($_POST['thecontent']);
            $data['uid'] = $this->uid;
            $data['uemail'] = text($_POST['uemail']);
            $data['b_uid'] = text($_POST['b_uid']);
            $data['b_uname'] = text($_POST['theuser']);
            $data['add_time'] = time();
            $data['add_ip'] = get_client_ip();
            $newid = M('jubao')->add($data);
            if ($newid) {
                exit('1');
            } else {
                exit('0');
            }
        } else {
            $id = (int) ($_GET['id']);
            $u['id'] = $id;
            $u['uname'] = M('members')->getFieldById($id, 'user_name');
            $u['uemail'] = M('members')->getFieldById($this->uid, 'user_email');
            $this->assign('u', $u);
            $data['content'] = $this->fetch('Public:jubao');
            exit(json_encode($data));
        }
    }

    public function ajax_invest()
    {
        if (!$this->uid) {
            ajaxmsg('请先登陆', 0);
        }

        /****** 防止模拟表单提交 *********/
        $cookieTime = 15 * 3600;
        $cookieKey = md5(MODULE_NAME.'-'.time());
        cookie(strtolower(MODULE_NAME).'-invest', $cookieKey, $cookieTime);
        $this->assign('cookieKey', $cookieKey);
        /****** 防止模拟表单提交 *********/
        $pre = C('DB_PREFIX');
        $id = (int) ($_GET['id']);

        $Bconfig = C('BORROW');
        $field = 'id,borrow_uid,borrow_money,borrow_status,borrow_type,has_borrow,has_vouch,borrow_interest_rate,borrow_duration,repayment_type,collect_time,borrow_min,borrow_max,password,borrow_use,pause,daishou,daishou_money,can_invest,is_bonus,is_experience';
        $vo = M('borrow_info')->field($field)->find($id);
        if ($this->uid == $vo['borrow_uid']) {
            ajaxmsg('不能去投自己的项目', 0);
        }

        $can_invest = unserialize($vo['can_invest']);
        $minfo = getMinfo($this->uid, true);
        /*$levename=getLeveId($minfo["credits"]);
        if(!in_array($levename, $can_invest)){
             ajaxmsg("您没有权限投此项目！",0);
        }*/

        if (3 == $vo['borrow_type']) {  //秒标检查
            if (0 != $vo['daishou']) {
                $dashou_name = array(0 => '不限制', 1 => '全部待收', 2 => '当月待收', 3 => '当日待收');

                if ($vo['daishou_money'] > daishou($vo['daishou'], $this->uid)) {
                    ajaxmsg('当前标的'.$dashou_name[$vo['daishou']].'限制为：'.$vo['daishou_money'], 0);
                }
            }
        }

        if (1 == $vo['pause']) {
            ajaxmsg('此标当前已经截标，请等待管理员开启。', 0);
        }

        // if($vo['borrow_status'] <> 2) ajaxmsg("只能投正在众筹中的项目",0);
        //担保标检测
        if (2 == $vo['borrow_type']) {
            if ($vo['has_vouch'] < $vo['borrow_money']) {
                ajaxmsg('此标担保还未完成，您可以担保此标或者等担保完成再投标', 0);
            }
        }

        $vo['need'] = bcsub($vo['borrow_money'], $vo['has_borrow'], 2);
        if ($vo['need'] < 0) {
            ajaxmsg('投标金额不能超出众筹剩余金额', 0);
        }
        $vo['lefttime'] = $vo['collect_time'] - time();
        $vo['progress'] = getFloatValue($vo['has_borrow'] / $vo['borrow_money'] * 100, 4); //ceil($vo['has_borrow']/$vo['borrow_money']*100);
        $vo['uname'] = M('members')->getFieldById($vo['borrow_uid'], 'user_name');
        $time1 = microtime(true) * 1000;
        $vm = getMinfo($this->uid, 'm.pin_pass,mm.account_money,mm.back_money,mm.money_experience');
        $amoney = $vm['account_money'] + $vm['back_money'];

        ////////////////////投标时自动填写可投标金额在投标文本框 2013-07-03 fan////////////////////////
        $capital = M('borrow_investor')->where("borrow_id={$id} AND investor_uid={$this->uid}")->sum('investor_capital');
        if ($amoney < (float) ($vo['borrow_min'])) {
            // ajaxmsg("您的账户可用余额小于本项目的最小投标金额限制，不能投标！",0);
        } elseif ($amoney >= (float) ($vo['borrow_max']) && (float) ($vo['borrow_max']) > 0) {
            $toubiao = bcsub($vo['borrow_max'], $capital, 2);
        } elseif ($amoney >= (float) ($vo['need'])) {
            $toubiao = (float) ($vo['need']);
        } else {
            $toubiao = (float) $amoney;
        }

        if ($toubiao > $vo['need']) {
            $toubiao = $vo['need'];
        }
        if (isset($_GET['money'])) {
            $vo['toubiao'] = $_GET['money'];
        } else {
            $vo['toubiao'] = $toubiao;
        }

        ////////////////////投标时自动填写可投标金额在投标文本框 2013-07-03 fan////////////////////////
        //红包信息
        $bonus_list = M('member_bonus')->where("uid='{$this->uid}' and status = 1 and (".time().' > start_time and '.time().' < end_time )')->field('id,money_bonus,end_time,bonus_invest_min')->select();
        $this->assign('bonus_list', $bonus_list);

        $pin_pass = $vm['pin_pass'];
        $has_pin = (empty($pin_pass)) ? 'no' : 'yes';
        $this->assign('has_pin', $has_pin);
        $this->assign('vo', $vo);
        $this->assign('account_money', $amoney);
        $this->assign('money_experience', $vm['money_experience']);
        $this->assign('Bconfig', $Bconfig);
        $data['content'] = $this->fetch();
        ajaxmsg($data);
    }

    public function ajax_vouch()
    {
        /****** 防止模拟表单提交 *********/
        $cookieTime = 15 * 3600;
        $cookieKey = md5(MODULE_NAME.'-'.time());
        cookie(strtolower(MODULE_NAME).'-vouch', $cookieKey, $cookieTime);
        $this->assign('cookieKey', $cookieKey);
        /****** 防止模拟表单提交 *********/
        $pre = C('DB_PREFIX');
        $id = (int) ($_GET['id']);
        $Bconfig = require C('APP_ROOT').'Conf/borrow_config.php';
        $field = 'id,borrow_uid,borrow_money,has_borrow,borrow_interest_rate,borrow_duration,repayment_type,collect_time,has_vouch,reward_vouch_rate,borrow_min,borrow_max';
        $vo = M('borrow_info')->field($field)->find($id);

        $vo['need'] = $vo['borrow_money'] - $vo['has_borrow'];
        $vo['lefttime'] = $vo['collect_time'] - time();
        $vo['progress'] = getFloatValue($vo['has_borrow'] / $vo['borrow_money'] * 100, 4); //ceil($vo['has_borrow']/$vo['borrow_money']*100);
        $vo['vouch_progress'] = getFloatValue($vo['has_vouch'] / $vo['borrow_money'] * 100, 4); //ceil($vo['has_vouch']/$vo['borrow_money']*100);
        $vo['vouch_need'] = $vo['borrow_money'] - $vo['has_vouch'];
        $vo['uname'] = M('members')->getFieldById($vo['borrow_uid'], 'user_name');
        $time1 = microtime(true) * 1000;
        $vm = getMinfo($this->uid, 'm.pin_pass,mm.invest_vouch_cuse');

        ////////////////////投标时自动填写可投标金额在投标文本框 2013-07-03 fan////////////////////////
        if (100 == $vo['vouch_progress']) {
            $amoney = $vm['account_money'] + $vm['back_money'];
            if ($amoney < (float) ($vo['borrow_min'])) {
                ajaxmsg('您的账户可用余额小于本标的最小投标金额限制，不能投标！', 0);
            } elseif ($amoney >= (float) ($vo['borrow_max']) && (float) ($vo['borrow_max']) > 0) {
                $toubiao = (int) ($vo['borrow_max']);
            } elseif ($amoney >= (float) ($vo['need'])) {
                $toubiao = (int) ($vo['need']);
            } else {
                $toubiao = floor($amoney);
            }

            $vo['toubiao'] = $toubiao;
        }
        ////////////////////投标时自动填写可投标金额在投标文本框 2013-07-03 fan////////////////////////

        $pin_pass = $vm['pin_pass'];
        $has_pin = (empty($pin_pass)) ? 'no' : 'yes';
        $this->assign('has_pin', $has_pin);
        $this->assign('vo', $vo);
        $this->assign('invest_vouch_cuse', $vm['invest_vouch_cuse']);
        $this->assign('Bconfig', $Bconfig);
        $data['content'] = $this->fetch();
        ajaxmsg($data, 1);
    }

    public function vouchcheck()
    {
        $pre = C('DB_PREFIX');
        if (!$this->uid) {
            ajaxmsg('', 3);
        }
        $pin = md5($_POST['pin']);
        $money = (int) ($_POST['vouch_money']);
        $vm = getMinfo($this->uid, 'm.pin_pass,mm.invest_vouch_cuse');
        $amoney = $vm['invest_vouch_cuse'];
        $uname = session('user_name');
        $pin_pass = $vm['pin_pass'];
        $amoney = (float) $amoney;
        if ($pin != $pin_pass) {
            ajaxmsg('支付密码错误，请重试', 0);
        }
        if ($money > $amoney) {
            $msg = "尊敬的{$uname}，您准备担保{$money}元，但您可用担保投资额度为{$amoney}元，要去申请更高额度吗？";
            ajaxmsg($msg, 2);
        } else {
            $msg = "尊敬的{$uname}，您可用担保投资额度为{$amoney}元，您确认担保{$money}元吗？";
            ajaxmsg($msg, 1);
        }
    }

    public function vouchmoney()
    {
        if (!$this->uid) {
            exit;
        }
        /****** 防止模拟表单提交 *********/
        $cookieKeyS = cookie(strtolower(MODULE_NAME).'-vouch');
        if ($cookieKeyS != $_REQUEST['cookieKey']) {
            $this->error('数据校验有误');
        }
        /****** 防止模拟表单提交 *********/
        $money = (int) ($_POST['vouch_money']);
        $borrow_id = (int) ($_POST['borrow_id']);
        $rate = M('borrow_info')->getFieldById($borrow_id, 'reward_vouch_rate');
        $amoney = M('member_money')->getFieldByUid($this->uid, 'invest_vouch_cuse');
        $uname = session('u_user_name');
        if ($amoney < $money) {
            $this->error("尊敬的{$uname}，您准备担保{$money}元，但您可用担保投资额度为{$amoney}元，请先去申请更高额度.");
        }

        $saveVouch['borrow_id'] = $borrow_id;
        $saveVouch['uid'] = $this->uid;
        $saveVouch['uname'] = $uname;
        $saveVouch['vouch_money'] = $money;
        $saveVouch['vouch_reward_rate'] = $rate;
        $saveVouch['vouch_reward_money'] = getFloatValue($money * $rate / 100, 2);
        $saveVouch['add_ip'] = get_client_ip();
        $saveVouch['vouch_time'] = time();
        $newid = M('borrow_vouch')->add($saveVouch);

        if ($newid) {
            $done = M('member_money')->where("uid={$this->uid}")->setDec('invest_vouch_cuse', $money);
        }
        //$this->assign("waitSecond",1000);
        if (true == $done) {
            M('borrow_info')->where("id={$borrow_id}")->setInc('has_vouch', $money);
            $this->success("恭喜成功担保{$money}元");
        } else {
            $this->error('对不起，担保失败，请重试!');
        }
    }

    public function getarea()
    {
        $rid = (int) ($_GET['rid']);
        if (empty($rid)) {
            $data['NoCity'] = 1;
            exit(json_encode($data));
        }
        $map['reid'] = $rid;
        $alist = M('area')->field('id,name')->order('sort_order DESC')->where($map)->select();
        if (0 === count($alist)) {
            $str = "<option value=''>--该地区下无下级地区--</option>\r\n";
        } else {
            if (1 == $rid) {
                $str .= "<option value='0'>请选择省份</option>\r\n";
            }
            foreach ($alist as $v) {
                $str .= "<option value='{$v['id']}'>{$v['name']}</option>\r\n";
            }
        }
        $data['option'] = $str;
        $res = json_encode($data);
        echo $res;
    }

    public function addfriend()
    {
        if (!$this->uid) {
            ajaxmsg('请先登陆', 0);
        }
        $fuid = (int) ($_POST['fuid']);
        $type = (int) ($_POST['type']);
        if (!$fuid || !$type) {
            ajaxmsg('提交的数据有误', 0);
        }

        $save['uid'] = $this->uid;
        $save['friend_id'] = $fuid;
        $vo = M('member_friend')->where($save)->find();

        if (1 == $type) {//加好友
            if ($this->uid == $fuid) {
                ajaxmsg('您不能对自己进行好友相关的操作', 0);
            }
            if (is_array($vo)) {
                if (3 == $vo['apply_status']) {
                    $msg = '已经从黑名单移至好友列表';
                    $newid = M('member_friend')->where($save)->setField('apply_status', 1);
                } elseif (1 == $vo['apply_status']) {
                    $msg = '已经在你的好友名单里，不用再次添加';
                } elseif (0 == $vo['apply_status']) {
                    $msg = '已经提交加好友申请，不用再次添加';
                } elseif (2 == $vo['apply_status']) {
                    $msg = '好友申请提交成功';
                    $newid = M('member_friend')->where($save)->setField('apply_status', 0);
                }
            } else {
                $save['uid'] = $this->uid;
                $save['friend_id'] = $fuid;
                $save['apply_status'] = 0;
                $save['add_time'] = time();
                $newid = M('member_friend')->add($save);
                $msg = '好友申请成功';
            }
        } elseif (2 == $type) {//加黑名单
            if ($this->uid == $fuid) {
                ajaxmsg('您不能对自己进行黑名单相关的操作', 0);
            }
            if (is_array($vo)) {
                if (3 == $vo['apply_status']) {
                    $msg = '已经在黑名单里了，不用再次添加';
                } else {
                    $msg = '成功移至黑名单';
                    $newid = M('member_friend')->where($save)->setField('apply_status', 3);
                }
            } else {
                $save['uid'] = $this->uid;
                $save['friend_id'] = $fuid;
                $save['apply_status'] = 3;
                $save['add_time'] = time();
                $newid = M('member_friend')->add($save);
                $msg = '成功加入黑名单';
            }
        }
        if ($newid) {
            ajaxmsg($msg);
        } else {
            ajaxmsg($msg, 0);
        }
    }

    public function innermsg()
    {
        if (!$this->uid) {
            ajaxmsg('请先登陆', 0);
        }
        $fuid = (int) ($_GET['uid']);
        if ($this->uid == $fuid) {
            ajaxmsg('您不能对自己进行发送站内信的操作', 0);
        }
        $this->assign('touid', $fuid);
        $data['content'] = $this->fetch('Public:innermsg');
        ajaxmsg($data);
    }

    public function doinnermsg()
    {
        $touid = (int) ($_POST['to']);
        $msg = text($_POST['msg']);
        $title = text($_POST['title']);
        $newid = addMsg($this->uid, $touid, $title, $msg);
        if ($newid) {
            ajaxmsg();
        } else {
            ajaxmsg('发送失败', 0);
        }
    }

    public function view()
    {
        $id = (int) ($_GET['id']);
        if ('subsite' == $_GET['type']) {
            $vo = M('article_area')->find($id);
        } else {
            $vo = M('article')->find($id);
        }

        $this->assign('vo', $vo);

        //left
        $typeid = $vo['type_id'];
        $listparm['type_id'] = $typeid;
        $listparm['limit'] = 20;
        if ('subsite' == $_GET['type']) {
            $listparm['area_id'] = $this->siteInfo['id'];
            $leftlist = getAreaTypeList($listparm);
        } else {
            $leftlist = getTypeList($listparm);
        }

        $this->assign('leftlist', $leftlist);
        $this->assign('cid', $typeid);

        if ('subsite' == $_GET['type']) {
            $vop = D('Aacategory')->field('type_name,parent_id')->find($typeid);
            if (0 != $vop['parent_id']) {
                $this->assign('cname', D('Aacategory')->getFieldById($vop['parent_id'], 'type_name'));
            } else {
                $this->assign('cname', $vop['type_name']);
            }
        } else {
            $vop = D('Acategory')->field('type_name,parent_id')->find($typeid);
            if (0 != $vop['parent_id']) {
                $this->assign('cname', D('Acategory')->getFieldById($vop['parent_id'], 'type_name'));
            } else {
                $this->assign('cname', $vop['type_name']);
            }
        }

        $this->display();
    }
    public function delxmjd(){
        $id = (int) ($_GET['id']);
        $map["id"]=$id;
        $dyinfo = M('dynamic')->where($map)->find();
        if($this->uid!=$dyinfo["uid"]){
              $this->error("无法删除他人添加的信息");
        }else{
            $res= M('dynamic')->where($map)->delete();
            if($res){
                $this->success('登删除成功！');
            }else{
                $this->error('删除失败！');
            }
        }
    }
    public function detailxg(){
        $id = (int) ($_GET['id']);
        $map["id"]=$id;
        $dyinfo = M('dynamic')->where($map)->find();
        if($this->uid!=$dyinfo["uid"]){
              $this->error("无法修改他人添加的信息");
        }else{
            $this->assign("info",$dyinfo);
             $this->display();
        }
    }
    public function doxmjd(){
        $id = (int) ($_POST['id']);
        $map["id"]=$id;
        $dyinfo = M('dynamic')->where($map)->find();
        if($this->uid!=$dyinfo["uid"]){
              $this->error("无法修改他人添加的信息");
        }else{
            $data["add_time"]=strtotime($_POST["add_time"]);
            $res= M('dynamic')->where($map)->save($data);
            //var_dump( M('dynamic')->getlastsql());exit();
            if($res){
                $this->success('修改成功！',__URL__."/detail/id/".$dyinfo["tid"].".html");
            }else{
                $this->error('修改失败！');
            }
        }
    }
}
