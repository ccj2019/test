<?php
//error_reporting("E_ALL & ~E_NOTICE");
/**
 * @funciton reg 注册
 * @funciton login 登录
 */
class ApiAction extends HCommonAction {
    
    var $domainUrl = 'http://www.tiannongjinrong.com';
    var $glo = '';
    var $pre = '';
    var $payConfig = NULL;
    var $return_url = "";
    var $notice_url = "";
    var $member_url = ""; //支付的
    var $merchant_id = '200584000018725';
    var $user_name = '20058400001872504';
    var $user_pass = '111111';

    public function __construct()
    {
        parent::__construct();
        // ini_set("display_statuss", "On");
        // error_reporting(E_ALL | E_STRICT);
        $this->pre = C('DB_PREFIX');
        header("Content-Type:text/html;charset=utf-8");
        $datag = get_global_setting();
        $this->glo = $datag;//供PHP里面使用
        $this->uid = intval(@$_REQUEST["uid"]);
    }   
    /**
    * 首页
    */
    public function index()
    {
       foreach (get_ad(29) as $key => $val) {
           $ad[$key]["picurl"]   = $this->domainUrl . '/' . $val["img"];    //图片路径
           $ad[$key]["linkurl"]   = $val["url"];    //链接地址
       }
       $jsons["banner"]  = is_array($ad) ? $ad : array();
       $news = getArtList(11,10);
       foreach ($news as $k=>$v) {
           $new[] = array(
               "aid"=> $v['id'],
               "title" => $v['title']
           );
       }
       $searchMaps['borrow_status']= array("in",'1');
       // $searchMaps['is_experience']= 0;
       $borrowList = M("borrow_info")->field("id as bid,borrow_money,start_time,borrow_interest_rate,borrow_duration,borrow_name")->where($searchMaps)->order('id desc')->find();
       $jsons["artileList"] = is_array($new) ? $new : array();
       if($borrowList){
            $borrowList['start_time'] = $borrowList['start_time']-time();
       }else{
            $searchMap['borrow_status']= array("in",'2');
            $borrowList = M("borrow_info")->field("id as bid,borrow_money,start_time,borrow_interest_rate,borrow_duration,borrow_name")->where($searchMap)->order('id desc')->find();
            $borrowList['start_time'] = 0;
       }
       $jsons["borrowList"] = $borrowList?$borrowList:array();
       $jsons["status"]   = "1";
       outJson($jsons);
    }
    /*
     * 投资列表
    */ 
    public function invest()
    {
        $p                          = intval($_REQUEST['p']) ? intval($_REQUEST['p']) : "1";
        $searchMap                  = array();
        $searchMap['borrow_status'] = array("in", '2,4,6,7');
        // $searchMap['is_experience']= 0;
        $searchMap        = array_merge($searchMap, $this->investFilterSearch($_REQUEST));
        $parm             = array();
        $parm['map']      = $searchMap;
        $parm['pagesize'] = 10;
        $parm['orderby']  = "b.borrow_status ASC,b.id DESC";
        $parm['map']      = $searchMap;
        $listBorrow       = getBorrowList($parm);
        $list             = $listBorrow['list'];
        foreach($lists as $key=>$val){
            $fieldx = "bi.investor_capital,bi.add_time,m.user_name,bi.is_auto,bi.investor_interest";
            $investinfo = M("borrow_investor bi")->field($fieldx)->join("lzh_members m ON bi.investor_uid = m.id")->where("bi.borrow_id={$val['id']}")->order("bi.id DESC")->select();
            $list[] = array(
            'bid' => $val['id'],
            'borrow_name' => $val['borrow_name'],   //项目名称
            'borrow_money' => getMoneyFormt($val['borrow_money']),
            'borrow_times' => $val['borrow_times'],
            'borrow_duration' => $val['borrow_duration'],   //项目期限
            'borrow_interest_rate' => $val['borrow_interest_rate'], //项目回收溢价
            'progress' => $val['progress'], //项目进度
            'borrow_num' => count($investinfo),
            'borrow_status' => $val['borrow_status_cn'],               
            );
        }
        $jsons['list']         = is_array($list) ? $list : array();
        $jsons['page']         = $listBorrow['_page'];
        $jsons["status"]       = "1";
        outJson($jsons);
    }    
    private function investFilterSearch($searchArr = array(),$type = '')
    {
        $searchMap                                                      = array();
        !empty($searchArr['borrow_type']) and $searchMap['borrow_type'] = intval($searchArr['borrow_type']);
        if (!empty($searchArr['borrow_interest_rate'])) {
            switch ($searchArr['borrow_interest_rate']) {
                case '1':
                    $searchMap['b.borrow_interest_rate'] = array('between', array('0', '10'));
                    break;
                case '2':
                    $searchMap['b.borrow_interest_rate'] = array('between', array('10', '15'));
                    break;
                case '3':
                    $searchMap['b.borrow_interest_rate'] = array('between', array('15', '20'));
                    break;
                case '4':
                    $searchMap['b.borrow_interest_rate'] = array('between', array('20', '25'));
                    break;
                case '5':
                    $searchMap['b.borrow_interest_rate'] = array('between', array('25', '100'));
                    break;
                default:
                    break;
            }
        }
        if (!empty($searchArr['borrow_duration'])) {
            switch ($searchArr['borrow_duration']) {
                case '1':
                    $searchMap['b.borrow_duration'] = array('between', array('1', '3'));
                    break;
                case '2':
                    $searchMap['b.borrow_duration'] = array('between', array('4', '6'));
                    break;
                case '3':
                    $searchMap['b.borrow_duration'] = array('between', array('7', '9'));
                    break;
                case '4':
                    $searchMap['b.borrow_duration'] = array('between', array('10', '12'));
                    break;
                case '5':
                    $searchMap['b.borrow_duration'] = array('between', array('13', '24'));
                    break;
                case '6':
                    $searchMap['b.borrow_duration'] = array("gt", '24');
                    break;
                default:
                    break;
            }
        }
        if (!empty($searchArr['borrow_money'])) {
            $moneyCondition = array();
            switch ($searchArr['borrow_money']) {
                case '1':
                    $moneyCondition = array('between', array('0', '10000'));
                    break;
                case '2':
                    $moneyCondition = array('between', array('10000', '50000'));
                    break;
                case '3':
                    $moneyCondition = array('between', array('50000', '100000'));
                    break;
                case '4':
                    $moneyCondition = array('between', array('100000', '200000'));
                    break;                
                case '5':
                    $moneyCondition = array("gt", '200000');
                    break;
                default:
                    break;
            }
            if($type == 'bond') {
                $searchMap['d.money'] = $moneyCondition;
            }else{
                $searchMap['b.borrow_money'] = $moneyCondition;
            }
            
        }
        !empty($searchArr['borrow_status']) and $searchMap['borrow_status'] = intval($searchArr['borrow_status']);
        return $searchMap;

    } 
    /*
     * 项目详情
    */
    public function invest_detail()
    {
        $id              = intval($_REQUEST['id']) ? intval($_REQUEST['id']) : die;
        $show_type       = isset($_REQUEST['show_type']) ? $_REQUEST['show_type'] : "";
        $uid             = intval($_REQUEST['uid']) ? intval($_REQUEST['uid']) : '';
        $pre = C('DB_PREFIX');
        $borrowinfo= M('borrow_info')->field('borrow_times,borrow_duration,id as bid,borrow_img,borrow_info,borrow_name,borrow_sccj as borrow_con,borrow_money,has_borrow,borrow_max,borrow_min,borrow_uid,borrow_status,start_time,collect_day,borrow_interest_rate,borrow_feasibility,budget_revenue')->where('borrow_status in(1,2,4,6,7) and id='.$id)->find();

        if (is_array($borrowinfo)) {
            switch ($show_type) {
                case 'borrow_info':
                    //项目介绍
                    outWeb($borrowinfo['borrow_info']);
                    break;
                case 'agreement':
                    $article_category = M('article_category')->where('id=10')->find();
                    //项目介绍
                    outWeb($article_category['type_content']);
                    break;
                case 'wind_control':
                    //风控及退出
                    outWeb($borrowinfo['borrow_feasibility']);
                    break;
                case 'bonus_income':
                    //分红及收益
                    outWeb($borrowinfo['budget_revenue']);
                    break;
                case 'invest_log':
                    //投资记录
                    $fieldx = "bi.investor_capital,bi.add_time,m.user_name,bi.investor_way,bi.member_interest_rate_id,bi.bonus_id,bi.is_experience";
                    $investinfo = M("borrow_investor bi")->field($fieldx)
                        ->join("{$pre}members m ON bi.investor_uid = m.id")
                        ->where("bi.borrow_id={$id}")
                        ->order("bi.id DESC")
                        ->select();
                    $invlog = array();
                    foreach ($investinfo as $key => $value) {
                        $invlog[$key]['user_name'] = hidecard($value['user_name'],5);
                        $invlog[$key]['investor_capital'] = $value['investor_capital'];
                        $invlog[$key]['inv_time'] = date('Y-m-d H:i:s',$value['add_time']);
                        if($value['member_interest_rate_id'] != 0){
                            $invlog[$key]['jx'] = intestrate($value['member_interest_rate_id'])?intestrate($value['member_interest_rate_id']):0;
                        }else{
                            $invlog[$key]['jx'] = 0;
                        }
                        if($value['bonus_id'] != 0){
                           $invlog[$key]['hb'] = bounsmoney($vi['bonus_id'])?bounsmoney($vi['bonus_id']):0;
                        }else{
                            $invlog[$key]['hb'] = 0;
                        }
                    }
                    $jsons["investlog"] = $invlog;
                    break;
                case 'detail':
                    //项目介绍详情
                        //预计万元回报
                        // 项目厂家
                        // 项目发起人
                        // 项目支持人数
                    $borrowinfo['borrow_user'] = (string)(get_user_info($borrowinfo['borrow_uid']));
                    $fieldx = "bi.investor_capital,bi.add_time,m.user_name,bi.is_auto,bi.investor_interest,bi.investor_way,bi.member_interest_rate_id,bi.bonus_id,bi.is_experience";
                    $investinfo = M("borrow_investor bi")->field($fieldx)->join("{$pre}members m ON bi.investor_uid = m.id")->where("bi.borrow_id={$id}")->order("bi.id DESC")->select();
                    $borrowinfo['count']=count($investinfo);
                    $project = arrayFilterValByKey($borrowinfo,array('id','borrow_interest_rate','borrow_con','borrow_user','count'),true);
                      
                    $jsons['project']  = $project;
                    break;
                default:
                    $borrowinfo['borrow_user'] = (string)(get_user_info($borrowinfo['borrow_uid']));
                    $borrowinfo['borrow_duration'] = $borrowinfo['borrow_duration'];
                    $borrowinfo['borrow_money'] = getFloatValue($borrowinfo['borrow_money'],2);
                    $borrowinfo['surplus_borrow'] = getFloatvalue($borrowinfo['borrow_money']-$borrowinfo['has_borrow'],2);
                    if($borrowinfo['borrow_status'] == 6 || $borrowinfo['borrow_status'] == 7 ){
                        $borrowinfo['endtimes'] = 0;
                    }else if($borrowinfo['borrow_status'] == 1 ){
                        $borrowinfo['endtimes'] = $borrowinfo['start_time']-time();
                    }else{
                        if(($borrowinfo['start_time'] + ($borrowinfo['collect_day']*60*60*24))>time()){

                            $borrowinfo['endtimes'] = $borrowinfo['start_time'] + ($borrowinfo['collect_day']*60*60*24)-time();  
                        }else{
                            $borrowinfo['endtimes'] = 0;
                        }
                    }
                    $borrowinfo['progress'] = getFloatValue($borrowinfo['has_borrow']/$borrowinfo['borrow_money']*100,-1);
          
                    $project = arrayFilterValByKey($borrowinfo,array('id','borrow_name','borrow_money','borrow_duration','borrow_min','surplus_borrow','progress','endtimes','borrow_interest_rate','borrow_con','borrow_user','borrow_status','progress'),true);
                      
                    $jsons['project']  = $project;
                    
                    break;

            }
            $jsons["status"] = "1";
            outJson($jsons);
        }else{
            $jsons["status"] = "0";
            $jsons["tips"] = "数据不存在！";
            outJson($jsons);
        }
    }
    /**
    *  投资支付页面
    * @param $uid
    */
    public function invest_pay()
    {
        $jsons["status"] = "0";
        $id              = intval($_REQUEST['id']) ? intval($_REQUEST['id']) : die;
        $uid             = intval($_REQUEST['uid']) ? intval($_REQUEST['uid']) : die;
        $jsons           = canInvestMoneyCheck($uid, $id);

        if ($jsons['status'] == "1") {
            $jsons['borrow_min'] = $jsons['borrowinfo']['borrow_min_cn'] ;
            $jsons['need'] = bcsub($jsons['borrowinfo']['borrow_money'], $jsons['borrowinfo']['has_borrow'],2);
            $jsons                 = arrayFilterValByKey($jsons, array('borrowinfo,is_experience,is_bonus,is_memberinterest'), false);
        }
        $jsons['account_money'] = isset($jsons['account_money']) ? $jsons['account_money']:"0";
        $jsons['money_experience'] = isset($jsons['money_experience']) ? $jsons['money_experience']:"0";
        $jsons['bonus_list'] = isset($jsons['bonus_list']) ? $jsons['bonus_list']:"0";
        $jsons['member_interest_rate_list'] = isset($jsons['member_interest_rate_list']) ? $jsons['member_interest_rate_list']:"0";
        
        // var_dump($jsons);exit;
        //echo $jsons['account_money'];exit;
        outJson($jsons);
    }
    /**
    *  投资支付
    * @param $uid
    */
    public function invest_pay_do()
    {
        $jsons["status"] = "0";
        $borrow_id       = intval($_REQUEST['id']) ? intval($_REQUEST['id']) : die;
        $uid             = intval($_REQUEST['uid']) ? intval($_REQUEST['uid']) : die;
        $pin        = isset($_REQUEST['pin_pass']) ? md5(text($_REQUEST['pin_pass'])) : "";
        $borrow_pass        = isset($_REQUEST['borrow_pass']) ? text($_REQUEST['borrow_pass']) : "";
        $money                   = floatval($_REQUEST['invest_money']) ? floatval(($_REQUEST['invest_money'])) : '0';
        $bonus_id                = isset($_REQUEST['bonus_id']) ? intval($_REQUEST['bonus_id']) : 0;
        $memberinterest_id                = isset($_REQUEST['memberinterest_id']) ? intval($_REQUEST['memberinterest_id']) : 0;
        $binfo = M("borrow_info")->field('borrow_money,has_borrow,has_vouch,borrow_max,borrow_min,borrow_type,password,pause,is_experience')->find($borrow_id);

        $is_experience                = $binfo['is_experience']==1 ? 1 : 0;
        $minfo          = getMinfo($uid, 'm.pin_pass,m.user_name,mm.account_money,mm.money_experience');
        $amoney         = $minfo['account_money'];
        $uname          = $minfo['u_user_name'];
        // $canInvestMoney = canInvestMoney($uid, $borrow_id, $money, 0, $bonus_id);
       
        // if ($canInvestMoney['status'] == 0) {
        //     $jsons["tips"] = $canInvestMoney['tips'];
        //     outJson($jsons);
        // }
        // $money_bonus = $canInvestMoney['money_bonus'];

        if($bonus_id>0){
            $canInvestMoney = canInvestMoney($this->uid, $borrow_id, $money, 0 , $is_experience,'0',$bonus_id,text($_REQUEST['borrow_pass']));
            if($canInvestMoney['status'] == 0){
                $jsons["tips"] = $canInvestMoney['tips'];
                outJson($jsons);
            }
            $money_bonus = $canInvestMoney['money_bonus'];
            $money = floatval($money+$money_bonus);
        }

        $pin_pass    = $minfo['pin_pass'];
        if($pin<>$pin_pass){
            $jsons["tips"]="支付密码错误，请重试";
            outJson($jsons);
        }
        $ids = M('members_status')->getFieldByUid($this->uid,'id_status');
        if($ids!=1){
            $jsons["tips"]="请先通过实名认证后在进行投标。";
            outJson($jsons);
        }
        $phones = M('members_status')->getFieldByUid($this->uid,'phone_status');
        if($phones!=1){
            $jsons["tips"]="请先通过手机认证后在进行投标。";
            outJson($jsons);
        }

        if($binfo['pause']==1){
            $jsons["tips"]="此标当前已经截止，请等待管理员开启。";
            outJson($jsons);
        }
        // 50 > 10
        if($money<$binfo['borrow_min']){
            $jsons["tips"]="此项目最小投资金额为".$binfo['borrow_min']."元";
            outJson($jsons);
        }
        if($money>$binfo['borrow_max'] and $binfo['borrow_max']!=0){
            $jsons["tips"]="此项目最大投资金额为".$binfo['borrow_max']."元";
            outJson($jsons);
        }

        if(!empty($binfo['password'])){
            if(empty($borrow_pass)){
                $jsons["tips"]="此标是约标，必须验证投标密码";
                outJson($jsons);
            }else if($binfo['password']<>$borrow_pass){
                $jsons["tips"]="投标密码不正确";
                outJson($jsons);
            }
        }
        //投标总数检测
        $capital = M('borrow_investor')->where("borrow_id={$borrow_id} AND investor_uid={$this->uid}")->sum('investor_capital');
        if(($capital+$money)>$binfo['borrow_max']&&$binfo['borrow_max']>0){
            $xtee = $binfo['borrow_max'] - $capital;
            $jsons["tips"]="您已投标{$capital}元，此投上限为{$binfo['borrow_max']}元，你最多只能再投{$xtee}";
            outJson($jsons);
        }
        
        if($binfo['has_vouch']<$binfo['borrow_money'] && $binfo['borrow_type'] == 2){
            $jsons["tips"]="此标担保还未完成，您可以担保此标或者等担保完成再投";
            outJson($jsons);
        }
        
        $need = bcsub($binfo['borrow_money'],$binfo['has_borrow'],2);
        $caninvest =bcsub($need ,$binfo['borrow_min'],2);
        // echo $binfo['borrow_money'] ;
        // exit;
        if( $money>$caninvest && ($need-$money)<>0 ){
            
            if(intval($need)==0 or $need=="0.00"){
                $jsons["tips"]="尊敬的{$uname}，此项目已经投满";
                outJson($jsons);
            }
            if($money>$need){
                $jsons["tips"]="尊敬的{$uname}，此项目还差{$need}元满标,您最多只能再投{$need}元";
                outJson($jsons);
            }
            
            $msg = "尊敬的{$uname}，此项目还差{$need}元满标,如果您投标{$money}元，将导致最后一次投标最多只能投".(bcsub($need,$money,2))."元，小于最小投标金额{$binfo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满标</font>或者投标金额必须<font color='#FF0000'>小于等于{$caninvest}元</font>";
            if($caninvest<$binfo['borrow_min']) $msg = "尊敬的{$uname}，此标还差{$need}元满标,如果您投标{$money}元，将导致最后一次投标最多只能投".(bcsub($need,$money,2))."元，小于最小投标金额{$binfo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满标</font>即投标金额必须<font color='#FF0000'>等于{$need}元</font>";
            $jsons["tips"]=$msg;
            outJson($jsons);
        }
        
        if(($need-$money) < 0 ){
            $jsons["tips"]="尊敬的{$uname}，此项目还差{$need}元满标,您最多只能再投{$need}元";
            outJson($jsons);
        }else{
            
            $capital = M('borrow_investor')->where("borrow_id={$borrow_id}")->sum('investor_capital');
            if($capital>$binfo["borrow_money"]){
                $jsons["tips"]="投资金额错误";
                outJson($jsons);
            }
            
            $capital = M('investor_detail')->where("borrow_id={$borrow_id}")->sum('capital');
            if($capital>$binfo["borrow_money"]){
                $jsons["tips"]="投资金额错误";
                outJson($jsons);
            }
            // $done = investMoney($this->uid,$borrow_id,$money);
            writeLog($memberinterest_id);
            if($memberinterest_id > 0){

                //加息券校验
                $canUseInterest = canUseInterest($this->uid,$memberinterest_id);
                writeLog($canUseInterest);
                if($canUseInterest['status'] == 0){
                    $jsons["tips"]="加息券不可用";
                    outJson($jsons);       
                }
                $interest_rate = $canUseInterest['interest_rate'];
            }else{
                $interest_rate=0;
            }
            //体验金校验         
            
            $canInvestMoney = canInvestMoney($this->uid, $borrow_id, $money, 0 , $is_experience,'0',$bonus_id,text(@$_REQUEST['borrow_pass']));
            if($canInvestMoney['status'] == 0){
                $this->error($canInvestMoney['tips']);          
            }       
            $money_bonus = $canInvestMoney['money_bonus'];

            if($canInvestMoney['money_type'] == 1 && $amoney<$money){
                $jsons["tips"]="尊敬的{$uname}，您准备投标{$money}元，但您的账户可用余额为{$amoney}元，请先去充值再投项目.";
                outJson($jsons); 
            }

            $done = investMoney($this->uid,$borrow_id,$money,'0','1', $is_experience,$memberinterest_id,$bonus_id,$money_bonus,text(@$_REQUEST['borrow_pass']),2);
        }
        
        //$this->display("Public:_footer");
        //$this->assign("waitSecond",1000);

        

        if($done===true) {
            $jsons["status"] = "1";
            $jsons["tips"]="恭喜成功投资{$money}元（其中使用红包{$money_bonus}元,使用加息券{$interest_rate}%）!";
            outJson($jsons); 
        }else if($done){
            $jsons["tips"]=$done;
            outJson($jsons); 
        }else{
            $jsons["tips"]="对不起，投资失败，请重试";
            outJson($jsons); 
        }
    }
    public function invest_pay_dos()
    {
        $jsons["status"] = "0";
        $borrow_id       = intval($_REQUEST['id']) ? intval($_REQUEST['id']) : die;
        $uid             = intval($_REQUEST['uid']) ? intval($_REQUEST['uid']) : die;
        $pin        = isset($_REQUEST['pin_pass']) ? md5(text($_REQUEST['pin_pass'])) : "";
        $money                   = floatval($_REQUEST['invest_money']) ? floatval(($_REQUEST['invest_money'])) : '0';
        $bonus_id                = isset($_REQUEST['bonus_id']) ? intval($_REQUEST['bonus_id']) : 0;
        $memberinterest_id                = isset($_REQUEST['memberinterest_id']) ? intval($_REQUEST['memberinterest_id']) : 0;
        $is_experience                = isset($_REQUEST['is_experience']) ? 1 : 0;

        $minfo          = getMinfo($uid, 'm.pin_pass,m.user_name,mm.account_money,mm.money_experience');
        $amoney         = $minfo['account_money'];
        $uname          = $minfo['u_user_name'];
        // $canInvestMoney = canInvestMoney($uid, $borrow_id, $money, 0, $bonus_id);
        $binfo = M("borrow_info")->field('borrow_money,has_borrow,has_vouch,borrow_max,borrow_min,borrow_type,password,pause')->find($borrow_id);
        // if ($canInvestMoney['status'] == 0) {
        //     $jsons["tips"] = $canInvestMoney['tips'];
        //     outJson($jsons);
        // }
        // $money_bonus = $canInvestMoney['money_bonus'];


        if($bonus_id>0){
            $canInvestMoney = canInvestMoney($this->uid, $borrow_id, $money, 0 , $is_experience,'0',$bonus_id,text($_REQUEST['borrow_pass']));
            if($canInvestMoney['status'] == 0){
                $jsons["tips"] = $canInvestMoney['tips'];
                outJson($jsons);
            }
            $money_bonus = $canInvestMoney['money_bonus'];
            $money = floatval($money+$$money);
        }

        $pin_pass    = $minfo['pin_pass'];
        if($pin<>$pin_pass){
            $jsons["tips"]="支付密码错误，请重试";
            outJson($jsons);
        }
        $ids = M('members_status')->getFieldByUid($this->uid,'id_status');
        if($ids!=1){
            $jsons["tips"]="请先通过实名认证后在进行投标。";
            outJson($jsons);
        }
        $phones = M('members_status')->getFieldByUid($this->uid,'phone_status');
        if($phones!=1){
            $jsons["tips"]="请先通过手机认证后在进行投标。";
            outJson($jsons);
        }

        if($binfo['pause']==1){
            $jsons["tips"]="此标当前已经截止，请等待管理员开启。";
            outJson($jsons);
        }
        // 50 > 10
        if($money<$binfo['borrow_min']){
            $jsons["tips"]="此项目最小投资金额为".$binfo['borrow_min']."元";
            outJson($jsons);
        }
        if($money>$binfo['borrow_max'] and $binfo['borrow_max']!=0){
            $jsons["tips"]="此项目最大投资金额为".$binfo['borrow_max']."元";
            outJson($jsons);
        }

        
        //投标总数检测
        $capital = M('borrow_investor')->where("borrow_id={$borrow_id} AND investor_uid={$this->uid}")->sum('investor_capital');
        if(($capital+$money)>$binfo['borrow_max']&&$binfo['borrow_max']>0){
            $xtee = $binfo['borrow_max'] - $capital;
            $jsons["tips"]="您已投标{$capital}元，此投上限为{$binfo['borrow_max']}元，你最多只能再投{$xtee}";
            outJson($jsons);
        }
        
        if($binfo['has_vouch']<$binfo['borrow_money'] && $binfo['borrow_type'] == 2){
            $jsons["tips"]="此标担保还未完成，您可以担保此标或者等担保完成再投";
            outJson($jsons);
        }
        
        $need = bcsub($binfo['borrow_money'],$binfo['has_borrow'],2);
        $caninvest =bcsub($need ,$binfo['borrow_min'],2);
        // echo $binfo['borrow_money'] ;
        // exit;
        if( $money>$caninvest && ($need-$money)<>0 ){
            
            if(intval($need)==0 or $need=="0.00"){
                $jsons["tips"]="尊敬的{$uname}，此项目已经投满";
                outJson($jsons);
            }
            if($money>$need){
                $jsons["tips"]="尊敬的{$uname}，此项目还差{$need}元满标,您最多只能再投{$need}元";
                outJson($jsons);
            }
            
            $msg = "尊敬的{$uname}，此项目还差{$need}元满标,如果您投标{$money}元，将导致最后一次投标最多只能投".(bcsub($need,$money,2))."元，小于最小投标金额{$binfo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满标</font>或者投标金额必须<font color='#FF0000'>小于等于{$caninvest}元</font>";
            if($caninvest<$binfo['borrow_min']) $msg = "尊敬的{$uname}，此标还差{$need}元满标,如果您投标{$money}元，将导致最后一次投标最多只能投".(bcsub($need,$money,2))."元，小于最小投标金额{$binfo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满标</font>即投标金额必须<font color='#FF0000'>等于{$need}元</font>";
            $jsons["tips"]=$msg;
            outJson($jsons);
        }
        
        if(($need-$money) < 0 ){
            $jsons["tips"]="尊敬的{$uname}，此项目还差{$need}元满标,您最多只能再投{$need}元";
            outJson($jsons);
        }else{
            
            $capital = M('borrow_investor')->where("borrow_id={$borrow_id}")->sum('investor_capital');
            if($capital>$binfo["borrow_money"]){
                $jsons["tips"]="投资金额错误";
                outJson($jsons);
            }
            
            $capital = M('investor_detail')->where("borrow_id={$borrow_id}")->sum('capital');
            if($capital>$binfo["borrow_money"]){
                $jsons["tips"]="投资金额错误";
                outJson($jsons);
            }
            // $done = investMoney($this->uid,$borrow_id,$money);
            writeLog($memberinterest_id);
            if($memberinterest_id > 0){

                //加息券校验
                $canUseInterest = canUseInterest($this->uid,$memberinterest_id);
                writeLog($canUseInterest);
                if($canUseInterest['status'] == 0){
                    $jsons["tips"]="加息券不可用";
                    outJson($jsons);       
                }
                $interest_rate = $canUseInterest['interest_rate'];
            }else{
                $interest_rate=0;
            }
            //体验金校验         
            $canInvestMoney = canInvestMoney($this->uid, $borrow_id, $money, 0 , $is_experience,'0',$bonus_id,text(@$_REQUEST['borrow_pass']));
            if($canInvestMoney['status'] == 0){
                $this->error($canInvestMoney['tips']);          
            }       
            $money_bonus = $canInvestMoney['money_bonus'];

            if($canInvestMoney['money_type'] == 1 && $amoney<$money){
                $jsons["tips"]="尊敬的{$uname}，您准备投标{$money}元，但您的账户可用余额为{$amoney}元，请先去充值再投项目.";
                outJson($jsons); 
            }

            $done = investMoney($this->uid,$borrow_id,$money,'0','1', $is_experience,$memberinterest_id,$bonus_id,$money_bonus,text(@$_REQUEST['borrow_pass']),2);
        }
        
        //$this->display("Public:_footer");
        //$this->assign("waitSecond",1000);

        

        if($done===true) {
            $jsons["status"] = "1";
            $jsons["tips"]="恭喜成功投资{$money}元（其中使用红包{$money_bonus}元,使用加息券{$interest_rate}%）!";
            outJson($jsons); 
        }else if($done){
            $jsons["tips"]=$done;
            outJson($jsons); 
        }else{
            $jsons["tips"]="对不起，投资失败，请重试";
            outJson($jsons); 
        }
    }
    /**
     * 登录接口
     * @param txtUsername  用户名
     * @param txtPwd  密码（md5）
     */
    public function login(){        
        $jsons["status"]="0";
        $pre = C('DB_PREFIX');
        $user_name = text(urldecode($_REQUEST['txtUsername']));
        $user_pass = text($_REQUEST['txtPwd']);
        if(empty($user_name) || empty($user_pass)){         
            $jsons["tips"]="登录失败，用户名或密码不能为空！";
            outJson($jsons);
        }

        if(filter_var($user_name, FILTER_VALIDATE_EMAIL)){
            $whereStr = "(m.user_email = '{$user_name}' and ms.email_status = 1) or m.user_name = '{$user_name}'";
        }else{
            $whereStr = "m.user_name = '{$user_name}'";
        }

        $vo = M('members m')->field('m.id,m.user_type,m.user_name,m.user_email,m.user_pass,m.pin_pass,m.is_ban,m.user_phone')->join($pre.'members_status ms on m.id=ms.uid')->where($whereStr)->find();
        if($vo['is_ban']==1){           
            $jsons["tips"]="您的帐户已被冻结，请联系客服处理！";
            outJson($jsons);
        }
        if( $vo["user_name"]=="" || !is_array($vo)){            
            $jsons["tips"]="用户名或者密码错误！";
            outJson($jsons);
        }
        if($vo['user_pass'] == $user_pass){
            $up['uid'] = $vo['id'];
            $up['add_time'] = time();
            $up['ip'] = get_client_ip();
            M('member_login')->add($up);
            $jsons['uid'] = text($vo['id']);
            $jsons['user_name'] = text($vo['user_name']);
            $jsons['user_type'] = text($vo['user_type']);
            $jsons['user_phone'] = $vo['user_phone'];
            $jsons["is_changepin"]=$vo['pin_pass']?1:0;
            $jsons["status"]="1";
            $jsons["tips"]="登录成功！";
            outJson($jsons);
        }else{
            $jsons["tips"]="用户名或者密码错误！";                
            outJson($jsons);
        }       
    } 
    /**
     * 手机登录接口
     * @param txtUsername  用户名
     * @param txtPwd  密码（md5）
     */
    public function phonelogin(){        
        $jsons["status"]="0";
        $pre = C('DB_PREFIX');
        $user_name = text(urldecode($_REQUEST['txtphone']));
        $user_pass = text($_REQUEST['txtPwd']);
        if(empty($user_name) || empty($user_pass)){         
            $jsons["tips"]="登录失败，用户名或密码不能为空！";
            outJson($jsons);
        }

        $whereStr = "(m.user_phone = '{$user_name}' and ms.phone_status = 1)";
        
        $vo = M('members m')->field('m.id,m.user_type,m.user_name,m.user_email,m.user_pass,m.pin_pass,m.is_ban,m.user_phone')->join($pre.'members_status ms on m.id=ms.uid')->where($whereStr)->find();
        if($vo['is_ban']==1){           
            $jsons["tips"]="您的帐户已被冻结，请联系客服处理！";
            outJson($jsons);
        }
        if( $vo["user_name"]=="" || !is_array($vo)){            
            $jsons["tips"]="用户名或者密码错误！";
            outJson($jsons);
        }
        if($vo['user_pass'] == $user_pass){
            $up['uid'] = $vo['id'];
            $up['add_time'] = time();
            $up['ip'] = get_client_ip();
            M('member_login')->add($up);
            $jsons['uid'] = text($vo['id']);
            $jsons['user_name'] = text($vo['user_name']);
            $jsons["is_changepin"]=$vo['pin_pass']?1:0;
            $jsons['user_phone'] = $vo['user_phone'];
            $jsons["status"]="1";
            $jsons["tips"]="登录成功！";
            outJson($jsons);
        }else{
            $jsons["tips"]="用户名或者密码错误！";                
            outJson($jsons);
        }       
    } 

