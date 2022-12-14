<?php

// 本类由系统自动生成，仅供测试用途

class InvestAction extends HCommonAction {

	

	    public function is_order(){

            $id      = isset($_GET['id']) ? intval($_GET['id']) : 1;

			 $guige      = isset($_GET['guige']) ? intval($_GET['guige']) : 1;

			  $num    = isset($_GET['num']) ? intval($_GET['num']) : 1;

			$mone= intval($guige)*intval($num);

			  $this->assign("num", $num);  

			  $this->assign("mone", empty($mone)?0:$mone);  

			   $this->assign("guige", $guige);  

        $pre     = C('DB_PREFIX');

        $Bconfig = C("BORROW");

        /****** 防止模拟表单提交 *********/

		$cookieTime = 15*3600;

		$cookieKey=md5(MODULE_NAME."-".time());

		cookie(strtolower(MODULE_NAME)."-invest",$cookieKey,$cookieTime);

		$this->assign("cookieKey",$cookieKey);

		/****** 防止模拟表单提交 *********/

//var_dump($Bconfig);



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

                    $this->assign('mone', $bespeak["bespeak_money"]);

                    $this->assign('invnums', (int)$bespeak["bespeak_money"] / $borrowinfo["borrow_min"]);

                    $this->assign('bonus_id', 0);

                    $this->assign('invest_nums', (int)$bespeak["bespeak_money"] / $borrowinfo["borrow_min"]);

                    $this->assign('bespeak', $bespeak);

                    $this->assign('point', $bespeak["bespeak_point"]);

                }

            }

        }



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



