<?php
// 本类由系统自动生成，仅供测试用途
class BorrowinAction extends MCommonAction {
    
            public function incordtime() {
 
           $M=     M("investor_detail");
         $row =  $M ->field("borrow_id,sum(receive_interest) as income")  ->group('borrow_id') ->select();
        // echo  M("investor_detail")->getlastsql();die;
             foreach($row as  $k=>$v){
                 $array=array("income"=>$v['income']);
                 
                 $M=     M("investor_detail")->where("borrow_id = ".$v["borrow_id"].' ');
                    M("borrow_info")->where("id = ".$v["borrow_id"].' ')->save(["repayment_interest"=>$v['income']]);
                //  echo 2;die;
                   $M ->save($array);
                    
                //  
                   //echo  M("borrow_info")->getlastsql();die;
             }
            echo 1 ;die;
// echo  M("borrow_info")->getlastsql();die;
        }
    public function index(){
    	$map['borrow_uid'] = $this->uid;
    	$borrow_status = '';
		if($_GET['borrow_status'] == 'nostart'){
			$map['borrow_status'] = array("in","0,3,5");
		
			$borrow_status = $_GET['borrow_status'];
		}else if($_GET['borrow_status'] == 'start'){
			$borrow_status = $_GET['borrow_status'];
			$map['borrow_status'] = array("in","1,2,6,7");
		}
    	//$map['status'] = array("neq","3");
		$list = getBorrowList($map,10);
		$this->assign('borrow_status',$borrow_status);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);

