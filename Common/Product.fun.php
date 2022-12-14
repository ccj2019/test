<?php 
function getPaoCat(){
    return M('pro_category')->select();
}
function getProductUrl($id)
{
    return __APP__ . "/product/detail/id/{$id}" . c("URL_HTML_SUFFIX");
}
function get_user_guanzhu($uid){
     $num=M('pro_guanzhu')->where("uid=".$uid)->count('id');
     return $num;
}
function get_user_yuetan($uid){
    $num = M("comment_yuetan")->where("uid=".$uid)->count("id");
    return $num;
}
function get_city($city){
     $area_list = C('BORROW_CITY');// 城市列表
     if($area_list[$city]==""){
        return "未知";
     }
     return $area_list[$city];
}
function get_user_info($uid){
     $vm = m("members")->field("user_name")->where("id=".$uid)->find();
     return $vm["user_name"];
}
function get_hangye($pid){
     $type=M('pro_category')->field(true)->where("id=".$pid)->find();
     return $type["type_name"];
}
function get_pro_guanzhu($bid){
     $num=M('pro_guanzhu')->where("bid=".$bid)->count('id');
     return $num;
}
function get_pro_yuetan($bid){
    $num = M("comment_yuetan")->where("tid=".$bid)->count("id");
    return $num;
}
function get_pro_pingfen($bid){
    $vm = M("comment")->where("tid=".$bid)->sum("stars");
    $num=get_pro_comment($bid);
    $pingjun=number_format($vm/$num,1);
    if($pingjun==0)$pingjun="5.0";
    return $pingjun;
}
function get_pro_comment($bid){
    $vm = M("comment")->where("tid=".$bid)->count();
    return $vm; 
}
function get_pro_invest($bid){
    
    //$vm = M("borrow_investor")->where("borrow_id=".$bid)->count("distinct(investor_uid)");
     $vm = M("borrow_investor")->where("borrow_id=".$bid)->count("id");
    return $vm; 
}
function get_invest_num($uid){
    $vm = M("borrow_investor")->where("investor_uid=".$uid)->count("distinct(borrow_id)");
    return $vm; 
}
//获取借款列表
function getProductList($parm=array()){
    if(empty($parm['map'])) return;
    $map= $parm['map'];
    $orderby= $parm['orderby'];
    //$map = array_merge($map,$search);
    
    if($parm['pagesize']){
        //分页处理
        import("ORG.Util.Page");
        $count = M('pro_borrow_info b')->where($map)->count('b.id');
        $p = new Page($count, $parm['pagesize']);
        $page = $p->show();
        $Lsql = "{$p->firstRow},{$p->listRows}";
        //分页处理
    }else{
        $page="";
        $Lsql="{$parm['limit']}";
    }
    $pre = C('DB_PREFIX');
    $suffix=C("URL_HTML_SUFFIX");
    $field = "b.id,b.borrow_name,b.pause,b.borrow_uid,b.first_verify_time,b.hits,b.collect_day,borrow_img,b.borrow_type,b.reward_type,b.borrow_times,b.borrow_status,b.borrow_money,b.borrow_min,b.borrow_max,b.borrow_use,b.repayment_type,b.borrow_interest_rate,b.borrow_duration,b.collect_time,b.add_time,b.province,b.has_borrow,b.has_vouch,b.city,b.area,b.reward_type,b.reward_num,b.password,m.user_name,m.id as uid,m.credits,m.customer_name,b.is_tuijian,b.each_money,b.each_number,b.invest_method";
    $list = M('pro_borrow_info b')->field($field)->join("{$pre}members m ON m.id=b.borrow_uid")->where($map)->order($orderby)->limit($Lsql)->select();
    $areaList = getArea();
    foreach($list as $key=>$v){
        $list[$key]['location'] = $areaList[$v['province']].$areaList[$v['city']];
        $list[$key]['biao'] = $v['borrow_times'];
        $list[$key]['borrow_img'] = str_replace("'","",$v["borrow_img"]);
        
        if($list[$key]['borrow_img']=="")$list[$key]['borrow_img']="UF/Uploads/borrowimg/nopic.png";
        
        $list[$key]['repayment_type'] = $v["repayment_type"];
        $list[$key]['need'] = $v['borrow_money'] - $v['has_borrow'];
        $list[$key]['leftdays'] = getLeftTime($v['collect_time'],2);
        
        //echo $v["borrow_status"];
        if($v["borrow_status"]>2){
            $list[$key]['leftdays']="已经结束";
        }
        $list[$key]['lefttime'] =time2string($v['collect_time'] - time());
        
        $list[$key]['progress'] = getFloatValue($v['has_borrow']/$v['borrow_money']*100,0);//2
        if(substr($list[$key]['progress'], -1)==".")$list[$key]['progress']=substr($list[$key]['progress'],0,strlen($list[$key]['progress'])-1); ;
        $list[$key]['vouch_progress'] = getFloatValue($v['has_vouch']/$v['borrow_money']*100,2);
        $list[$key]['burl'] = MU("Home/product","product",array("id"=>$v['id'],"suffix"=>$suffix));
    }
    
    $row=array();
    $row['list'] = $list;
    $row['page'] = $page;
    //print_r($list);
    //exit;
    return $row;
}
function investMoney_pro($uid, $borrow_id, $money, $_is_auto = 0)
{
    $pre = c("DB_PREFIX");
    $done = false;
    $datag = get_global_setting();
    $investMoney = d("pro_borrow_investor");
    $investMoney->startTrans();
    $binfo = m("pro_borrow_info")->lock(true)->field("borrow_uid,borrow_money,borrow_name,borrow_interest_rate,borrow_type,borrow_duration,borrow_duration,borrow_status,repayment_type,has_borrow")->find($borrow_id);
    if($binfo)
    {
    $vminfo = getminfo($uid, "m.user_leve,m.time_limit,mm.account_money,mm.back_money");
    if($binfo["borrow_status"]!=2){
        $investMoney->rollback();
         return "当前标还没有审核，请等待开启后进行投标";  
    }
    if ($money<50) {
        $investMoney->rollback();
        return "最低投标金额为50元";
    }
    if ($vminfo['account_money'] < $money) {
        $investMoney->rollback();
        return "对不起，可用余额不足，不能投标";
    }
    
    $borrow_name=$binfo["borrow_name"];
    $minfo =getMinfo($uid,true);
    $levename=getLeveName($minfo["credits"]);
    $levetixian=getLeveTixian($minfo["credits"]);
    //对应等级的 利息管理费  资海
    if($binfo["borrow_type"]==3)$levetixian=0;
    $fee_rate = $levetixian / 100;
    
    $havemoney = $binfo['has_borrow'];
    $has=bcsub($binfo['borrow_money'], $havemoney,2);
    $hasb=bcsub($has, $money,2);
    if ($hasb < 0) {
        $investMoney->rollback();
        return "对不起，此标还差" . (bcsub($binfo['borrow_money'] , $havemoney,2)) . "元满标，您最多投标" . (bcsub($binfo['borrow_money'] , $havemoney,2)) . "元";
    }
    
    $investinfo['status'] = 1;
    $investinfo['borrow_id'] = $borrow_id;
    $investinfo['investor_uid'] = $uid;
    $investinfo['borrow_uid'] = $binfo['borrow_uid'];
    $investinfo['investor_capital'] = $money;
    $investinfo['is_auto'] = $_is_auto;
    $investinfo['add_time'] = time();
    $investinfo['deadline'] = time();
    $endTime = strtotime(date("Y-m-d", time()) . " 23:59:59");
    if ($binfo['repayment_type'] == 1) {
        $deadline_last = strtotime("+{$binfo['borrow_duration']} day", $endTime);
    } else {
        $deadline_last = strtotime("+{$binfo['borrow_duration']} month", $endTime);
    }
    $investinfo['deadline'] = $deadline_last;
    $investinfo['status'] = 6;
    
    
    //echo getfloatvalue($binfo['borrow_interest_rate'] * $investinfo['investor_capital'] * $binfo['borrow_duration'] / 100, 2)."<br>";
    //echo $fee_rate."<br>";
    //$investinfo['investor_interest']=getfloatvalue($binfo['borrow_interest_rate'] * $investinfo['investor_capital'] * $binfo['borrow_duration'] / 100, 2);
    //echo getfloatvalue($fee_rate * $investinfo['investor_interest'] / 100, 5)."<br>";
    //借款金额*年利率/12/30*天数
    //print_r($binfo);
    
    //echo ($binfo['borrow_interest_rate'] * $investinfo['investor_capital'] * $binfo['borrow_duration']/100/12/30*$binfo["borrow_duration"])."<br>";
    //echo ($investinfo['investor_capital']*$binfo['borrow_interest_rate']/100/12/30*$binfo["borrow_duration"]*$fee_rate)."<br>";
    $savedetail = array();
    switch ($binfo['repayment_type']) {
        case 1 :            
            $investinfo['investor_interest'] = getfloatvalue($binfo['borrow_interest_rate'] * $investinfo['investor_capital'] * $binfo['borrow_duration']/100/365, 2);
           // $investinfo['invest_fee'] = getfloatvalue($fee_rate * $investinfo['investor_interest'] / 100, 2);
            $investinfo['invest_fee'] = getfloatvalue($fee_rate * $investinfo['investor_interest'], 2);
            $invest_info_id = m("pro_borrow_investor")->add($investinfo);
            $i = 1;
            $investdetail['borrow_id'] = $borrow_id;
            $investdetail['invest_id'] = $invest_info_id;
            $investdetail['investor_uid'] = $uid;
            $investdetail['borrow_uid'] = $binfo['borrow_uid'];
            $investdetail['capital'] = $investinfo['investor_capital'];
            $investdetail['interest'] = $investinfo['investor_interest'];
            $investdetail['interest_fee'] = $investinfo['invest_fee'];
            $investdetail['deadline'] = $investinfo['deadline'];            
            $investdetail['status'] = 7;
            $investdetail['sort_order'] = $i;
            $investdetail['total'] =1;
            $savedetail[] = $investdetail;
            break;
        case 2 :
            $monthData['type'] = "all";
            $monthData['money'] = $investinfo['investor_capital'];
            $monthData['year_apr'] = $binfo['borrow_interest_rate'];
            $monthData['duration'] = $binfo['borrow_duration'];
            $repay_detail = equalmonth($monthData);
            $investinfo['investor_interest'] = $repay_detail['repayment_money'] - $investinfo['investor_capital'];
            $investinfo['invest_fee'] = getfloatvalue($fee_rate * $investinfo['investor_interest'], 2);
            $invest_info_id = m("pro_borrow_investor")->add($investinfo);
            $monthDataDetail['money'] = $investinfo['investor_capital'];
            $monthDataDetail['year_apr'] = $binfo['borrow_interest_rate'];
            $monthDataDetail['duration'] = $binfo['borrow_duration'];
            $repay_list = equalmonth($monthDataDetail);
            $i = 1;
            $last_money=0;
            $last_in_money=0;            
            foreach ($repay_list as $key => $v) {
                $last_money+=$v['interest'];
                $last_in_money+=$v['capital'];
                $investdetail['borrow_id'] = $borrow_id;
                $investdetail['invest_id'] = $invest_info_id;
                $investdetail['investor_uid'] = $uid;
                $investdetail['borrow_uid'] = $binfo['borrow_uid'];
                if(($key+1)==count($repay_list)){
                    $end_interest=bcsub($last_money,$investinfo['investor_interest'],2);
                    if($end_interest!=0){
                        $v['interest']=bcsub($v['interest'],$end_interest,2);
                    }
                }
                
                if(($key+1)==count($repay_list)){
                    $end_in_interest=bcsub($last_in_money,$investinfo['investor_capital'],2);
                    if($end_in_interest!=0){
                        //echo $end_in_interest;
                        $v['capital']=bcsub($v['capital'],$end_in_interest,2);
                    }
                }
                $investdetail['capital'] = $v['capital'];
                $investdetail['interest'] = $v['interest'];
                $investdetail['interest_fee'] = getfloatvalue($fee_rate * $v['interest'], 2);
                $investdetail['status'] = 7;
                $investdetail['sort_order'] = $i;
                $investdetail['total'] = $binfo['borrow_duration'];
                $deadline = 0;
                $deadline = strtotime("+{$i} month", $endTime);                
                $investdetail['deadline'] = $deadline;
                ++$i;
                $savedetail[] = $investdetail;
            }            
            break;
        case 3 :
            $monthData['month_times'] = $binfo['borrow_duration'];
            $monthData['account'] = $investinfo['investor_capital'];
            $monthData['year_apr'] = $binfo['borrow_interest_rate'];
            $monthData['type'] = "all";
            $repay_detail = equalseason($monthData);
            $investinfo['investor_interest'] = $repay_detail['repayment_money'] - $investinfo['investor_capital'];
            $investinfo['invest_fee'] = getfloatvalue($fee_rate * $investinfo['investor_interest'], 2);
            $invest_info_id = m("pro_borrow_investor")->add($investinfo);
            $monthDataDetail['month_times'] = $binfo['borrow_duration'];
            $monthDataDetail['account'] = $investinfo['investor_capital'];
            $monthDataDetail['year_apr'] = $binfo['borrow_interest_rate'];
            $repay_list = equalseason($monthDataDetail);
            $i = 1;
            $last_money=0;
            $last_in_money=0;
            foreach ($repay_list as $key => $v) {
                $last_money+=$v['interest'];
                $last_in_money+=$v['capital'];
                $investdetail['borrow_id'] = $borrow_id;
                $investdetail['invest_id'] = $invest_info_id;
                $investdetail['investor_uid'] = $uid;
                $investdetail['borrow_uid'] = $binfo['borrow_uid'];
                if(($key+1)==count($repay_list)){
                    $end_interest=bcsub($last_money,$investinfo['investor_interest'],2);
                    if($end_interest!=0){
                        $v['interest']=bcsub($v['interest'],$end_interest,2);
                    }
                }
                
                if(($key+1)==count($repay_list)){
                    $end_in_interest=bcsub($last_in_money,$investinfo['investor_capital'],2);
                    if($end_in_interest!=0){
                        //echo $end_in_interest;
                        $v['capital']=bcsub($v['capital'],$end_in_interest,2);
                    }
                }
                $investdetail['capital'] = $v['capital'];
                $investdetail['interest'] = $v['interest'];
                $investdetail['interest_fee'] = getfloatvalue($fee_rate * $v['interest'], 2);
                $investdetail['status'] = 7;
                $investdetail['sort_order'] = $i;
                $investdetail['total'] = $binfo['borrow_duration'];
                $deadline = 0;
                $deadline = strtotime("+{$i} month", $endTime);                
                $investdetail['deadline'] = $deadline;
                ++$i;
                $savedetail[] = $investdetail;
            }
            break;
        case 4 :
            $monthData['month_times'] = $binfo['borrow_duration'];
            $monthData['account'] = $investinfo['investor_capital'];
            $monthData['year_apr'] = $binfo['borrow_interest_rate'];
            $monthData['type'] = "all";
            $repay_detail = equalendmonth($monthData);
            $investinfo['investor_interest'] = $repay_detail['repayment_account'] - $investinfo['investor_capital'];
            $investinfo['invest_fee'] = getfloatvalue($fee_rate * $investinfo['investor_interest'], 2);
            $invest_info_id = m("pro_borrow_investor")->add($investinfo);
            $monthDataDetail['month_times'] = $binfo['borrow_duration'];
            $monthDataDetail['account'] = $investinfo['investor_capital'];
            $monthDataDetail['year_apr'] = $binfo['borrow_interest_rate'];
            $repay_list = equalendmonth($monthDataDetail);
            $i = 1;            
            $last_money=0;
            $last_in_money=0;
            foreach ($repay_list as $key => $v) {
                $last_money+=$v['interest'];
                $last_in_money+=$v['capital'];
                $investdetail['borrow_id'] = $borrow_id;
                $investdetail['invest_id'] = $invest_info_id;
                $investdetail['investor_uid'] = $uid;
                $investdetail['borrow_uid'] = $binfo['borrow_uid'];
                if(($key+1)==count($repay_list)){
                    $end_interest=bcsub($last_money,$investinfo['investor_interest'],2);
                    if($end_interest!=0){
                        $v['interest']=bcsub($v['interest'],$end_interest,2);
                    }
                }
                
                if(($key+1)==count($repay_list)){
                    $end_in_interest=bcsub($last_in_money,$investinfo['investor_capital'],2);
                    if($end_in_interest!=0){
                        //echo $end_in_interest;
                        $v['capital']=bcsub($v['capital'],$end_in_interest,2);
                    }
                }
                $investdetail['capital'] = $v['capital'];
                $investdetail['interest'] = $v['interest'];
                $investdetail['interest_fee'] = getfloatvalue($fee_rate * $v['interest'], 2);
                $investdetail['status'] = 7;
                $investdetail['sort_order'] = $i;
                $investdetail['total'] = $binfo['borrow_duration'];
                $deadline = 0;
                $deadline = strtotime("+{$i} month", $endTime);                
                $investdetail['deadline'] = $deadline;
                ++$i;
                $savedetail[] = $investdetail;
            }
            break;
        case 5 :
            $monthData['month_times'] = $binfo['borrow_duration'];
            $monthData['account'] = $investinfo['investor_capital'];
            $monthData['year_apr'] = $binfo['borrow_interest_rate'];
            $monthData['type'] = "all";
            $repay_detail = EqualEndMonthOnly($monthData);
            $investinfo['investor_interest'] = $repay_detail['interest'];
            $investinfo['invest_fee'] = getfloatvalue($fee_rate * $repay_detail['interest'], 2);
            $invest_info_id = m("pro_borrow_investor")->add($investinfo);
            $investdetail['borrow_id'] = $borrow_id;
            $investdetail['invest_id'] = $invest_info_id;
            $investdetail['investor_uid'] = $uid;
            $investdetail['borrow_uid'] = $binfo['borrow_uid'];
            $investdetail['capital'] = $money;
            $investdetail['interest'] = $repay_detail['interest'];
            $investdetail['interest_fee'] = getfloatvalue($fee_rate * $repay_detail['interest'], 2);
            $investdetail['status'] = 7;
            $investdetail['sort_order'] = 1;
            $investdetail['total'] = 1;
            $investdetail['deadline'] = $investinfo['deadline']; 
            $savedetail[] = $investdetail;
            break;
        //半年分期还款
        case 6 :
            $monthData['month_times'] = $binfo['borrow_duration'];
            $monthData['account'] = $investinfo['investor_capital'];
            $monthData['year_apr'] = $binfo['borrow_interest_rate'];
            $monthData['type'] = "all";
            $repay_detail = EqualYearHalf($monthData);
            $investinfo['investor_interest'] = $repay_detail['repayment_money'] - $investinfo['investor_capital'];
            $investinfo['invest_fee'] = getfloatvalue($fee_rate * $investinfo['investor_interest'], 2);
            $invest_info_id = m("pro_borrow_investor")->add($investinfo);
            $monthDataDetail['month_times'] = $binfo['borrow_duration'];
            $monthDataDetail['account'] = $investinfo['investor_capital'];
            $monthDataDetail['year_apr'] = $binfo['borrow_interest_rate'];
            $repay_list = EqualYearHalf($monthDataDetail);
            $i = 1;
            $last_money=0;
            $last_in_money=0;
            foreach ($repay_list as $key => $v) {
                $last_money+=$v['interest'];
                $last_in_money+=$v['capital'];
                $investdetail['borrow_id'] = $borrow_id;
                $investdetail['invest_id'] = $invest_info_id;
                $investdetail['investor_uid'] = $uid;
                $investdetail['borrow_uid'] = $binfo['borrow_uid'];
                if(($key+1)==count($repay_list)){
                    $end_interest=bcsub($last_money,$investinfo['investor_interest'],2);
                    if($end_interest!=0){
                        $v['interest']=bcsub($v['interest'],$end_interest,2);
                    }
                }
                
                if(($key+1)==count($repay_list)){
                    $end_in_interest=bcsub($last_in_money,$investinfo['investor_capital'],2);
                    if($end_in_interest!=0){
                        //echo $end_in_interest;
                        $v['capital']=bcsub($v['capital'],$end_in_interest,2);
                    }
                }
                $investdetail['capital'] = $v['capital'];
                $investdetail['interest'] = $v['interest'];
                $investdetail['interest_fee'] = getfloatvalue($fee_rate * $v['interest'], 2);
                $investdetail['status'] = 7;
                $investdetail['sort_order'] = $i;
                $investdetail['total'] = $binfo['borrow_duration'];
                $deadline = 0;
                $deadline = strtotime("+{$i} month", $endTime);                
                $investdetail['deadline'] = $deadline;
                ++$i;
                $savedetail[] = $investdetail;
            }
            break;
        //一年分期还款
        case 7 :
            $monthData['month_times'] = $binfo['borrow_duration'];
            $monthData['account'] = $investinfo['investor_capital'];
            $monthData['year_apr'] = $binfo['borrow_interest_rate'];
            $monthData['type'] = "all";
            $repay_detail = EqualYear($monthData);
            $investinfo['investor_interest'] = $repay_detail['repayment_money'] - $investinfo['investor_capital'];
            $investinfo['invest_fee'] = getfloatvalue($fee_rate * $investinfo['investor_interest'], 2);
            $invest_info_id = m("pro_borrow_investor")->add($investinfo);
            $monthDataDetail['month_times'] = $binfo['borrow_duration'];
            $monthDataDetail['account'] = $investinfo['investor_capital'];
            $monthDataDetail['year_apr'] = $binfo['borrow_interest_rate'];
            $repay_list = EqualYear($monthDataDetail);
            $i = 1;
            $last_money=0;
            $last_in_money=0;
            foreach ($repay_list as $key => $v) {
                $last_money+=$v['interest'];
                $last_in_money+=$v['capital'];
                $investdetail['borrow_id'] = $borrow_id;
                $investdetail['invest_id'] = $invest_info_id;
                $investdetail['investor_uid'] = $uid;
                $investdetail['borrow_uid'] = $binfo['borrow_uid'];
                if(($key+1)==count($repay_list)){
                    $end_interest=bcsub($last_money,$investinfo['investor_interest'],2);
                    if($end_interest!=0){
                        $v['interest']=bcsub($v['interest'],$end_interest,2);
                    }
                }
                
                if(($key+1)==count($repay_list)){
                    $end_in_interest=bcsub($last_in_money,$investinfo['investor_capital'],2);
                    if($end_in_interest!=0){
                        //echo $end_in_interest;
                        $v['capital']=bcsub($v['capital'],$end_in_interest,2);
                    }
                }
                $investdetail['capital'] = $v['capital'];
                $investdetail['interest'] = $v['interest'];
                $investdetail['interest_fee'] = getfloatvalue($fee_rate * $v['interest'], 2);
                $investdetail['status'] = 7;
                $investdetail['sort_order'] = $i;
                $investdetail['total'] = $binfo['borrow_duration'];
                $deadline = 0;
                $deadline = strtotime("+{$i} month", $endTime);                
                $investdetail['deadline'] = $deadline;
                ++$i;
                $savedetail[] = $investdetail;
            }
            break;       
    }
    
    
    //$binfo = m("borrow_info")->field("borrow_uid,borrow_money,borrow_name,borrow_interest_rate,borrow_type,borrow_duration,repayment_type,has_borrow")->find($borrow_id);
    
    $havemoney = $binfo['has_borrow'];
    $has=bcsub($binfo['borrow_money'], $havemoney,2);
    $hasb=bcsub($has, $money,2);
    
    if ($hasb < 0) {
        $investMoney->rollback();
        return "对不起，此标还差" . (bcsub($binfo['borrow_money'] , $havemoney,2)) . "元满标，您最多投标" . (bcsub($binfo['borrow_money'] , $havemoney,2)) . "元";
    }
    
    if($has==0){
        $investMoney->rollback();
        return "此标已经投满";
    }
    
    if($havemoney==$binfo['borrow_money']){
        $investMoney->rollback();
        return "此标已经投满";    
    }
    
    $capital = M('pro_investor_detail')->where("borrow_id={$borrow_id}")->sum('capital');
    if($capital>$binfo["borrow_money"]){
        $investMoney->rollback();
        return "投资金额有误";
    }
    
    
    $capital = M('pro_borrow_investor')->where("borrow_id={$borrow_id}")->sum('investor_capital');
    
    if($capital>$binfo["borrow_money"]){
        $investMoney->rollback();
        return "投资金额有误";
    }
    
    $invest_defail_id = m("pro_investor_detail")->addAll($savedetail);
    if ($invest_defail_id && $invest_info_id) {
        
        
        $res = memberMoneyLog($uid, 6, 0 - $money, "对{$borrow_name}进行投标", $binfo['borrow_uid'],"",false);
        memberMoneyLog($uid, 15, $investinfo['investor_capital'], "{$borrow_name}系统确认，冻结本金成为待收金额", $binfo['borrow_uid'],false);
        memberMoneyLog($uid, 28, $investinfo['investor_interest'], "{$borrow_name}系统确认，应收利息成为待收金额", $binfo['borrow_uid'],false);
            
        
        //续投的奖励预奖励
        if($binfo["borrow_type"]!=3 and $binfo["repayment_type"]!=1){
            if(false &&$money>=1000){
              if(back_money($uid)>=1000){
                  $today_reward = explode("|",$datag['today_reward']);
                  $i_back_money=back_money($uid);
                  if($i_back_money>$money)$i_back_money=$money;
                  if($binfo["borrow_duration"]==1){
                    $reward_money=getfloatvalue($i_back_money*$today_reward[0]/1000,2);
                  }elseif($binfo["borrow_duration"]==2){
                     $reward_money=getfloatvalue($i_back_money*$today_reward[1]/1000,2);
                  }else{
                     $reward_money=getfloatvalue($i_back_money*$today_reward[2]/1000,2); 
                  }
                  $save_reward['borrow_id'] = $borrow_id;
                  $save_reward['reward_uid'] = $uid;
                  $save_reward['invest_money'] = $i_back_money;//续投有效金额
                  $save_reward['reward_money'] = $reward_money;//续投奖励
                  $save_reward['reward_status'] = 0;
                  $save_reward['add_time'] = time();
                  $save_reward['add_ip'] = get_client_ip();
                  $newidxt = M("today_reward")->add($save_reward);
                  m()->execute("update lzh_members set i_back_money=i_back_money+".$i_back_money." where id=".$uid);
                 // $result=membermoneylog($uid,33,$reward_money,"({$borrow_name})续投的奖励预奖励",0,"@网站管理员@");
              }
            }
        }
        
        //续投的奖励预奖励
        
        //$binfo = m("borrow_info")->field("borrow_uid,borrow_money,borrow_name,borrow_interest_rate,borrow_type,borrow_duration,repayment_type,has_borrow")->find($borrow_id);
        $capital = M('pro_investor_detail')->where("borrow_id={$borrow_id}")->sum('capital');
        $havemoney = $binfo['has_borrow'];
        if($capital>$binfo['borrow_money']){
            $investMoney->rollback();
            return "投资金额有误";    
        }
        //$upborrowsql = "update `{$pre}borrow_info` set ";
        //$upborrowsql .= "`has_borrow`=" . ($capital) . ",`borrow_times`=`borrow_times`+1";
        //$havemoney = $binfo['has_borrow'];
        $upborrowsql = "update `{$pre}pro_borrow_info` set ";
        $upborrowsql .= "`has_borrow`=" . ($havemoney + $money) . ",`borrow_times`=`borrow_times`+1";
        $upborrowsql .= " WHERE `id`={$borrow_id}";
        $upborrow_res = m()->execute($upborrowsql);
        
        if ($havemoney + $money == $binfo['borrow_money']) {
            borrowFull_pro($borrow_id, $binfo['borrow_type']);
        }
        
        if (!$res) {
            $investMoney->rollback();
            m("pro_investor_detail")->where("invest_id={$invest_info_id}")->delete();
            m("pro_borrow_investor")->where("id={$invest_info_id}")->delete();
            //$capital = M('investor_detail')->where("borrow_id={$borrow_id}")->sum('capital');
            //$upborrowsql = "update `{$pre}borrow_info` set ";
            //$upborrowsql .= "`has_borrow`=" . $capital . ",`borrow_times`=`borrow_times`-1";
            $upborrowsql .= "`has_borrow`=" . $havemoney . ",`borrow_times`=`borrow_times`-1";
            $upborrowsql .= " WHERE `id`={$borrow_id}";
            $upborrowsql .= " WHERE `id`={$borrow_id}";
            $upborrow_res = m()->execute($upborrowsql);
            $done = false;
            return "投资金额有误";    
        } else {
            $done = true;
        }
        
        $binfo = m("pro_borrow_info")->field("borrow_uid,borrow_money,borrow_name,borrow_interest_rate,borrow_type,borrow_duration,repayment_type,has_borrow")->find($borrow_id);
        $capital = M('pro_borrow_investor')->where("borrow_id={$borrow_id}")->sum('investor_capital');
        $havemoney = $binfo['has_borrow'];
        if($capital>$binfo['borrow_money']){
            $investMoney->rollback();
            return "投资金额有误";    
        }
        
        $sql="select t.borrow_money,t.id from lzh_borrow_info as t where t.borrow_money<(select sum(capital) from lzh_pro_investor_detail where borrow_id={$borrow_id}) and t.id={$borrow_id}";
        
        $check_money=m()->query($sql);
        
        if($check_money){
            $investMoney->rollback();
            return "投资金额有误";
        }else{
            $investMoney->commit();
        }
        
    } else {
        $investMoney->rollback();
    }
    return $done;
    
    }else{
        return "系统繁忙请重试";   
    }
}
function borrowFull_pro($borrow_id, $btype = 0)
{       
    $saveborrow['pause'] = 0;
    $saveborrow['borrow_status'] = 6;
    $saveborrow['full_time'] = time();
    $upborrow_res = m("pro_borrow_info")->where("id={$borrow_id}")->save($saveborrow);
    borrowApproved_pro($borrow_id);
}
function borrowApproved_pro($borrow_id)
{   
    $pre = c("DB_PREFIX");
    $done = false;
    $borrowInvestor = d("pro_borrow_investor");
    $borrowInvestor->startTrans();
    $binfo = m("pro_borrow_info")->field("borrow_type,reward_type,reward_num,borrow_name,borrow_fee,borrow_money,borrow_uid,borrow_duration,repayment_type")->find($borrow_id);
    $borrow_name=$binfo["borrow_name"];
    $investorList = $borrowInvestor->field("id,investor_uid,investor_capital,investor_interest")->where("borrow_id={$borrow_id}")->select();
    $endTime = strtotime(date("Y-m-d", time()) . " 23:59:59");
    if ($binfo['repayment_type'] == 1) {
        $deadline_last = strtotime("+{$binfo['borrow_duration']} day", $endTime);
    } else {
        $deadline_last = strtotime("+{$binfo['borrow_duration']} month", $endTime);
    }
    if ($binfo['repayment_type'] == 5) {
        $deadline_last = strtotime("+{$binfo['borrow_duration']} day", $endTime);
    }
    $_investor_num = count($investorList);
    $glodata = get_global_setting();
    $credit_investor = $glodata['credit_investor']?$glodata['credit_investor']:"100";//投资人奖励规则
    foreach ($investorList as $key => $v) {
        //print_r($v);
        //echo $v['investor_uid']."<br>";
        //echo getfloatvalue($v['investor_capital']);
        membercreditslog($v['investor_uid'],12,$v['investor_capital']/$credit_investor, "成功投标{$borrow_id}号众筹项目，奖励积分");
        //membercreditslog($v['investor_uid'],12,getfloatvalue($v['investor_capital']), "成功投标{$borrow_name}，奖励积分");
        //$credits_result = membercreditslog($binfo['borrow_uid'], 6, $credits_money * 2, $credits_info););
        if (0 < $binfo['reward_type']) {
            if ($binfo['reward_type'] == 1) {
                $_reward_money = getfloatvalue($v['investor_capital'] * $binfo['reward_num'] / 100, 2);
            } else if ($binfo['reward_type'] == 2) {
                $_reward_money = getfloatvalue($binfo['reward_num'] / $_investor_num, 2);
            }
        }
        
        $investorList[$key]['reward_money'] = floatval($_reward_money);
        mtip("chk14", $v['investor_uid'], $borrow_id);
        $saveinfo = array();
        $saveinfo['id'] = $v['id'];
        $saveinfo['reward_money'] = floatval($_reward_money);
        //$saveinfo['deadline'] = $deadline_last;
        $saveinfo['status'] = 6;
        $upsummary_res = $borrowInvestor->save($saveinfo);
    }
       $upborrow_res = m()->execute("update `{$pre}pro_borrow_info` set `deadline`={$deadline_last} WHERE `id`={$borrow_id}");
        
        $done = true;
        
        $_P_fee = get_global_setting();
        $_borraccount = memberMoneyLog($binfo['borrow_uid'], 17, $binfo['borrow_money'], "{$borrow_name}系统确认，众筹金额入帐");
        if (!$_borraccount) {
            return false;
        }
        $_investor_num = count($investorList);
        $_remoney_do = true;
        foreach ($investorList as $v) {
            
        }
            
        if (!$_remoney_do) {
        $borrowInvestor->rollback();
        return false;
    } else {
        $borrowInvestor->commit();
    }
    return $done;
}
function getTenderList_pro($map,$size,$limit=10){
    $pre = C('DB_PREFIX');
    $Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";
    //if(empty($map['i.investor_uid'])) return;
    if(empty($map['investor_uid'])) return;    
    if($size){
        //分页处理
        import("ORG.Util.Page");
        $count = M('pro_borrow_investor i')->where($map)->count('i.id');
        $p = new Page($count, $size);
        $page = $p->show();
        $Lsql = "{$p->firstRow},{$p->listRows}";
        //分页处理
    }else{
        $page="";
        $Lsql="{$parm['limit']}";
    }
    
    $type_arr =C('BORROW_TYPE');
    
    /*$field = "i.*,i.add_time as invest_time,m.user_name as borrow_user,b.borrow_duration,b.has_pay,b.borrow_interest_rate,b.add_time as borrow_time,b.borrow_money,b.borrow_name,m.credits,b.repayment_type,b.borrow_type";
    
    $list = M('borrow_investor i')->field($field)->where($map)->join("{$pre}borrow_info b ON b.id=i.borrow_id")->join("{$pre}members m ON m.id=b.borrow_uid")->order('i.deadline ASC')->limit($Lsql)->select();*/
    
    /////////////////////////视图查询 fan 20130522//////////////////////////////////////////
    
    if($map['add_time']!=""){
        $map['invest_time']=$map['add_time'];
        unset($map['add_time']);    
    }
    //$list=$Model->field(true)->where($map)->order('times ASC')->group('id')->limit($Lsql)->select();
     $list = M("pro_borrow_investor t1")->field('t1.id,t1.borrow_id,t1.investor_capital,t1.investor_interest,t1.add_time,t1.receive_capital,t1.receive_interest,t2.borrow_uid,t2.borrow_name,t2.borrow_duration,t2.borrow_interest_rate,t2.total,t1.deadline,t3.user_name')->join($pre.'pro_borrow_info t2 on t1.borrow_id=t2.id')->join($pre.'members as t3 on t2.borrow_uid=t3.id ')->where($map)->limit($Lsql)->select();
    //echo $Model->getlastsql();
    ////////////////////////视图查询 fan 20130522//////////////////////////////////////////
    $row=array();
    $row['list'] = $list;
    $row['page'] = $page;
    if($map['invest_time']!=""){
        $map['add_time']=$map['invest_time'];
        unset($map['invest_time']); 
    }
    $row['total_money'] = M('pro_borrow_investor i')->where($map)->sum('investor_capital');
    $row['lixi'] = M('pro_borrow_investor i')->where($map)->sum('investor_interest');
    $row['total_num'] = $count;
    return $row;
}
function getMemberProductScan($uid)
{
    $field = "borrow_status,count(id) as num,sum(borrow_money) as money,sum(repayment_money) as repayment_money";
    $borrowNum = m("pro_borrow_info")->field($field)->where("borrow_uid = {$uid}")->group("borrow_status")->select();
    foreach ($borrowNum as $v) {
        $borrowCount[$v['borrow_status']] = $v;
    }
    $field = "status,sort_order,borrow_id,sum(capital) as capital,sum(interest) as interest";
    $repaymentNum = m("pro_investor_detail")->field($field)->where("borrow_uid = {$uid}")->group("sort_order,borrow_id")->select();
    foreach ($repaymentNum as $v) {
        $repaymentStatus[$v['status']]['capital'] += $v['capital'];
        $repaymentStatus[$v['status']]['interest'] += $v['interest'];
        ++$repaymentStatus[$v['status']]['num'];
    }
    $field = "status,count(id) as num,sum(investor_capital) as investor_capital,sum(reward_money) as reward_money,sum(investor_interest) as investor_interest,sum(receive_capital) as receive_capital,sum(receive_interest) as receive_interest";
    $investNum = m("pro_borrow_investor")->field($field)->where("investor_uid = {$uid}")->group("status")->select();
    $_reward_money = 0;
    foreach ($investNum as $v) {
        $investStatus[$v['status']] = $v;
        $_reward_money += floatval($v['reward_money']);
    }
    $field = "borrow_id,sort_order,sum(`capital`) as capital,count(id) as num";
    //$expiredNum = m("investor_detail")->field($field)->where("`repayment_time`=0 and `deadline`<" . time() . " and borrow_uid={$uid}")->join()->group("borrow_id,sort_order")->select();
    $pre = C('DB_PREFIX');
    $sql = "select b.borrow_id,b.sort_order,sum(`b.capital`) as capital,count(b.id) as num from {$pre}pro_investor_detail d left join {$pre}pro_borrow_info b ON b.id=d.borrow_id where d.borrow_uid ={$uid} AND b.borrow_status=6 AND d.deadline<".time()." AND d.repayment_time=0 group by d.sort_order,d.borrow_id order by d.borrow_id,d.sort_order limit";
    $expiredNum = M()->query($sql);
    
    $_expired_money = 0;
    foreach ($expiredNum as $v) {
        $expiredStatus[$v['borrow_id']][$v['sort_order']] = $v;
        $_expired_money += floatval($v['capital']);
    }
    //print_r($expiredNum);
    //$Model = new Model();
    //count($expiredNum);
    
    $rowtj['expiredMoney'] = getfloatvalue($_expired_money, 2);
    $rowtj['expiredNum'] = ($_expired_money=="0"?0:count($expiredNum));
    //echo $rowtj['expiredNum'];
    $field = "borrow_id,sort_order,sum(`capital`) as capital,count(id) as num";
    $expiredInvestNum = m("pro_investor_detail")->field($field)->where("`repayment_time`=0 and `deadline`<" . time() . " and investor_uid={$uid} AND status <> 0")->group("borrow_id,sort_order")->select();
    $_expired_invest_money = 0;
    foreach ($expiredInvestNum as $v) {
        $expiredInvestStatus[$v['borrow_id']][$v['sort_order']] = $v;
        $_expired_invest_money += floatval($v['capital']);
    }
    //print_r($borrowCount);
    //exit;
    $rowtj['expiredInvestMoney'] = getfloatvalue($_expired_invest_money, 2);
    $rowtj['expiredInvestNum'] = count($expiredInvestNum);
    $rowtj['jkze'] = getfloatvalue(floatval($borrowCount[6]['money'] + $borrowCount[7]['money'] + $borrowCount[8]['money'] + $borrowCount[9]['money']), 2);
    $rowtj['yhze'] = getfloatvalue(floatval($borrowCount[6]['repayment_money'] + $borrowCount[7]['repayment_money'] + $borrowCount[8]['repayment_money'] + $borrowCount[9]['repayment_money']), 2);
    $rowtj['dhze'] = getfloatvalue($rowtj['jkze'] - $rowtj['yhze'], 2);
    $rowtj['jcze'] = getfloatvalue(floatval($investStatus[4]['investor_capital'] + $investStatus[5]['investor_capital'] + $investStatus[6]['investor_capital']), 2);
    $rowtj['ysze'] = getfloatvalue(floatval($investStatus[4]['receive_capital'] + $investStatus[5]['receive_capital'] + $investStatus[6]['receive_capital']), 2);
    $rowtj['dsze'] = getfloatvalue($rowtj['jcze'] - $rowtj['ysze'], 2);
    $rowtj['fz'] = getfloatvalue($rowtj['jcze'] - $rowtj['jkze'], 2);
    $rowtj['dqrtb'] = getfloatvalue($investStatus[1]['investor_capital'], 2);
    $rowtj['earnInterest'] = getfloatvalue(floatval($investStatus[5]['receive_interest'] + $investStatus[6]['receive_interest']), 2);
    $rowtj['payInterest'] = getfloatvalue(floatval($repaymentStatus[1]['interest'] + $repaymentStatus[2]['interest'] + $repaymentStatus[3]['interest'] + $repaymentStatus[3]['interest']), 2);
    $rowtj['willgetInterest'] = getfloatvalue(floatval($investStatus[4]['investor_interest'] - $investStatus[4]['receive_interest']), 2);
    $rowtj['willpayInterest'] = getfloatvalue(floatval($repaymentStatus[7]['interest']), 2);
    $rowtj['borrowOut'] = getfloatvalue(floatval($investStatus[4]['investor_capital'] + $investStatus[5]['investor_capital'] + $investStatus[6]['investor_capital']), 2);
    $rowtj['borrowIn'] = getfloatvalue(floatval($borrowCount[6]['money'] + $borrowCount[7]['money'] + $borrowCount[8]['money'] + $borrowCount[9]['money']), 2);
    $rowtj['jkcgcs'] = $borrowCount[6]['num'] + $borrowCount[7]['num'] + $borrowCount[8]['num'] + $borrowCount[9]['num'];
    $rowtj['jkallnum'] = $borrowCount[6]['num'] + $borrowCount[7]['num'] + $borrowCount[8]['num'] + $borrowCount[9]['num']+ $rowtj['expiredNum'];
    $rowtj['tbjl'] = $_reward_money;
    $row = array();
    $row['borrow'] = $borrowCount;
    $row['repayment'] = $repaymentStatus;
    $row['invest'] = $investStatus;
    $row['tj'] = $rowtj;
    return $row;
}
 ?>