    /*
    *   会员注册
    *   @param txtPhone  手机号
    *   txtCode  验证码
    */
    public function reg(){
        $jsons['status'] = "0";
        $action       = isset($_REQUEST['action']) ? $_REQUEST['action'] : "phoneverify";
        switch ($action) {
            case 'phoneverify':
                $user_phone = text($_REQUEST['txtPhone']);
                if(empty($user_phone)){            
                    $jsons["tips"]="手机验证失败，手机号不能为空！";
                    outJson($jsons);
                }
                if(preg_match("/^1[34578]\d{9}$/", $user_phone)){            
                    $map['user_phone'] = $user_phone;
                    $count = M('members')->where($map)->count('id');
                    if ($count>0) {             
                        $jsons["tips"]="手机验证失败，当前手机已经使用！";
                        outJson($jsons);
                    }
                }
                $count = M('members')->where("user_name='{$user_phone}'")->count('id');
                if($count>0){           
                    $jsons["tips"]="手机验证失败，用户名已经有人使用！";
                    outJson($jsons);
                }

                
                $codeId = @$_REQUEST['codeId'];
                $txtCode = @$_REQUEST['txtCode'];
                $verifyRs = M('verify_code')->where("id = '{$codeId}' and content = {$txtCode}")->count('id');
                if($verifyRs!=1){           
                    $jsons["tips"]="验证码错误！";
                    outJson($jsons);
                }
                $jsons['status'] = "1";
                $jsons['txtPhone'] = $user_phone;
                $jsons["tips"]="手机验证成功，请设置密码！";
                outJson($jsons);
                break;
            case 'successreg':
                $data['user_name'] = text($_REQUEST['txtPhone']); 
                $data['user_phone'] = text($_REQUEST['txtPhone']);
                $data['user_pass'] = md5($_REQUEST['txtPwd']);
                //获取推荐人
                $txtIncode = text($_REQUEST['txtIncode']);
                if(!empty($txtIncode)){
                    $txtRecUserid = M('members')->where("user_phone='".$txtIncode."'")->getField('id');
                    if(!empty($txtRecUserid)) {
                        $data['recommend_id']=$txtRecUserid;
                    }else{                              
                        $jsons["tips"]="推荐人不存在，若没有推荐人请留空。";
                        outJson($jsons);
                    };
                }
                if(session("tmp_invite_user"))  $data['recommend_id'] = session("tmp_invite_user");
                $data['reg_time'] = time();
                $data['reg_ip'] = get_client_ip();
                $data['lastlog_time'] = time();
                $data['lastlog_ip'] = get_client_ip();
                $newid = M('members')->add($data);
                if($newid){
                    // 设置手机验证状态
                    M("members_status")->add(array(
                          "uid" => $newid,
                          "phone_status" => 1,
                          "id_status" => 0,
                          "email_status" => 0,
                          "account_status" => 0,
                          "credit_status" => 0,
                          "safequestion_status" => 0,
                          "video_status" => 0,
                          "face_status" => 0
                      ));       
                    M('member_info')->add(array(
                        "uid" => $newid,
                    ));
                    if($this->glo['award_reg']>0){
                        pubExperienceMoney($newid,$this->glo['award_reg'],4,30);
                    }
                    pubBonusByRules($newid,2);
                    $jsons['uid'] = text($newid);           
                    $jsons["status"]="1";
                    $jsons["tips"]="注册成功！";
                    outJson($jsons);
                }
                else{
                    $jsons["tips"]="注册失败，请重试！";
                    outJson($jsons);
                }
                break;
            default:
                $jsons["tips"]="注册失败";
                outJson($jsons);
                break;
        }
    }
    /**
     * 发送验证码
     */
    public function sendcode(){
        $jsons["status"]= "0";
        $code=rand(100000,999999);
        $txtPhone = $_REQUEST["txtPhone"];
        $act = isset($_REQUEST["act"]) ? text($_REQUEST["act"]) : '';       
        
        switch ($act) {
            case 'reg':
                $content= "您正在注册本站会员，验证码为:".$code;
                break;          
            case 'findpass':
                $content= "您正在使用本站找回密码功能，验证码为:".$code;
                break;
            default:
                $content= "您的验证码为:".$code;              
                break;
        }

        if( $txtPhone == "" || !preg_match("/^1[34578]\d{9}$/", $txtPhone) ){
            $jsons["status"]="0";           
            $jsons["tips"]="手机号格式不正确！";         
            outJson($jsons);
        }
        
        $map['user_phone'] = text($txtPhone);
        $count = M('members')->where($map)->count('id');
        if ($act == 'reg' && $count>0) {
            $jsons["status"]="0";           
            $jsons["tips"]="手机号已存在！";                           
            outJson($jsons);
        }elseif($act == 'findpass' && $count<=0){
            $jsons["status"]="0";           
            $jsons["tips"]="手机号不存在！";                           
            outJson($jsons);
        }
        sendsms($txtPhone, $content);
        $addData = array('content'=>$code,'add_time'=>time());
        $codeId = M('verify_code')->add($addData);
        if($codeId){
                $jsons["status"]="1";
                $jsons["codeId"] = $codeId;
                // $jsons["tips"]= '验证码已经发送至您的手机！'.$code.'';
                $jsons["tips"]= '验证码已经发送至您的手机！';
        }
        outJson($jsons);
    }
    public function idcard_apply()
    {
        $uid             = $this->uid;
        $jsons["status"] = '0';
        $real_name = text($_REQUEST["realname"]) or die;
        $cardid = text($_REQUEST["idcard"]) or die;
        $data['real_name'] = $real_name;
        $data['idcard'] = $cardid;
        $data['up_time'] = time();
        /////////////////////////
        $data1['idcard'] = text($cardid);
        $data1['up_time'] = time();
        $data1['uid'] = $uid;
        $datag = get_global_setting();
        $data1['status'] = 0;
        $hasApply = M('name_apply')->where("uid = {$uid}")->count('uid');
        if ($hasApply) {
            M('name_apply')->where("uid ={$uid}")->save($data1);
        } else {
            M('name_apply')->add($data1);
        }
        $hasUid = M('member_info')->getFieldByIdcard($data['idcard'], 'uid');
        if ($hasIdcard && $hasUid != $uid) {
            $jsons["tips"] = "此身份证号码已使用！";
            outJson($jsons);
        }
        $hasInfo = M('member_info')->where("uid = {$uid}")->count('uid');
        if ($hasInfo) {
            $newid = M('member_info')->where("uid = {$uid}")->save($data);
        } else {
            $data['uid'] = $uid;
            $newid = M('member_info')->add($data);
        }
        if (isset($newid) && $newid) {

            $realname = $data['real_name'];
            $idcard   = $data['idcard'];
            $rsVerify = getIdVerify($realname,$idcard);
            if($rsVerify){
                addInnerMsg($this->uid,"实名认证通过",'自动认证');
                $ms = M('members_status')->where("uid={$this->uid}")->setField('id_status',1);
                $name_apply_data['status']=1;
                $name_apply_data['deal_info']= '自动认证';
                $new = M("name_apply")->where("uid={$this->uid}")->save($name_apply_data);
                $jsons["status"] = '1';
                $jsons["tips"] =  "实名认证通过";
                outJson($jsons);
            }else{
                $ms=M('members_status')->where("uid={$this->uid}")->setField('id_status',3);
                if($ms==1){
                    $jsons["status"] = '1';
                    $jsons["tips"] =  "实名认证申请审核中……";
                    outJson($jsons);
                }else{
                    $dt['uid'] = $this->uid;
                    $dt['id_status'] = 3;
                    $rs = M('members_status')->add($dt);
                    if ($rs) {
                        $jsons["status"] = '1';
                        $jsons["tips"] = "实名认证申请已提交，请耐心等待审核……";
                        outJson($jsons);
                    }
                }
            }

        }
        $jsons["tips"] = "申请失败，请重试！";
        outJson($jsons);
    }
    /**
     * 找回密码
     * @param txtPhone  手机号
     * @param txtPassword  新密码
     * @param txtRePassword  确认密码
     * @param codeId  验证码id
     * @param txtCode  验证码
     */
    public function findpass(){
        $jsons['status']="0";
        $per = C('DB_PREFIX');
        $map['user_phone'] = text($_REQUEST['txtPhone']);

        $txtPassword = text($_REQUEST['txtPassword']);
        $txtRePassword = text($_REQUEST['txtRePassword']);
        if($map['user_phone']==""){         
            $jsons['tips'] = "手机号不能为空";
            outJson($jsons);
        }
        if($txtPassword==""){           
            $jsons['tips'] = "密码不能为空";
            outJson($jsons);
        }
        if($txtPassword !=$txtRePassword){           
            $jsons['tips'] = "新密码与确认密码不同";
            outJson($jsons);
        }
        $user = M('members')->where($map)->find();
        if ($user["id"]=="") {          
            $jsons['tips'] = "暂无此手机绑定的用户";
            outJson($jsons);
        }

        $codeId = @$_REQUEST['codeId'];
        $txtCode = @$_REQUEST['txtCode'];
        $verifyRs = M('verify_code')->where("id = '{$codeId}' and content = {$txtCode}")->count('id');
        if($verifyRs!=1){           
            $jsons['tips']="验证码错误！";
            outJson($jsons);
        }

        $oldpass = M("members")->getFieldById($user["id"],'user_pass');
        if($oldpass == md5($txtPassword)){
            $newid = true;
        }else{
            $newid = M()->execute("update {$per}members set `user_pass`='".md5($txtPassword)."' where id=".$user["id"]);
        }
        if($newid){
            $jsons['status']="1";
            $jsons['tips'] = "修改成功！";
            outJson($jsons);
        }else{
            $jsons['tips'] = "修改失败，请重试！";
            outJson($jsons);
        }
    }
    /**
     * 找回密码
     * @param txtPhone  手机号
     * @param txtPassword  新密码
     * @param txtRePassword  确认密码
     * @param codeId  验证码id
     * @param txtCode  验证码
     */
    public function findpinpass(){
        $jsons['status']="0";
        $per = C('DB_PREFIX');
        $map['user_phone'] = text($_REQUEST['txtPhone']);

        $txtPassword = text($_REQUEST['txtPassword']);
        $txtRePassword = text($_REQUEST['txtRePassword']);
        if($map['user_phone']==""){         
            $jsons['tips'] = "手机号不能为空";
            outJson($jsons);
        }
        if($txtPassword==""){           
            $jsons['tips'] = "密码不能为空";
            outJson($jsons);
        }
        if($txtPassword !=$txtRePassword){           
            $jsons['tips'] = "新密码与确认密码不同";
            outJson($jsons);
        }
        $user = M('members')->where($map)->find();
        if ($user["id"]=="") {          
            $jsons['tips'] = "暂无此手机绑定的用户";
            outJson($jsons);
        }

        $codeId = @$_REQUEST['codeId'];
        $txtCode = @$_REQUEST['txtCode'];
        $verifyRs = M('verify_code')->where("id = '{$codeId}' and content = {$txtCode}")->count('id');
        if($verifyRs!=1){           
            $jsons['tips']="验证码错误！";
            outJson($jsons);
        }

        $oldpass = M("members")->getFieldById($user["id"],'pin_pass');
        if($oldpass == md5($txtPassword)){
            $newid = true;
        }else{
            $newid = M()->execute("update {$per}members set `pin_pass`='".md5($txtPassword)."' where id=".$user["id"]);
        }
        if($newid){
            $jsons['status']="1";
            $jsons['tips'] = "修改成功！";
            outJson($jsons);
        }else{
            $jsons['tips'] = "修改失败，请重试！";
            outJson($jsons);
        }
    }

