<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends WCommonAction {	
	
	public function pay() {
		 
		 
	}
		public function asscs() {
		$z=0;   
		$a=4417;
	 //4410
	 for($i=0;$i<4417;$i++){
		 $zcr=M("borrow_investor")->order("id") ->where("id >".$i)  ->find();
 
		 //echo(M("borrow_investor")->getlastsql());
 	
		$zcri=$zcr["id"];
		echo("<span style='color:#f00'>".$zcri."</span><br/>"); 
		 
				$mx=M("borrow_investor")->where("id={$a} ")->find();
				$mx2=M("borrow_investor")->where("investor_uid= ".$mx['investor_uid']." and "."investor_capital=".$mx['investor_capital']." and id !=".$mx['id']) ->select();
				 if(count($mx2)>=1){
					 foreach($mx2 as $k=>$v){
						if($v['id']-$mx['id']<3  ){
							M("borrow_investor")->where("id={$v['id']} ")->delete();
							echo $v['id']."<br/>";
						}
					
					}
					}
					$z=$i;
	 }
		
	 
	if($z==$a+49){
		echo "<script language=JavaScript> location.replace(location.href);</script>"; 
		
	}
		
		 
	 
		 
	}
	public function notice1() {
		$uid=2018;
		var_dump($uid);die;
 		var_dump(notice1("5", $uid, $data = array("MONEY"=>500)));
	  	var_dump(notice1("6", $uid, $data = array("MONEY"=>500)));
	 	var_dump(notice1("7", $uid, $data = array("BORROW_NAME"=>"aini")));
		var_dump(notice1("8", $uid, $data = array("MONEY"=>500)));
	  	var_dump(notice1("9", $uid, $data = array("BORROW_NAME"=>"aini")));

	}
	public function index(){
		if(is_mobile()==0){
			echo "<script type='text/javascript'>";
     		echo "window.location.href='/';";
			echo "</script>";die;
		}
		//新闻资讯
		$map["tuijian"]=1;
		$map["type_id"]=array("neq",11);
		$gg = M('article')->field("id,title,type_id")->where($map)->order('id desc')->find();

		$gtype= M('article_category')->where('id = '.$gg["type_id"])->find();
		$gg["tname"]=$gtype["type_name"];
		//var_dump($gg);exit();
		$this->assign("gg",$gg);

		$xmap["tuijian"]=1;
		$xmap["type_id"]=11;
		$xinshou = M('article')->field("id,title,art_time,type_id")->where($xmap)->order('id desc')->find();
		$this->assign("xinshou",$xinshou);


		$searchMap['pid']=array("neq", "4");
		$searchMap['borrow_status'] = array("in", '1,2,4,6,7');
        $parm['map']      = $searchMap;
        $parm['limit'] =3; 
		//排序
		(strtolower($_GET['sort'])=="asc")?$sort="desc":$sort="asc";
		unset($surl['orderby'],$surl['sort']);
		$orderUrl = http_build_query($surl);
	 
		//$parm['orderby']="b.borrow_status ASC,b.id DESC";
	 
		//排序
		
		$parm['orderby']="borrow_status asc,b.id DESC";
		$list_type1 = getBorrowList($parm);
		$this->assign("list_type1",$list_type1);	

		//var_dump($list_type1["list"]);exit();

		$this->display();
	}
    public function indexbf(){
// $smsTxt = fs("Webconfig/payconfig");
//	var_dump($smsTxt);
 		$payonline = $this->payConfig['allinpay']['MerNo'];
// 		var_dump($msgconfig["sms"]);
// 		var_dump($payonline);
    	 	if(is_mobile()==0){
    				 
	 
			echo "<script type='text/javascript'>";

     	echo "window.location.href='/';";
			echo "</script>";die;
			// echo $_SERVER['PHP_SELF']; #/PHP/XX.php
		}
        session($_REQUEST['session_id'],$UID);
		$strUrl = U("invest/index").'?1=1';
		static $newpars;
		$Bconfig = C("BORROW");
		$per = C('DB_PREFIX');
		$curl = $_SERVER['REQUEST_URI'];
		 
		$searchMap['pid']=array("eq", "1");
	 	//$searchMap['is_tuijian'] = array("eq", "1");
		$searchMap['borrow_status'] = array("in", '1,2,4,6,7');
        $parm['map']      = $searchMap;
		$parm['limit'] =2; 
				//排序
		(strtolower($_GET['sort'])=="asc")?$sort="desc":$sort="asc";
		unset($surl['orderby'],$surl['sort']);
		$orderUrl = http_build_query($surl);
	 
			$parm['orderby']="b.borrow_status ASC,b.id DESC";
	 
		//排序
		
		$parm['orderby']="b.id DESC";
		$list_type1 = getBorrowList($parm);	
			
		$searchMap['pid']=array("eq", "2");
		$parm['map']      = $searchMap;
		
		$list_type2 = getBorrowList($parm);	
 
		$searchMap['pid']=array("eq", "3");
		$parm['map']      = $searchMap;
		$list_type3 = getBorrowList($parm);	
 
		$this->assign("list_type1",$list_type1);
		$this->assign("list_type2",$list_type2);
		$this->assign("list_type3",$list_type3);		
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
    	$this->assign($glo);	
    	$this->assign("strUrl",$strUrl);
		$tiyanbiao=M('borrow_info')->where('id = 1') ->find();
		$this->assign("tiyanbiao",$tiyanbiao);
		$ctiyan=M('borrow_investor')->where('borrow_id = 1') ->count();
		$this->assign("ctiyan",$ctiyan);
		$mtiyan=M('borrow_investor')->where('borrow_id = 1') ->sum("investor_capital");
		$this->assign("mtiyan",$mtiyan);	
 
		$this->display();	
			
   
	
		$pre = C('DB_PREFIX');
		$searchMap = array();
		$parm=array();
		$parm['orderby']="b.id DESC";

        $searchMaps['borrow_status']= array("in",'1');
        // $searchMaps['is_tuijian'] = array('eq',1);       
        $parm['limit'] = 4;
        $parm['map'] = $searchMaps;
        $listProduct_yr = getBorrowList($parm);  
	
        $this->assign("listProduct_yr",$listProduct_yr);

		//推荐项目
		$searchMap['borrow_status']= array("in",'2,4,7');
		// $searchMap['is_tuijian'] = array('eq',1);		
		$parm['limit'] = 4;
		$parm['map'] = $searchMap;
		$listProduct_top = getBorrowList($parm);		
		$this->assign("listProduct_top",$listProduct_top);
		
		unset($searchMap['is_tuijian']);
		$searchMap['borrow_status']= array("in",'4,6');;		
        $searchMap['pid'] = 20;
		$parm['limit'] = 4;
		$parm['map'] = $searchMap;
		$listProduct_sell = getBorrowList($parm);		
        $this->assign("listProduct_sell",$listProduct_sell);

        


        unset($searchMap['pid']);
		$searchMap['borrow_status']= 7;		
		$parm['limit'] = 4;
		$parm['map'] = $searchMap;
		$listProduct_success = getBorrowList($parm);		
		$this->assign("listProduct_success",$listProduct_success);
		
		$searchMap['borrow_status']= array("in",'1,2,4,6');
		$parm['limit'] = 8;
		$cateList = M('pro_category')->field('id,type_name')->where('parent_id = 0')->select();					
		foreach ($cateList as $k => $v) {
			//融资项目
			$searchMap['pid'] = $v['id'];
			$parm['map'] = $searchMap;
			$cateList[$k]['borrowList'] = getBorrowList($parm);					
		}		
		$this->assign("cateList",$cateList);
		//新闻资讯
		$artCate = M('article_category')->where('parent_id = 429')->order('sort_order desc,id asc')->select();
		foreach ($artCate as $key => $value) {
			$artCate[$key]['artList'] = getArtList($value['id'],4);
		}

		$this->assign("artCate",$artCate);	

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
       $permoney  = 118.33 / 10000;
       $permoney += 1;
       $affect_money = 30068.00 * $permoney;
       echo $affect_money."<br />";
        echo bcsub($affect_money, 30068.00, 2);
    }
}