		$this->display();
    }
    public function summary(){
		$pre = C('DB_PREFIX');
		
		$this->assign("mx",getMemberBorrowScan($this->uid));
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }
	
	public function yuyuelist(){
		$map['borrow_uid'] = $this->uid;
		$map['borrow_status'] = array("in","0,2");
		
		if($_GET['start_time2']&&$_GET['end_time2']){
			$_GET['start_time2'] = strtotime($_GET['start_time2']." 00:00:00");
			$_GET['end_time2'] = strtotime($_GET['end_time2']." 23:59:59");
			
			if($_GET['start_time2']<$_GET['end_time2']){
				$map['add_time']=array("between","{$_GET['start_time2']},{$_GET['end_time2']}");
				$search['start_time2'] = $_GET['start_time2'];
				$search['end_time2'] = $_GET['end_time2'];
			}
		}
		$map['start_time'] = array('gt',time());
		$list = getBorrowList($map,10);
		$this->assign('search',$search);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
	
	
		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}
	
	public function fensilist(){
		$list = M('pro_guanzhu i')->field("i.*,b.borrow_name")->join("lzh_borrow_info b ON b.id=i.bid")->where("b.borrow_uid=".$this->uid)->select();
		$this->assign("list",$list);
		$this->assign("pagebar",$list['page']);
		$this->assign("total",$list['total_money']);
		$this->assign("num",$list['total_num']);
		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}
	
	
	public function taolunlist(){
		$list = M('comment i')->field("i.*,b.borrow_name")->join("lzh_borrow_info b ON b.id=i.tid")->where("b.borrow_uid=".$this->uid)->select();
		//echo M('comment i')->getlastsql();
		//print_r($list);
		//exit;
		$this->assign("list",$list);
		$this->assign("pagebar",$list['page']);
		$this->assign("total",$list['total_money']);
		$this->assign("num",$list['total_num']);
		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}
	
	public function sendtaolun(){
		
		$data['deal_info'] = $_REQUEST["comment"];
		$data['deal_time'] = time();
		M("comment")->where("id=".$_REQUEST["tid"])->save($data);	
		//echo M("comment")->getlastsql();
		ajaxmsg();
		
	}
	
	public function yuetanlist(){
		$list = M('comment_yuetan i')->field("i.*,b.borrow_name")->join("lzh_borrow_info b ON b.id=i.tid")->where("i.touid=".$this->uid)->select();
		$this->assign("list",$list);
		$this->assign("pagebar",$list['page']);
		$this->assign("total",$list['total_money']);
		$this->assign("num",$list['total_num']);
		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}
	
	public function sendyuetan(){
		
		$data['deal_info'] = $_REQUEST["comment"];
		$data['deal_time'] = time();
		M("comment_yuetan")->where("id=".$_REQUEST["tid"])->save($data);	
		//echo M("comment_yuetan")->getlastsql();
		ajaxmsg();
			
	}
	
	public function borrowing(){
		$map['borrow_uid'] = $this->uid;
 
		
	 
		if(isset($_GET['type']) && !empty($_GET['type']) && $_GET['type']!=0){
				$map['Borrow.pid']=$_GET["type"];
			//$Wsql.='AND (b.pid = '.$_GET["type"].')';
		}
		$list = getBorrowList($map,10);
		$this->assign('search',$search);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
	
		$this->display();
	}
	public function borrowpaying(){
		$map['borrow_uid'] = $this->uid;
		$borrow_status = '';
		if($_GET['borrow_status'] == 'paying'){
			$map['borrow_status'] = array("in","6");
			$borrow_status = $_GET['borrow_status'];
		}else if($_GET['borrow_status'] == 'paydone'){
			$map['borrow_status'] = array("in","7");
			$borrow_status = $_GET['borrow_status'];
		}else{
			$map['borrow_status'] = array("in","6,7");
		}
		if(!empty($_GET['name'])){
            $map['borrow_name'] = array("like","%".$_GET["name"]."%");
        }
        $this->assign("name",$_GET["name"]);
        $this->assign("borrow_status",$_GET["borrow_status"]);


		$list = getBorrowList($map,10);
		$this->assign("borrow_status",$borrow_status);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
	
		$this->display();
	}
	public function borrowbreak(){
		$map['borrow_uid'] = $this->uid;				
		$map['borrow_status'] = 6;
		
		if($_GET['start_time']&&$_GET['end_time']){
			$_GET['start_time'] = strtotime($_GET['start_time']." 00:00:00");
			$_GET['end_time'] = strtotime($_GET['end_time']." 23:59:59");
			
			if($_GET['start_time']<$_GET['end_time']){
				$map['add_time']=array("between","{$_GET['start_time']},{$_GET['end_time']}");
				$search['start_time'] = $_GET['start_time'];
				$search['end_time'] = $_GET['end_time'];
			}
		}
		$map['borrow_status'] = array('gt',5);
		$list = getBorrowList($map,10);
		$this->assign('search',$search);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
		$this->display();die;

		 
		//exit(json_encode($data));
	}
	
	public function borrowfail(){
		$map['borrow_uid'] = $this->uid;
		$map['borrow_status'] = 3;
		
		if($_GET['start_time4']&&$_GET['end_time4']){
			$_GET['start_time4'] = strtotime($_GET['start_time4']." 00:00:00");
			$_GET['end_time4'] = strtotime($_GET['end_time4']." 23:59:59");
			
			if($_GET['start_time4']<$_GET['end_time4']){
				$map['add_time']=array("between","{$_GET['start_time4']},{$_GET['end_time4']}");
				$search['start_time4'] = $_GET['start_time4'];
				$search['end_time4'] = $_GET['end_time4'];
			}
		}
		
		$list = getBorrowList($map,10);
		$this->assign('search',$search);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
	
	
		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}
	
	public function borrowfail2(){
		$map['borrow_uid'] = $this->uid;
		$map['borrow_status'] = 5;
		
		if($_GET['start_time5']&&$_GET['end_time5']){
			$_GET['start_time5'] = strtotime($_GET['start_time5']." 00:00:00");
			$_GET['end_time5'] = strtotime($_GET['end_time5']." 23:59:59");
			
			if($_GET['start_time5']<$_GET['end_time5']){
				$map['add_time']=array("between","{$_GET['start_time5']},{$_GET['end_time5']}");
				$search['start_time5'] = $_GET['start_time5'];
				$search['end_time5'] = $_GET['end_time5'];
			}
		}
		
		$list = getBorrowList($map,10);
		$this->assign('search',$search);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
	
	
		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}
	
	public function borrowfail1(){
		$map['borrow_uid'] = $this->uid;
		$map['borrow_status'] = 1;
		
		if($_GET['start_time6']&&$_GET['end_time6']){
			$_GET['start_time6'] = strtotime($_GET['start_time6']." 00:00:00");
			$_GET['end_time6'] = strtotime($_GET['end_time6']." 23:59:59");
			
			if($_GET['start_time6']<$_GET['end_time6']){
				$map['add_time']=array("between","{$_GET['start_time6']},{$_GET['end_time6']}");
				$search['start_time6'] = $_GET['start_time6'];
				$search['end_time6'] = $_GET['end_time6'];
			}
		}
		
		$list = getBorrowList($map,10);
		$this->assign('search',$search);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
	
	
		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}
	public function borrowdone(){
		$map['borrow_uid'] = $this->uid;
		$map['borrow_status'] = 7;
		
		if($_GET['start_time8']&&$_GET['end_time8']){
			$_GET['start_time8'] = strtotime($_GET['start_time8']." 00:00:00");
			$_GET['end_time8'] = strtotime($_GET['end_time8']." 23:59:59");
			
			if($_GET['start_time8']<$_GET['end_time8']){
				$map['add_time']=array("between","{$_GET['start_time8']},{$_GET['end_time8']}");
				$search['start_time8'] = $_GET['start_time8'];
				$search['end_time8'] = $_GET['end_time8'];
			}
		}
		if(isset($_GET['type']) && !empty($_GET['type']) && $_GET['type']!=0){
				$map['Borrow.pid']=$_GET["type"];
			//$Wsql.='AND (b.pid = '.$_GET["type"].')';
		}
		$list = getBorrowList($map,10);
		
		$this->assign('search',$search);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
		//var_dump($list['list']);die;
		$this->display();
	}
	public function cancel(){
		$id = intval($_POST['id']);
		$newid = M('borrow_info')->where("borrow_uid={$this->uid} AND id={$id} AND borrow_status=0")->delete();
		if($newid) ajaxmsg("撤消成功");
		else ajaxmsg("出错，如果您正在撤回的是还未初审的标，请重试，如已经初审，则不能撤回",0);
			
	}
	
	public function doexpired(){
		$borrow_id = intval($_POST['bid']);
		$sort_order = intval($_POST['sort_order']);
		$newid = borrowRepayment($borrow_id,$sort_order);
		if($newid===true) ajaxmsg();
		elseif($newid===false) ajaxmsg('还款失败，请重试',0);
		else ajaxmsg($newid,0);
	}
	public function fenhong()
    {
        //dorepayment
        //获取投资列表
        $pre = C('DB_PREFIX');
        $id = empty($_GET['id']) ? $_POST['id'] : $_GET['id'];
        $has_capital = '1';
        $has_done = '1';
        $borrowinfo = M('borrow_info')->find($id);

        //var_dump($borrowinfo);exit;
        if($borrowinfo["borrow_uid"]!=$this->uid){
        	$this->error('数据有误',"__APP__/Index");
        }
        if (7 == $borrowinfo['borrow_status']) {
            $this->error('已还款');
        }

        if (!is_array($borrowinfo) || (0 == $borrowinfo['borrow_status'] && $this->uid != $borrowinfo['borrow_uid'])) {
            $this->error('数据有误');
        }

        $this->assign('binfo', $borrowinfo);

        $pre = C('DB_PREFIX');
        $list = array();
        $Osql = "sort_order ASC";
        $list = m("investor_detail")->where("borrow_id={$id}")->order($Osql)->select();
        $borrow_duration = $borrowinfo['borrow_duration'];
        foreach ($list as $key => $value) {
           $list[$key]['borrow_duration'] = $borrowinfo['borrow_duration']/$borrowinfo["total"];
           $list[$key]['year_invest'] = $borrowinfo['borrow_interest_rate'] / $borrow_duration * 12/100;

           //$investor = m('borrow_investor')->field('id,investor_uid,investor_capital,investor_interest,is_experience,member_interest_rate_id')->where("borrow_id={$id} and id=".$value['invest_id'])->find();
           //$interest = round($value['income'] * ($investor['investor_capital'] / $borrowinfo['borrow_money']), 2);
           //$list[$key]['allmoney'] = $interest + $investor['investor_capital']; //收益
            $list[$key]['allmoney'] = $value["interest"] + $value['capital']; //收益
           $is_log = m("investor_log")->where("borrow_id={$id} and sort_order=".$value['sort_order'])->find();
           if($is_log){
                $list[$key]['is_repmeny'] = 1;
           }else{
                $list[$key]['is_repmeny'] = 0;
           }

        }
        //var_dump($list);
        $date=array();
        for($i=1;$i<=$borrowinfo['total'];$i++){
        	$date[$i]="{$i}";
        }
        $this->assign("date",$date);
        $this->assign("list",$list);

        $this->assign('bid', $id);
        $this->display();
    }
    public function getshouyi(){
        $borrow_id=$_POST["borrow_id"];
        $sort_order=$_POST["qi"];
        $borrowinfo = M('borrow_info')->field(true)->find($borrow_id);
        if (7 == $borrowinfo['borrow_status']) {
            $json['status'] = '0';
            $json['msg'] = '已还款';
            outJson($json);
        }
        $xishu=1;
        if($borrowinfo['borrow_duration']>12){
            $xishu=$borrowinfo['borrow_duration']/12;
        }
        $json['xishu'] = $xishu;
        $json['shoujia'] = $borrowinfo["shoujia"];
        if(empty($borrowinfo["shoujia"])){
            $borrowInvestor = D('borrow_investor');
            $income=sprintf("%.2f",$borrowinfo['borrow_interest_rate'] * $xishu*($borrowinfo['has_borrow'] / $borrowinfo['total']/100));
            $json['income1'] = $borrowinfo['borrow_interest_rate'];
            $json['income'] = $income;
            if($borrowinfo['total'] == $sort_order){
                $json['capital'] =  $borrowinfo['has_borrow'];
                $chajia=0;//$borrowinfo["shoujia"]-($borrowinfo["borrow_interest_rate"]*$borrowinfo["borrow_min"]/100+$borrowinfo["borrow_min"]);
                //$investorList = $borrowInvestor->field('id,xsfenshu')->where("borrow_id={$borrow_id} and lzh_borrow_investor.status!=3")->select();
                $sc=0;
//                foreach ($investorList as $k=>$v){
//                    $sc+=$v["xsfenshu"];
//                }
                $json['chajia'] =0;// $sc*$chajia;
            }else{
                $json['capital']  = 0;
                $json['chajia']=0;
            }
            $json['status'] = '1';
        }else{
            $borrowInvestor = D('borrow_investor');
            $income=sprintf("%.2f",$borrowinfo['borrow_interest_rate'] * $xishu *($borrowinfo['sghas_borrow'] / $borrowinfo['total']/100));
            $json['income'] = $income;
            //$json['income1'] = empty($borrowinfo["xs_time"]).'1---'.empty($borrowinfo["shoujia"]);
            if($borrowinfo['total'] == $sort_order){
                $json['capital'] =  $borrowinfo['sghas_borrow'];
                $chajia=$borrowinfo["shoujia"]-($borrowinfo["borrow_interest_rate"]*$borrowinfo["borrow_min"]/100+$borrowinfo["borrow_min"]);
                $investorList = $borrowInvestor->field('id,xsfenshu')->where("borrow_id={$borrow_id} and lzh_borrow_investor.status!=3")->select();
                $sc=0;
                foreach ($investorList as $k=>$v){
                    $sc+=$v["xsfenshu"];
                }
                $json['chajia'] = $sc*$chajia;
            }else{
                $json['capital']  = 0;
                $json['chajia']=0;
            }
            $json['status'] = '1';
        }
        outJson($json);
    }
    public function generate()
    {
        
//      $_POST=   array(
//   "investyear"=>"2019",
//   "sort_order"=> "1",
//   "income"=>"50000",
//   "starttime"=>"2019-01-12",
//   "endtime"=>"2019-01-26",
//   "has_capital"=>"1",
//   "bid"=>"1186",
// );
        $data['investyear'] = $investyear = $_POST['investyear'];
        $data['sort_order'] = $sort_order = $_POST['sort_order'];
        $data['income'] = $income = $_POST['income'];
        $data['starttime'] = $starttime = $_POST['starttime'];
        $data['endtime'] = $endtime = $_POST['endtime'];
        //$data['has_capital'] = $has_capital = $_POST['has_capital'];
        $data['borrow_id'] = $borrow_id = $bid = $_POST['bid'];

        $has_capital=0;
        // var_dump($sort_order);die;
        $borrowinfo = M('borrow_info')->field(true)->find($bid);
        if (7 == $borrowinfo['borrow_status']) {
            $this->error('已还款');
        }
        $isnew=true;
        if($borrowinfo["shoujia"]==0) {

            $chajia=0;
            if($borrowinfo['total'] == $sort_order){
                $data['has_capital'] = $has_capital=1;
            }
            $m = M('member_money')->field('account_money')->find($this->uid);
            if (1 == $has_capital) {
                //$capital = $income + $borrowinfo['borrow_money'];
                $capital1= $income + $borrowinfo['has_borrow']+$_POST['chajia'];;
            } else {
                //$capital = $income;
                $capital1= $income;
            }
            $isnew=false;
        }else{
            $chajia=$borrowinfo["shoujia"]-($borrowinfo["borrow_interest_rate"]*$borrowinfo["borrow_min"]/100+$borrowinfo["borrow_min"]);
            if($borrowinfo['total'] == $sort_order){
                $data['has_capital'] = $has_capital=1;
            }
            $m = M('member_money')->field('account_money')->find($this->uid);
            if (1 == $has_capital) {
                //$capital = $income + $borrowinfo['borrow_money'];
                $capital1= $income + $borrowinfo['sghas_borrow']+$_POST['chajia'];;
            } else {
                //$capital = $income;
                $capital1= $income;
            }
        }


        if ($m['account_money'] < $capital1) {
            $this->error('还款金额不足，请先充值再进行还款！');
        }
        $investor_detail = M('investor_detail')->where("borrow_id={$borrow_id} and sort_order={$sort_order}")->find();
        if ($investor_detail) {
            $this->error('本期已经提交申请了');
        }

        $borrowInvestor = D('borrow_investor');
        $borrowInvestor->startTrans();
        $investorList = $borrowInvestor->field('id,investor_uid,xsfenshu,fenshu,investor_capital,xsinvestor_capital,investor_interest,is_experience,member_interest_rate_id')->where("borrow_id={$borrow_id} and lzh_borrow_investor.status!=3")->select();
        $done = true;
        //var_dump(D('borrow_investor')->getlastsql());
        foreach ($investorList as $key => $value) {
            if (!$done) {
                break;
            }
           // $buname = m('members')->getFieldById($borrowinfo['borrow_uid'], 'user_name');
            
            $buname = m('members')->getFieldById($value['investor_uid'], 'user_name');
           
            //$ interest
            //每期收益
            if($isnew){
                $interest = sprintf("%.2f",$income *($value['xsinvestor_capital'] / $borrowinfo['sghas_borrow']));
            }else{
                $interest = sprintf("%.2f",$income *($value['investor_capital'] / $borrowinfo['has_borrow']));
            }

            $investdetail['borrow_id'] = $borrow_id;
            $investdetail['invest_id'] = $value['id'];
            $investdetail['investor_uid'] = $value['investor_uid'];
            $investdetail['borrow_uid'] = $borrowinfo['borrow_uid'];
            if (1 == $has_capital) {
                if($isnew){
                    $investdetail['capital'] = $value['xsinvestor_capital'];
                    if($value["xsfenshu"]!=0){
                        $investdetail['chajia'] = $chajia*$value["xsfenshu"];
                    }else{
                        $investdetail['chajia'] =0;
                    }
                }else{
                    $investdetail['capital'] = $value['investor_capital'];
                    $investdetail['chajia'] =0;
                }
            } else {
                $investdetail['capital'] = 0;
                $investdetail['chajia'] =0;
            }
            $investdetail['investyear'] = $investyear;
            $investdetail['interest'] = $interest;
            $investdetail['interest_fee'] = 0;
            $investdetail['income'] = $income;
            $investdetail['status'] = 0;
            $investdetail['sort_order'] = $sort_order;
            $investdetail['total'] = $borrowinfo['total'];
            $investdetail['starttime'] = strtotime($starttime);
            $investdetail['endtime'] = strtotime($endtime);
            $investdetail['has_capital'] = $has_capital;

            if($value["xsinvestor_capital"]!=0||$value["fenshu"]==0){
                $savedetail[] = $investdetail;
            }

            $investlog['nums'] = $key + 1;
            $investlog['has_capital'] = $has_capital;
            $investlog['investor'] = $buname;
            if($isnew){
                $investlog['capital'] = $value['xsinvestor_capital']; //投资金额
            }else{
                $investlog['capital'] = $value['investor_capital']; //投资金额
            }

            $investlog['benjin'] = $value['investor_capital']; //本金
            $investlog['invest'] = $interest; //收益
            $investlog['chajia'] = $investdetail['chajia']; //收益

            $investlog['allmoney'] = $interest + $investdetail['capital']+$investdetail['chajia']; //收益
            //$investlog['rate'] = rand($value['investor_capital'] / $borrowinfo['borrow_money'], 2); //支持比例
             
            $investlog['rate'] = sprintf("%.2f", $value['investor_capital'] / $borrowinfo['borrow_money']*100); //支持比例

           // var_dump($value['investor_capital'], $borrowinfo['borrow_money'],$investlog['rate'],$income, $interest );die;
            // var_dump($value['investor_capital'],$borrowinfo['borrow_money'],$investlog['rate'],1 ); die;
            if($value["xsinvestor_capital"]!=0||$value["fenshu"]==0) {
                $savedetaillog[] = $investlog;
            }
        }
        //平均年收益率 平均年收益率=本期收益金额/已筹总额/收益周期（月数）*12
        $borrow_duration = $borrowinfo['borrow_duration'];
        $data['year_invest'] = rand(($income / $borrowinfo['borrow_money'] / $borrow_duration) * 12*100, 2);
        $data['borrow_duration'] = $borrow_duration;
        $data['capital'] = $capital1;
        $data['chajia']=$chajia;
        $done = true;
        
      $invest_defail_id = m('investor_detail')->addAll($savedetail);

        if ($invest_defail_id) {
            $borrowInvestor->commit();
            $json['status'] = '1';
            $json['sort_order'] = $sort_order;
            $json['datalog'] = $data;
            $json['savedetaillog'] = $savedetaillog;
            //$json['savedetaillog1'] = $savedetail;
            exit(json_encode($json));
        }
    }
    public function invest_detail_del(){
        $id = empty($_GET['id']) ? $_POST['id'] : $_GET['id'];
        // $row = m("investor_detail")->where("id={$id}")->find();
        m("investor_detail")->where("id={$id}")->delete();

        $this->success('删除成功');
    }
    public function subgenerate()
    {
        $log['info'] = $info = $_POST['info'];
        $log['borrow_id'] = $borrow_id = $_POST['borrow_id'];
        $log['sort_order'] = $invest_orderid = $_POST['invest_orderid'];
        $log['capital'] = $capital = $_POST['capital'];
        $log['has_capital'] = $has_capital = $_POST['has_capital'];
        $borrowinfo = M('borrow_info')->field(true)->find($borrow_id);
        if (7 == $borrowinfo['borrow_status']) {
            $this->error('已还款');
        }
        if($borrowinfo['total'] == $invest_orderid){
        	$log['has_capital'] = $has_capital=1;
        }
        $investor_detail = M('investor_detail')->where("borrow_id={$borrow_id} and sort_order={$invest_orderid}")->find();
        $investor_log = M('investor_log')->where("borrow_id={$borrow_id} and sort_order={$invest_orderid}")->find();
        if (!isset($investor_detail)) {
            $this->error('请先生成收益明细');
        }
        if ($investor_log) {
            $this->error('收益申请重复');
        }
        $log['status'] = 0;
        $log['deadline'] = time();
        $investor_log_id = m('investor_log')->add($log);
        if ($investor_log_id) {
            memberMoneyLog($borrowinfo['borrow_uid'], 121, 0 - $capital, "对{$borrowinfo['borrow_name']}进行还款申请");
            $this->success('操作成功！', '/Member/');
        } else {
            M('investor_detail')->where("borrow_id={$borrow_id} and sort_order={$invest_orderid}")->delete();
            $this->error('操作失败！');
        }
    }
    public function borrowlog(){
    	$map['borrow_uid'] = $this->uid;
		$borrow_status = '';
		if($_GET['borrow_status'] == 'paying'){
			$map['repayment_time'] = 0;
			$borrow_status = $_GET['borrow_status'];
		}else if($_GET['borrow_status'] == 'paydone'){
			$map['repayment_time'] = array("gt","0");
			$borrow_status = $_GET['borrow_status'];
		}else{
			
		}
		import("ORG.Util.Page");
		$count = m("investor_detail")->where($map)->count();
		$p = new Page($count, $size);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		$list=m("investor_detail")->field(true)->where($map)->order('id desc')->limit($Lsql)->select();
		foreach($list as $key=>$v){
			$vo = M('borrow_info')->find($v['borrow_id']);
			$list[$key]['pid']=$vo['pid'];
			$list[$key]['borrow_img']=$vo['borrow_img'];
			$list[$key]['borrow_name']=$vo['borrow_name'];
		}
		$this->assign("borrow_status",$borrow_status);
		$this->assign("list",$list);
		$this->assign("pagebar",$page);
    	$this->display();
    }
}