    public function memberindex(){
        $jsons['status']="0";
        $uid = $_REQUEST['uid'];
        $minfo =getMinfo($uid,true);
        // 站内消息
        $msgcount = M('inner_msg')->where('uid='.$this->uid.' and status = 0')->count();
        //最近30天收益
        $stime = strtotime(date('Y-m-d 00:00:00', strtotime("-30 day")));
        $invest = M('borrow_investor')->where('investor_uid = '.$this->uid.' and repayment_time >='.$stime)->sum('investor_interest');
        $jsons["minfo"] = array(
            'mid'=>$uid,
            'user_name'=>$minfo['user_name'],
            'userpic'=>$this->domainUrl . get_app_avatar($uid),
            'all_money'=>getFloatvalue($minfo['account_money']+$minfo['money_collect']+$minfo['money_freeze'],2),
            'account_money'=>$minfo['account_money'],
            'freeze_money'=>$minfo['money_freeze'],
            'experience_money'=>$minfo['money_experience'],
            'msgcount'=>$msgcount,
            'invest'=>$invest?$invest:'0.00'
            );     
        $jsons['status']="1";        
        outJson($jsons);
    }
    
    /**
    * 用户基息
    * @param uid  用户id
    */
   public function userinfo()
   {
       $uid = $_REQUEST['uid'];           
       $minfo = getMinfo($uid, "m.id,m.user_name,pin_pass,IFNULL(m.user_email,'') as user_email,m.user_phone,m.user_leve,m.time_limit,mi.sex,IFNULL(mi.real_name,'') as real_name,IFNULL(mi.idcard,'') as idcard");
       if ($minfo) {
           $minfo['userpic'] = $this->domainUrl . get_app_avatar($minfo['id']);
       }
       $membersStatus = M('members_status')->field('id_status,phone_status,email_status')->where("uid='{$uid}'")->find();
       // $minfo['id_status'] = @$membersStatus['id_status'] == 1 ? '1' : (@$membersStatus['id_status'] == 3 ? '2' : '0');
       // var_dump($minfo);

       $minfo['phone_status'] = @$membersStatus['phone_status'] == 1 ? '1' : '0';
       $minfo['email_status'] = @$membersStatus['email_status'] == 1 ? '1' : '0';

       $bank_num = M('member_banks')->where("uid='{$uid}'")->find();
       $minfo['is_bank'] = $bank_num['bank_num']>0 ? 1 : 0;   
       $bankname = C('BANK_NAME');

       if ($minfo) {
           $jsons['status'] = "1";
           $jsons['minfo'] = array(
                'mid'=>$minfo['id'],
                'userpic'=>$minfo['userpic'],
                'user_name'=>$minfo['user_name'],
                'real_name'=>$membersStatus['id_status'] == 1 ?$minfo['real_name']:'',
                'idcard'=>$membersStatus['id_status'] == 1 ? $minfo['idcard']:'',
                'user_phone'=>$minfo['user_phone'],
                'is_bank'=>$minfo['is_bank'],
                'id_status'=>$membersStatus['id_status'],
                'bank_num'=>substr($bank_num['bank_num'], -4),
                'bank_name'=>$bankname[$bank_num['bank_name']],
            );
       } else {
           $jsons['status'] = "0";
           $jsons['tips'] = "获取用户信息失败！";
       }
       outJson($jsons);
   }

