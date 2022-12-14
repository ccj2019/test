<?php
    /**
    * 普通标债权转让控制器类
    * 
    * @author  zhangjili 404851763@qq.com
    * @time 2014-01-03 16:28
    * @copyright lvmaque 超级版
    * @link www.lvmaque.com
    */
    class BondAction extends HCommonAction
    {
        
        /**
        * 债权转让列表
        * 
        */
        public function index()
        {
		
		$vo1 = M('members')->field('id,user_name,user_email,user_pass,is_ban')->where("id={$this->uid}")->find();
		if($vo1['is_ban']==1||$vo1['is_ban']==2) $this->error("您的帐户已被冻结，请联系客服处理！",__APP__."/index.html");
		
		$curl = $_SERVER['REQUEST_URI'];
		$urlarr = parse_url($curl);
		parse_str($urlarr['query'],$surl);//array获取当前链接参数，2.
        $urlArr = array('borrow_status','borrow_duration','leve');
		$leveconfig = FS("Webconfig/leveconfig");
		foreach($urlArr as $v){
			$newpars = $surl;//用新变量避免后面的连接受影响
			unset($newpars[$v],$newpars['type'],$newpars['order_sort'],$newpars['orderby']);//去掉公共参数，对掉当前参数
			foreach($newpars as $skey=>$sv){
				if($sv=="all") unset($newpars[$skey]);//去掉"全部"状态的参数,避免地址栏全满
			}
			
			$newurl = http_build_query($newpars);//生成此值的链接,生成必须是即时生成
			$searchUrl[$v]['url'] = $newurl;
			$searchUrl[$v]['cur'] = empty($_GET[$v])?"all":text($_GET[$v]);
		}
		$searchMap['borrow_status'] = array("all"=>"不限制","2"=>"进行中","4"=>"复审中","6"=>"还款中","7"=>"已完成");
		$searchMap['borrow_duration'] = array("all"=>"不限制","0-3"=>"3个月以内","3-6"=>"3-6个月","6-12"=>"6-12个月","12-24"=>"12-24个月");
		$searchMap['leve'] = array("all"=>"不限制","{$leveconfig['1']['start']}-{$leveconfig['1']['end']}"=>"{$leveconfig['1']['name']}","{$leveconfig['2']['start']}-{$leveconfig['2']['end']}"=>"{$leveconfig['2']['name']}","{$leveconfig['3']['start']}-{$leveconfig['3']['end']}"=>"{$leveconfig['3']['name']}","{$leveconfig['4']['start']}-{$leveconfig['4']['end']}"=>"{$leveconfig['4']['name']}","{$leveconfig['5']['start']}-{$leveconfig['5']['end']}"=>"{$leveconfig['5']['name']}","{$leveconfig['6']['start']}-{$leveconfig['6']['end']}"=>"{$leveconfig['6']['name']}","{$leveconfig['7']['start']}-{$leveconfig['7']['end']}"=>"{$leveconfig['7']['name']}");

		$search = array();
		//搜索条件
		foreach($urlArr as $v){
			if($_GET[$v] && $_GET[$v]<>'all'){
				switch($v){
					case 'leve':
						$barr = explode("-",text($_GET[$v]));
						$search["m.credits"] = array("between",$barr);
					break;
					case 'borrow_status':
						$search["b.".$v] = intval($_GET[$v]);
					break;
					default:
						$barr = explode("-",text($_GET[$v]));
						$search["b.".$v] = array("between",$barr);
					break;
				}
			}
		}
	
		if($search['b.borrow_status']==0){
			$search['b.borrow_status']=array("in","2,4,6,7");
		}
		$str = "%".urldecode($_REQUEST['searchkeywords'])."%";
		if($_GET['is_keyword']=='1'){
			$search['m.user_name']=array("like",$str);
		}elseif($_GET['is_keyword']=='2'){
			$search['b.borrow_name']=array("like",$str);
			
		}
		
		$parm['map'] = $search;
         
       		//print_r($parm);
        //dump(M()->GetLastsql());exit;
		
            D("BondBehavior");
            $Bond = new BondBehavior();
            $list = $Bond->listAll($parm);
            $this->assign("list", $list);
			$this->assign("searchUrl",$searchUrl);
        	$this->assign("searchMap",$searchMap);
            $this->display();  
        }
        
        /**
        * 购买债权提示框
        * 
        */
        public function buybond()
        {
			if(!$this->uid){echo "<h4>请登陆后进行操作！</h4>";exit;}
            $invest_id = intval($_REQUEST['invest_id']);
			
            if(!$invest_id){echo "<h4>参数错误！</h4>";exit;}
            $bond = M("invest_bond")->field("transfer_price, money")->where("invest_id={$invest_id}")->find();
            $buy_user = M("member_money")->field("account_money, back_money")->where("uid={$this->uid}")->find();
            $account =  $buy_user['account_money'] + $buy_user['back_money'];
            $this->assign('bond', $bond);
            $this->assign('account', $account);
            $this->assign('invest_id', $invest_id);
			$this->display();
			exit;
            $d['content'] = $this->fetch();
            echo json_encode($d);
            
        }
        
        /**
        * 确认购买
        * 流程： 检测购买条件
        * 购买
        */
        public function buy()
        {
            $paypass = strval($_REQUEST['paypass']);
            $invest_id = intval($_REQUEST['invest_id']);
            
            D("BondBehavior");
            $Bond = new BondBehavior($this->uid);
            // 检测是否可以购买  密码是否正确，余额是否充足
            $result = $Bond->buy($paypass, $invest_id);

            if($result === 'TRUE'){
                ajaxmsg('购买成功');
            }else{
                ajaxmsg($result, 1);
            }
        }
		
		//`mxl:bondnow`//start
		public function detail(){
			if($_GET['type']=='commentlist'){
				//评论
				$cmap['tid'] = intval($_GET['id']);
				$clist = getCommentList($cmap,5);
				$this->assign("commentlist",$clist['list']);
				$this->assign("commentpagebar",$clist['page']);
				$this->assign("commentcount",$clist['count']);
				$data['html'] = $this->fetch('commentlist');
				exit(json_encode($data));
			}


			$pre = C('DB_PREFIX');
			$id = intval($_GET['id']);
			$Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";
			
			//合同ID
			if($this->uid){
				$invs = M('borrow_investor')->field('id')->where("borrow_id={$id} AND (investor_uid={$this->uid} OR borrow_uid={$this->uid})")->find();
				if($invs['id']>0) $invsx=$invs['id'];
				elseif(!is_array($invs)) $invsx='no';
			}else{
				$invsx='login';
			}
			$this->assign("invid",$invsx);
			//合同ID
			$borrowinfo = M("borrow_info bi")->field('bi.*,ac.title,ac.id as aid')->join('lzh_article ac on ac.id= bi.danbao')->where('bi.id='.$id)->find();
			if(!is_array($borrowinfo) || ($borrowinfo['borrow_status']==0 && $this->uid!=$borrowinfo['borrow_uid']) ) $this->error("数据有误");
			$borrowinfo['biao'] = $borrowinfo['borrow_times'];
			$borrowinfo['need'] = $borrowinfo['borrow_money'] - $borrowinfo['has_borrow'];
			$borrowinfo['lefttime'] =$borrowinfo['collect_time'] - time();
			$borrowinfo['progress'] = getFloatValue($borrowinfo['has_borrow']/$borrowinfo['borrow_money']*100,2);
			
			
			$this->assign("vo",$borrowinfo);

			$memberinfo = M("members m")->field("m.id,m.customer_name,m.customer_id,m.user_name,m.reg_time,m.credits,fi.*,mi.*,mm.*")->join("{$pre}member_financial_info fi ON fi.uid = m.id")->join("{$pre}member_info mi ON mi.uid = m.id")->join("{$pre}member_money mm ON mm.uid = m.id")->where("m.id={$borrowinfo['borrow_uid']}")->find();
			$areaList = getArea();
			$memberinfo['location'] = $areaList[$memberinfo['province']].$areaList[$memberinfo['city']];
			$memberinfo['location_now'] = $areaList[$memberinfo['province_now']].$areaList[$memberinfo['city_now']];
			$memberinfo['zcze']=$memberinfo['account_money']+$memberinfo['back_money']+$memberinfo['money_collect']+$memberinfo['money_freeze'];
			$this->assign("minfo",$memberinfo);

			//data_list
			$data_list = M("member_data_info")->field('type,add_time,count(status) as num,sum(deal_credits) as credits')->where("uid={$borrowinfo['borrow_uid']} AND status=1")->group('type')->select();
			$this->assign("data_list",$data_list);
			//data_list
			
			//`mxl:bondnow`
			$invest_id = intval($_GET['iid']);
			if (isset($invest_id) && is_numeric($invest_id) && $invest_id > 0){
				$bondinfo = M("invest_bond")->where("invest_id = {$invest_id}")->find();
				$bondinfo['sell_uname'] = M("members")->getFieldById($bondinfo['sell_uid'], "user_name");
				$bondinfo['deadline'] = M("borrow_investor")->getFieldById($invest_id, "deadline");
				$this->assign('bondinfo', $bondinfo);
			}
			//`mxl:bondnow`
			
			// 投资记录
			$this->investRecord($id);
			$this->assign('borrow_id', $id);

			//近期还款的投标
			//$time1 = microtime(true)*1000;
			$history = getDurationCount($borrowinfo['borrow_uid']);
			$this->assign("history",$history);

			//investinfo
			$fieldx = "bi.investor_capital,bi.add_time,m.user_name,bi.is_auto";
			$investinfo = M("borrow_investor bi")->field($fieldx)->join("{$pre}members m ON bi.investor_uid = m.id")->limit(10)->where("bi.borrow_id={$id}")->order("bi.id DESC")->select();
			$this->assign("investinfo",$investinfo);
			//investinfo
			
			//帐户资金情况
			$this->assign("investInfo", getMinfo($this->uid,true));
			$this->assign("mainfo", getMinfo($borrowinfo['borrow_uid'],true));
			$this->assign("capitalinfo", getMemberBorrowScan($borrowinfo['borrow_uid']));
			//帐户资金情况
			//展示资料
			$show_list = M("member_borrow_show")->where("uid={$borrowinfo['borrow_uid']}")->order('sort DESC')->select();
			$this->assign("show_list",$show_list);
			//展示资料
			
			//上传资料类型  注释  
			//$upload_type = FilterUploadType(FS("Webconfig/integration"));
			//$this->assign("upload_type", $upload_type); // 上传资料所有类型
			
			//判断是否已经关注
			$map['id'] = $this->uid;
			$mess = M('members')->where($map)->select();
			if(strpos($mess[0]['guanzhu'],$borrowinfo['id'])!==false){
				$borr='已关注';
			}else{
				$borr='加关注';
			}
			$this->assign("borr",$borr);
			//评论
			$cmap['tid'] = $id;
			$clist = getCommentList($cmap,5);
			$this->assign("Bconfig",$Bconfig);
			$this->assign("gloconf",$this->gloconf);
			$this->assign("commentlist",$clist['list']);
			$this->assign("commentpagebar",$clist['page']);
			$this->assign("commentcount",$clist['count']);
			$this->display();
		}
		
		public function investRecord($borrow_id=0)
		{
			
			isset($_GET['borrow_id']) && $borrow_id = intval($_GET['borrow_id']);
			$Page = D('Page');       
			import("ORG.Util.Page");       
			$count = M("borrow_investor")->where('borrow_id='.$borrow_id)->count('id');
			$Page     = new Page($count,10);
			
			
			$show = $Page->ajax_show();
			$this->assign('page', $show);
			if($_GET['borrow_id']){
				$list = M("borrow_investor as b")
							->join(C(DB_PREFIX)."members as m on  b.investor_uid = m.id")
							->join(C(DB_PREFIX)."borrow_info as i on  b.borrow_id = i.id")
							->field('i.borrow_interest_rate, i.repayment_type, b.investor_capital, b.add_time, b.is_auto, m.user_name')
							->where('b.borrow_id='.$borrow_id)->order('b.id')->limit($Page->firstRow.','.$Page->listRows)->select();
				$string = '';
			   foreach($list as $k=>$v){
				   $relult=$k%2;
					if(!$relult){
				   $string .= "<tr style='background-color: rgb(255, 255, 255);' class='borrowlist3'>
					   <td width='148' class='txtC'>".hidecard($v['user_name'],5)."</td>
						  <td  width='148' class='txtC'>";
						  }else{
							   $string .= "<tr style='background-color: rgb(236, 249, 255);' class='borrowlist5'>
					   <td width='148' class='txtC'>".hidecard($v['user_name'],5)."</td>
						  <td  width='148' class='txtC'>";
							  }
						$string .= $v['is_auto']?'自动':'手动'; 
					$string .= "</td>
						  <td  width='128' class='txtRight pr30'>".Fmoney($v['investor_capital'])."元</td>
						  <td width='198' class='txtC'>".date("Y-m-d H:i",$v['add_time'])."</td>
						 <td></td></tr>";
				}
				echo empty($string)?'暂时没有投资记录':$string;
			}
			
		}
		//`mxl:bondnow`//end
    }
?>
