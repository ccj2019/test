<?php
// 本类由系统自动生成，仅供测试用途
class CsAction extends WCommonAction {

    public function ceshi(){
        for ($i=1; $i <6 ; $i++) { 
            $aa=memberMoneyLog(1310, 121, 0 - $i*10, "对进行还款申请");
            var_dump($aa);
        } 


        //$aa=memberMoneyLog(1310, 121, 0 - 100, "对进行还款申请");

    }
    public function tjht(){

        $borrow_id=3443;
        $binfo = M("borrow_info")->field("loan_certificate,borrow_type,reward_type,reward_num,borrow_name,borrow_fee,borrow_money,borrow_uid,borrow_duration,repayment_type")->find($borrow_id);
        $borrow_name=$binfo["borrow_name"];
        $investorList = M('borrow_investor')->field("id,investor_uid,investor_capital,investor_interest,is_experience,bonus_id,yubi")->where("borrow_id={$borrow_id}")->select();
        $condata['itemid']=$borrow_id;
        $condata['loan_certificate']=$binfo['loan_certificate'];

        foreach ($investorList as $key => $v) {
            $condata['conid']=$v['id'];
            $condata['uid']= $v['investor_uid'];
            $condata['add_time']=time();
            $conds[]=$condata;
        }
        var_dump($conds);
       exit;

        $res5=M("contract")->addAll($conds);

    }
    public function index(){
        $i=$_GET['i'];
        $dt='2022-12-30';
        var_dump(date('Y-n-d', strtotime("$dt+" . $i ."month")));

        $aa=strtotime("last day of +" .  $i . " month", strtotime($dt));
var_dump(date('Y-n-d',$aa));

    }
    public function indexx(){
        var_dump(convertAmountToCn(3651.00));
        var_dump(C('web'));
        $aa=array (
            'gmt_create' => '2021-07-07 14:33:43',
            'charset' => 'UTF-8',
            'seller_email' => '18953308695@163.com',
            'subject' => '海鲜商城购买',
            'sign' => 'dZQKdNFNaj4IyChB2Jwt0URMtad+JoHCk5QBCPU0uj9IYza5Hx82IJ0waSoDp21iEwoXAWD4vXUrp40XLC+/LnIMwQY8/onhG5TKGgThD7pShm2fwXy6xkW3ZHDk2IKdz4kifRJnheeAVQlhHsX3ktZYE3n2fDD+KRA6/8KOqRfXYQfS6hJu5BrMlnJik+KBSnUwWIWLIgmbMWJ8Of3F8SynERvuZUcykVMHd50QruymDL4E9w5ixilnnZvXH7wmhUvR369NyxOwfYZQvkEo3f6vXBntu7f90iJWQEU5S/L3r+F+/8n0HL+vrkLxnMj2XIsiwlKgLUCcUUCX52OzEg==',
            'body' => '精品海带丝 67g*6袋x1/13.00',
            'buyer_id' => '2088502994830659',
            'invoice_amount' => '13.00',
            'notify_id' => '2021070700222143343030651410859239',
            'fund_bill_list' => '[{&quot;amount&quot;:&quot;13.00&quot;,&quot;fundChannel&quot;:&quot;PCREDIT&quot;}]',
            'notify_type' => 'trade_status_sync',
            'trade_status' => 'TRADE_SUCCESS',
            'receipt_amount' => '13.00',
            'buyer_pay_amount' => '13.00',
            'app_id' => '2019111569140663',
            'sign_type' => 'RSA2',
            'seller_id' => '2088621180325374',
            'gmt_payment' => '2021-07-07 14:33:43',
            'notify_time' => '2021-07-07 14:36:03',
            'version' => '1.0',
            'out_trade_no' => 'SDD-1310-1625639600',
            'total_amount' => '13.00',
            'trade_no' => '2021070722001430651428959429',
            'auth_app_id' => '2019111569140663',
            'buyer_logon_id' => 'che***@sina.cn',
            'point_amount' => '0.00',
        );
        echo str_replace("&quot;",'"','[{&quot;amount&quot;:&quot;13.00&quot;,&quot;fundChannel&quot;:&quot;PCREDIT&quot;}]');
//        print_r($aa);
//        var_dump(stripslashes('[{&quot;amount&quot;:&quot;13.00&quot;,&quot;fundChannel&quot;:&quot;PCREDIT&quot;}]'));
exit();
        $amoney=-2000;
        $yubi=1999.2;
        $MM['account_money']=0.8;
        $MM['money_freeze']=0;

        $data['amoney']=bcadd($amoney,$yubi,2);
        $bamoney=-$yubi;


        $data['freeze_money'] = bcsub($MM['money_freeze'] , $yamoney,2);

        var_dump($data);exit;

        var_dump(sprintf("%.2f",12));

         $dt='2020-12-30';
         $yue=1;
         $i=4;
         $stime=strtotime("$dt+".$yue*$i."month");

          var_dump(date("Y-m-d",$stime));
          exit;  



        var_dump(date("Y-m-d",strtotime('+15 day')));
        var_dump(date("Y-m-d H:i:s",strtotime('+1 month +1 day')));
                  if(!empty(1)){

        var_dump("1");
          }
          exit();
   		 $this->assign("dh",1);

		$Bconfig = C("BORROW");
		$per = C('DB_PREFIX');
		$curl = $_SERVER['REQUEST_URI'];
 	   if(is_mobile()==1){
    				 
	 
			echo "<script type='text/javascript'>";

     	    echo "window.location.href='/app.html';";
			echo "</script>";die;
			// echo $_SERVER['PHP_SELF']; #/PHP/XX.php
		}
		//预热项目
		$pre = C('DB_PREFIX');
		  
        $searchMaps['borrow_status']= array("in",'1,2');
		// $searchMaps['start_time']= array("gt",time());     
		$searchMaps['pid'] = array('neq',4);
        $parm['limit'] = 3;
        $parm['map'] = $searchMaps;
        $parm['orderby']="b.id desc";
        
        $listProduct_yr = getBorrowList($parm);  
        foreach ($listProduct_yr['list'] as $key => $vo) {
            $listProduct_yr['list'][$key]['progress'] = getFloatValue($vo['has_borrow'] / $vo['borrow_money'] * 100, 4);
        }
        $this->assign("listProduct_yr",$listProduct_yr);


        $horder="art_time desc";
        $hlist=M("article")->field('id,title,art_keyword,art_img')->where("type_id=531")->order($horder)->select();
        $this->assign('hlist', $hlist);

        $xlist=M("article")->field('id,type_id,title,art_keyword,art_img')->where("type_id=526")->limit("0,3")->order($horder)->select();
        $this->assign('xlist', $xlist);

        $rlist=M("article")->field('id,type_id,title,art_keyword,art_img')->where("type_id=54")->limit("0,3")->order($horder)->select();
        $this->assign('rlist', $rlist);

        $mlist=M("article")->field('id,type_id,title,art_keyword,art_img')->where("type_id=22")->limit("0,3")->order($horder)->select();
        $this->assign('mlist', $mlist);


        $yzmap["borrow_status"]=array("in",array(1,2,4,6,7));

        $yzz=M("borrow_info")->field("id,borrow_uid,borrow_type,borrow_money,borrow_name")->where($yzmap)->order('id desc')->find();
        if($yzz["borrow_type"]=="1"){
            $yzz["type"]="联合养殖";
            $yzz["entype"]="Combined culture";
        }
        if($yzz["borrow_type"]=="2"){
            $yzz["type"]="联合销售";
            $yzz["entype"]="Joint sale";
        }

        $this->assign('yzz', $yzz);

        //成功项目
        unset($searchMaps['start_time']);
        $searchMaps['borrow_status']= array("in",'6');  
        $parm['limit'] = 3;
        $parm['map'] = $searchMaps;
         $parm['orderby']="b.start_time desc"; 
        $listProduct_suc = getBorrowList($parm);  
        $this->assign("listProduct_suc",$listProduct_suc);
		$glo = array('web_title'=>'我要投资');
    	$this->assign($glo);	

		
		//商城
		$_REQUEST["pid"]=$_REQUEST["pid"]==0?"0":$_REQUEST["pid"];
		$_REQUEST["jifen"]=$_REQUEST["jifen"]==0?"0":$_REQUEST["jifen"];
		$map=array();
		$map['art_set'] = 0;
		if($_REQUEST["type_id"]!=0){
			$map["type_id"]=$_REQUEST["type_id"];	
		} 

		//分页处理
		$field= '*';
		$list['list'] = M('market')->where($map)->limit(3)->order("id DESC")->select();
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);


		
		