   /**
    * 修改手机号
    * @param uid  用户id
   */
   public function changephone()
   {
        $per = C('DB_PREFIX');
        $uid = $_REQUEST['uid'];
        $act = isset($_REQUEST['act']) ? text($_REQUEST['act']) : '';
        switch ($act) {
            case 'firstphone':
                $minfo = getMinfo($uid, "m.id,m.user_phone");
                $jsons['status'] = "1";
                $jsons['minfo'] = array(
                    'mid'=>$minfo['id'],
                    'user_phone'=>$minfo['user_phone'],
                );
                break;
            case 'firstphoneact':
                $user_phone = text($_REQUEST['txtPhone']);
                if(empty($user_phone)){            
                    $jsons["tips"]="手机验证失败，手机号不能为空！";
                    outJson($jsons);
                }
                $codeId = @$_REQUEST['codeId'];
                $txtCode = @$_REQUEST['txtCode'];
                $verifyRs = M('verify_code')->where("id = '{$codeId}' and content = {$txtCode}")->count('id');
                if($verifyRs!=1){           
                    $jsons["tips"]="验证码错误！";
                    outJson($jsons);
                }
                $jsons['status'] = "1";
                $jsons['txtPhone'] = $user_phone;
                $jsons["tips"]="手机验证成功，请设置新的手机号！";
                break;
            case 'successphone':
                $user_phone = text($_REQUEST['newPhone']);
                if(empty($user_phone)){            
                    $jsons["tips"]="手机验证失败，手机号不能为空！";
                    outJson($jsons);
                }
                $codeId = @$_REQUEST['codeId'];
                $txtCode = @$_REQUEST['txtCode'];
                $verifyRs = M('verify_code')->where("id = '{$codeId}' and content = {$txtCode}")->count('id');
                if($verifyRs!=1){           
                    $jsons["tips"]="验证码错误！";
                    outJson($jsons);
                }
                $newid = M()->execute("update {$per}members set `user_phone`='".$user_phone."' where id=".$uid);
                if($newid){
                    $jsons['status']="1";
                    $jsons['tips'] = "修改成功！";
                }else{
                    $jsons['tips'] = "修改失败，请重试！";
                }
                break;
        }
        outJson($jsons);
   }

    /*
    *    消息中心
    */
    public function sysmsg(){
        $uid = $_REQUEST['uid'];
        $json['status'] = "0";
        $act = isset($_REQUEST['act']) ? text($_REQUEST['act']) : 'personalnews';
        $p = intval($_REQUEST['p']) ? intval($_REQUEST['p']) : 1;
        switch ($act) {
            case 'financenotice':
                $map['type_id'] = 11;
                import("ORG.Util.Page");
                $count = M('article')->where($map)->count('id');
                $page = new Page($count, 15);
                $page->show();
                $pageSet['nowPage'] = strval($p);
                $pageSet['totalPages'] = strval(intval($page->totalPages));
                $pageSet['totalRows'] = strval($page->totalRows);
                $pageSet['pageSize'] = strval(15);
                $Lsql = "{$page->firstRow},{$page->listRows}";
                $list = M('article')->where($map)->order('id DESC')->limit($Lsql)->select();
                foreach($list as $v){
                    $str[] = array(
                        "title" => $v['title'],
                        "addtime" => $v['art_time'],
                        "id" => $v['id'],
                    );
                }
                $json['page'] = $pageSet;
                if (ceil($count / 15) < $_REQUEST["p"]) {
                    unset($msglist);
                    $msglist = array();
                    $json["status"] = 1;            
                    $json["list"] = array();
                    $json["tips"] = "已经是最后一页啦";
                    outJson($json);
                }
                if($list){
                    $json['list'] = $str;
                    $json['tips'] = "成功";
                    $json['status'] = "1";
                }else{
                    $json['list'] = array();
                    $json['tips'] = "暂无信息";
                    $json['status'] = "1";
                }
                break;
            case 'personalnews':
                if(empty($uid)){
                    $json['tips'] = "信息有误，重新填写";
                    $json['status'] = "0";
                    outJson($json); 
                }
                $map['uid'] = $uid;
                //分页处理
                import("ORG.Util.Page");
                $count = M('inner_msg')->where($map)->count('id');
                $page = new Page($count, 15);
                $page->show();
                $pageSet['nowPage'] = strval($p);
                $pageSet['totalPages'] = strval(intval($page->totalPages));
                $pageSet['totalRows'] = strval($page->totalRows);
                $pageSet['pageSize'] = strval(15);
                $Lsql = "{$page->firstRow},{$page->listRows}";
                //分页处理
                $list = M('inner_msg')->where($map)->order('id DESC')->limit($Lsql)->select();  
                foreach($list as $v){
                    $str[] = array(
                        "title" => $v['title'],
                        "addtime" => $v['send_time'],
                        "id" => $v['id'],
                        "is_show" => $v['status'],

                    );
                }
                $json['page'] = $pageSet;
                if (ceil($count / 15) < $_REQUEST["p"]) {
                    unset($msglist);
                    $msglist = array();
                    $json["status"] = 1;            
                    $json["list"] = array();
                    $json["tips"] = "已经是最后一页啦";
                    outJson($json);
                }
                if($list){
                    $json['list'] = $str;
                    $json['tips'] = "成功";
                    $json['status'] = "1";
                }else{
                    $json['list'] = array();
                    $json['tips'] = "暂无信息";
                    $json['status'] = "1";
                }
                break;
        }
        outJson($json);         
    }

    /*
    *   查看消息中心
    */
    public function msgview()
    {
        $jsons["status"] = 1;
        $id = intval($_REQUEST['id']) or die;
        $uid = $_REQUEST['uid'];
        $act = isset($_REQUEST['act']) ? text($_REQUEST['act']) : 'personalnews';
        switch ($act) {
            case 'financenotice':
                $article = M('article')->where("id={$id}")->find();
                $jsons["title"] = $article['title'];
                $jsons["content"] = $article['art_content'];
                $jsons["add_time"] = date("Y-m-d  H:i:s", $article["art_time"]);
                break;
            case 'personalnews':
                M("inner_msg")->where("id={$id} AND uid={$this->uid}")->setField("status", 1);
                $vo = M("inner_msg")->where("id='{$id}' AND uid={$this->uid}")->find();
                $jsons["title"] = $vo['title'];
                $jsons["content"] = $vo['msg'];
                $jsons["add_time"] = date("Y-m-d  H:i:s", $vo["send_time"]);
                break;
        }
        out_Web($jsons['title'], $jsons['add_time'], $jsons['content']);
    }
    /*
    *   文章详情、公告详情
    */
    public function article_view(){
        $id = intval($_REQUEST['id']) or die;
        $article = M('article')->where("id={$id}")->find();
        $jsons["title"] = $article['title'];
        $jsons["content"] = $article['art_content'];
        $jsons["add_time"] = date("Y-m-d  H:i:s", $article["art_time"]);
        out_Web($jsons['title'], $jsons['add_time'], $jsons['content']);
    }
    
    /*
    *   新手指南
    */
    public function category_view(){
        $id = 17;
        $article = M('article_category')->where("id={$id}")->find();
        $jsons["title"] = $article['type_name'];
        $jsons["content"] = $article['type_content'];
        $jsons["add_time"] = date("Y-m-d  H:i:s", $article["add_time"]);
        out_Web($jsons['title'], $jsons['add_time'], $jsons['content']);
    }

    /*
    *   投资记录 收益中、已结清
    */
    public function investlog(){
        $uid = $_REQUEST['uid'];
        $act = isset($_REQUEST['act']) ? text($_REQUEST['act']) : 'income';
        $field = "status,count(id) as num,sum(investor_capital) as investor_capital,sum(reward_money) as reward_money,sum(investor_interest) as investor_interest,sum(receive_capital) as receive_capital,sum(receive_interest) as receive_interest";
        $investNum = m("borrow_investor")->field($field)->where("investor_uid = {$uid}")->group("status")->select();
        foreach ($investNum as $v) {
            $investStatus[$v['status']] = $v;
        }
        $jsons['status']=1;
        $jsons['dsbj'] = getfloatvalue(floatval($investStatus[4]['investor_capital']), 2);   //待收本金
        $jsons['dssy'] = getfloatvalue(floatval($investStatus[4]['investor_interest']), 2);  //待收收益
        $jsons['yhsy'] = getfloatvalue(floatval($investStatus[5]['investor_interest']), 2);   //已获收益
        $map['investor_uid'] = $_REQUEST['uid'];
        switch ($act) {
            case 'income':
                //收益中
                $map['status'] = 4;
                $list = get_TenderList($map, 10);
                break;
            case 'haveclosed':
                //已结清
                $map['status'] = array("in", "5,6");
                $list = get_TenderList($map, 10);
                break;
        }
        $jsons['list'] = $list?$list:array();
        outJson($jsons);
    }