//		var_dump($borrowinfo['borrow_uid'],$this->uid);

		   if (!is_array($borrowinfo) ||($borrowinfo['borrow_status'] == 0)){

		   	

            	    	echo "<script type='text/javascript'>";

			 		echo "alert('数据有误！');";

        				echo "window.history.go(-1);";

			        	echo "</script>";die;

        }

		    if (is_array($borrowinfo) &&($this->uid == $borrowinfo['borrow_uid'])) {

            			    	echo "<script type='text/javascript'>";

			 		echo "alert('不能投自己的标哦！');";

        				echo "window.history.go(-1);";

			        	echo "</script>";die;

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

		$bonus_list =  M('member_bonus')->where("uid='{$this->uid}' and status = 1 and (".time()." > start_time and ".time()." < end_time )")->field('id,money_bonus,end_time,bonus_invest_min')->order("money_bonus DESC")->select();

		$this->assign("bonus_list",$bonus_list);

//		var_dump($bonus_list);

//var_dump(M('member_bonus')->getlastsql());die;

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

	    //	$this->display();

	    	

	    }



    public function bespeak_order(){
        $id = isset($_GET['id']) ? intval($_GET['id']) : 1;
        $guige = isset($_GET['guige']) ? intval($_GET['guige']) : 1;
        $num = isset($_GET['num']) ? intval($_GET['num']) : 1;
        $mone = intval($guige)*intval($num);
        $this->assign("num", $num);
        $this->assign("mone", empty($mone)?0:$mone);
        $this->assign("guige", $guige);
        /****** 防止模拟表单提交 *********/
        $cookieTime = 15*3600;
        $cookieKey=md5(MODULE_NAME."-".time());
        cookie(strtolower(MODULE_NAME)."-invest",$cookieKey,$cookieTime);
        $this->assign("cookieKey",$cookieKey);
        /****** 防止模拟表单提交 *********/

        $borrowinfo = M("borrow_info")->field(true)->find($id);

        if ($borrowinfo['bespeak_able'] != 1) {
            echo "<script type='text/javascript'>";
            echo "alert('此标不支持预约');";
            echo "window.history.go(-1);";
            echo "</script>";die;
        }
        if($borrowinfo["start_time"] < time()){
            echo "<script type='text/javascript'>";
            echo "alert('此标不在预热期');";
            echo "window.history.go(-1);";
            echo "</script>";die;
        }

        if (bcsub($borrowinfo['borrow_money'], $borrowinfo['bespeak_money'], 2) == 0 and $borrowinfo["borrow_status"] == 1) {
            echo "<script type='text/javascript'>";
            echo "alert('预约已满');";
            echo "window.history.go(-1);";
            echo "</script>";die;
        }
        if($id != 1){
            if (!is_array($borrowinfo) || ($borrowinfo['borrow_status'] == 0 && $this->uid != $borrowinfo['borrow_uid'])) {
                $this->error("数据有误");
            }
            if (!is_array($borrowinfo) ||($borrowinfo['borrow_status'] == 0)){

                echo "<script type='text/javascript'>";
                echo "alert('数据有误！');";
                echo "window.history.go(-1);";
                echo "</script>";die;
            }
            if (is_array($borrowinfo) &&($this->uid == $borrowinfo['borrow_uid'])) {
                echo "<script type='text/javascript'>";
                echo "alert('不能预约自己的标哦！');";
                echo "window.history.go(-1);";
                echo "</script>";die;
            }
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

        $borrowinfo["borrow_img"] = str_replace("'", "", $borrowinfo["borrow_img"]);
        if ($borrowinfo['borrow_img'] == "") {
            $borrowinfo['borrow_img'] = "UF/Uploads/borrowimg/nopic.png";
        }
        $this->assign("binfo", $borrowinfo);

        $glo = array('web_title' => $borrowinfo['borrow_name'] . ' - 预约投资');
        $this->assign($glo);

        $this->display();

    }



    public function index(){

      

    	$strUrl = U("invest/index").'?1=1';

		static $newpars;

		$Bconfig = C("BORROW");

		$per = C('DB_PREFIX');

		$curl = $_SERVER['REQUEST_URI'];

		$urlarr = parse_url($curl);

		parse_str($urlarr['query'],$surl);//array获取当前链接参数，2.

		

		$urlArr    = array('borrow_name', 'borrow_status', 'borrow_type', 'borrow_city', 'borrow_model', 'orderby', 'sort');

		$maprow = array();

		$searchMap =  array();

		foreach($urlArr as $vs){

			$maprow[$vs] = text($surl[$vs]);

		}

		if (isset($_GET['borrow_type'])&&!empty($_GET['borrow_type'])&&($_GET['borrow_type']!=0)) {

            $searchMap['pid'] = $_GET['borrow_type'];

				$this->assign("borrow_type",$_GET['borrow_type']);

        }else{

             $searchMap['pid'] = array('neq',4);

        }

        if (in_array($maprow['borrow_status'], array(1, 2, 4, 6, 7, 8))) {

            $searchMap['borrow_status'] = $maprow['borrow_status'];

            $strUrl .= '&borrow_status=' . $maprow['borrow_status'];

        } else {

            $searchMap['borrow_status'] = array("in", '1,2,4,6,7');

        }

        if ($_GET["key"] != "") {

            $searchMap['borrow_name'] = array("like", "%" . $_GET["key"] . "%");

        }
        if($maprow['borrow_type']!=3){
            $searchMap['b.add_time'] = array('gt', '1512057600');
        }
        $parm['map']      = $searchMap;

        $parm['pagesize'] = 16;

		//排序

		$parm['orderby']="borrow_status asc,b.id DESC";

		$Sorder['Corderby'] = strtolower(text($_GET['orderby']));

		$Sorder['Csort'] = strtolower(text($_GET['sort']));

		$Sorder['url'] = $orderUrl;

		$Sorder['sort'] = $sort;

		$Sorder['orderby'] = text($_GET['orderby']);

		//排序

	

		$list = getBorrowList($parm,15);		

// 		var_dump($list["list"]);

		$this->assign("Sorder",$Sorder);

		$this->assign("orderby",$Sorder['orderby']);

		$this->assign("searchMap",$maprow);

		$this->assign("Bconfig",$Bconfig);

		$this->assign("pa",$pa);

		

		$this->assign("list",$list["list"]);

		$this->assign("page",$list["page"]);

 

				

		// 预热项目

	$pre = C('DB_PREFIX');

		$searchMap = array();

		$parm=array();

		$parm['limit'] = 3;

		$parm['orderby']="b.id DESC";

		$searchMap['borrow_status']=array("in",'1,2,4,6,7');

		$searchMap['is_tuijian'] = array('eq',1);

		$parm['map'] = $searchMap;

		$listProduct_top = getBorrowList($parm);		

		$this->assign("listProduct_top",$listProduct_top);		

		$this->assign($tpl_var);

		$glo = array('web_title'=>'我要投资');

    	$this->assign($glo);		/**/

    	//DIE;

    	$this->assign("strUrl",$strUrl);

     

		$this->display();

    }

	/////////////////////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////////////////////

	private function _tendlist($list){

		$areaList = getArea();

		$row=array();

		foreach($list as $key=>$v){

			$v['location'] = $areaList[$v['province']].$areaList[$v['city']].$areaList[$v['area']];

			$v['breakday'] = getExpiredDays($v['deadline']);

			$v['expired_money'] = getExpiredMoney($v['breakday'],$v['total_expired'],$v['interest']);

			$v['call_fee'] = getExpiredCallFee($v['breakday'],$v['total_expired'],$v['interest']);

			$row[$key]=$v;

		}

		return $row;

	}

	public function agreement(){

		$id = intval($_GET['id']);

		$binfo = M('borrow_info')->field('agreement')->find($id);		

		$agreement = $binfo['agreement'];

		$this->assign('agreement',$agreement);

		$this->display();

	}

	////////////////////////////////////////////////////////////////////////////////////

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

     $shortcut_c = explode(",", $borrowinfo['shortcut']);

        $this->assign("shortcut_c",$shortcut_c);

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

//		var_dump(is_array($borrowinfo),$borrowinfo['borrow_status']);

// 		die;

		if($id != 1){

        if (!is_array($borrowinfo) || ($borrowinfo['borrow_status'] == 0 && $this->uid != $borrowinfo['borrow_uid'])) {

           	echo "<script type='text/javascript'>";

			 		echo "alert('数据有误！');";

        				echo "window.history.go(-1);";

			        	echo "</script>";die;

      	  }

		}

        $borrowinfo['biao'] = $borrowinfo['borrow_times'];

        $borrowinfo['need'] = bcsub($borrowinfo['borrow_money'], $borrowinfo['has_borrow'], 2);



        $borrowinfo['leftdays'] = getLeftTime($borrowinfo['collect_time'], 2);

        $borrowinfo['lefttime'] = $borrowinfo['start_time'] - time();

        $borrowinfo['endtimes'] = $borrowinfo['start_time'] + ($borrowinfo['collect_day']*60*60*24);



        $borrowinfo['progress']       = getFloatValue($borrowinfo['has_borrow']/$borrowinfo['borrow_money']*100,0);

        if($borrowinfo['bespeak_able'] == 1) {

            $borrowinfo['bespeak_progress'] = getFloatValue($borrowinfo['bespeak_money'] / $borrowinfo['borrow_money'] * 100, 0);

        }

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

        $article_category = M('article')->where('type_id = 525 AND title  LIKE "'.$vo['templateid'].'"')->find();

        $this->assign("article_category", $article_category);



        $dynamiclist = m("dynamic")->where("tid={$id}")->order("id desc")->select();
        $this->assign('dynamiclist', $dynamiclist);





        // $fieldx     = "bi.investor_capital,bi.add_time,m.user_name,bi.investor_uid,bi.is_auto,bi.investor_interest,bi.investor_way,bi.member_interest_rate_id,bi.bonus_id,bi.is_experience";

        // $investinfo = M("borrow_investor bi")->field($fieldx)->join("{$pre}members m ON bi.investor_uid = m.id")->where("bi.borrow_id={$id}")->order("bi.id DESC")->group("bi.investor_uid")->select();

       // echo M("borrow_investor bi")->getlastsql();exit;



        $fieldx = "bi.investor_capital,bi.add_time,m.user_name,bi.is_auto,bi.investor_interest,bi.investor_way,bi.member_interest_rate_id,bi.bonus_id,bi.is_experience,mi.user_img";

        $investinfo = M("borrow_investor bi")->field($fieldx)

            ->join("{$pre}members m ON bi.investor_uid = m.id")

			  ->join("{$pre}member_info mi ON bi.investor_uid = mi.uid")

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

	

//	 	var_dump($this->uid);

if($id != 1){

        $this->display();

            }else{

            	$member_money=M("member_money")->where("uid=".$this->uid )->find();

//				 var_dump(M("member_money")->getlastsql());die;

             $money_experience = $member_money["money_experience"];

//           var_dump($member_money["money_experience"]);

//			 var_dump(M("member_experience")->getlastsql());die;

			 $this->assign('money_experience',empty($money_experience)?0:$money_experience);	

             //var_dump($member_experience);

           

                $this->display("idetail");	

            }

    }

	

	public function investcheck(){

		$pre = C('DB_PREFIX');

	//layer.alert("传入任意的文本或html", 1,!1);

		if(!$this->uid) ajaxmsg('',0);

		$pin = md5($_POST['pin']);

		$borrow_id = intval($_POST['borrow_id']);

		$money = floatval($_POST['money']);

		

		$is_experience = isset($_POST['is_experience']) && $_POST['is_experience'] == 1 ? 1 : 0;

		var_dump($_POST['is_experience']);die;

		$member_interest_rate_id = 0;

		$bonus_id = isset($_POST['bonus_id']) ? intval($_POST['bonus_id']) : 0;

		if($bonus_id>0){

			$canInvestMoneys = canInvestMoney($this->uid, $borrow_id, $money, 0 , $is_experience,'0',$bonus_id,text($_POST['borrow_pass']));

			if($canInvestMoneys['status'] == 0){

				ajaxmsg($canInvestMoney['tips'],$canInvestMoney['tips_type']);

			}

			$money_bonuss = $canInvestMoneys['money_bonus'];

			$money = floatval($money+$money_bonuss);

		}



		$memberinterest_id = isset($_POST['memberinterest_id']) ? intval($_POST['memberinterest_id']) : 0;



		$vm = getMinfo($this->uid,'m.pin_pass,mm.account_money,mm.back_money,mm.money_experience');

		$pin_pass = $vm['pin_pass'];

		//var_dump($pin_pass);die;

		if($pin<>$pin_pass){

			echo "<script type='text/javascript'>";

			echo "alert('支付密码错误，请重试！');";

        	echo "window.history.go(-1);";

			echo "</script>";die;

		} else if($pin_pass==''){

			echo "<script type='text/javascript'>";

			echo "alert('支付密码未设置，请您先设置支付密码！');";

        	echo "window.history.go(-1);";

			echo "</script>";die;

			

		}

		//ajaxmsg("支付密码错误，请重试",0);



		$amoney = $vm['account_money']+$vm['back_money'];

		$uname = session('u_user_name');

		$amoney = floatval($amoney);

		

		$binfo = M("borrow_info")->field('borrow_money,collect_time,borrow_status,has_borrow,has_vouch,borrow_max,borrow_min,borrow_type,password,pause,new_user_only')->find($borrow_id);

		$minfo =getMinfo($this->uid,true);

	    $levename=getLeveId($minfo["credits"]);





		$ids = M('members_status')->getFieldByUid($this->uid,'id_status');

		// if($ids!=1){

		// 	ajaxmsg("请先通过实名认证后再进行投标。",3);

		// }

		// $phones = M('members_status')->getFieldByUid($this->uid,'phone_status');

		// if($phones!=1){

		// 	ajaxmsg("请先通过手机认证后再进行投标。",3);

		// }

		// $emails = M('members_status')->getFieldByUid($this->uid,'email_status');

		// if($emails!=1){

		// 	//ajaxmsg("请先通过邮箱认证后再进行投标。",3);

		// }

		// if($binfo['pause']==1){

		// 	ajaxmsg("此标当前已经截标，请等待管理员开启。",0);

		// }







		// 50 > 10 

 

	 

 

		//ajaxmsg("此标担保还未完成，您可以担保此标或者等担保完成再投标",3);

		if(!empty($binfo['password'])){

			if(empty($_POST['borrow_pass'])){

					echo "<script type='text/javascript'>";

					echo "alert('此标是定向标，必须验证投标密码');";

		        	echo "window.history.go(-1);";

					echo "</script>";die;

			} 

			//ajaxmsg("此标是定向标，必须验证投标密码",3);

			else if($binfo['password']<>$_POST['borrow_pass']){

					echo "<script type='text/javascript'>";

					echo "alert('投标密码不正确');";

		        	echo "window.history.go(-1);";

					echo "</script>";die;

			} 

			//ajaxmsg("投标密码不正确",3);

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

                echo "<script type='text/javascript'>";

                echo "alert('此标是新手专享标');";

                echo "window.location.href='" . getenv("HTTP_REFERER") . "';";

                echo "</script>";

                die;
            }

        }



		//投标总数检测

		$capital = M('borrow_investor')->where("borrow_id={$borrow_id} AND investor_uid={$this->uid}")->sum('investor_capital');

	 

		

		$need = bcsub($binfo['borrow_money'],$binfo['has_borrow'],2);

		$caninvest =bcsub($need ,$binfo['borrow_min'],2);

		if( $money>$caninvest && ($need-$money)<>0 ){

			

			if(intval($need)==0 or $need=="0.00"){

				echo "<script type='text/javascript'>";

					echo "alert('尊敬的".$uname."，该项目已经投资完成，请选择其他下项目！');";

		        	echo "window.history.go(-1);";

					echo "</script>";die;

			//	exit(json_encode(array('status'=>0,'message'=>"尊敬的{$uname}，该项目已经投资完成，请选择其他下项目！",'invest_money'=>'0')));

				// ajaxmsg("尊敬的{$uname}，此标已经投满",0);

			}

			if($money>$need){

				echo "<script type='text/javascript'>";

					echo "alert('尊敬的".$uname."，此标还差".$need."元满标,您最多只能再投".$need."元！');";

		        	echo "window.history.go(-1);";

					echo "</script>";die;

			//	exit(json_encode(array('status'=>0,'message'=>"尊敬的{$uname}，此标还差{$need}元满标,您最多只能再投{$need}元！",'invest_money'=>$need)));

				// ajaxmsg("尊敬的{$uname}，此标还差{$need}元满标,您最多只能再投{$need}元",0);

			}

			

			

			$msg = "尊敬的{$uname}，此标还差{$need}元满标,如果您投标{$money}元，将导致最后一次投标最多只能投".(bcsub($need,$money,2))."元，小于最小投标金额{$binfo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满标</font>或者投标金额必须<font color='#FF0000'>小于等于{$caninvest}元</font>";

			if($caninvest<$binfo['borrow_min']) $msg = "尊敬的{$uname}，此标还差{$need}元满标,如果您投标{$money}元，将导致最后一次投标最多只能投".(bcsub($need,$money,2))."元，小于最小投标金额{$binfo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满标</font>即投标金额必须<font color='#FF0000'>等于{$need}元</font>";

						echo "<script type='text/javascript'>";

				echo "alert('".$msg."');";

		        	echo "window.history.go(-1);";

					echo "</script>";die;

		//	exit(json_encode(array('status'=>0,'message'=>$msg,'invest_money'=>$need)));

			// ajaxmsg($msg,0);

		}

		

		if(($need-$money) < 0 ){

					echo "<script type='text/javascript'>";

				echo "alert('尊敬的".$uname."，此标还差".$need."元满标,您最多只能再投".$need."元！');";

		        	echo "window.history.go(-1);";

					echo "</script>";die;

			//exit(json_encode(array('status'=>0,'message'=>'尊敬的{$uname}，此标还差{$need}元满标,您最多只能再投{$need}元！','invest_money'=>$need)));

			// ajaxmsg("尊敬的{$uname}，此标还差{$need}元满标,您最多只能再投{$need}元",0);			

		}

		

		$canInvestMoney = canInvestMoney($this->uid, $borrow_id, $money, 0 , $is_experience,'0',$bonus_id,text($_POST['borrow_pass']));

		if($canInvestMoney['status'] == 0){

					echo "<script type='text/javascript'>";

				echo "alert('失败');";

		        	echo "window.history.go(-1);";

					echo "</script>";die;

			ajaxmsg($canInvestMoney['tips'],$canInvestMoney['tips_type']);

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

		if($canInvestMoney['money_type'] == 2){

			if($memberinterest_id >0){

				$msg = "尊敬的{$uname}，您的体验金账户余额为{$vm['money_experience']}元，您确认投标{$money}元,";

			}else{	

				$msg = "尊敬的{$uname}，您的体验金账户余额为{$vm['money_experience']}元，您确认投标{$money}元吗？";

			}

					echo "<script type='text/javascript'>";

				echo "alert('".$msg."');";

		        	echo "window.history.go(-1);";

					echo "</script>";die;

			ajaxmsg($msg,1);

		}elseif($canInvestMoney['money_type'] == 3){

			if($memberinterest_id >0){

				$msg = "尊敬的{$uname}，您确认投标{$money}元（使用{$money_bonus}元红包";

			}else{	

				$msg = "尊敬的{$uname}，您确认投标{$money}元（使用{$money_bonus}元红包），投资吗？";

			}

				echo "<script type='text/javascript'>";

				echo "alert('".$msg."');";

		       	echo "window.location.href='/wapmember/';";

					echo "</script>";die;

		//	ajaxmsg($msg,1);

		}elseif($money <= $amoney){

			if($memberinterest_id >0){

				$msg = "尊敬的{$uname}，您的账户可用余额为{$amoney}元，您确认投标{$money}元 ";

			}else{	

				$msg = "尊敬的{$uname}，您的账户可用余额为{$amoney}元，您确认投标{$money}元吗？";

			}

				echo "<script type='text/javascript'>";

				echo "alert('".$msg."');";

		        		echo "window.location.href='/wapmember/';";

					echo "</script>";die;

			ajaxmsg($msg,1);

		}else{

			$msg = "尊敬的{$uname}，您准备投标{$money}元，但您的账户可用余额为{$amoney}元，您要先去充值吗？";

				echo "<script type='text/javascript'>";

				echo "alert('".$msg."');";

		        	echo "window.location.href='/wapmember/';";

					echo "</script>";die;

			ajaxmsg($msg,2);

		}

	}	

 public function investmoney()

    {

		//		$_POST=array(

		//"borrow_id"=> "1",

		//"cookieKey"=>"de0834c9ab9c1ce49253dc3d54811d09",

		//"money"=> "100",

		//"pin"=> "123456",

		//'is_experience'=>1,

		//"__hash__"=>"b26848768b4d679c7b28246915ac3fd4_086bc420153320c0061453c63de02a3f"

		//);

		//var_dump($_POST["money"]);

		if(!empty($_POST["bonus_id"])){

			$borrow=M("member_bonus")->where("id=".$_POST["bonus_id"])->find();

			$_POST["money"]=$_POST["money"]-$borrow["money_bonus"];

		}

       if(!$this->uid){

		echo "<script type='text/javascript'>";

 

		echo "alert('用户未登录.');";

 

    	echo "window.location.href='/wapmember/';";

		echo "</script>";

 

		die; 

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
            echo "<script type='text/javascript'>";
            echo "alert('禁止重复提交表单');";
            echo "window.location.href='" . getenv("HTTP_REFERER") . "';";
            echo "</script>";
            exit();
        }

        /****** 防止模拟表单提交 *********/

        $money = $_POST['money'];

        writeLog($_POST);

        $borrow_id = (int) ($_POST['borrow_id']);

        $is_experience = isset($_POST['is_experience']) && 1 == $_POST['is_experience'] ? 1 : 0;



        $bonus_id = isset($_POST['bonus_id']) ? (int) ($_POST['bonus_id']) : 0;

        $memberinterest_id = isset($_POST['memberinterest_id']) ? (int) ($_POST['memberinterest_id']) : 0;

        if ($bonus_id > 0) {

            $bs = M('member_bonus')->where("id='{$bonus_id}'")->find();

            $canInvestMoneys = canInvestMoney($this->uid, $borrow_id, $money+$bs['money_bonus'], 0, $is_experience, '0', $bonus_id, text($_POST['borrow_pass']));

            if (0 == $canInvestMoneys['status']) {

                		echo "<script type='text/javascript'>";

 

		echo "alert('".$canInvestMoneys['tips']."');";

 

    	echo "window.location.href='/wapmember/';";

		echo "</script>";

 

		die; 

		//ajaxmsg($canInvestMoneys['tips'], $canInvestMoneys['status']);

            }

            $money_bonuss = $canInvestMoneys['money_bonus'];

            $money = (float) ($money + $money_bonuss);

        }



        $m = M('member_money')->field('account_money,back_money')->find($this->uid);

        $amoney = $m['account_money'] + $m['back_money'];

        $uname = session('u_user_name');



        $vm = getMinfo($this->uid, 'm.pin_pass,mm.account_money,mm.back_money,mm.money_experience');

        $pin_pass = $vm['pin_pass'];

        $pin = md5($_POST['pin']);

       		if($pin_pass==''){

			 

				echo "<script type='text/javascript'>";

				echo "alert('支付密码为空，请您设置密码.');";

//		        echo "layer.alert('支付密码为空，请您设置密码', 1,!1);";

 				echo "window.location.href='"."/wapmember/user/pinpass"."';";

				echo "</script>"; 

//				  header("location:".getenv("HTTP_REFERER")); 

				die; 

		}else if($pin<>$pin_pass){

			echo "<script type='text/javascript'>";

			echo "alert('支付密码错误，请重试');";

//		   echo "layer.alert('支付密码错误，请重试', 1,!1);";

			echo "window.location.href='".getenv("HTTP_REFERER")."';";

			echo "</script>";

			die;

//			//time_sleep_until(time()+20); 

//			header("location:".getenv("HTTP_REFERER")); 

//die; 

		}

        $binfo = M('borrow_info')->field('borrow_money,has_borrow,has_vouch,borrow_max,borrow_min,borrow_type,password,pause,max_limit,start_time,new_user_only,bespeak_able,bespeak_money,bespeak_days')->find($borrow_id);

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

	 

				echo "<script type='text/javascript'>";

				echo "alert('请先通过实名认证后再进行投标。');";

//		  echo "layer.alert('尊敬的{$uname}，此项目已经投满', 1,!1);";

				echo "window.location.href='/wapmember';";

				echo "</script>";

//				time_sleep_until(time()+20);

//				  header("location:".getenv("HTTP_REFERER")); 

				die;

				//$this->error("尊敬的{$uname}，此项目已经投满",3);

		 

        	$this->error("请先通过实名认证后再进行投标。",3);

        }

        $phones = M('members_status')->getFieldByUid($this->uid,'phone_status');

        if($phones!=1){

				echo "<script type='text/javascript'>";

				echo "alert('请先通过实名认证后再进行投标。');";

//		  echo "layer.alert('尊敬的{$uname}，此项目已经投满', 1,!1);";

				echo "window.location.href='/wapmember';";

				echo "</script>"; 

				die;

        	$this->error("请先通过手机认证后再进行投标。",3);

        }

        // $emails = M('members_status')->getFieldByUid($this->uid,'email_status');

        // if($emails!=1){

        // 	//$this->error("请先通过邮箱认证后在进行投标。",3);

        // }

        if($binfo['pause']==1){

			 

				echo "<script type='text/javascript'>";

				echo "alert('此标当前已经截止，请等待管理员开启');";

//		  echo "layer.alert('尊敬的{$uname}，此项目已经投满', 1,!1);";

				echo "window.location.href='".getenv("HTTP_REFERER")."';";

				echo "</script>";

//				time_sleep_until(time()+20);

//				  header("location:".getenv("HTTP_REFERER")); 

				die;

			 

			 

        	$this->error("此标当前已经截止，请等待管理员开启。",3);

        }

        // 50 > 10

        if ($money < $binfo['borrow_min']) {

				echo "<script type='text/javascript'>";

				echo "alert('此项目最小投资金额为".$binfo['borrow_min']."元');";

//		  echo "layer.alert('尊敬的{$uname}，此项目已经投满', 1,!1);";

				echo "window.location.href='".getenv("HTTP_REFERER")."';";

				echo "</script>";

//				time_sleep_until(time()+20);

//				  header("location:".getenv("HTTP_REFERER")); 

				die;

            $this->error('此项目最小投资金额为'.$binfo['borrow_min'].'元');

        }

        



        if ($binfo['has_vouch'] < $binfo['borrow_money'] && 2 == $binfo['borrow_type']) {

				echo "<script type='text/javascript'>";

				echo "alert('此标担保还未完成，您可以担保此标或者等担保完成再投资.');";

				echo "window.location.href='".getenv("HTTP_REFERER")."';";

				echo "</script>";

				die;

            $this->error('此标担保还未完成，您可以担保此标或者等担保完成再投投资');

        }

        if (!empty($binfo['password'])) {

            if (empty($_POST['borrow_pass'])) {

				echo "<script type='text/javascript'>";

				echo "alert('此标是定向项目，必须验证项目密码.');";

				echo "window.location.href='".getenv("HTTP_REFERER")."';";

				echo "</script>";

				die;

                $this->error('此标是定向项目，必须验证项目密码');

            } elseif ($binfo['password'] != $_POST['borrow_pass']) {

				echo "<script type='text/javascript'>";

				echo "alert('投标密码不正确.');";

				echo "window.location.href='".getenv("HTTP_REFERER")."';";

				echo "</script>";

				die;

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

            if ($newUser > 0) {

                echo "<script type='text/javascript'>";

                echo "alert('此标是新手专享标');";

                echo "window.location.href='" . getenv("HTTP_REFERER") . "';";

                echo "</script>";

                die;
            }

        }



        //投标总数检测

        $capital = M('borrow_investor')->where("borrow_id={$borrow_id} AND investor_uid={$this->uid}")->sum('investor_capital');

        if (($capital + $money) > $binfo['borrow_max'] && $binfo['borrow_max'] > 0) {

            $xtee = $binfo['borrow_max'] - $capital;

				echo "<script type='text/javascript'>";

				echo "alert('您已投标".$capital."元，上限为".$binfo['borrow_max']."元，你最多只能再投".$xtee."元');";

				echo "window.location.href='/Wap/invest/detail/id/".$borrow_id."';";

				echo "</script>";

				die;

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

                    echo "<script type='text/javascript'>";

                    echo "alert('此标为预约标，当前预约已满，您未参与预约，请等待');";

                    echo "window.location.href='/Wap/invest/detail/id/".$borrow_id."';";

                    echo "</script>";

                    die;

                } elseif($money > $can_invest_money){

                    echo "<script type='text/javascript'>";

                    echo "alert('此标为预约标，当前最多可投{$can_invest_money}元');";

                    echo "window.location.href='/Wap/invest/detail/id/".$borrow_id."';";

                    echo "</script>";

                    die;

                } elseif(bcsub($can_invest_money, $binfo['borrow_min'], 2) > 0 && bcsub($can_invest_money, $binfo['borrow_min'], 2) < $binfo["borrow_min"]){

                    echo "<script type='text/javascript'>";

                    echo "alert('此标为预约标，如果您投{$money}元，将导致最后一次投标最多只能投".bcsub($can_invest_money, $binfo['borrow_min'], 2)."元，小于最小投标金额{$binfo['borrow_min']}元,请重新选择投标金额');";

                    echo "window.location.href='/Wap/invest/detail/id/".$borrow_id."';";

                    echo "</script>";

                    die;

                }

            } elseif($bespeak_money != $money){

                echo "<script type='text/javascript'>";

                echo "alert('此标为预约标，您已参与预约，此次支持金额必须与预约金额相等');";

                echo "window.location.href='/Wap/invest/detail/id/".$borrow_id."';";

                echo "</script>";

                die;

            }

            if($borrow_id == 1 || $is_experience == 1){

                echo "<script type='text/javascript'>";

                echo "alert('新手标不支持预约');";

                echo "window.location.href='/Wap/invest/detail/id/".$borrow_id."';";

                echo "</script>";

                die;

            }

            if($bonus_id > 0){

                echo "<script type='text/javascript'>";

                echo "alert('预约标不支持使用红包');";

                echo "window.location.href='/Wap/invest/detail/id/".$borrow_id."';";

                echo "</script>";

                die;

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

				echo "<script type='text/javascript'>";

				echo "alert('尊敬的".$uname."，此项目已经投满.');";

				echo "window.location.href='".getenv("HTTP_REFERER")."';";

				echo "</script>";

				die;

                $this->error("尊敬的{$uname}，此项目已经投满", 3);

            }

            if ($money > $need) {

				/*echo "<script type='text/javascript'>";

				echo "alert('尊敬的".$uname."，此项目还差".$need."元满标,您最多只能再投".$need."元.');";

				echo "window.location.href='/Wap/invest/detail/id/".$borrow_id."';";

				echo "</script>";

				die;

                	$this->error("尊敬的{$uname}，此项目还差{$need}元满标,您最多只能再投{$need}元", 3);*/
			$money = $need;

            }



            /*$msg = "尊敬的{$uname}，此项目还差{$need}元满标,如果您投标{$money}元，将导致最后一次投标最多只能投".(bcsub($need, $money, 2))."元，小于最小投标金额{$binfo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满标</font>或者投标金额必须<font color='#FF0000'>小于等于{$caninvest}元</font>";

            if ($caninvest < $binfo['borrow_min']) {

                $msg = "尊敬的{$uname}，此标还差{$need}元满标,如果您投标{$money}元，将导致最后一次投标最多只能投".(bcsub($need, $money, 2))."元，小于最小投标金额{$binfo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满标</font>即投标金额必须<font color='#FF0000'>等于{$need}元</font>";

            }

				echo "<script type='text/javascript'>";

				echo "alert('".$msg.".');";

				echo "window.location.href='".getenv("HTTP_REFERER")."';";
				echo "</script>";

				die;

            $this->error($msg);*/

        }



        if (($need - $money) < 0) {

				/*echo "<script type='text/javascript'>";

				echo "alert('"."尊敬的{$uname}，此项目还差{$need}元满标,您最多只能再投{$need}元".".');";

				echo "window.location.href='/Wap/invest/detail/id/".$borrow_id."';";

				echo "</script>";

				die;

            $this->error("尊敬的{$uname}，此项目还差{$need}元满标,您最多只能再投{$need}元");*/
				$money = $need;

        } else {

            $capital = M('borrow_investor')->where("borrow_id={$borrow_id}")->sum('investor_capital');

            if ($capital > $binfo['borrow_money']) {

				echo "<script type='text/javascript'>";

				echo "alert('投资金额错误.');";

				echo "window.location.href='".getenv("HTTP_REFERER")."';";

				echo "</script>";

				die;

                $this->error('投资金额错误4');

            }



            $capital = M('investor_detail')->where("borrow_id={$borrow_id}")->sum('capital');

            if ($capital > $binfo['borrow_money']) {

						echo "<script type='text/javascript'>";

				echo "alert('投资金额错误.');";

				echo "window.location.href='".getenv("HTTP_REFERER")."';";

				echo "</script>";

				die;

                $this->error('投资金额错误5');

            }

            // $done = investMoney($this->uid,$borrow_id,$money);

            writeLog($memberinterest_id);

            if ($memberinterest_id > 0) {

                //加息券校验

                $canUseInterest = canUseInterest($this->uid, $memberinterest_id);

                writeLog($canUseInterest);

                if (0 == $canUseInterest['status']) {

					echo "<script type='text/javascript'>";

					echo "alert('加息券不可用.');";

					echo "window.location.href='".getenv("HTTP_REFERER")."';";

					echo "</script>";

					die;

                    $this->error('加息券不可用');

                }

                $interest_rate = $canUseInterest['interest_rate'];

            } else {

                $interest_rate = 0;

            }

            //体验金校验

            $canInvestMoney = canInvestMoney($this->uid, $borrow_id, $money, 0, $is_experience, '0', $bonus_id, text(@$_POST['borrow_pass']));

            if (0 == $canInvestMoney['status']) {

					echo "<script type='text/javascript'>";

					echo "alert('".$canInvestMoney['tips'].".');";

					echo "window.location.href='".getenv("HTTP_REFERER")."';";

					echo "</script>";

					die;

                $this->error($canInvestMoney['tips']);

            }

            $money_bonus = $canInvestMoney['money_bonus'];



            if (1 == $canInvestMoney['money_type'] && $amoney < $money) {

					echo "<script type='text/javascript'>";

					echo "alert('"."尊敬的{$uname}，您准备投标{$money}元，但您的账户可用余额为{$amoney}元，请先去充值再投项目.".".');";

					echo "window.location.href='".getenv("HTTP_REFERER")."';";

					echo "</script>";

					die;

                $this->error("尊敬的{$uname}，您准备投标{$money}元，但您的账户可用余额为{$amoney}元，请先去充值再投项目.");

            }



//          $done = investMoney($this->uid, $borrow_id, $money, '0', '1', $is_experience, $memberinterest_id, $bonus_id, $money_bonus, text(@$_POST['borrow_pass']));

 if($is_experience==1){

 		$money_experience = M("member_money")->where("uid=".$this->uid)->find();

	 

// 	var_dump($money_experience);die;

 		$done = investMoney($this->uid,$borrow_id,$money,'0','1', 1,$memberinterest_id,$bonus_id,$money_bonus,text(@$_POST['borrow_pass']));

		if($done===true) {

			$zrm=$binfo["borrow_money"]+$money;

			M('borrow_info')->where("id={$borrow_id}")->save(['borrow_money'=>$zrm]);

		}

 }else{

// 	var_dump(2);die;

 		$done = investMoney($this->uid,$borrow_id,$money,'0','1', 0,$memberinterest_id,$bonus_id,$money_bonus,text(@$_POST['borrow_pass']));

 }

        }



        //$this->display("Public:_footer");

        //$this->assign("waitSecond",1000);



        if (true === $done) {

				echo "<script type='text/javascript'>";

					echo "alert('"."恭喜成功投资{$money}元（其中使用红包{$money_bonus}元）!".".');";

					echo "window.location.href='/wapmember/tendout/index';";

					echo "</script>";

					die;

        	    $this->success("恭喜成功投资{$money}元（其中使用红包{$money_bonus}元）!", U('/member/tendout/index', array('type' => 1)));

         // $this->success("恭喜成功投资{$money}元（其中使用红包{$money_bonus}元）!", U('invest/detail', array('id' => $borrow_id)));

        } elseif ($done) {

				echo "<script type='text/javascript'>";

					echo "alert('". $done.".');";

					echo "window.location.href='/member/tendout/index';";

					echo "</script>";

					die;

            $this->error($done);

        } else {

			echo "<script type='text/javascript'>";

					echo "alert('对不起，投资失败，请重试!');";

					echo "window.location.href='/member/tendout/index';";

					echo "</script>";

					die;

            $this->error('对不起，投资失败，请重试!');

        }

    }



    public function bespeakmoney()

    {

        if(!$this->uid){

            echo "<script type='text/javascript'>";

            echo "alert('用户未登录');";

            echo "window.location.href='/wapmember/';";

            echo "</script>";

            die;

        }

        /****** 防止模拟表单提交 *********/

        $cookieKeyS = cookie(strtolower(MODULE_NAME).'-invest');

        if ($cookieKeyS != $_REQUEST['cookieKey']) {

        }

        /****** 防止模拟表单提交 *********/

        $money = $_POST['money'];

        writeLog($_POST);

        $borrow_id = (int) ($_POST['borrow_id']);

        $is_experience = isset($_POST['is_experience']) && 1 == $_POST['is_experience'] ? 1 : 0;



        $m = M('member_money')->field('account_money,back_money')->find($this->uid);

        $amoney = $m['account_money'] + $m['back_money'];

        $uname = session('u_user_name');



        $vm = getMinfo($this->uid, 'm.pin_pass,mm.account_money,mm.back_money,mm.money_experience');

        $pin_pass = $vm['pin_pass'];

        $pin = md5($_POST['pin']);

        if($pin_pass==''){

            echo "<script type='text/javascript'>";

            echo "alert('支付密码为空，请您设置密码');";

            echo "window.location.href='"."/wapmember/user/pinpass"."';";

            echo "</script>";

            die;

        }else if($pin<>$pin_pass){

            echo "<script type='text/javascript'>";

            echo "alert('支付密码错误，请重试');";

            echo "window.location.href='".getenv("HTTP_REFERER")."';";

            echo "</script>";

            die;

        }

        $binfo = M('borrow_info')->field('borrow_money,has_borrow,has_vouch,borrow_max,borrow_min,borrow_type,password,pause,max_limit,start_time,new_user_only,bespeak_able,bespeak_money,bespeak_days')->find($borrow_id);

        $binfo['borrow_max'] = $binfo['borrow_min']*$binfo['max_limit'];



        $ids = M('members_status')->getFieldByUid($this->uid, 'id_status');

        if($ids != 1){

            echo "<script type='text/javascript'>";

            echo "alert('请先通过实名认证后再进行预约');";

            echo "window.location.href='/wapmember';";

            echo "</script>";

            die;

        }

        $phones = M('members_status')->getFieldByUid($this->uid,'phone_status');

        if($phones != 1){

            echo "<script type='text/javascript'>";

            echo "alert('请先通过手机认证后再进行预约');";

            echo "window.location.href='/wapmember';";

            echo "</script>";

            die;

        }

        if($binfo['pause']==1){

            echo "<script type='text/javascript'>";

            echo "alert('此标当前已经截止，请等待管理员开启');";

            echo "window.location.href='".getenv("HTTP_REFERER")."';";

            echo "</script>";

            die;

        }

        if ($money < $binfo['borrow_min']) {

            echo "<script type='text/javascript'>";

            echo "alert('此项目最小预约金额为".$binfo['borrow_min']."元');";

            echo "window.location.href='".getenv("HTTP_REFERER")."';";

            echo "</script>";

            die;

        }

        if ($binfo['has_vouch'] < $binfo['borrow_money'] && 2 == $binfo['borrow_type']) {

            echo "<script type='text/javascript'>";

            echo "alert('此标担保还未完成，您可以担保此标或者等担保完成再预约');";

            echo "window.location.href='".getenv("HTTP_REFERER")."';";

            echo "</script>";

            die;

        }

        if (!empty($binfo['password'])) {

            if (empty($_POST['borrow_pass'])) {

                echo "<script type='text/javascript'>";

                echo "alert('此标是定向项目，必须验证项目密码');";

                echo "window.location.href='".getenv("HTTP_REFERER")."';";

                echo "</script>";

                die;

            } elseif ($binfo['password'] != $_POST['borrow_pass']) {

                echo "<script type='text/javascript'>";

                echo "alert('投标密码不正确');";

                echo "window.location.href='".getenv("HTTP_REFERER")."';";

                echo "</script>";

                die;

            }

        }

        if ($money < 1) {

            echo "<script type='text/javascript'>";

            echo "alert('最低预约金额为1元');";

            echo "window.location.href='".getenv("HTTP_REFERER")."';";

            echo "</script>";

            die;

        }

        if($binfo['borrow_max'] > 0 && $money > $binfo['borrow_max']){

            echo "<script type='text/javascript'>";

            echo "alert('此标预约上限金额".$binfo['borrow_max']."元');";

            echo "window.location.href='".getenv("HTTP_REFERER")."';";

            echo "</script>";

            die;

        }

        if ($binfo['bespeak_able'] != 1) {

            echo "<script type='text/javascript'>";

            echo "alert('此标不支持预约');";

            echo "window.location.href='".getenv("HTTP_REFERER")."';";

            echo "</script>";

            die;

        }
	if($binfo["start_time"] < time()){
            echo "<script type='text/javascript'>";
            echo "alert('此标不在预热期');";
            echo "window.location.href='".getenv("HTTP_REFERER")."';";
            echo "</script>";
            die;
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

                echo "<script type='text/javascript'>";

                echo "alert('此标是新手专享标');";

                echo "window.location.href='" . getenv("HTTP_REFERER") . "';";

                echo "</script>";

                die;
            }

        }



        if($borrow_id == 1){

            echo "<script type='text/javascript'>";

            echo "alert('新手标不支持预约');";

            echo "window.location.href='".getenv("HTTP_REFERER")."';";

            echo "</script>";

            die;

        }



        $capital = M('bespeak')->where("borrow_id={$borrow_id} AND bespeak_uid={$this->uid}")->find();

        if($capital){

            echo "<script type='text/javascript'>";

            echo "alert('您已参与预约');";

            echo "window.location.href='".getenv("HTTP_REFERER")."';";

            echo "</script>";

            die;

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

            echo "<script type='text/javascript'>";

            echo "alert('尊敬的".$uname."，此项目预约已满');";

            echo "window.location.href='".getenv("HTTP_REFERER")."';";

            echo "</script>";

            die;

        }

        if ($money > $need) {

            echo "<script type='text/javascript'>";

            echo "alert('尊敬的".$uname."，此项目还差".$need."元满预约,您最多只能再预约".$need."元');";

            echo "window.location.href='/Wap/invest/detail/id/".$borrow_id."';";

            echo "</script>";

            die;

        }

        $capital = M('bespeak')->where("borrow_id={$borrow_id}")->sum('bespeak_money');

        if ($capital > $binfo['borrow_money']) {

            echo "<script type='text/javascript'>";

            echo "alert('投资金额错误');";

            echo "window.location.href='".getenv("HTTP_REFERER")."';";

            echo "</script>";

            die;

        }



        $capital = M('investor_detail')->where("borrow_id={$borrow_id}")->sum('capital');

        if ($capital > $binfo['borrow_money']) {

            echo "<script type='text/javascript'>";

            echo "alert('投资金额错误');";

            echo "window.location.href='".getenv("HTTP_REFERER")."';";

            echo "</script>";

            die;

        }

        if ($money > $caninvest && 0 != ($need - $money)) {

            $msg = "尊敬的{$uname}，此项目还差{$need}元满预约,如果您预约{$money}元，将导致最后一次预约最多只能预约".(bcsub($need, $money, 2))."元，小于最小预约金额{$binfo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满预约</font>或者预约金额必须<font color='#FF0000'>小于等于{$caninvest}元</font>";

            if ($caninvest < $binfo['borrow_min']) {

                $msg = "尊敬的{$uname}，此标还差{$need}元满预约,如果您预约{$money}元，将导致最后一次预约最多只能预约".(bcsub($need, $money, 2))."元，小于最小预约金额{$binfo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满预约</font>即预约金额必须<font color='#FF0000'>等于{$need}元</font>";

            }

            echo "<script type='text/javascript'>";

            echo "alert('".$msg."');";

            echo "window.location.href='".getenv("HTTP_REFERER")."';";

            echo "</script>";

            die;

        }



        if ($amoney < $money*$this->glo["bespeak_point"]) {

            echo "<script type='text/javascript'>";

            echo "alert('"."尊敬的{$uname}，您准备预约{$money}元，需要支付".$money*$this->glo["bespeak_point"]."元，但您的账户可用余额为{$amoney}元，请先去充值再预约项目"."');";

            echo "window.location.href='".getenv("HTTP_REFERER")."';";

            echo "</script>";

            die;

        }



        $done = bespeakMoney($this->uid,$borrow_id,$money);



        if (true === $done) {

            echo "<script type='text/javascript'>";

            echo "alert('成功预约{$money}元,请于开标后".Sec2Time(floatval($binfo["bespeak_days"]) * 24 * 60 * 60)."天内交清余款，完成投标，逾期预约作废');";

            echo "window.location.href='/wapmember/tendout/index';";

            echo "</script>";

            die;

        } elseif ($done) {

            echo "<script type='text/javascript'>";

            echo "alert('". $done."');";

            echo "window.location.href='/member/tendout/index';";

            echo "</script>";

            die;

        } else {

            echo "<script type='text/javascript'>";

            echo "alert('对不起，预约失败，请重试!');";

            echo "window.location.href='/member/tendout/index';";

            echo "</script>";

            die;

        }

    }



   public function addvote()

    {

        if (!$this->uid) {

            ajaxmsg("请先登陆", 0);

        }



        $borrow_id = intval(@$_POST['borrow_id']);

        $status    = intval(@$_POST['status']) ? 1 : 0;

        $can_vote  = M('borrow_info')->getFieldById($borrow_id, 'can_vote');

        if ($can_vote != 1) {

            ajaxmsg("投票未开放！", 0);

        }

        $canVote = M('borrow_investor')->where('borrow_id=' . $borrow_id . ' and investor_uid = ' . $this->uid)->count('id');

        if (empty($canVote)) {

            ajaxmsg("未投资此项目不可投票！", 0);

        }

        $voteCount = M('borrow_vote')->where("borrow_id=" . $borrow_id . " and vote_uid = '{$this->uid}'")->count('id');

        if ($voteCount > 0) {

            ajaxmsg("你已经投过票！", 0);

        } else {

            $data['add_time']  = time();

            $data['vote_uid']  = $this->uid;

            $data['borrow_id'] = $borrow_id;

            $data['status']    = $status;

            $newid             = M('borrow_vote')->add($data);

            if ($newid) {

                $user_phone = M('members')->getFieldById($this->uid,'user_phone');

                if(preg_match("/^1[34578]\d{9}$/", $user_phone)){

                    $borrow_name  = M('borrow_info')->getFieldById($borrow_id, 'borrow_name');

                    $content = "尊敬的用户，您好！你对“{$borrow_name}”项目投票成功，感谢您的参与！";

                    // $sendRs = sendsms($user_phone, $content,1);

                }

                ajaxmsg("投票成功", 1);

            } else {

                ajaxmsg("投票失败", 0);

            }



        }

    }	

	public function addcomment(){

		$data['comment'] = text($_POST['comment']);

		if(!$this->uid)  ajaxmsg("请先登陆",0);

		if(empty($data['comment']))  ajaxmsg("留言内容不能为空",0);

		$data['type'] = 1;

		$data['add_time'] = time();

		$data['uid'] = $this->uid;

		$data['uname'] = session("u_user_name");

		$data['tid'] = intval($_POST['tid']);

		$data['name'] = M('borrow_info')->getFieldById($data['tid'],'borrow_name');

		$data['stars'] = intval($_POST['stars']);

		$newid = M('comment')->add($data);

		if($newid) ajaxmsg();

		else ajaxmsg("留言失败，请重试",0);

	}

	

	public function addyuetan(){

		$data['comment'] = text($_POST['comment']);

		if(!$this->uid)  ajaxmsg("请先登陆",0);

		if(empty($data['comment']))  ajaxmsg("内容不能为空",0);

		$data['type'] = 1;

		$data['add_time'] = time();

		$data['uid'] = $this->uid;

		$data['tid'] = intval($_POST['tid']);

		$data['touid'] = $_POST["touid"];

		$newid = M('comment_yuetan')->add($data);

		if($newid) ajaxmsg();

		else ajaxmsg("发送失败，请重试",0);

	}

	

	public function addguanzhu(){

		$jsons['status']  = '0';

		$jsons['message'] = '关注失败';

		if(!$this->uid){

			$jsons['message'] = "请先登陆";

		}else{

			$b = M('pro_guanzhu')->where("bid=".$_POST['tid']." and uid = {$this->uid}")->count('id');

			if($b==1){

				 M('pro_guanzhu')->where("bid=".$_POST['tid']." and uid = {$this->uid}")->delete();

				 $jsons['message'] = "取消关注成功";

				 $jsons['status']  = '1';

			}else{

				$data['add_time'] = time();

				$data['uid'] = $this->uid;

				$data['bid'] = intval($_POST['tid']);

				$newid = M('pro_guanzhu')->add($data);

				if($newid){

					$jsons['message'] = "关注成功";

					$jsons['status']  = '1';	

				}				

			}

		}

		$jsons['has_guanzhu'] = get_pro_guanzhu(intval($_POST['tid']));

		exit(json_encode($jsons));

	}

	

	public function jubao(){

		if($_POST['checkedvalue']){

			$data['reason'] = text($_POST['checkedvalue']);

			$data['text'] = text($_POST['thecontent']);

			$data['uid'] = $this->uid;

			$data['uemail'] = text($_POST['uemail']);

			$data['b_uid'] = text($_POST['b_uid']);

			$data['b_uname'] = text($_POST['theuser']);

			$data['add_time'] = time();

			$data['add_ip'] = get_client_ip();

			$newid = M('jubao')->add($data);

			if($newid) exit("1");

			else exit("0");

		}else{

			$id=intval($_GET['id']);

			$u['id'] = $id;

			$u['uname']=M('members')->getFieldById($id,"user_name");

			$u['uemail']=M('members')->getFieldById($this->uid,"user_email");

			$this->assign("u",$u);

			$data['content'] = $this->fetch("Public:jubao");

			exit(json_encode($data));

		}

	}

	

	public function ajax_invest(){

		if(!$this->uid) {

			ajaxmsg("请先登陆",0);

		}

		

		/****** 防止模拟表单提交 *********/

		$cookieTime = 15*3600;

		$cookieKey=md5(MODULE_NAME."-".time());

		cookie(strtolower(MODULE_NAME)."-invest",$cookieKey,$cookieTime);

		$this->assign("cookieKey",$cookieKey);

		/****** 防止模拟表单提交 *********/

		$pre = C('DB_PREFIX');

		$id=intval($_GET['id']);

		

		$Bconfig = C("BORROW");

		$field = "id,borrow_uid,borrow_money,borrow_status,borrow_type,has_borrow,has_vouch,borrow_interest_rate,borrow_duration,repayment_type,collect_time,borrow_min,borrow_max,password,borrow_use,pause,daishou,daishou_money,can_invest,is_bonus,is_experience";

		$vo = M('borrow_info')->field($field)->find($id);

		if($this->uid == $vo['borrow_uid']) ajaxmsg("不能去投自己的项目",0);

		

		$can_invest = unserialize($vo['can_invest']);

		$minfo =getMinfo($this->uid,true);

	    /*$levename=getLeveId($minfo["credits"]);	    

	    if(!in_array($levename, $can_invest)){

	    	 ajaxmsg("您没有权限投此项目！",0);

	    }*/

		

		if($vo['borrow_type']==3){  //秒标检查

			

			if($vo["daishou"]!=0){

				$dashou_name=array(0=>"不限制",1=>"全部待收",2=>"当月待收",3=>"当日待收");

				

				if($vo["daishou_money"]>daishou($vo["daishou"],$this->uid)){

					ajaxmsg("当前标的".$dashou_name[$vo["daishou"]]."限制为：".$vo["daishou_money"],0);	

				}	

			}

			

		}

		

		if($vo['pause']==1){

			ajaxmsg("此标当前已经截标，请等待管理员开启。",0);

		}

		

		// if($vo['borrow_status'] <> 2) ajaxmsg("只能投正在众筹中的项目",0);

		//担保标检测

		if($vo['borrow_type']==2){

			if($vo['has_vouch']<$vo['borrow_money']) ajaxmsg("此标担保还未完成，您可以担保此标或者等担保完成再投标",0);

		}

		

		$vo['need'] = bcsub($vo['borrow_money'],$vo['has_borrow'],2);

		if($vo['need']<0){

			ajaxmsg("投标金额不能超出众筹剩余金额",0);

		}

		$vo['lefttime'] =$vo['collect_time'] - time();

		$vo['progress'] = getFloatValue($vo['has_borrow']/$vo['borrow_money']*100,4);//ceil($vo['has_borrow']/$vo['borrow_money']*100);

		$vo['uname'] = M("members")->getFieldById($vo['borrow_uid'],'user_name');

		$time1 = microtime(true)*1000;		

		$vm = getMinfo($this->uid,'m.pin_pass,mm.account_money,mm.back_money,mm.money_experience');

		$amoney = $vm['account_money']+$vm['back_money'];

		

		////////////////////投标时自动填写可投标金额在投标文本框 2013-07-03 fan////////////////////////

		$capital = M('borrow_investor')->where("borrow_id={$id} AND investor_uid={$this->uid}")->sum('investor_capital');

		if($amoney<floatval($vo['borrow_min'])){

			// ajaxmsg("您的账户可用余额小于本项目的最小投标金额限制，不能投标！",0);

		}elseif($amoney>=floatval($vo['borrow_max']) && floatval($vo['borrow_max'])>0){

			$toubiao = bcsub($vo['borrow_max'],$capital,2);

		}else if($amoney>=floatval($vo['need'])){

			$toubiao = floatval($vo['need']);

		}else{

			$toubiao = floatval($amoney);

		}

		

		if($toubiao>$vo['need'])$toubiao=$vo['need'];

		if(isset($_GET["money"])){

			$vo['toubiao'] =$_GET["money"];	

		}else{

			$vo['toubiao'] =$toubiao;

		}

		

		////////////////////投标时自动填写可投标金额在投标文本框 2013-07-03 fan////////////////////////

		//红包信息

		$bonus_list =  M('member_bonus')->where("uid='{$this->uid}' and status = 1 and (".time()." > start_time and ".time()." < end_time )")->field('id,money_bonus,end_time,bonus_invest_min')->select();

		$this->assign("bonus_list",$bonus_list);



		$pin_pass = $vm['pin_pass'];

		$has_pin  = (empty($pin_pass)) ? "no" : "yes";

		$this->assign("has_pin",$has_pin);

		$this->assign("vo",$vo);

		$this->assign("account_money",$amoney);

		$this->assign("money_experience",$vm['money_experience']);

		$this->assign("Bconfig",$Bconfig);

		$data['content'] = $this->fetch();

		ajaxmsg($data);

	}

	

	

	public function ajax_vouch(){

		/****** 防止模拟表单提交 *********/

		$cookieTime = 15*3600;

		$cookieKey=md5(MODULE_NAME."-".time());

		cookie(strtolower(MODULE_NAME)."-vouch",$cookieKey,$cookieTime);

		$this->assign("cookieKey",$cookieKey);

		/****** 防止模拟表单提交 *********/

		$pre = C('DB_PREFIX');

		$id=intval($_GET['id']);

		$Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";

		$field = "id,borrow_uid,borrow_money,has_borrow,borrow_interest_rate,borrow_duration,repayment_type,collect_time,has_vouch,reward_vouch_rate,borrow_min,borrow_max";

		$vo = M('borrow_info')->field($field)->find($id);

		

		$vo['need'] = $vo['borrow_money'] - $vo['has_borrow'];

		$vo['lefttime'] =$vo['collect_time'] - time();

		$vo['progress'] = getFloatValue($vo['has_borrow']/$vo['borrow_money']*100,4);//ceil($vo['has_borrow']/$vo['borrow_money']*100);

		$vo['vouch_progress'] = getFloatValue($vo['has_vouch']/$vo['borrow_money']*100,4);//ceil($vo['has_vouch']/$vo['borrow_money']*100);

		$vo['vouch_need'] = $vo['borrow_money'] - $vo['has_vouch'];

		$vo['uname'] = M("members")->getFieldById($vo['borrow_uid'],'user_name');

		$time1 = microtime(true)*1000;

		$vm = getMinfo($this->uid,"m.pin_pass,mm.invest_vouch_cuse");

		

		////////////////////投标时自动填写可投标金额在投标文本框 2013-07-03 fan////////////////////////

		if($vo['vouch_progress']==100){

			$amoney = $vm['account_money']+$vm['back_money'];

			if($amoney<floatval($vo['borrow_min'])){

				ajaxmsg("您的账户可用余额小于本标的最小投标金额限制，不能投标！",0);

			}elseif($amoney>=floatval($vo['borrow_max']) && floatval($vo['borrow_max'])>0){

				$toubiao = intval($vo['borrow_max']);

			}else if($amoney>=floatval($vo['need'])){

				$toubiao = intval($vo['need']);

			}else{

				$toubiao = floor($amoney);

			}

			

			$vo['toubiao'] =$toubiao;

		}

		////////////////////投标时自动填写可投标金额在投标文本框 2013-07-03 fan////////////////////////

		

		$pin_pass = $vm['pin_pass'];

		$has_pin = (empty($pin_pass))?"no":"yes";

		$this->assign("has_pin",$has_pin);

		$this->assign("vo",$vo);

		$this->assign("invest_vouch_cuse",$vm['invest_vouch_cuse']);

		$this->assign("Bconfig",$Bconfig);

		$data['content'] = $this->fetch();

		ajaxmsg($data,1);

	}

	

	public function vouchcheck(){

		$pre = C('DB_PREFIX');

		if(!$this->uid) ajaxmsg('',3);

		$pin = md5($_POST['pin']);

		$money = intval($_POST['vouch_money']);

		$vm = getMinfo($this->uid,"m.pin_pass,mm.invest_vouch_cuse");

		$amoney = $vm['invest_vouch_cuse'];

		$uname = session('user_name');

		$pin_pass = $vm['pin_pass'];

		$amoney = floatval($amoney);

		if($pin<>$pin_pass) ajaxmsg("支付密码错误，请重试",0);

		if($money>$amoney){

			$msg = "尊敬的{$uname}，您准备担保{$money}元，但您可用担保投资额度为{$amoney}元，要去申请更高额度吗？";

			ajaxmsg($msg,2);

		}else{

			$msg = "尊敬的{$uname}，您可用担保投资额度为{$amoney}元，您确认担保{$money}元吗？";

			ajaxmsg($msg,1);

		}

	}

		

	public function vouchmoney(){

		if(!$this->uid) exit;

			/****** 防止模拟表单提交 *********/

		$cookieKeyS = cookie(strtolower(MODULE_NAME)."-vouch");

		if($cookieKeyS!=$_REQUEST['cookieKey']){

			$this->error("数据校验有误");

		}

		/****** 防止模拟表单提交 *********/

		$money = intval($_POST['vouch_money']);

		$borrow_id = intval($_POST['borrow_id']);

		$rate = M('borrow_info')->getFieldById($borrow_id,'reward_vouch_rate');

		$amoney = M("member_money")->getFieldByUid($this->uid,'invest_vouch_cuse');

		$uname = session('u_user_name');

		if($amoney<$money) $this->error("尊敬的{$uname}，您准备担保{$money}元，但您可用担保投资额度为{$amoney}元，请先去申请更高额度.");

		

		$saveVouch['borrow_id'] = $borrow_id;

		$saveVouch['uid'] = $this->uid;

		$saveVouch['uname'] = $uname;

		$saveVouch['vouch_money'] = $money;

		$saveVouch['vouch_reward_rate'] = $rate;

		$saveVouch['vouch_reward_money'] = getFloatValue($money*$rate/100,2);

		$saveVouch['add_ip'] = get_client_ip();

		$saveVouch['vouch_time'] = time();

		$newid = M('borrow_vouch')->add($saveVouch);

		

		if($newid) $done = M("member_money")->where("uid={$this->uid}")->setDec('invest_vouch_cuse',$money);

		//$this->assign("waitSecond",1000);

		if($done==true){

			M("borrow_info")->where("id={$borrow_id}")->setInc('has_vouch',$money);

			$this->success("恭喜成功担保{$money}元");

		}

		else $this->error("对不起，担保失败，请重试!");

	}

	

	public function getarea(){

		$rid = intval($_GET['rid']);

		if(empty($rid)){

			$data['NoCity'] = 1;

			exit(json_encode($data));

		}

		$map['reid'] = $rid;

		$alist = M('area')->field('id,name')->order('sort_order DESC')->where($map)->select();

		if(count($alist)===0){

			$str="<option value=''>--该地区下无下级地区--</option>\r\n";

		}else{

			if($rid==1) $str.="<option value='0'>请选择省份</option>\r\n";

			foreach($alist as $v){

				$str.="<option value='{$v['id']}'>{$v['name']}</option>\r\n";

			}

		}

		$data['option'] = $str;

		$res = json_encode($data);

		echo $res;

	}	

	

	public function addfriend(){

		if(!$this->uid) ajaxmsg("请先登陆",0);

		$fuid = intval($_POST['fuid']);

		$type = intval($_POST['type']);

		if(!$fuid||!$type) ajaxmsg("提交的数据有误",0);

		

		$save['uid'] = $this->uid;

		$save['friend_id'] = $fuid;

		$vo = M('member_friend')->where($save)->find();	

		

		if($type==1){//加好友

		if($this->uid == $fuid) ajaxmsg("您不能对自己进行好友相关的操作",0);

			if(is_array($vo)){

				if($vo['apply_status']==3){

					$msg="已经从黑名单移至好友列表";

					$newid = M('member_friend')->where($save)->setField("apply_status",1);

				}elseif($vo['apply_status']==1){

					$msg="已经在你的好友名单里，不用再次添加";

				}elseif($vo['apply_status']==0){

					$msg="已经提交加好友申请，不用再次添加";

				}elseif($vo['apply_status']==2){

					$msg="好友申请提交成功";

					$newid = M('member_friend')->where($save)->setField("apply_status",0);

				}

			}else{

				$save['uid'] = $this->uid;

				$save['friend_id'] = $fuid;

				$save['apply_status'] = 0;

				$save['add_time'] = time();

				$newid = M('member_friend')->add($save);	

				$msg="好友申请成功";

			}

		}elseif($type==2){//加黑名单

		if($this->uid == $fuid) ajaxmsg("您不能对自己进行黑名单相关的操作",0);

			if(is_array($vo)){

				if($vo['apply_status']==3) $msg="已经在黑名单里了，不用再次添加";

				else{

					$msg="成功移至黑名单";

					$newid = M('member_friend')->where($save)->setField("apply_status",3);	

				}

			}else{

				$save['uid'] = $this->uid;

				$save['friend_id'] = $fuid;

				$save['apply_status'] = 3;

				$save['add_time'] = time();

				$newid = M('member_friend')->add($save);	

				$msg="成功加入黑名单";

			}

		}

		if($newid) ajaxmsg($msg);

		else ajaxmsg($msg,0);

	}

	

	

	public function innermsg(){

		if(!$this->uid) ajaxmsg("请先登陆",0);

		$fuid = intval($_GET['uid']);

		if($this->uid == $fuid) ajaxmsg("您不能对自己进行发送站内信的操作",0);

		$this->assign("touid",$fuid);

		$data['content'] = $this->fetch("Public:innermsg");

		ajaxmsg($data);

	}

	public function doinnermsg(){

		$touid = intval($_POST['to']);

		$msg = text($_POST['msg']);	

		$title = text($_POST['title']);	

		$newid = addMsg($this->uid,$touid,$title,$msg);

		if($newid) ajaxmsg();

		else ajaxmsg("发送失败",0);

		

	}

	

	public function view(){

		$id = intval($_GET['id']);

		if($_GET['type']=="subsite") $vo = M('article_area')->find($id);

		else $vo = M('article')->find($id);

	

		$this->assign("vo",$vo);

		

		//left

		$typeid = $vo['type_id'];

		$listparm['type_id']=$typeid;

		$listparm['limit']=20;

		if($_GET['type']=="subsite"){

			$listparm['area_id'] = $this->siteInfo['id'];

			$leftlist = getAreaTypeList($listparm);

		}else	$leftlist = getTypeList($listparm);

		

		$this->assign("leftlist",$leftlist);

		$this->assign("cid",$typeid);

		

		if($_GET['type']=="subsite"){

			$vop = D('Aacategory')->field('type_name,parent_id')->find($typeid);

			if($vop['parent_id']<>0) $this->assign('cname',D('Aacategory')->getFieldById($vop['parent_id'],'type_name'));

			else $this->assign('cname',$vop['type_name']);

		}else{

			$vop = D('Acategory')->field('type_name,parent_id')->find($typeid);

			if($vop['parent_id']<>0) $this->assign('cname',D('Acategory')->getFieldById($vop['parent_id'],'type_name'));

			else $this->assign('cname',$vop['type_name']);

		}

		

		$this->display();

	}

	

}