		//文章
		
        $parmarticle = array(
        'type_id'=> 21,
        'pagesize'=> 7
        );  
        $article['article']['name'] = $nowCategory['type_name'];
        $article['list'] = getArticleList($parmarticle);
        // var_dump($tpl_var['list']['list']); 
      

        $this->assign('article',$article['list']); 
		$this->assign('articlerightList',$article['rightList']);

	    $this->display(); 
		


		/****************************募集期内标未满,自动流标 新增 2013-03-13****************************/
        //流标返回
        $mapT                  = array();
        $mapT['collect_time']  = array("lt", time());
        $mapT['borrow_status'] = 2;
        $tlist                 = M("borrow_info")->field("id,borrow_uid,borrow_type,borrow_money,first_verify_time,borrow_interest_rate,borrow_duration,repayment_type,collect_day,collect_time")->where($mapT)->select();
        if (empty($tlist)) {
            exit;
        }

        foreach ($tlist as $key => $vbx) {
            $borrow_id = $vbx['id'];
            //流标
            $done           = false;
            $borrowInvestor = D('borrow_investor');
            $binfo          = M("borrow_info")->field("borrow_type,borrow_money,borrow_uid,borrow_duration,repayment_type")->find($borrow_id);
            $investorList   = $borrowInvestor->field('id,investor_uid,investor_capital')->where("borrow_id={$borrow_id}")->select();
            M('investor_detail')->where("borrow_id={$borrow_id}")->delete();
            if ($binfo['borrow_type'] == 1) {
                $limit_credit = memberLimitLog($binfo['borrow_uid'], 12, ($binfo['borrow_money']), $info = "{$binfo['id']}号标流标");
            }
			
            //返回额度
            $borrowInvestor->startTrans();

            $bstatus       = 3;
            $upborrow_info = M('borrow_info')->where("id={$borrow_id}")->setField("borrow_status", $bstatus);
            //处理借款概要
            $buname = M('members')->getFieldById($binfo['borrow_uid'], 'user_name');
            //处理借款概要
            if (is_array($investorList)) {
                $upsummary_res = M('borrow_investor')->where("borrow_id={$borrow_id}")->setField("status", $type);
                foreach ($investorList as $v) {
                    MTip('chk15', $v['investor_uid']); //sss
                    $accountMoney_investor        = M("member_money")->field(true)->find($v['investor_uid']);
                    $datamoney_x['uid']           = $v['investor_uid'];
                    $datamoney_x['type']          = ($type == 3) ? 16 : 8;
                    $datamoney_x['affect_money']  = $v['investor_capital'];
                    $datamoney_x['account_money'] = ($accountMoney_investor['account_money'] + $datamoney_x['affect_money']); //投标不成功返回充值资金池
                    $datamoney_x['collect_money'] = $accountMoney_investor['money_collect'];
                    $datamoney_x['freeze_money']  = $accountMoney_investor['money_freeze'] - $datamoney_x['affect_money'];
                    $datamoney_x['back_money']    = $accountMoney_investor['back_money'];

                    //会员帐户
                    $mmoney_x['money_freeze']  = $datamoney_x['freeze_money'];
                    $mmoney_x['money_collect'] = $datamoney_x['collect_money'];
                    $mmoney_x['account_money'] = $datamoney_x['account_money'];
                    $mmoney_x['back_money']    = $datamoney_x['back_money'];

                    //会员帐户
                    $_xstr                       = ($type == 3) ? "复审未通过" : "募集期内标未满,流标";
                    $datamoney_x['info']         = "第{$borrow_id}号标" . $_xstr . "，返回冻结资金";
                    $datamoney_x['add_time']     = time();
                    $datamoney_x['add_ip']       = get_client_ip();
                    $datamoney_x['target_uid']   = $binfo['borrow_uid'];
                    $datamoney_x['target_uname'] = $buname;
                    $moneynewid_x                = M('member_moneylog')->add($datamoney_x);
                    if ($moneynewid_x) {
                        $bxid = M('member_money')->where("uid={$datamoney_x['uid']}")->save($mmoney_x);
                    }

                }
            } else {
                $moneynewid_x  = true;
                $bxid          = true;
                $upsummary_res = true;
            }

            if ($moneynewid_x && $upsummary_res && $bxid && $upborrow_info) {
                $done = true;
                $borrowInvestor->commit();
            } else {
                $borrowInvestor->rollback();
            }
            if (!$done) {
                continue;
            }
	
            MTip('chk11', $vbx['borrow_uid'], $borrow_id);
            $verify_info['borrow_id']     = $borrow_id;
            $verify_info['deal_info_2']   = text($_POST['deal_info_2']);
            $verify_info['deal_user_2']   = 0;
            $verify_info['deal_time_2']   = time();
            $verify_info['deal_status_2'] = 3;
            if ($vbx['first_verify_time'] > 0) {
                M('borrow_verify')->save($verify_info);
            } else {
                M('borrow_verify')->add($verify_info);
            }

            $vss = M("members")->field("user_phone,user_name")->where("id = {$vbx['borrow_uid']}")->find();
            SMStip("refuse", $vss['user_phone'], array("#USERANEM#", "ID"), array($vss['user_name'], $verify_info['borrow_id']));
            //@SMStip("refuse",$vss['user_phone'],array("#USERANEM#","ID"),array($vss['user_name'],$verify_info['borrow_id']));
            //updateBinfo
            $newBinfo                       = array();
            $newBinfo['id']                 = $borrow_id;
            $newBinfo['borrow_status']      = 3;
            $newBinfo['second_verify_time'] = time();
            $x                              = M("borrow_info")->save($newBinfo);
        }
        /****************************募集期内标未满,自动流标 新增 2013-03-13****************************/
    }
    public function test(){
		if(empty($_GET['p'])){
			$_GET['p']=0;
		}else{
			$_GET['p']=$_GET['p']-1;
		}
       	  $invitation = M('members')->field("id,incode")  ->limit($_GET['p'],100) ->select();
		  foreach($invitation as $k=>$v){
			   $add["incode"]=getincode();
			   var_dump($add["incode"],M('members')->where("id=".$v["id"]) ->save($add));
		  }
    }
 
    public function islogin(){
        $uid = $_SESSION[$session_id];
        if(!empty($uid)){
            writelog($uid);
            $vo = M('members')->field("id,user_name")->find($uid);
            if(is_array($vo)){
                session(array('name'=>'session_id','expire'=>15*3600));
                foreach($vo as $key=>$v){
                    session("u_{$key}",$v);
                }
                $up['uid'] = $vo['id'];
                $up['add_time'] = time();
                $up['ip'] = get_client_ip();
                M('member_login')->add($up);
                // session($session_id, '');  
                ajaxmsg(); 
                exit();
            }else{
                ajaxmsg("",0);
            }
        }
    }
    public function xxhuikuan(){
//        $res=$this->doxxhuikuan(1311,'鲁东港渔养-66169项目已线下回款扣除待收本金');
//        var_dump($res);
    }
    public function doxxhuikuan($id,$info){
        //981  1
        $hinfo=M("borrow_info")->where("id=".$id)->find();
        if($hinfo["borrow_status"]!='6' || $hinfo["pid"]!='3'){
            return $hinfo['borrow_name'].'不满足线下回款条件';
        }
        M()->startTrans();
        $ddt["borrow_status"]=7;
        $res1=M("borrow_info")->where("id=".$id)->save($ddt);

       // pre($binfo);
//        $file="id,investor_uid,investor_capital,status";
//        $blist=M("borrow_investor")->field($file)->where("borrow_id=981")->select();
//        pre($blist);
//        $sql="select sum(investor_capital) as he from lzh_borrow_investor where borrow_id=981";
//        var_dump(M()->query($sql));
        //$done=memberMoneyLog($uid,73,floatval($_POST['money_collect']),$info);

        $sql="SELECT investor_uid,sum(investor_capital) as b,status  FROM `lzh_borrow_investor` WHERE borrow_id=".$id." and status=4 GROUP BY investor_uid";
        $list=M()->query($sql);
        //pre($list);exit;
        //$info='诗意和远方项目已线下回款扣除代收本金';
        $res4=true;
        foreach ($list as $k => $v) {
            //2
            $res2=memberMoneyLog($v['investor_uid'],73,-floatval($v['b']),$info, 0,"",false);
            if(!$res2){
                $res4=false;
               // var_dump('121');
                break;
            }
//3
        }
        $data["status"]=5;
        $res3=M("borrow_investor")->where("borrow_id=".$id)->save($data);

        if($res1&&$res4&&$res3){
            M()->commit();
            return '更新成功';
        }else{
            M()->rollback();
            return '更新失败';
        }


//        $borrowInvestor->commit();
//    } else {
//$borrowInvestor->rollback();

       // pre($list);
//        M("borrow_investor")->where("borrow_id=981")->group('investor_uid')->count();
//        var_dump(M("borrow_investor")->getLastSql());
//        $blist=M("borrow_investor")->field($file)->where("borrow_id=981")->select();
    }
    
}