    /*
    *   待回款
    */
    public function tendout(){
        $uid = $_REQUEST['uid'];
        $act = isset($_REQUEST['act']) ? text($_REQUEST['act']) : 'tendbacking';
        $map['investor_uid'] = $_REQUEST['uid'];
        $monthd = $_REQUEST['monthd'];
        switch ($act) {
            case 'tendbacking':
                //待回款
                $map['status'] = 4;
                $list = get_TenderingList($map, $monthd);
                break;
            case 'tenddone':
                //已回款
                $map['status'] = array("in", "5,6");
                $list = get_TenderingList($map, $monthd);
                break;
        }

        $jsons['list'] = $list;
        $jsons['status'] = 1;
        outJson($jsons);
    }

    /**
    *  投资管理详情
    * @param $uid
    */
    public function investor_details(){
        $uid = $_REQUEST['uid'];
        $id = $_REQUEST['id'];
        $fieldx = "bi.investor_capital,bi.add_time,bi.deadline,bi.investor_interest,bi.member_interest_rate_id,sum(bi.investor_capital+bi.investor_interest) as allcap,b.borrow_name,b.borrow_interest_rate,b.borrow_duration,b.borrow_status";
        $investinfo =  M("borrow_investor bi")->field($fieldx)->join("lzh_borrow_info b ON bi.borrow_id = b.id")->where("bi.id={$id} and bi.investor_uid = {$uid}")->order("bi.id DESC")->find();
        $res = array();
        $bstatus = array(
                '0'=>'初审中',
                '1'=>'预热中',
                '2'=>'众筹中',
                '4'=>'复审中',
                '6'=>'已成功',
                '7'=>'已分红',
                '8'=>'已结项',
                '9'=>'',
                '10'=>'',
                '-1'=>'初审未通过',
                '3'=>'复审未通过',
                '5'=>'复审未通过',
            );
        foreach ($investinfo as $key => $value) {
           if($key == 'borrow_status'){
                $res['list']['borrow_status'] =  $bstatus[$value];
           }else if($key == 'add_time'){
                $res['add_time'] =  date('Y-m-d',$value);
           }else if($key == 'deadline'){
                $res['deadline'] =  date('Y-m-d',$value);
           }else{
            $res[$key] = $value;
           } 
        }
        $res['list']['hkje'] =  $investinfo['allcap'];
        $res['list']['qs'] =  '第一期';
        $res['list']['hkbj'] =  $investinfo['investor_capital'];
        $res['list']['hklx'] =  $investinfo['investor_interest'];
        $res['list']['xtjl'] =  '0.00';
        $res['list']['glfy'] =  '0.00';
        $res['list']['yqfy'] =  '0.00';
        //加息券
        if($investinfo['member_interest_rate_id'] != 0){

            $rid = $investinfo['member_interest_rate_id'];
            $canUseInterest = M('member_interest_rate')->where(" id = '{$rid}' and uid='{$uid}'")->find();
            $res['list']['jxjl']= round($investinfo['investor_capital']*$canUseInterest['interest_rate']/100/360*$investinfo['borrow_duration'],2);

        }else{
            $res['list']['jxjl'] =  0;
        }
        $res['list']['start_time'] =  date('Y-m-d',$investinfo['add_time']);
        $res['list']['end_time'] =  date('Y-m-d',$investinfo['deadline']);
        $res['status']=1;
        outJson($res);
    }
    

    /*
    *   滚动时间范围
    */
    public function timeframe(){

        $jsons['start_time'] = '2015-01';
        $jsons['end_time'] = '2035-12';
        $jsons['status'] = 1;
        outJson($jsons); 
    }
    /**
    *  银行卡添加修改
    * @param $uid
    */
    public function banksave()
    {
        $uid = $_REQUEST['uid'];
        $data['bank_num'] = text($_REQUEST['bank_num']);
        $bank_name = text($_REQUEST['bank_name']);
        $data['real_name'] = text(@$_REQUEST['real_name']);
        $bankname = C('BANK_NAME');
        $data['bank_name'] = array_search($bank_name, $bankname);
        if(empty($uid) || empty($data['bank_name']) || empty($data['bank_num']) || empty($data['real_name'])){
            $jsons['status'] = '0';
            $jsons['tips'] = '填写完整信息！';
            outJson($jsons);
        }
        $data['add_ip'] = get_client_ip();
        $data['add_time'] = time();
        $res = M('member_banks')->where('uid='.$uid)->find();
        if (empty($res)) {
            $data['uid'] = $uid;
            $rs = M('member_banks')->add($data);
        } else {
            $rs = M('member_banks')->where(array( 'uid' => $uid))->save($data);
        }
        if (!($rs === false)) {
            $mdata =  M("members")->find($this->uid);
            if(!empty($mdata['recommend_id']) && $mdata['is_fafang'] == 0){
                $rec_bonus_money = explode('|', $this->glo['rec_bonus_money']);
                $recount = M('members')->where("recommend_id = ".$mdata['recommend_id'])->count('id');
                $bonus['uid'] = $mdata['recommend_id'];
                //加息券
                $rdata['uid']=$mdata['recommend_id'];
                $rdata['interest_rate'] = $rec_bonus_money['0'];
                $rdata['start_time'] = time();
                $rdata['end_time'] = strtotime(date('Y-m-d H:i:s', strtotime("+30 day")));
                $rdata['status'] = '1';
                $rdata['type'] = '1';
                $rdata['add_time'] = time();
                $rs = M('member_interest_rate')->add($rdata);
                $od['is_fafang'] = 1;
                M('members')->where('id='.$this->uid)->save($od);
            }
            $jsons['status'] = '1';
            $jsons['tips'] = '操作成功！';
        } else {
            $jsons['status'] = '0';
            $jsons['tips'] = '操作失败！';
        }
        outJson($jsons);
    }      
    /**
    *  银行卡发卡行
    * @param $uid
    */
    public function bankinfo(){
        $jsons['bank_list'] = C('BANK_NAME');
        $jsons['status'] = 1;
        outJson($jsons);
    }
    /*
    *   银行卡列表
    */
    public function banklist(){
        $uid = $_REQUEST['uid'];
        M("member_banks")->where("uid=" . $uid . " and (bank_num ='' or bank_num is null) ")->delete();
        $vobank = M("member_banks")->field("uid,bank_num,bank_name,id")->where('uid=' . $uid)->order('add_time desc')->select();
        $bankname = C('BANK_NAME');
        foreach ($vobank as $k => $v) {
            $num = substr_replace($v['bank_num'],'','0','-4');
            $banklist[] = array(
                'bank_num' => $num,     
                'bank_name' => $bankname[$v['bank_name']],
                'uid' => $v['uid'],
                'bank_ico' => $this->domainUrl . '/Public/bank/' .$v['bank_name'].".png",
            );
        }
        $jsons['banklist'] = is_array($vobank) ? $banklist : array();
        $jsons['status'] = '1';
        outJson($jsons);
    }
    /*
    *   银行卡解除绑定
    */
    public function unbindbank()
    {
        $uid = $_REQUEST['uid'];
        if(empty($uid)){
            $jsons['status'] = '0';
            $jsons['tips'] = '填写完整信息！';
            outJson($jsons);
        }
        $rs = M('member_banks')->where(array('uid' => $uid))->delete();
        if (!($rs === false)) {
            $jsons['status'] = '1';
            $jsons['tips'] = '操作成功！';
            outJson($jsons);
        } else {
            $jsons['status'] = '0';
            $jsons['tips'] = '操作失败！';
            outJson($jsons);
        }
    }
    /*
    *   加息券列表
    */
    public function interest_rate_list(){
        $uid = $_REQUEST['uid'];
        $ps = $p = intval($_REQUEST['p']) ? intval($_REQUEST['p']) : "1";
        $irstatus = isset($_REQUEST['irstatus']) ? text($_REQUEST['irstatus']) : '';
        $whereStr = "status not in(2,3) and end_time < UNIX_TIMESTAMP()";
        $whereStr .= " and uid = {$uid}";
        $rs = M('member_interest_rate')->where($whereStr)->save(array('status'=>3));
        if($irstatus){
            $map['t1.status'] = $irstatus;
        }else{
            $map['t1.status'] = array('in', '0,2,3');
        }
        $map['t1.uid'] = $uid;
        $count = M('member_interest_rate t1')->where($map)->count('t1.id');     
        import("ORG.Util.Page");
        $p = new Page($count, 10);
        $page = $p->show();
        $Lsql = "{$p->firstRow},{$p->listRows}";
        $memberinterestList = M('member_interest_rate t1')->where($map)->order('t1.id desc,t1.end_time asc')->limit($Lsql)->select();
        $pageSet['nowPage'] = strval($ps);
        $pageSet['totalPages'] = strval(intval($p->totalPages));
        $pageSet['totalRows'] = strval($p->totalRows);
        $pageSet['pageSize'] = strval('10');
        $json['page']         = $pageSet;
        $json['status']         = 1;
        if (ceil($count / 10) < $ps) {
            $json["status"] = 1;            
            $json["list"] = array();
            $json["tips"] = "已经是最后一页啦";
            outJson($json);
        }
        $statusArr = array('已禁用','未使用','已使用','已过期');
        if($memberinterestList){
            $res = array();
            foreach ($memberinterestList as $key => $value) {
                $res[$key]['interest_rate']=$value['interest_rate'];
                $res[$key]['start_time']=date('Y-m-d',$value['start_time']);
                $res[$key]['end_time']=date('Y-m-d',$value['end_time']);
                $res[$key]['status']=$statusArr[$value['status']];
            }
            $json['list'] = $res;
            $json['tips'] = "成功";
            $json['status'] = "1";
        }else{
            $json['list'] = array();
            $json['tips'] = "暂无信息";
            $json['status'] = "1";
        }
        outJson($json);
    }
    /*
    *   我的红包
    */
    public function bonus(){
        $uid = $_REQUEST['uid'];
        $ps = $p = intval($_REQUEST['p']) ? intval($_REQUEST['p']) : "1";
        // $bonusstatus = isset($_REQUEST['bonusstatus']) ? text($_REQUEST['bonusstatus']) : '1';
        $bonusstatus = '1';
        $whereStr = "status not in(2,3) and end_time < UNIX_TIMESTAMP()";
        $whereStr .= " and uid = {$uid}";
        $rs = M('member_bonus')->where($whereStr)->save(array('status'=>3));
        $map['t1.status'] = $bonusstatus;

        $map['t1.uid'] = $uid;
        $count = M('member_bonus t1')->where($map)->count('t1.id');     
        import("ORG.Util.Page");
        $p = new Page($count, 10);
        $page = $p->show();
        $Lsql = "{$p->firstRow},{$p->listRows}";
        $bonusList = M('member_bonus t1')->where($map)->order('t1.id desc,t1.end_time asc')->limit($Lsql)->select();
        $pageSet['nowPage'] = strval($ps);
        $pageSet['totalPages'] = strval(intval($p->totalPages));
        $pageSet['totalRows'] = strval($p->totalRows);
        $pageSet['pageSize'] = strval('10');
        $json['page']         = $pageSet;
        $json['status']         = 1;
        if (ceil($count / 10) < $ps) {
            $json["status"] = 1;            
            $json["list"] = array();
            $json["tips"] = "已经是最后一页啦";
            outJson($json);
        }
        $statusArr = array('已禁用','未使用','已使用','已过期');
        if($bonusList){
            $res = array();
            foreach ($bonusList as $key => $value) {
                $res[$key]['money_bonus']=$value['money_bonus'];
                $res[$key]['start_time']=date('Y/m/d',$value['start_time']);
                $res[$key]['end_time']=date('Y/m/d',$value['end_time']);
                // $res[$key]['status']=$statusArr[$value['status']];
                $res[$key]['id']=$value['id'];
                $res[$key]['bonus_invest_min']=$value['bonus_invest_min'];
            }
            $json['list'] = $res;
            $json['tips'] = "成功";
            $json['status'] = "1";
        }else{
            $json['list'] = array();
            $json['tips'] = "暂无信息";
            $json['status'] = "1";
        }
        outJson($json);
    }
    /*
    * 删除红包
    */
    public function delbonus(){
        $jsons['status'] = "0";
        $uid = $_REQUEST['uid'];
        $id = intval($_REQUEST['id']) ? intval($_REQUEST['id']) : "";
        if(empty($id)){
            $jsons['tips'] = '填写完整信息！';
            outJson($jsons);
        }
        $r = M("member_bonus")->where("uid=" . $uid . " and id = ".$id)->find();
        if(empty($r)){
            $jsons['tips'] = '红包不存在！';
            outJson($jsons);
        }
        $new = M("member_bonus")->where("uid=" . $uid . " and id = ".$id)->delete();
        if($new){
            $jsons['status'] = "1";
            $jsons['tips'] = '红包删除成功！';
            outJson($jsons);
        }else{
            $jsons['tips'] = '红包删除失败！';
            outJson($jsons);
        }
    }
    /*
    *   平台数据
    */
    public function platform_data(){
        $common = (time()-strtotime('2016-01-01'));
        $a = floor($common/86400/360);    //整数年
        $date=floor(((time()-strtotime('2016-01-01'))/86400)-$a*360);
        $hour=floor((time()-strtotime('2016-01-01'))%86400/3600);
        $jsons['operation_time'] = $a."年".$date."天".$hour."小时";
        //累计成交金额
        $jsons['ljcjje']=getDataCount(4);
        //待收总额
        $jsons['ljdsze']=getDataCount(6);
        //累计收益
        $jsons['ljsy']=getDataCount(3);
        //交易笔数
        $jsons['jybs']=intval(getDataCount(2));
        //投资人总数
        $jsons['tzrzs']=intval(getDataCount(5))-intval(getDataCount(15));
        //借款人总数
        $jsons['jkrzs']=intval(getDataCount(15));
        //逾期90天以上的金额
        $jsons['yqje']=getDataCount(16);
        //逾期90天以上的笔数
        $jsons['yqbs']=intval(getDataCount(17));

        $jsons['status'] = "1";
        outJson($jsons);
    }
    public function useraterule(){
        $info = '<span>1、加息券可用于加息券项目<br /></span>';
        outWeb($info);
    }
    public function withdrawtip(){
        $info = '1、尊敬的会员，提现操作涉及您的资金变动，请仔细核对您的提现信息<br>2、一般用户单日提现上限为30万元<br>3、涉及到您的资金安全，请仔细操作';
        outWeb($info);
    }
    /*
     * 用户提现
     */
    public function withdraw(){
        $pre = C('DB_PREFIX');
        
        // $tqfee = getfloatvalue($this->glo['fee_tqtx'],2);
        // $tqfee = explode("|",$this->glo['fee_tqtx']);
        $uid = $_REQUEST['uid'] or die;
        $withdraw_money = floatval($_REQUEST['amount']) or die;
        $pwd = md5(text($_REQUEST['txtPassword'])) or die;
        
        $bank_num = M('member_banks')->where("uid='{$uid}'")->find();
        if($bank_num['bank_num'] == '') {
            $jsons["status"]       = "0";
            $jsons["tips"]       = "请先绑定银行卡";
            outJson($jsons);
        }
        
        $vm = getMinfo($this->uid,'m.pin_pass');
        if(empty($vm['pin_pass'])){
            $vo = M('members m')->field('mm.account_money,mm.back_money,m.user_leve,m.time_limit')->join("{$pre}member_money mm on mm.uid = m.id")->where("m.id={$uid} AND (m.user_pass='{$pwd}')")->find(); 
        }else{
            $vo = M('members m')->field('mm.account_money,mm.back_money,m.user_leve,m.time_limit')->join("{$pre}member_money mm on mm.uid = m.id")->where("m.id={$uid} AND (m.pin_pass='{$pwd}')")->find(); 
        }        
        
        if(empty($_REQUEST['txtPassword']) || !is_array($vo)) {
            $jsons["status"]       = "0";
            $jsons["tips"]       = "支付密码错误";
            outJson($jsons);
        }
        $all_money=$vo['account_money'];
        if($vo['account_money']<$withdraw_money) {
            $jsons["status"]       = "0";
            $jsons["tips"]       = "账户余额不足";
            outJson($jsons);
        }



        $start = strtotime(date("Y-m-d",time())." 00:00:00");
        $end = strtotime(date("Y-m-d",time())." 23:59:59");
        $wmap['uid'] = $this->uid;
        $wmap['withdraw_status'] = array("neq",3);
        $wmap['add_time'] = array("between","{$start},{$end}");
        $today_money = M('member_withdraw')->where($wmap)->sum('withdraw_money');   
        $today_time = M('member_withdraw')->where($wmap)->count('id');  
        $tqfee = explode("|",$this->glo['fee_tqtx']);
        /*
        ：   1、提现次数：每天可以提现10次
            2、提现上下限：1元<=提现金额<=1000000元
            3、提现手续费：2元/笔
            4、未投资手续费：未投资的部分另外扣取千分之3的手续费
        */
        $today_limit = $tqfee[0];       
        if(!empty($today_limit) && $today_time>=$today_limit){
            $message = "一天最多只能提现{$today_limit}次";
            $jsons["status"]       = "0";
            $jsons["tips"]       = $message;
            outJson($jsons);
        }
        $one_limit_min = $tqfee[1];
        $one_limit_max = $tqfee[2];

        if($withdraw_money < $one_limit_min || $withdraw_money > $one_limit_max*10000){
            $jsons["status"]       = "0";
            $jsons["tips"]       = "单笔提现金额限制为{$one_limit_min}-{$one_limit_max}万元";
            outJson($jsons);
        }
        //获取充值未投资充值部分
        $accountIn = M('member_money_account_in')->field('sum((money_in - money_out)) as money')->where("uid = '{$this->uid}' and status in(1,2) and money_in_type in(0,1,2)")->order('id asc')->find();        
        $noInvestMoney = isset($accountIn['money']) ? $accountIn['money'] : '0.00';
        $noInvestMoney = $noInvestMoney > $vo['account_money'] ? $vo['account_money'] : $noInvestMoney;     
        $noNeedFeeMoney = $vo['account_money'] - $noInvestMoney;
        $needFeeMoney = $noNeedFeeMoney > $withdraw_money ? '0.00' : bcsub($withdraw_money, $noNeedFeeMoney, 2);
        $uInfo = M('members')->field('user_name')->find($uid);
        // $fee = getfloatvalue($tqfee[3],2);
        $fee = 0.00;     
        // $fee += $needFeeMoney * getfloatvalue($tqfee[3],2) / 1000;  
        $moneydata['withdraw_money'] = $withdraw_money;
        $moneydata['withdraw_fee'] = 0;
        $moneydata['second_fee'] = $fee;
        $moneydata['second_money'] = $withdraw_money;
        $moneydata['withdraw_status'] = 0;
        $moneydata['uid'] =$this->uid;
        $moneydata['add_time'] = time();
        $moneydata['add_ip'] = get_client_ip();         
        $newid = M('member_withdraw')->add($moneydata); 
        if($newid){
            memberMoneyLog($uid,4,-$withdraw_money,"提现，手续费".$fee."元",'0','@网站管理员@',-($fee));
            MTip('chk6',$this->uid);
            $jsons["status"]       = "1";
            $jsons["tips"]       = "提现申请成功";
            outJson($jsons);         
        }else{
            $jsons["status"]       = "0";
            $jsons["tips"]       = "提现申请失败";
            outJson($jsons);
        }
    }
    /**
    * 修改密码
    * @param $uid
    */
   public function change_pass()
    {
        $uid = $_REQUEST['uid'] or die;
        $old = !empty($_REQUEST['oldpassword']) ? md5($_REQUEST['oldpassword']) : die;
        $newpwd = !empty($_REQUEST['newpassword']) ? md5($_REQUEST['newpassword']) : die;
        $newpwd1 = !empty($_REQUEST['newpasswordre']) ? md5($_REQUEST['newpasswordre']) : die;
        if ($newpwd != $newpwd1) {
            $jsons["tips"] = "两次密码输入不一样";
            outJson($jsons);
        }
        $user_pass = M('members')->getFieldById($uid, 'user_pass');
        if ($old != $user_pass) {
            $jsons["tips"] = "原密码错误，请重新输入";
            outJson($jsons);
        }
        if ($old == $newpwd) {
            $jsons["tips"] = "设置失败，请勿让新密码与老密码相同";
            outJson($jsons);
        }
        $newid = M('members')->where("id={$uid}")->setField('user_pass', $newpwd1);
        if ($newid) {
            MTip('chk1', $uid);
            $jsons["status"] = '1';
            $jsons["tips"] = '修改成功';
            outJson($jsons);
        }
        $jsons["tips"] = "操作失败，请联系客服！";
        outJson($jsons);
    }
    
