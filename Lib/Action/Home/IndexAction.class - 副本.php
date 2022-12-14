<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends WCommonAction {	

    public function index(){
   		 $this->assign("dh",1);

		$Bconfig = C("BORROW");
		$per = C('DB_PREFIX');
		$curl = $_SERVER['REQUEST_URI'];
 	   if(is_mobile()==1){
    				 
	 
			echo "<script type='text/javascript'>";

     	    echo "window.location.href='/m/?version=2.2';";

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
}