    /**
    * 修改支付密码
    * @param $uid
    */
    public function change_pinpass()
    {
        $uid = $_REQUEST['uid'] or die;
        $old = !empty($_REQUEST['oldpassword']) ? md5($_REQUEST['oldpassword']) : die;
        $newpwd = !empty($_REQUEST['newpassword']) ? md5($_REQUEST['newpassword']) : die;
        $newpwd1 = !empty($_REQUEST['newpasswordre']) ? md5($_REQUEST['newpasswordre']) : die;
        if ($newpwd != $newpwd1) {
            $jsons["tips"] = "两次密码输入不一样";
            outJson($jsons);
        }
        $members = M('members')->find($uid);
        if ($old == $newpwd1) {
            $jsons["tips"] = "设置失败，请勿让新密码与老密码相同";
            outJson($jsons);
        }
        if (empty($members['pin_pass'])) {
            if ($members['user_pass'] == $old) {
                $newid = M('members')->where("id={$uid}")->setField('pin_pass', $newpwd1);
                if ($newid) {
                    $jsons['status'] = "1";
                    outJson($jsons);
                } else {
                    $jsons["tips"] = "设置失败，请重试";
                    outJson($jsons);
                }
            } else {
                $jsons["tips"] = "原支付密码错误，请重试！";
                outJson($jsons);
            }
        } else {
            if ($members['pin_pass'] == $old) {
                $newid = M('members')->where("id={$uid}")->setField('pin_pass', $newpwd1);
                if ($newid) {
                    $jsons["status"] = '1';
                    $jsons["tips"] = '修改成功';
                    outJson($jsons);
                }
            } else {
                $jsons["tips"] = "原支付密码错误，请重试！";
                outJson($jsons);
            }
        }
        $jsons["tips"] = "操作失败，请联系客服！";
        outJson($jsons);
    }
    /*
    *   我的邀请
    */
    public function promotion_friend(){
        $p = intval($_REQUEST['p']) ? intval($_REQUEST['p']) : 1;
        $uid = $_REQUEST['uid'] or die;

        $hy_num = M("members m")->where("m.recommend_id ={$uid}")->count();
        $all = M('members')->getFieldById($uid, 'reward_money');
        $jsons['jjjl'] = $all;
        $jsons['tjrs'] = isset($hy_num) ? $hy_num : 0;
        $jsons['status'] = "1";
        import("ORG.Util.Page");
        $count = M("members")->where("recommend_id ={$uid}")->count('id');
        $page = new Page($count, 15);
        $page->show();
        $pageSet['nowPage'] = strval($p);
        $pageSet['totalPages'] = strval(intval($page->totalPages));
        $pageSet['totalRows'] = strval($page->totalRows);
        $pageSet['pageSize'] = strval(15);
        $Lsql = "{$page->firstRow},{$page->listRows}";


        $members_list = M("members")->field("user_name,FROM_UNIXTIME(reg_time) as reg_time")->where("recommend_id ={$uid}")->order('id DESC')->limit($Lsql)->select();
                writeLog($members_list);
        foreach ($members_list as $key => $value) {
            if($value['reg_time'] >= strtotime("2018-07-18 00:00:00")){
                $members_list[$key]['reward'] = '1%的加息劵';
            }else{
                $members_list[$key]['reward'] = '';
            }
        }
        $jsons['page'] = $pageSet;
        if (ceil($count / 15) < $_REQUEST["p"]) {
            $jsons["status"] = 1;            
            $jsons['members_list'] = array();
            $jsons["tips"] = "已经是最后一页啦";
            outJson($jsons);
        }
        $jsons['members_list'] = (array)$members_list;
        outJson($jsons);
    }
    public function promotion(){
        $uid = $_REQUEST['uid'] or die;
        $jsons['promotion_link'] = $this->domainUrl.'/member/common/register?invite='.$uid;//邀请链接
        $jsons['incode'] = $uid;//邀请码
        $hy_num = M("members m")->where("m.recommend_id ={$uid}")->count();
        $jsons['innums'] = isset($hy_num) ? $hy_num : 0;//邀请人数
        $jsons['logo'] = $this->domainUrl.'/Public/default/Home/images/logo.png';
        $jsons['status'] = "1";
        //调用查看结果  
        $files =  C("APP_ROOT").'../Public/qrcode/member_'.$uid.'.png';
        if(!file_exists($files)){
            $url = $this->domainUrl.'/member/common/register?invite='.$uid;
            $httpurl = self::makeQRcode($url);
        }

        $jsons['qrcode'] = $this->domainUrl.'/Public/qrcode/member_'.$uid.'.png';
        outJson($jsons);
    }
    public function promotiontips(){
         $info = '1、邀请好友注册并投资成功（不限投资时间、金额），双方可获得加息券一张。<br />2、每邀请1人并投标成功，邀请人即可获得奖励，无上限要求，邀请越多则奖励越多';
        outWeb($info);
    }
    function makeQRcode($data , $size=4 , $need_save = false , $file_name='' , $level='L')
    {
        include C("APP_ROOT")."/Lib/Phpqrcode/qrcode.php";  

        //本地文档相对路径
        $url = C("APP_ROOT").'../Public/qrcode/';

        //引入php QR库文件

        $value = $data;
        $errorCorrentionLevel = 'L'; //容错级别
        $matrixPoinSize = 6; //生成图片大小
        $a = time().'.png';
        //生成二维码,第二个参数为二维码保存路径
        QRcode::png($value,$url.$a,$errorCorrentionLevel,$matrixPoinSize,2);
        //如不加logo，下面logo code 注释掉，输出$url.qrcode.png即可。
        $logo =$url.'logo.png'; //logo
        $QR = $url.$a; //已经生成的二维码

        if($logo !== FALSE){
        $QR = imagecreatefromstring(file_get_contents($QR));
        $logo = imagecreatefromstring(file_get_contents($logo));
        $QR_width = imagesx($QR);//二维码图片宽度
        $QR_height = imagesy($QR);//二维码图片高度
        $logo_width = imagesx($logo);//logo图片宽度
        $logo_height = imagesy($logo);//logo图片高度
        $logo_qr_width = $QR_width / 5;
        $scale = $logo_width/$logo_qr_width;
        $logo_qr_height = $logo_height/$scale;
        $from_width = ($QR_width - $logo_qr_width) / 2;
        //重新组合图片并调整大小
        imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
        $logo_qr_height, $logo_width, $logo_height);
        }

        //新图片
        $img = $url.'member_'.$this->uid.'.png';
        //输出图片
        imagepng($QR, $img);
        return $img;
    }
    
    
    public function actlogout(){
        $vo = array("id","user_name");
        foreach($vo as $v){
            session("u_{$v}",NULL);
        }
        cookie("Ukey",NULL);
        cookie("Ukey2",NULL);
        //uc登陆
        $loginconfig = FS("Webconfig/loginconfig");
        $uc_mcfg  = $loginconfig['uc'];
        if($uc_mcfg['enable']==1){
            require_once C('APP_ROOT')."Lib/Uc/config.inc.php";
            require C('APP_ROOT')."Lib/Uc/uc_client/client.php";
            $logout = uc_user_synlogout();
        }
        //uc登陆
        $jsons["status"] = '1';
        $jsons["tips"]   = "注销成功";   
        outJson($jsons);
    }
    public function about(){
        $jsons["status"] = '1';
        $jsons["version"]   = "v1.0.1";   
        $jsons["wx"]   = "673401805@qq.com";   
        $jsons["email"]   = "BD@tiannongjinrong.com";   
        $jsons["url"]   = "www.tiannongjinrong.com";   
        $jsons["tel"]   = "400-1031-191";   
        $jsons["bottom"]   = "客服工作时间（周一至周五 09:00~17:00）\n © Copyright(c)2016 天农众筹 .All Rights Reserved 天农众筹 粤ICP备17153534号-1 \n 众筹有风险 请谨慎众筹";   
        outJson($jsons);
    }
    public function support(){
        $jsons["status"] = '1';
        $uid = $_REQUEST['uid'] or die;
        $bid = $_REQUEST['bid'] or die;
        $binfo = M('borrow_info')->where('id='.$bid)->find();
        $bonus_list =  M('member_bonus')->where("uid='{$this->uid}' and status = 1 and (".time()." > start_time and ".time()." < end_time )")->field('id,money_bonus,bonus_invest_min')->select();
        $inrate_list =  M('member_interest_rate')->where("uid='{$this->uid}' and status = 1 and (".time()." > start_time and ".time()." < end_time )")->field('id,interest_rate')->select();
        $jsons['inrate_list'] = $inrate_list?$inrate_list:array();
        $jsons['bonus_list'] = $bonus_list?$bonus_list:array();
        $vm = getMinfo($this->uid,'m.pin_pass,mm.account_money,mm.back_money,mm.money_experience');
        $jsons['account_money'] = $vm['account_money'];
        $jsons['money_experience'] = $vm['money_experience'];

        $jsons['is_experience'] = $binfo['is_experience']?$binfo['is_experience']:0;
        $jsons['is_bonus'] = $binfo['is_bonus']?$binfo['is_bonus']:0;
        $jsons['is_memberinterest'] = $binfo['is_memberinterest']?$binfo['is_memberinterest']:0;
        $jsons['is_binpass'] = $binfo['password']?1:0;


        outJson($jsons);
    }

    public function withshow(){
        $uid = $_REQUEST['uid'] or die;
        $bank_num = M('member_banks')->where("uid='{$uid}'")->find();
        
        $bankname = C('BANK_NAME');
        $vm = getMinfo($this->uid,'m.pin_pass,mm.account_money,mm.back_money,mm.money_experience');
        $minfo['account_money'] = $vm['account_money'];
        if ($vm) {
           $jsons['status'] = "1";
           $jsons['account_money'] = $minfo['account_money'];
           $tqfee = explode("|",$this->glo['fee_tqtx']);  
           $fee = $tqfee[3]/1000;
           $jsons['fee_money'] = $fee;

           $today_limit = $tqfee[0];
           $one_limit_min = getfloatvalue($tqfee[1],2);
           $one_limit_max = getfloatvalue($tqfee[2]*10000,2);
           $jsons['feetips'] = "① 充值的资金经投资回款后，无提现手续费；\r\n② 充值的资金未经投资，在网站时间超过15日的，无提现手续费；\r\n③ 充值的资金未经投资，在网站时间未超过15日(含15日)的，提现金额的".$fee."手续费；\r\n④ 平台最低提现金额为{$one_limit_min}元起。";
           if($bank_num['bank_num'] == ''){
               $jsons['status'] = "2";
               $jsons['tips'] = "请先绑定银行卡"; 
            }else{
               $jsons['bank_num'] = substr($bank_num['bank_num'], -4);
               $jsons['bank_name'] = $bankname[$bank_num['bank_name']];
               $jsons['bank_ico'] = $this->domainUrl . '/Public/bank/' .$bank_num['bank_name'].".png";  
            }
        } else {
           $jsons['status'] = "0";
           $jsons['tips'] = "获取用户信息失败！";
        }
         
        outJson($jsons);
    }
    public function limitbank(){
        $array = array(
            array('bank'=>'中国银行','binfo'=>'单笔99万，单日99万，单月无限额','bank_ico'=>$this->domainUrl . '/Public/bank/BOCSH.png'),
            array('bank'=>'招商银行','binfo'=>'单笔99万，单日99万，单月无限额','bank_ico'=>$this->domainUrl . '/Public/bank/CMB.png'),
            array('bank'=>'工商银行','binfo'=>'单笔99万，单日99万，单月无限额','bank_ico'=>$this->domainUrl . '/Public/bank/ICBC.png'),
            array('bank'=>'建设银行','binfo'=>'单笔99万，单日99万，单月无限额','bank_ico'=>$this->domainUrl . '/Public/bank/CCB.png'),
        );
        $jsons['status'] = "1";
        $jsons['limitlist'] =  $array;
        outJson($jsons);
    }


    public function artrule(){
        $info = '1、邀请好友注册并投资成功（不限投资时间、金额），双方可获得加息券一张。<br />2、每邀请1人并投标成功，邀请人即可获得奖励，无上限要求，邀请越多则奖励越多';
        outWeb($info);
    }
    public function bill(){

        $log_type = !empty($_REQUEST['log_type']) ? $_REQUEST['log_type'] : '';
        // $p = !empty($_REQUEST['p']) ? $_REQUEST['p'] : '1';
        $map['uid'] = $this->uid;
        if(!empty($_REQUEST['log_type'])){
            if(intval($_REQUEST['log_type']) == 11){
                $map['type'] = 9;
            }else{
                $map['type'] = intval($_REQUEST['log_type']);
            }
        }else{
            $map['type'] = array('in','3,29,15,20,11,9');
        }
        $list = getMoneyLog($map,1000);
        $lst = array();
        $i=0;
        $array=array(
            3=>'充值',29=>'提现', 15=>'投标', 20=>'奖励', 11=>'回款'
            );
        foreach ($list['list'] as $key => $value) {
            $t = date('Y-m',$value['add_time']);
            $lst[$t]['date'] = date('Y年m月',$value['add_time']);
            $lst[$t]['list'][$i]['add_time'] = date('Y-m-d H:i:s',$value['add_time']);
            $lst[$t]['list'][$i]['info'] = $value['type'];
            $lst[$t]['list'][$i]['money'] = $value['affect_money']>0?'+￥'.$value['affect_money']:'-￥'.str_replace("-", "", $value['affect_money']);
            $lst[$t]['list'][$i]['typeid'] = $value['typeid'];
            $i++;
        }
        $i=0;
        foreach ($lst as $k => $val) {
            $lsts[$i]['date'] = $val['date'];
            foreach ($val['list'] as $k1 => $val1) {
                $lsts[$i]['list'][] = $val1;
            }
            $i++;
        }
        $jsons['status'] = "1";
        $jsons['list'] = $lsts?$lsts:array();
        // $jsons['page'] = $list['_page'];
        outJson($jsons);
    }
    public function bills(){
        $array=array(
            3=>'充值',29=>'提现', 15=>'投标', 20=>'奖励', 11=>'回款'
            );
        $log_type = !empty($_REQUEST['log_type']) ? $_REQUEST['log_type'] : '';
        $p = !empty($_REQUEST['p']) ? $_REQUEST['p'] : '1';
        $map['uid'] = $this->uid;
        if(!empty($_REQUEST['log_type'])){
            $map['type'] = intval($_REQUEST['log_type']);
        }else{
            $map['type'] = array('in','3,29,15,20,11');
        }
        $list = getMoneyLog($map,20);
        $lst = array();
        $i=0;
        foreach ($list['list'] as $key => $value) {
            $t = date('Y-m',$value['add_time']);
            $lst[$t]['date'] = date('Y年m月',$value['add_time']);
            $lst[$t]['list'][$i]['add_time'] = date('Y-m-d H:i:s',$value['add_time']);
            $lst[$t]['list'][$i]['info'] = $value['type'];
            $lst[$t]['list'][$i]['money'] = $value['affect_money']>0?'+￥'.$value['affect_money']:'-￥'.str_replace("-", "", $value['affect_money']);
            $lst[$t]['list'][$i]['typeid'] = $value['typeid'];
            $i++;
        }

        $i=0;
        foreach ($lst as $k => $val) {
            $lsts[$i]['date'] = $val['date'];
            foreach ($val['list'] as $k1 => $val1) {
                $lsts[$i]['list'][] = $val1;
            }
            $i++;
        }
        $jsons['status'] = "1";
        $jsons['list'] = $lsts;
        $jsons['page'] = $list['_page'];

        outJson($jsons);
    }
    function binary_to_file($file){
        $content = $GLOBALS['HTTP_RAW_POST_DATA'];  // 需要php.ini设置
        if(empty($content)){
            $content = file_get_contents('php://input');    // 不需要php.ini设置，内存压力小
        }
        $ret = file_put_contents($file, $content, true);
        return $ret;
    }
    
    
    public function feedback(){
        $jsons["status"] = '0';
        $msg = $_REQUEST['opinion'];
        $user_phone = $_REQUEST['user_phone'];
        $img1 = '';
        $img2 = '';

        if(!empty($_FILES['img1']['name'])){
            // $now =$_SERVER['REQUEST_TIME'];  
            $this->saveRule = 'uniqid';
            $this->savePathNew = 'UF/Uploads/feedback/';
            $info = $this->CUpload();
            $img1 = $info[0]['savepath'].$info[0]['savename'];

        }
        if(!empty($_FILES['img2']['name'])){

            // $this->saveRule = time().rand(0,1000);
            // $this->savePathNew = 'UF/Uploads/feedback/';
            // $info = $this->CUpload();
            $img2 = $info[1]['savepath'].$info[1]['savename'];

        }
        if(empty($user_phone)){
             $jsons["tips"]   = "联系电话必填！"; 
             outJson($jsons); 
        }
        // $uInfo = M('members')->find($this->uid);
        $addData = array(
            'type'     => 3,
            'contact'  => $user_phone,
            'msg'      => $msg,
            'ip'       => get_client_ip(),
            'add_time' => time(),
            'status'   => 0,
            'img1'   => $img1,
            'img2'   => $img2
            );
        $rs = M('feedback')->add($addData);
        if($rs){
            $jsons["status"] = '1';
            $jsons["tips"]   = "提交成功，网站工作人员将会及时联系您！";
            outJson($jsons);  
        }else{
            $jsons["tips"]   = "提交失败，请稍后重试！";  
            outJson($jsons);
        }
        
    }
    public function qqshow(){
        $jsons["status"] = '1';
        $jsons["qq"]   = '1870664219';
        outJson($jsons);  
    }
    public function uploads(){

        // $base64_image_content = $_REQUEST["fileimg"];
     //    $path="UF/Uploads/feedback/";
     //    if($base64_image_content){
     //        $new_file = $path.date('YmdHis').rand(1000,9999).".jpg";
     //        file_put_contents($new_file, base64_decode($base64_image_content));
     //        $url="UF/Uploads/feedback/".date('YmdHis').rand(1000,9999).".jpg";
     //        $jsons["status"] = '1';
     //        $jsons["imgurl"]   = $url;
     //        outJson($jsons);
     //    }else{
     //     $jsons["status"] = '0';
     //        $jsons["tips"]   = '图片上传失败';
     //        outJson($jsons);
     //    }
    }
    public function reg_service(){
        $article = M('article_category')->where("id=28")->find();
        $jsons["content"] = $article['type_content'];
        outWeb($jsons['content']);
    }
    public function risk_warning(){
        $info = '在您通过平台进行资金出借的过程中，也许会面临以下可能导致您出借资金及收益受损的各种风险。请您在进行资金出借前仔细阅读以下内容，了解融资项目信贷风险，确保自身具备相应的投资风险意识、风险认知能力、风险识别能力和风险承受能力，拥有非保本类金融产品投资的经历并熟悉互联网，并请您根据自身的风险承受能力选择是否出借资金及出借资金的数额。<br />1. 法律及监管风险：因许多法律和法规相对较新且可能发生变化，相关官方解释和执行可能存在较大不确定性等因素引起的风险。有些新制定的法律、法规或修正案的生效可能被延迟；有些新颁布或生效的法律法规可能具有追溯力从而对您的投资利益造成不利影响。<br />2. 政策风险：因国家宏观政策、财政政策、货币政策、监管导向、行业政策、地区发展政策等因素引起的风险。<br />3. 市场风险：因市场资金面紧张或利率波动、行业不景气、企业效益下滑等因素引起的风险。<br />4. 技术风险：由于无法控制和不可预测的系统故障、设备故障、通讯故障、电力故障、网络故障、黑客或计算机病毒攻击、以及其它因素，可能导致平台出现非正常运行或者瘫痪，由此导致您无法及时进行查询、充值、出借、提现等操作。<br />5. 不可抗力风险：由于战争、动乱、自然灾害等不可抗力因素的出现而可能导致您出借资金损失的风险。';
        outWeb($info);
    }
    public function setavatar(){      
        $uid= intval($_REQUEST["uid"]);

        if(!empty($_FILES['img']['name'])){
            

            $this->saveRule = 'uniqid';
            $this->savePathNew = '/UF/Uploads/avatar/';
            $info = $this->CUpload();
            $img1 = $info[0]['savepath'].$info[0]['savename'];
            $data['avatar']=$img1;
            M('members')->where('id='.$uid)->save($data);
            writeLog($img1);
        }else{
             $jsons["status"] = '0';
            $jsons["tips"]   = "上传头像失败！";
            $jsons["url"]   = $this->domainUrl ."/Style/header/images/noavatar_big.gif";
            outJson($jsons);  
        }
        $jsons["url"]   =$this->domainUrl . $img1;
        $jsons["status"] = '1';
        $jsons["tips"]   = "上传头像成功！";
        outJson($jsons);  

    }
    public function paybank(){
        $jsons['bank_list'] = array('中国工商银行','中国农业银行','中国银行','中国建设银行','中国邮政储蓄银行','中信银行','中国光大银行','兴业银行');
        $jsons['status'] = 1;
        outJson($jsons);

        
    }

    public function charge_sign_pay(){

        header('Content-Type: text/html; Charset=UTF-8');
        $uid = $this->uid;
        require_once C('APP_ROOT') . "Lib/AllinpayInter/libs/ArrayXml.class.php";
        require_once C('APP_ROOT') . "Lib/AllinpayInter/libs/cURL.class.php";
        require_once C('APP_ROOT') . "Lib/AllinpayInter/libs/PhpTools.class.php";

        $tools=new PhpTools();
        $minf = M('member_info')->where('uid='.$uid)->find();
        $bank_name = $_REQUEST['bank_name'];
        $account_no = $_REQUEST['account_no'];
        $account_name = $minf['real_name'];
        $idcard = $minf['idcard'];
        $tel = $_REQUEST['tel'];

        $mi['bank_name']=$bank_name;
        $mi['account_no']=$account_no;
        M('member_info')->where('uid='.$uid)->save($mi);
        $bank_list = array(
            '中国工商银行'          =>  '0102'    ,
            '中国农业银行'          =>  '0103'    ,
            '中国银行'        =>  '0104'    ,
            '中国建设银行'          =>  '0105'    ,
            '中国邮政储蓄银行'        =>  '0403'    ,
            '中信银行'        =>  '0302'    ,
            '中国光大银行'          =>  '0303'    ,
            '兴业银行'        =>  '0309'    
        );
        $bank_code = $bank_list[$bank_name]; 
        $orid = time().rand(10000,99999);
        $params = array(
            'INFO' => array(
                'TRX_CODE' => '310001',
                'VERSION' => '04',
                'DATA_TYPE' => '2',
                'LEVEL' => '5',
                'MERCHANT_ID' => $this->merchant_id,
                'USER_NAME' => $this->user_name,
                'USER_PASS' => $this->user_pass,
                'REQ_SN' => $orid,
            ),
            'FAGRA' => array(
                'MERCHANT_ID' => $this->merchant_id,
                'BANK_CODE' => $bank_code,          //银行代码
                'ACCOUNT_TYPE' => '00',         //账号类型
                'ACCOUNT_NO' => $account_no,  //账号
                'ACCOUNT_NAME' => $this->characet($account_name),  //账号名
                'ACCOUNT_PROP' => '0',      //账号属性  私人
                'ID_TYPE' => '0',       //开户证件类型 身份证
                'ID'=>$idcard,                //证件号
                'TEL'=>$tel,                //手机号
                'CVV2'=>'',                //CVV2
                'VAILDDATE'=>'',                //有效期
                'MERREM'=>'',                //商户保留信息
                'REMARK' => $orid,
            ),
        );
        $result = $tools->send($params);
        writeLog($result);
        if($result['AIPG']['INFO']['RET_CODE']=="0000" && $result['AIPG']['FAGRARET']['RET_CODE']=="0000"){
            $jsons['status']=1;
            $jsons['tips']= "请求成功";
            $jsons['ordid']= $result['AIPG']['INFO']['REQ_SN'];;
        }else{
            $jsons['status']=0;
            $jsons['tips']= "请求失败";
            $jsons['ordid']=0;
        }
        outJson($jsons);

        
    }
    //2.1.2 协议支付签约
    public function payment_sign(){
        header('Content-Type: text/html; Charset=UTF-8');
        $uid = $this->uid;
        $ordid= $_REQUEST["ordid"];
        $vercode= $_REQUEST["vercode"];
        if($ordid == 0 || empty($ordid)){
            $jsons['status']=0;
            $jsons['tips']= "原请求流水号不能为空";
            outJson($jsons);
        }
        require_once C('APP_ROOT') . "Lib/AllinpayInter/libs/ArrayXml.class.php";
        require_once C('APP_ROOT') . "Lib/AllinpayInter/libs/cURL.class.php";
        require_once C('APP_ROOT') . "Lib/AllinpayInter/libs/PhpTools.class.php";

        $tools=new PhpTools();
        $orid = time().rand(10000,99999);
        $params = array(
            'INFO' => array(
                'TRX_CODE' => '310002',
                'VERSION' => '04',
                'DATA_TYPE' => '2',
                'LEVEL' => '5',
                'MERCHANT_ID' => $this->merchant_id,
                'USER_NAME' => $this->user_name,
                'USER_PASS' => $this->user_pass,
                'REQ_SN' => $orid,
            ),
            'FAGRC' => array(
                'MERCHANT_ID' => $this->merchant_id,
                'SRCREQSN' => $ordid,          //原请求流水
                'VERCODE' => $vercode,         //账号类型
            ),
        );
        $result = $tools->send($params);
        writeLog($result);
        if($result['AIPG']['INFO']['RET_CODE']=="0000" && $result['AIPG']['FAGRCRET']['RET_CODE']=="0000" ){
            $data['agrmno']=$result['AIPG']['FAGRCRET']['AGRMNO'];
            M('members')->where('id='.$uid)->save($data);
            $jsons['status']=1;
            $jsons['tips']= "请求签约成功";
        }else{
            $jsons['status']=0;
            $jsons['tips']= "请求签约失败";
        }
        outJson($jsons);
    }

    //2.1.4 协议支付
    public function charge(){
        header('Content-Type: text/html; Charset=UTF-8');
        $uid = $this->uid;
        $minf = M('member_info')->where('uid='.$uid)->find();
        $account_name= $minf["real_name"];
        $amount= $_REQUEST["amount"];
        $orid = time().rand(10000,99999);

        $data['money'] = getFloatValue($amount,2);
        $data['fee'] = 0;
        $data['add_time'] = time();
        $data['add_ip'] = get_client_ip();
        $data['status'] = 0;
        $data['uid'] = $uid;
        $data['nid'] = $orid;
        $data['way'] = "allinpayapp";
        M("member_payonline" )->add( $data );


        $minfo = m('members')->find($uid);
        require_once C('APP_ROOT') . "Lib/AllinpayInter/libs/ArrayXml.class.php";
        require_once C('APP_ROOT') . "Lib/AllinpayInter/libs/cURL.class.php";
        require_once C('APP_ROOT') . "Lib/AllinpayInter/libs/PhpTools.class.php";

        $tools=new PhpTools();
        
        $params = array(
            'INFO' => array(
                'TRX_CODE' => '310011',
                'VERSION' => '04',
                'DATA_TYPE' => '2',
                'LEVEL' => '5',
                'MERCHANT_ID' => $this->merchant_id,
                'USER_NAME' => $this->user_name,
                'USER_PASS' => $this->user_pass,
                'REQ_SN' => $orid,
            ),
            'FASTTRX' => array(
                'BUSINESS_CODE' => '12301',             //业务代码
                'MERCHANT_ID' => $this->merchant_id,
                'SUBMIT_TIME' => date('YmdHis'),          //提交时间
                'AGRMNO' => $minfo['agrmno'],         //签约时返回的协议号
                'ACCOUNT_NO' => '',         //账号
                'ACCOUNT_NAME' => $account_name,         //账号名
                'AMOUNT' => $amount*100,         //金额
                'CURRENCY' => 'CNY',         //金额
                'ID_TYPE' => '',         //开户证件类型
                'ID' => '',         //证件号
                'TEL' => '',         //手机号
                'CVV2' => '',         //手机号
                'VAILDDATE' => '',         //手机号
                'CUST_USERID' => '',         //手机号
                'SUMMARY' => '',         //手机号
                'REMARK' => '',         //手机号
            ),
        );
        $result = $tools->send($params);
        writeLog($result);
        if($result['AIPG']['INFO']['RET_CODE']=="0000"){
            if($result['AIPG']['FASTTRXRET']['RET_CODE']=="0000"){
                $ordid=$result['AIPG']['INFO']['REQ_SN'];
                $done = $this->payDone(1,$ordid,$ordid);
                $jsons['status']=1;
                $jsons['tips']= "支付成功";  
            }else{
                $jsons['status']=0;
                $jsons['tips']= $result['AIPG']['FASTTRXRET']['ERR_MSG'];
            }
        }else{
            $jsons['status']=0;
            $jsons['tips']= "支付失败";
        }
        outJson($jsons);
    }
    //2.1.3协议支付解约
    public function cancel(){
        header('Content-Type: text/html; Charset=UTF-8');
        $uid = $this->uid;
        $minf = M('member_info')->where('uid='.$uid)->find();
        $account_no= $minf["account_no"];
        $amount= $_REQUEST["amount"];
        $orid = time().rand(10000,99999);



        $minfo = m('members')->find($uid);
        require_once C('APP_ROOT') . "Lib/AllinpayInter/libs/ArrayXml.class.php";
        require_once C('APP_ROOT') . "Lib/AllinpayInter/libs/cURL.class.php";
        require_once C('APP_ROOT') . "Lib/AllinpayInter/libs/PhpTools.class.php";

        $tools=new PhpTools();
        
        $params = array(
            'INFO' => array(
                'TRX_CODE' => '310003',
                'VERSION' => '04',
                'DATA_TYPE' => '2',
                'LEVEL' => '5',
                'MERCHANT_ID' => $this->merchant_id,
                'USER_NAME' => $this->user_name,
                'USER_PASS' => $this->user_pass,
                'REQ_SN' => $orid,
            ),
            'FAGRCNL' => array(
                'MERCHANT_ID' => $this->merchant_id,            //业务代码
                'ACCOUNT_NO' => $account_no,         //账号
                'AGRMNO' => $minfo['agrmno'],         //签约时返回的协议号
            ),
        );
        $result = $tools->send($params);
        writeLog($result);
        if($result['AIPG']['INFO']['RET_CODE']=="0000"){
            if($result['AIPG']['FAGRCNLRET']['RET_CODE']=="0000"){
                $datas['agrmno']='';
                M('members')->where('id='.$uid)->save($datas);
                $mi['bank_name']='';
                $mi['account_no']='';
                M('member_info')->where('uid='.$uid)->save($mi);
                $jsons['status']=1;
                $jsons['tips']= "解约成功";  

            }else{
                $jsons['status']=0;
                $jsons['tips']= $result['AIPG']['FAGRCNLRET']['ERR_MSG'];
            }
        }else{
            $jsons['status']=0;
            $jsons['tips']= "解约失败";
        }
        outJson($jsons);
    }
    public function characet($data){
        if(!empty($data)){
            $fileType = mb_detect_encoding($data , array('UTF-8','GBK','GB2312','LATIN1','BIG5')) ;
            if( $fileType != 'UTF-8'){
                $data = mb_convert_encoding($data ,'UTF-8' , $fileType);
            }
        }
       return $data;
     }
    private function payDone($status,$nid,$oid){
        $done = false;
        $Moneylog = D('member_payonline');
        if($this->locked) return false;
        $this->locked = true;
        
        switch($status){
            case 1:
                $updata['status'] = $status;
                
                $updata['tran_id'] = $oid;
                
                $vo = M('member_payonline')->field('uid,money,fee,status')->where("nid='{$nid}'")->find();
                if($vo['status']!=0 || !is_array($vo)) return;
                $xid = $Moneylog->where("uid={$vo['uid']} AND nid='{$nid}'")->save($updata);
                //print_r($xid);
                $tmoney = floatval($vo['money'] - $vo['fee']);
                if($xid) $newid = memberMoneyLog($vo['uid'],3,$tmoney,"充值订单号:".$oid,0,'@网站管理员@');//更新成功才充值,避免重复充值
                

                if($newid){
                 $u=M("member_moneylog")->where("uid=".$vo['uid']." and type=50")->count();
                  if($u==0){
                      $frist_money = getFloatValue($this->glo['frist_money'],2);
                      $jiangli = getFloatValue($frist_money*$tmoney/100,2);
                      if(!empty($jiangli)){
                          memberMoneyLog($vo['uid'],50,$jiangli,"首次线上充值奖励",0,'@网站管理员@');//首次充值奖励
                      }
                  }
                }
                 
                if(!$newid){
                    $updata['status'] = 0;
                    $Moneylog->where("uid={$vo['uid']} AND nid='{$nid}'")->save($updata);
                    return false;
                }
                $vx = M("members")->field("user_phone,user_name")->find($vo['uid']);
                SMStip("payonline",$vx['user_phone'],array("#USERNAME#","#MONEY#"),array($vx['user_name'],$vo['money']));
            break;
            case 2:
                $updata['status'] = $status;
                $updata['tran_id'] = text($oid);
                $xid = $Moneylog->where("uid={$vo['uid']} AND nid='{$nid}'")->save($updata);
            break;
            case 3:
                $updata['status'] = $status;
                $xid = $Moneylog->where("uid={$vo['uid']} AND nid='{$nid}'")->save($updata);
            break;
        }
        
        if($status>0){
            if($xid) $done = true;
        }
        $this->locked = false;
        return $done;
    }
    public function is_signpay(){
        $uid = $this->uid;
        $minfo =  M('members')->where('id='.$uid)->find();
        $mstatus =  M('members_status')->where('uid='.$uid)->find();
        if($mstatus['id_status'] == 1){
            if($minfo['agrmno']){
                $jsons['is_sign'] = 1;
                $jsons['status'] = 1;
                $jsons['agrmno'] = $minfo['agrmno'];
            }else{
                $jsons['is_sign'] = 0;
                $jsons['status'] = 1;
                $jsons['agrmno'] = 0;
            }
            
        }else{
            $jsons['is_sign'] = 0;
            $jsons['status'] = 2;
            $jsons['agrmno'] = 0; 
        }
        outJson($jsons);
    }
    public function payminfo(){
        $uid = $this->uid;
        $minfo =  M('member_info')->where('uid='.$uid)->find();
        $mmoney =  M('member_money')->where('uid='.$uid)->find();
        $jsons['status'] = 1;
        $jsons['bank_name'] = $minfo['bank_name'];
        $jsons['bank_tips'] = "单笔限额10万，单日限额20万";
        $jsons['account_no'] = $minfo['account_no'];
        $jsons['amoney'] = $mmoney['account_money'];
        $bank_list = array(
            '中国工商银行'          =>  'ICBC'    ,
            '中国农业银行'          =>  'ABC'    ,
            '中国银行'        =>  'BOCSH'    ,
            '中国建设银行'          =>  'CCB'    ,
            '中国邮政储蓄银行'        =>  'PSBC'    ,
            '中信银行'        =>  'CNCB'    ,
            '中国光大银行'          =>  'CEB'    ,
            '兴业银行'        =>  'CIB'    
        );
        $bank_code = $bank_list[$minfo['bank_name']]; 
        $jsons['bank_ico'] = $this->domainUrl . '/Public/bank/' .$bank_code.".png";
        outJson($jsons);
    }
}
                