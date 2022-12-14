<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 5                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Original Author <author@example.com>                        |
// |          Your Name <you@example.com>                                 |
// +----------------------------------------------------------------------+
//
// $Id:$
//error_reporting("E_ALL & ~E_NOTICE");

/**
 * @funciton reg 注册
 * @funciton login 登录
 */
class ApiAction extends Action {
    var $domainUrl = "https://www.rzmwzc.com"; //.$_SERVER['SERVER_NAME'];
    var $glo = '';
    var $pre = '';
    var $payConfig = NULL;
    var $return_url = "";
    var $notice_url = "";
    var $member_url = ""; //支付的
    var $merchant_id = '200584000018725';
    var $user_name = '20058400001872504';
    var $user_pass = '111111';
    function _initialize() {
        $allow_origin = array(
            'houtai.rzmwzc.com',
            'rzmwzc.com',
            'www.rzmwzc.com',
        );
        //跨域访问的时候才会存在此字段
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        if (in_array($origin, $allow_origin)) {
            header('Access-Control-Allow-Origin:' . $origin);
        }
        header('Access-Control-Allow-Origin:*'); //允许跨域,不限域名
        header("Access-Control-Allow-Methods:POST,GET,OPTIONS");
        header('Access-Control-Allow-Headers: Content-Type'); // 设置允许自定义请求头的字段
        header('Access-Control-Allow-Credentials:true');
        $_POST = $_REQUEST = I('post.');
        Vendor('Jwt');
        $this->jwt = new jwt();
        //var_dump($this->jwt->getToken($_REQUEST["uid"]));exit;
        if ($_REQUEST["uid"]) {
            $this->uid = $_REQUEST["uid"] = $this->jwt->verifyToken($_REQUEST["uid"]);
        }
        // var_dump($_REQUEST["uid"]);exit;

    }
    public function __construct() {
        parent::__construct();
        // ini_set("display_statuss", "On");
        // error_reporting(E_ALL | E_STRICT);
        $this->pre = C('DB_PREFIX');
        header("Content-Type:text/html;charset=utf-8");
        $datag = get_global_setting();
        $this->glo = $datag; //供PHP里面使用
        $this->uid = intval(@$_REQUEST["uid"]);
        $this->domainUrl = $this->urls = "https://" . $_SERVER['SERVER_NAME'];
        $zl = M('borrow_info')->where("start_time <= UNIX_TIMESTAMP() and borrow_status = 1")->find();
        if ($zl) {
            $invest = M("borrow_info");
            //var_dump($invest);
            $invest->startTrans();
            $zdlist = M('borrow_info')->lock(true)->where("start_time <= UNIX_TIMESTAMP() and borrow_status = 1")->select();
            // 项目定时上线
            $changeCount = M('borrow_info')->where("start_time <= UNIX_TIMESTAMP() and borrow_status = 1")->setField('borrow_status', '2');
            $invest->commit();
        }
        if ($changeCount && $zdlist) {
            $bespeak = M("bespeak");
            $member_money = M("member_money");
            foreach ($zdlist as $k => $v) {
                $map["borrow_id"] = $v["id"];
                $map["bespeak_status"] = 0;
                $map["bespeak_point"] = 1;
                $tzlist = $bespeak->where($map)->select();
                foreach ($tzlist as $tk => $tv) {
                    $uid = $tv["bespeak_uid"];
                    $borrow_id = $tv["borrow_id"];
                    $money = $tv["bespeak_money"];
                    $yubi = $tv["yubi"];
                    $done = zinvestMoney($uid, $borrow_id, $money, $yubi);
                    if ($done === true) {
                        $bespeak->where("id = " . $tv["id"])->setField('bespeak_status', "1");
                    }
                }
            }
        }
    }
    private function hehe($uid) {
        $this->uid = $uid;
        $per = C('DB_PREFIX');
        $borrow_investor = M("borrow_investor");
        $borrow_investor->startTrans();
        $borrow_investor->lock(true)->field("id")->select();
        $borrowinvestor = M("borrow_investor bi")->join("{$per}borrow_info b ON bi.borrow_id=b.id")->field('bi.investor_capital,bi.id,b.borrow_interest_rate ,bi.investor_uid as uid,bi.id as iid,bi.add_time')->where("bi.investor_uid=" . $this->uid . "  and bi.borrow_id=1 and bi.status!=110")->find();
        if ($borrowinvestor['add_time'] + 86400 > time()) {
            return false;
        }
        //$receive_interest=  $borrowinvestor['investor_capital']*$borrowinvestor['borrow_interest_rate']/100/360;
        //echo  $borrowinvestor['borrow_interest_rate']/100/360;die;
        //echo ;die;
        //echo bcdiv(bcdiv($borrowinvestor['borrow_interest_rate'], 100, 2), 360);die;
        //echo bcdiv(bcdiv($borrowinvestor['borrow_interest_rate'], 100, 2), 360, 2);die;
        $receive_interest = bcadd($borrowinvestor['investor_capital'] * $borrowinvestor['borrow_interest_rate'] / 100 / 360, 0, 2);
        //echo M("borrow_investor bi") ->getlastsql();die;
        $money = M('member_money mm')->join("lzh_members m ON m.id=mm.uid")->where("m.id=" . $this->uid)->find();
        // lzh_ member_experience
        // echo M('member_money mm')  ->getlastsql();die;
        //每期收益
        //,b.user_name
        if (!empty($borrowinvestor)) {
            $investlog['starttime'] = time();
            $investlog['endtime'] = time();
            $investlog['deadline'] = time();
            $investlog['substitute_time'] = time();
            $investlog['income'] = 1;
            $investlog['has_capital'] = 1;
            $investlog['borrow_interest_rate'] = $borrowinvestor['borrow_interest_rate'];
            $investlog['total'] = $receive_interest + $borrowinvestor['investor_capital'];
            $investlog['receive_capital'] = $borrowinvestor['investor_capital'];
            $investlog['receive_interest'] = $receive_interest;
            $investlog['interest'] = $receive_interest;
            $investlog['repayment_time'] = time();
            $investlog['borrow_id'] = 1;
            $investlog['investor_uid'] = $this->uid;
            $investlog['borrow_uid'] = 0;
            $investlog['invest_id'] = $borrowinvestor['iid'];
            $investlog['nums'] = 1;
            $investlog['has_capital'] = 0;
            $investlog['investor'] = $money['user_name'];
            $investlog['capital'] = $borrowinvestor['investor_capital']; //投资金额
            $investlog['benjin'] = $borrowinvestor['investor_capital']; //本金
            $investlog['invest'] = $receive_interest; //收益
            $investlog['allmoney'] = $receive_interest + $borrowinvestor['investor_capital']; //收益
            $investlog['rate'] = 0; //支持比例
            $investlog['status'] = 0; //支持比例
            //var_dump($investlog,1);die;
            $invest_defail_id = M('investor_detail')->add($investlog);
            //echo M('investor_detail')->getlastsql();die;
            //$invest_defail_id = M('member_moneylog')->add(['uid'=>$this->uid,'type'=>'9','affect_money'=>$receive_interest,'account_money'=>$money['account_money']+$receive_interest,'back_money'=>'0','collect_money'=>'0','freeze_money'=>'0','info'=>'体验金回款','add_time'=>time(),'add_ip'=>get_client_ip(),'target_uid'=>0,'target_uname'=>'','experience_money'=>$borrowinvestor['investor_capital']]);
            $add_times = $borrowinvestor['add_time'] + 86400;
            $invest_defail_id = M('member_moneylog')->add(['uid' => $this->uid, 'type' => '9', 'yubi' => $money['yubi'], 'freeze_yubi' => $money['yubi_freeze'], 'yongjin' => $money['yongjin'], 'affect_money' => $receive_interest, 'account_money' => $money['account_money'] + $receive_interest, 'back_money' => $money['back_money'], 'collect_money' => $money['collect_money'], 'freeze_money' => $money['money_freeze'] - $borrowinvestor['investor_capital'], 'info' => '体验金回款', 'add_time' => $add_times, 'add_ip' => get_client_ip() , 'target_uid' => 0, 'target_uname' => '', 'experience_money' => $money['money_experience']]);
            $experience = M('member_experience')->where("uid=" . $this->uid . " and paystatus=1")->find();
            if (isset($experience) && !empty($experience)) {
                M('member_experience')->where("id = {$experience['id']}")->save(["paystatus" => 3]);
            }
            //$invest_defail_id = M('member_money')->save(["uid"=>$money['uid'],"money_freeze"=>$money['money_freeze']-$borrowinvestor['investor_capital'],'yubi'=>$money['yubi'],'yubi_freeze'=>$money['yubi_freeze'],'yongjin'=>$money['yongjin'],"money_collect"=>$money['money_collect'],"account_money"=>$money['account_money']+$receive_interest,"back_money"=>$money['back_money'],"credit_limit"=>$money['credit_limit'],"credit_cuse"=>$money['credit_cuse'],"borrow_vouch_limit"=>$money['borrow_vouch_limit'],"borrow_vouch_cuse"=>$money['borrow_vouch_cuse'],"invest_vouch_limit"=>$money['invest_vouch_limit'],"invest_vouch_cuse"=>$money['invest_vouch_cuse'],"money_experience"=>$money['money_experience'],"update_time"=>time()]);
            $invest_defail_id = M('member_money')->save(["uid" => $money['uid'], "money_freeze" => $money['money_freeze'] - $borrowinvestor['investor_capital'], "account_money" => $money['account_money'] + $receive_interest, "update_time" => time() ]);
            M("borrow_investor")->where("id=" . $borrowinvestor['iid'])->save(['status' => 110]);
        }
        $borrow_investor->commit();
    }
    /*** 首页*/
    public function index() {
        foreach (get_ad(39) as $key => $val) {
            $ad[$key]["picurl"] = $this->domainUrl . '/' . $val["img"]; //图片路径
            $ad[$key]["linkurl"] = $val["url"]; //链接地址

        }
        $jsons["banner"] = is_array($ad) ? $ad : array();
        $tiYanJin = array(
            "nianHua" => "24.00%",
            "tiYanJin" => "8888",
            "zhouQi" => "1",
            "id" => "1"
        );
        $jsons["tiYanJin"] = $tiYanJin;
        $xmap["tuijian"] = 1;
        $xmap["type_id"] = 55;
        $xinshou = M('article')->field("id,title,art_info,art_time,art_imgs,type_id")->where($xmap)->limit('0,5')->order('id desc')->select();
        $jsons["shipin"] = $xinshou;
        //新闻资讯
        $map["tuijian"] = 1;
        $map["type_id"] = array(
            "neq",
            11
        );
        $gg = M('article')->field("id,title,type_id")->where($map)->order('id desc')->find();
        $gtype = M('article_category')->where('id = ' . $gg["type_id"])->find();
        $gg["tname"] = $gtype["type_name"];
        $jsons["gongGao"] = $gg;
        $xmap["tuijian"] = 1;
        $xmap["type_id"] = 11;
        $xinshou = M('article')->field("id,title,art_time,type_id")->where($xmap)->order('id desc')->find();
        $xinshou["tname"] = "新手帮助";
        $jsons["xinShou"] = $xinshou;
        //新增
        foreach (get_ad(43) as $key => $val) {
            $ad[$key]["picurl"] = $this->domainUrl . '/' . $val["img"]; //图片路径
            $ad[$key]["linkurl"] = $val["url"]; //链接地址

        }
        $jsons["guanggao"] = is_array($ad) ? $ad : array();
        //新添加
        $tjgoods = M("market")->field("title,art_miaoshu,art_jiage,id,art_img,zhongliang")->where("tuijian=1")->limit('0,6')->select();
        $jsons["tjgoods"] = $tjgoods;
        //新添加
        $tjlist = M("market")->field("title,art_miaoshu,art_jiage,id,art_img")->where("tuijian=1")->limit('5,6')->select();
        $jsons["tjlist"] = $tjlist;
        $danpin = M("market")->field("title,art_miaoshu,art_jiage,id,art_img")->where("leixing=1")->limit('0,4')->select();
        $jsons["danpin"] = $danpin;
        //团购
        //lzh_pt_goods
        $tuangou = M("pt_goods")->field("title,yhprice,id,images")->where("tuijian=1")->limit('0,16')->select();
        //var_dump($tuangou);exit;
        foreach ($tuangou as $k => $v) {
            $tuangou[$k]["images"] = unserialize($v["images"]);
        }
        $jsons["tuangou"] = $tuangou;
        $jsons["status"] = "1";
        outJson($jsons);
    }
    /*



     * 投资列表



    */
    public function invest() {
        //$p                          = intval($_REQUEST['p']) ? intval($_REQUEST['p']) : "1";
        $searchMap = array();
        $searchMap['borrow_status'] = array(
            "in",
            '2,4,6,7'
        );
        // $searchMap['is_experience']= 0;
        $searchMap = array_merge($searchMap, $this->investFilterSearch($_REQUEST));
        $parm = array();
        $parm['map'] = $searchMap;
        $parm['pagesize'] = 10;
        $parm['orderby'] = "b.borrow_status ASC,b.id DESC";
        $parm['map'] = $searchMap;
        $listBorrow = getBorrowList($parm);
        $list = $listBorrow['list'];
        foreach ($lists as $key => $val) {
            $fieldx = "bi.investor_capital,bi.add_time,m.user_name,bi.is_auto,bi.investor_interest";
            $investinfo = M("borrow_investor bi")->field($fieldx)->join("lzh_members m ON bi.investor_uid = m.id")->where("bi.borrow_id={$val['id']}")->order("bi.id DESC")->select();
            $list[] = array(
                'bid' => $val['id'],
                'borrow_name' => $val['borrow_name'], //项目名称
                'borrow_money' => getMoneyFormt($val['borrow_money']) ,
                'borrow_times' => $val['borrow_times'],
                'borrow_duration' => $val['borrow_duration'], //项目期限
                'borrow_interest_rate' => $val['borrow_interest_rate'], //项目回收溢价
                'progress' => $val['progress'], //项目进度
                'borrow_num' => count($investinfo) ,
                'borrow_status' => $val['borrow_status_cn'],
            );
        }
        $jsons['list'] = is_array($list) ? $list : array();
        $jsons['page'] = $listBorrow['_page'];
        $jsons["status"] = "1";
        outJson($jsons);
    }
    private function investFilterSearch($searchArr = array() , $type = '') {
        $searchMap = array();
        !empty($searchArr['borrow_type']) and $searchMap['borrow_type'] = intval($searchArr['borrow_type']);
        if (!empty($searchArr['borrow_interest_rate'])) {
            switch ($searchArr['borrow_interest_rate']) {
                case '1':
                    $searchMap['b.borrow_interest_rate'] = array(
                        'between',
                        array(
                            '0',
                            '10'
                        )
                    );
                    break;

                case '2':
                    $searchMap['b.borrow_interest_rate'] = array(
                        'between',
                        array(
                            '10',
                            '15'
                        )
                    );
                    break;

                case '3':
                    $searchMap['b.borrow_interest_rate'] = array(
                        'between',
                        array(
                            '15',
                            '20'
                        )
                    );
                    break;

                case '4':
                    $searchMap['b.borrow_interest_rate'] = array(
                        'between',
                        array(
                            '20',
                            '25'
                        )
                    );
                    break;

                case '5':
                    $searchMap['b.borrow_interest_rate'] = array(
                        'between',
                        array(
                            '25',
                            '100'
                        )
                    );
                    break;

                default:
                    break;
            }
        }
        if (!empty($searchArr['borrow_duration'])) {
            switch ($searchArr['borrow_duration']) {
                case '1':
                    $searchMap['b.borrow_duration'] = array(
                        'between',
                        array(
                            '1',
                            '3'
                        )
                    );
                    break;

                case '2':
                    $searchMap['b.borrow_duration'] = array(
                        'between',
                        array(
                            '4',
                            '6'
                        )
                    );
                    break;

                case '3':
                    $searchMap['b.borrow_duration'] = array(
                        'between',
                        array(
                            '7',
                            '9'
                        )
                    );
                    break;

                case '4':
                    $searchMap['b.borrow_duration'] = array(
                        'between',
                        array(
                            '10',
                            '12'
                        )
                    );
                    break;

                case '5':
                    $searchMap['b.borrow_duration'] = array(
                        'between',
                        array(
                            '13',
                            '24'
                        )
                    );
                    break;

                case '6':
                    $searchMap['b.borrow_duration'] = array(
                        "gt",
                        '24'
                    );
                    break;

                default:
                    break;
            }
        }
        if (!empty($searchArr['borrow_money'])) {
            $moneyCondition = array();
            switch ($searchArr['borrow_money']) {
                case '1':
                    $moneyCondition = array(
                        'between',
                        array(
                            '0',
                            '10000'
                        )
                    );
                    break;

                case '2':
                    $moneyCondition = array(
                        'between',
                        array(
                            '10000',
                            '50000'
                        )
                    );
                    break;

                case '3':
                    $moneyCondition = array(
                        'between',
                        array(
                            '50000',
                            '100000'
                        )
                    );
                    break;

                case '4':
                    $moneyCondition = array(
                        'between',
                        array(
                            '100000',
                            '200000'
                        )
                    );
                    break;

                case '5':
                    $moneyCondition = array(
                        "gt",
                        '200000'
                    );
                    break;

                default:
                    break;
            }
            if ($type == 'bond') {
                $searchMap['d.money'] = $moneyCondition;
            } else {
                $searchMap['b.borrow_money'] = $moneyCondition;
            }
        }
        !empty($searchArr['borrow_status']) and $searchMap['borrow_status'] = intval($searchArr['borrow_status']);
        return $searchMap;
    }
    /*



     * 项目详情



    */
    public function invest_detail() {
        $id = intval($_REQUEST['id']) ? intval($_REQUEST['id']) : die;
        $show_type = isset($_REQUEST['show_type']) ? $_REQUEST['show_type'] : "";
        $uid = intval($_REQUEST['uid']) ? intval($_REQUEST['uid']) : '';
        $pre = C('DB_PREFIX');
        $borrowinfo = M('borrow_info')->field('shoujia,borrow_times,borrow_duration,id as bid,borrow_img,borrow_info,borrow_name,borrow_sccj as borrow_con,borrow_money,has_borrow,borrow_max,borrow_min,borrow_uid,borrow_status,start_time,collect_day,borrow_interest_rate,borrow_feasibility,budget_revenue')->where('borrow_status in(1,2,4,6,7) and id=' . $id)->find();
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
                    $investinfo = M("borrow_investor bi")->field($fieldx)->join("{$pre}members m ON bi.investor_uid = m.id")->where("bi.borrow_id={$id}")->order("bi.id DESC")->select();
                    $invlog = array();
                    foreach ($investinfo as $key => $value) {
                        $invlog[$key]['user_name'] = hidecard($value['user_name'], 5);
                        $invlog[$key]['investor_capital'] = $value['investor_capital'];
                        $invlog[$key]['inv_time'] = date('Y-m-d H:i:s', $value['add_time']);
                        if ($value['member_interest_rate_id'] != 0) {
                            $invlog[$key]['jx'] = intestrate($value['member_interest_rate_id']) ? intestrate($value['member_interest_rate_id']) : 0;
                        } else {
                            $invlog[$key]['jx'] = 0;
                        }
                        if ($value['bonus_id'] != 0) {
                            $invlog[$key]['hb'] = bounsmoney($vi['bonus_id']) ? bounsmoney($vi['bonus_id']) : 0;
                        } else {
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
                    $borrowinfo['count'] = count($investinfo);
                    $project = arrayFilterValByKey($borrowinfo, array(
                        'id',
                        'borrow_interest_rate',
                        'borrow_con',
                        'borrow_user',
                        'count'
                    ) , true);
                    $jsons['project'] = $project;
                    break;

                default:
                    $borrowinfo['borrow_user'] = (string)(get_user_info($borrowinfo['borrow_uid']));
                    $borrowinfo['borrow_duration'] = $borrowinfo['borrow_duration'];
                    $borrowinfo['borrow_money'] = getFloatValue($borrowinfo['borrow_money'], 2);
                    $borrowinfo['surplus_borrow'] = getFloatvalue($borrowinfo['borrow_money'] - $borrowinfo['has_borrow'], 2);
                    if ($borrowinfo['borrow_status'] == 6 || $borrowinfo['borrow_status'] == 7) {
                        $borrowinfo['endtimes'] = 0;
                    } else if ($borrowinfo['borrow_status'] == 1) {
                        $borrowinfo['endtimes'] = $borrowinfo['start_time'] - time();
                    } else {
                        if (($borrowinfo['start_time'] + ($borrowinfo['collect_day'] * 60 * 60 * 24)) > time()) {
                            $borrowinfo['endtimes'] = $borrowinfo['start_time'] + ($borrowinfo['collect_day'] * 60 * 60 * 24) - time();
                        } else {
                            $borrowinfo['endtimes'] = 0;
                        }
                    }
                    $borrowinfo['progress'] = getFloatValue($borrowinfo['has_borrow'] / $borrowinfo['borrow_money'] * 100, -1);
                    $project = arrayFilterValByKey($borrowinfo, array(
                        'id',
                        'shoujia',
                        'borrow_name',
                        'borrow_money',
                        'borrow_duration',
                        'borrow_min',
                        'surplus_borrow',
                        'progress',
                        'endtimes',
                        'borrow_interest_rate',
                        'borrow_con',
                        'borrow_user',
                        'borrow_status',
                        'progress'
                    ) , true);
                    $jsons['project'] = $project;
                    break;
            }
            $jsons["status"] = "1";
            outJson($jsons);
        } else {
            $jsons["status"] = "0";
            $jsons["msg"] = "数据不存在！";
            outJson($jsons);
        }
    }
    /**
     *  投资支付页面
     * @param $uid
     */
    public function invest_pay() {
        $jsons["status"] = "0";
        $id = intval($_REQUEST['id']) ? intval($_REQUEST['id']) : die;
        $uid = intval($_REQUEST['uid']) ? intval($_REQUEST['uid']) : die;
        $jsons = canInvestMoneyCheck($uid, $id);
        if ($jsons['status'] == "1") {
            $jsons['borrow_min'] = $jsons['borrowinfo']['borrow_min_cn'];
            $jsons['need'] = bcsub($jsons['borrowinfo']['borrow_money'], $jsons['borrowinfo']['has_borrow'], 2);
            $jsons = arrayFilterValByKey($jsons, array(
                'borrowinfo,is_experience,is_bonus,is_memberinterest'
            ) , false);
        }
        $jsons['account_money'] = isset($jsons['account_money']) ? $jsons['account_money'] : "0";
        $jsons['money_experience'] = isset($jsons['money_experience']) ? $jsons['money_experience'] : "0";
        $jsons['bonus_list'] = isset($jsons['bonus_list']) ? $jsons['bonus_list'] : "0";
        $jsons['member_interest_rate_list'] = isset($jsons['member_interest_rate_list']) ? $jsons['member_interest_rate_list'] : "0";
        // var_dump($jsons);exit;
        //echo $jsons['account_money'];exit;
        outJson($jsons);
    }
    /**
     *  投资支付
     * @param $uid
     */
    public function invest_pay_do() {
        $jsons["status"] = "0";
        $borrow_id = intval($_REQUEST['id']) ? intval($_REQUEST['id']) : die;
        $uid = intval($_REQUEST['uid']) ? intval($_REQUEST['uid']) : die;
        $pin = isset($_REQUEST['pin_pass']) ? md5(text($_REQUEST['pin_pass'])) : "";
        $borrow_pass = isset($_REQUEST['borrow_pass']) ? text($_REQUEST['borrow_pass']) : "";
        $money = floatval($_REQUEST['invest_money']) ? floatval(($_REQUEST['invest_money'])) : '0';
        $bonus_id = isset($_REQUEST['bonus_id']) ? intval($_REQUEST['bonus_id']) : 0;
        $memberinterest_id = isset($_REQUEST['memberinterest_id']) ? intval($_REQUEST['memberinterest_id']) : 0;
        $binfo = M("borrow_info")->field('borrow_money,has_borrow,has_vouch,borrow_max,borrow_min,borrow_type,password,pause,is_experience')->find($borrow_id);
        $is_experience = $binfo['is_experience'] == 1 ? 1 : 0;
        $minfo = getMinfo($uid, 'm.pin_pass,m.user_name,mm.account_money,mm.money_experience');
        $amoney = $minfo['account_money'];
        $uname = $minfo['u_user_name'];
        // $canInvestMoney = canInvestMoney($uid, $borrow_id, $money, 0, $bonus_id);
        // if ($canInvestMoney['status'] == 0) {
        //     $jsons["msg"] = $canInvestMoney['msg'];
        //     outJson($jsons);
        // }
        // $money_bonus = $canInvestMoney['money_bonus'];
        if ($bonus_id > 0) {
            $canInvestMoney = canInvestMoney($this->uid, $borrow_id, $money, 0, $is_experience, '0', $bonus_id, text($_REQUEST['borrow_pass']));
            if ($canInvestMoney['status'] == 0) {
                $jsons["msg"] = $canInvestMoney['msg'];
                outJson($jsons);
            }
            $money_bonus = $canInvestMoney['money_bonus'];
            $money = floatval($money + $money_bonus);
        }
        $pin_pass = $minfo['pin_pass'];
        if ($pin <> $pin_pass) {
            $jsons["msg"] = "支付密码错误，请重试";
            outJson($jsons);
        }
        $ids = M('members_status')->getFieldByUid($this->uid, 'id_status');
        if ($ids != 1) {
            $jsons["msg"] = "请先通过实名认证后在进行投标。";
            outJson($jsons);
        }
        $phones = M('members_status')->getFieldByUid($this->uid, 'phone_status');
        if ($phones != 1) {
            $jsons["msg"] = "请先通过手机认证后在进行投标。";
            outJson($jsons);
        }
        if ($binfo['pause'] == 1) {
            $jsons["msg"] = "此标当前已经截止，请等待管理员开启。";
            outJson($jsons);
        }
        // 50 > 10
        if ($money < $binfo['borrow_min']) {
            $jsons["msg"] = "此项目最小投资金额为" . $binfo['borrow_min'] . "元";
            outJson($jsons);
        }
        if ($money > $binfo['borrow_max'] and $binfo['borrow_max'] != 0) {
            $jsons["msg"] = "此项目最大投资金额为" . $binfo['borrow_max'] . "元";
            outJson($jsons);
        }
        if (!empty($binfo['password'])) {
            if (empty($borrow_pass)) {
                $jsons["msg"] = "此标是约标，必须验证投标密码";
                outJson($jsons);
            } else if ($binfo['password'] <> $borrow_pass) {
                $jsons["msg"] = "投标密码不正确";
                outJson($jsons);
            }
        }
        //投标总数检测
        $capital = M('borrow_investor')->where("borrow_id={$borrow_id} AND investor_uid={$this->uid}")->sum('investor_capital');
        if (($capital + $money) > $binfo['borrow_max'] && $binfo['borrow_max'] > 0) {
            $xtee = $binfo['borrow_max'] - $capital;
            $jsons["msg"] = "您已投标{$capital}元，此投上限为{$binfo['borrow_max']}元，你最多只能再投{$xtee}";
            outJson($jsons);
        }
        if ($binfo['has_vouch'] < $binfo['borrow_money'] && $binfo['borrow_type'] == 2) {
            $jsons["msg"] = "此标担保还未完成，您可以担保此标或者等担保完成再投";
            outJson($jsons);
        }
        $need = bcsub($binfo['borrow_money'], $binfo['has_borrow'], 2);
        $caninvest = bcsub($need, $binfo['borrow_min'], 2);
        // echo $binfo['borrow_money'] ;
        // exit;
        if ($money > $caninvest && ($need - $money) <> 0) {
            if (intval($need) == 0 or $need == "0.00") {
                $jsons["msg"] = "尊敬的{$uname}，此项目已经投满";
                outJson($jsons);
            }
            if ($money > $need) {
                $jsons["msg"] = "尊敬的{$uname}，此项目还差{$need}元满标,您最多只能再投{$need}元";
                outJson($jsons);
            }
            $msg = "尊敬的{$uname}，此项目还差{$need}元满标,如果您投标{$money}元，将导致最后一次投标最多只能投" . (bcsub($need, $money, 2)) . "元，小于最小投标金额{$binfo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满标</font>或者投标金额必须<font color='#FF0000'>小于等于{$caninvest}元</font>";
            if ($caninvest < $binfo['borrow_min']) $msg = "尊敬的{$uname}，此标还差{$need}元满标,如果您投标{$money}元，将导致最后一次投标最多只能投" . (bcsub($need, $money, 2)) . "元，小于最小投标金额{$binfo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满标</font>即投标金额必须<font color='#FF0000'>等于{$need}元</font>";
            $jsons["msg"] = $msg;
            outJson($jsons);
        }
        if (($need - $money) < 0) {
            $jsons["msg"] = "尊敬的{$uname}，此项目还差{$need}元满标,您最多只能再投{$need}元";
            outJson($jsons);
        } else {
            $capital = M('borrow_investor')->where("borrow_id={$borrow_id}")->sum('investor_capital');
            if ($capital > $binfo["borrow_money"]) {
                $jsons["msg"] = "投资金额错误";
                outJson($jsons);
            }
            $capital = M('investor_detail')->where("borrow_id={$borrow_id}")->sum('capital');
            if ($capital > $binfo["borrow_money"]) {
                $jsons["msg"] = "投资金额错误";
                outJson($jsons);
            }
            // $done = investMoney($this->uid,$borrow_id,$money);
            writeLog($memberinterest_id);
            if ($memberinterest_id > 0) {
                //加息券校验
                $canUseInterest = canUseInterest($this->uid, $memberinterest_id);
                writeLog($canUseInterest);
                if ($canUseInterest['status'] == 0) {
                    $jsons["msg"] = "加息券不可用";
                    outJson($jsons);
                }
                $interest_rate = $canUseInterest['interest_rate'];
            } else {
                $interest_rate = 0;
            }
            //体验金校验
            $canInvestMoney = canInvestMoney($this->uid, $borrow_id, $money, 0, $is_experience, '0', $bonus_id, text(@$_REQUEST['borrow_pass']));
            if ($canInvestMoney['status'] == 0) {
                $this->error($canInvestMoney['msg']);
            }
            $money_bonus = $canInvestMoney['money_bonus'];
            if ($canInvestMoney['money_type'] == 1 && $amoney < $money) {
                $jsons["msg"] = "尊敬的{$uname}，您准备投标{$money}元，但您的账户可用余额为{$amoney}元，请先去充值再投项目.";
                outJson($jsons);
            }
            $done = investMoney($this->uid, $borrow_id, $money, '0', '1', $is_experience, $memberinterest_id, $bonus_id, $money_bonus, text(@$_REQUEST['borrow_pass']) , 2);
        }
        //$this->display("Public:_footer");
        //$this->assign("waitSecond",1000);
        if ($done === true) {
            $jsons["status"] = "1";
            $jsons["msg"] = "恭喜成功投资{$money}元（其中使用红包{$money_bonus}元,使用加息券{$interest_rate}%）!";
            outJson($jsons);
        } else if ($done) {
            $jsons["msg"] = $done;
            outJson($jsons);
        } else {
            $jsons["msg"] = "对不起，投资失败，请重试";
            outJson($jsons);
        }
    }
    public function invest_pay_dos() {
        $jsons["status"] = "0";
        $borrow_id = intval($_REQUEST['id']) ? intval($_REQUEST['id']) : die;
        $uid = intval($_REQUEST['uid']) ? intval($_REQUEST['uid']) : die;
        $pin = isset($_REQUEST['pin_pass']) ? md5(text($_REQUEST['pin_pass'])) : "";
        $money = floatval($_REQUEST['invest_money']) ? floatval(($_REQUEST['invest_money'])) : '0';
        $bonus_id = isset($_REQUEST['bonus_id']) ? intval($_REQUEST['bonus_id']) : 0;
        $memberinterest_id = isset($_REQUEST['memberinterest_id']) ? intval($_REQUEST['memberinterest_id']) : 0;
        $is_experience = isset($_REQUEST['is_experience']) ? 1 : 0;
        $minfo = getMinfo($uid, 'm.pin_pass,m.user_name,mm.account_money,mm.money_experience');
        $amoney = $minfo['account_money'];
        $uname = $minfo['u_user_name'];
        // $canInvestMoney = canInvestMoney($uid, $borrow_id, $money, 0, $bonus_id);
        $binfo = M("borrow_info")->field('borrow_money,has_borrow,has_vouch,borrow_max,borrow_min,borrow_type,password,pause')->find($borrow_id);
        // if ($canInvestMoney['status'] == 0) {
        //     $jsons["msg"] = $canInvestMoney['msg'];
        //     outJson($jsons);
        // }
        // $money_bonus = $canInvestMoney['money_bonus'];
        if ($bonus_id > 0) {
            $canInvestMoney = canInvestMoney($this->uid, $borrow_id, $money, 0, $is_experience, '0', $bonus_id, text($_REQUEST['borrow_pass']));
            if ($canInvestMoney['status'] == 0) {
                $jsons["msg"] = $canInvestMoney['msg'];
                outJson($jsons);
            }
            $money_bonus = $canInvestMoney['money_bonus'];
            $money = floatval($money + $$money);
        }
        $pin_pass = $minfo['pin_pass'];
        if ($pin <> $pin_pass) {
            $jsons["msg"] = "支付密码错误，请重试";
            outJson($jsons);
        }
        $ids = M('members_status')->getFieldByUid($this->uid, 'id_status');
        if ($ids != 1) {
            $jsons["msg"] = "请先通过实名认证后在进行投标。";
            outJson($jsons);
        }
        $phones = M('members_status')->getFieldByUid($this->uid, 'phone_status');
        if ($phones != 1) {
            $jsons["msg"] = "请先通过手机认证后在进行投标。";
            outJson($jsons);
        }
        if ($binfo['pause'] == 1) {
            $jsons["msg"] = "此标当前已经截止，请等待管理员开启。";
            outJson($jsons);
        }
        // 50 > 10
        if ($money < $binfo['borrow_min']) {
            $jsons["msg"] = "此项目最小投资金额为" . $binfo['borrow_min'] . "元";
            outJson($jsons);
        }
        if ($money > $binfo['borrow_max'] and $binfo['borrow_max'] != 0) {
            $jsons["msg"] = "此项目最大投资金额为" . $binfo['borrow_max'] . "元";
            outJson($jsons);
        }
        //投标总数检测
        $capital = M('borrow_investor')->where("borrow_id={$borrow_id} AND investor_uid={$this->uid}")->sum('investor_capital');
        if (($capital + $money) > $binfo['borrow_max'] && $binfo['borrow_max'] > 0) {
            $xtee = $binfo['borrow_max'] - $capital;
            $jsons["msg"] = "您已投标{$capital}元，此投上限为{$binfo['borrow_max']}元，你最多只能再投{$xtee}";
            outJson($jsons);
        }
        if ($binfo['has_vouch'] < $binfo['borrow_money'] && $binfo['borrow_type'] == 2) {
            $jsons["msg"] = "此标担保还未完成，您可以担保此标或者等担保完成再投";
            outJson($jsons);
        }
        $need = bcsub($binfo['borrow_money'], $binfo['has_borrow'], 2);
        $caninvest = bcsub($need, $binfo['borrow_min'], 2);
        // echo $binfo['borrow_money'] ;
        // exit;
        if ($money > $caninvest && ($need - $money) <> 0) {
            if (intval($need) == 0 or $need == "0.00") {
                $jsons["msg"] = "尊敬的{$uname}，此项目已经投满";
                outJson($jsons);
            }
            if ($money > $need) {
                $jsons["msg"] = "尊敬的{$uname}，此项目还差{$need}元满标,您最多只能再投{$need}元";
                outJson($jsons);
            }
            $msg = "尊敬的{$uname}，此项目还差{$need}元满标,如果您投标{$money}元，将导致最后一次投标最多只能投" . (bcsub($need, $money, 2)) . "元，小于最小投标金额{$binfo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满标</font>或者投标金额必须<font color='#FF0000'>小于等于{$caninvest}元</font>";
            if ($caninvest < $binfo['borrow_min']) $msg = "尊敬的{$uname}，此标还差{$need}元满标,如果您投标{$money}元，将导致最后一次投标最多只能投" . (bcsub($need, $money, 2)) . "元，小于最小投标金额{$binfo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满标</font>即投标金额必须<font color='#FF0000'>等于{$need}元</font>";
            $jsons["msg"] = $msg;
            outJson($jsons);
        }
        if (($need - $money) < 0) {
            $jsons["msg"] = "尊敬的{$uname}，此项目还差{$need}元满标,您最多只能再投{$need}元";
            outJson($jsons);
        } else {
            $capital = M('borrow_investor')->where("borrow_id={$borrow_id}")->sum('investor_capital');
            if ($capital > $binfo["borrow_money"]) {
                $jsons["msg"] = "投资金额错误";
                outJson($jsons);
            }
            $capital = M('investor_detail')->where("borrow_id={$borrow_id}")->sum('capital');
            if ($capital > $binfo["borrow_money"]) {
                $jsons["msg"] = "投资金额错误";
                outJson($jsons);
            }
            // $done = investMoney($this->uid,$borrow_id,$money);
            writeLog($memberinterest_id);
            if ($memberinterest_id > 0) {
                //加息券校验
                $canUseInterest = canUseInterest($this->uid, $memberinterest_id);
                writeLog($canUseInterest);
                if ($canUseInterest['status'] == 0) {
                    $jsons["msg"] = "加息券不可用";
                    outJson($jsons);
                }
                $interest_rate = $canUseInterest['interest_rate'];
            } else {
                $interest_rate = 0;
            }
            //体验金校验
            $canInvestMoney = canInvestMoney($this->uid, $borrow_id, $money, 0, $is_experience, '0', $bonus_id, text(@$_REQUEST['borrow_pass']));
            if ($canInvestMoney['status'] == 0) {
                $this->error($canInvestMoney['msg']);
            }
            $money_bonus = $canInvestMoney['money_bonus'];
            if ($canInvestMoney['money_type'] == 1 && $amoney < $money) {
                $jsons["msg"] = "尊敬的{$uname}，您准备投标{$money}元，但您的账户可用余额为{$amoney}元，请先去充值再投项目.";
                outJson($jsons);
            }
            $done = investMoney($this->uid, $borrow_id, $money, '0', '1', $is_experience, $memberinterest_id, $bonus_id, $money_bonus, text(@$_REQUEST['borrow_pass']) , 2);
        }
        //$this->display("Public:_footer");
        //$this->assign("waitSecond",1000);
        if ($done === true) {
            $jsons["status"] = "1";
            $jsons["msg"] = "恭喜成功投资{$money}元（其中使用红包{$money_bonus}元,使用加息券{$interest_rate}%）!";
            outJson($jsons);
        } else if ($done) {
            $jsons["msg"] = $done;
            outJson($jsons);
        } else {
            $jsons["msg"] = "对不起，投资失败，请重试";
            outJson($jsons);
        }
    }
    /**
     * 登录接口
     * @param txtUsername  用户名
     * @param txtPwd  密码（md5）
     */
    public function login() {
        $jsons["status"] = "0";
        $pre = C('DB_PREFIX');
        $user_name = text(urldecode($_REQUEST['txtUsername']));
        $user_pass = text($_REQUEST['txtPwd']);
        // var_dump($_REQUEST);//exit();
        if (empty($user_name) || empty($user_pass)) {
            $jsons["msg"] = "登录失败，用户名或密码不能为空！";
            outJson($jsons);
        }
        if (filter_var($user_name, FILTER_VALIDATE_EMAIL)) {
            $whereStr = "(m.user_email = '{$user_name}' and ms.email_status = 1) or m.user_name = '{$user_name}'";
        } else {
            $whereStr = "m.user_name = '{$user_name}' or m.user_phone = '{$user_name}'";
        }
        $vo = M('members m')->field('m.id,m.user_type,m.user_name,m.user_email,m.user_pass,m.pin_pass,m.is_ban,m.user_phone')->join($pre . 'members_status ms on m.id=ms.uid')->where($whereStr)->find();
        if ($vo['is_ban'] == 1) {
            $jsons["msg"] = "您的帐户已被冻结，请联系客服处理！";
            outJson($jsons);
        }
        if ($vo["user_name"] == "" || !is_array($vo)) {
            $jsons["msg"] = "用户名或者密码错误！";
            outJson($jsons);
        }
        if ($vo['user_pass'] == md5($user_pass)) {
            $up['uid'] = $vo['id'];
            $up['add_time'] = time();
            $up['ip'] = get_client_ip();
            M('member_login')->add($up);
            $jsons['data']['uid'] = $this->jwt->getToken(text($vo['id']));
            //$jsons['data']['uid'] =text($vo['id']);
            $jsons['data']['user_name'] = text($vo['user_name']);
            $jsons['data']['user_type'] = text($vo['user_type']);
            $jsons['data']['user_phone'] = $vo['user_phone'];
            $jsons['data']["is_changepin"] = $vo['pin_pass'] ? 1 : 0;
            $jsons["status"] = "1";
            $jsons["msg"] = "登录成功！";
            outJson($jsons);
        } else {
            $jsons["msg"] = "用户名或者密码错误！";
            outJson($jsons);
        }
    }
    /**
     * 微信登录接口
     * @param txtUsername  用户名
     * @param txtPwd  密码（md5）
     */
    public function wxlogin() {
        $globalz = M('global')->select();
        foreach ($globalz as $k => $v) {
            $global[$v['code']] = $v['text'];
        }
        $appid = $global['weixinappid']; //"wx0a01a5ed7857bad7";
        //$appid="wx275dca4b41d86791";
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=";
        $url.= $appid;
        $url.= "&redirect_uri=";
        $url.= "https://" . $_SERVER['SERVER_NAME'] . "/Wap/Api/weiixnhuidiao";
        $url.= "&response_type=code";
        $url.= "&scope=snsapi_userinfo";
        $url.= "&state=" . "123";
        $url.= "#wechat_redirect";
        //$this->redirect($url);
        header("location:$url");
        //参数    是否必须    说明
        //appid 是   公众号的唯一标识
        //redirect_uri  是   授权后重定向的回调链接地址， 请使用 urlEncode 对链接进行处理
        //response_type 是   返回类型，请填写code
        //scope 是   应用授权作用域，snsapi_base （不弹出授权页面，直接跳转，只能获取用户openid），snsapi_userinfo （弹出授权页面，可通过openid拿到昵称、性别、所在地。并且， 即使在未关注的情况下，只要用户授权，也能获取其信息 ）
        //state 否   重定向后会带上state参数，开发者可以填写a-zA-Z0-9的参数值，最多128字节
        //#wechat_redirect  是   无论直接打开还是做页面302重定向时候，必须带此参数

    }
    public function weiixnhuidiao() {
        //echo 1111;exit;
        //第二步    
        $globalz = M('global')->select();
        foreach ($globalz as $k => $v) {
            $global[$v['code']] = $v['text'];
        }
        $appid = $global['weixinappid']; //"wx0a01a5ed7857bad7";
        $secret = $global['weixinsecret']; //"4d5be00eaa31ca639f5078b04f20a177";
        // $appid="wx275dca4b41d86791";
        // $secret="2f46140df07853ac09098638e435b0cc";
        // var_dump($_GET['code']);
        //参数    是否必须    说明
        //appid 是   公众号的唯一标识
        //secret    是   公众号的appsecret
        //code  是   填写第一步获取的code参数
        //grant_type    是   填写为 authorization_code
        $code = $_GET['code'];
        $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=';
        $get_token_url.= $appid;
        $get_token_url.= '&secret=';
        $get_token_url.= $secret;
        $get_token_url.= '&code=';
        $get_token_url.= $code;
        $get_token_url.= '&grant_type=authorization_code';
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $get_token_url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true); // 从证书中检查SSL加密算法是否存在
        $res = curl_exec($curl); //返回api的json对象
        //关闭URL请求
        curl_close($res);
        $json_obj = json_decode($res, true);
        $access_token = $json_obj['access_token'];
        //var_dump($json_obj);die;
        $openid = $json_obj['openid'];
        $get_user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';
        //var_dump($get_user_info_url);
        //die;
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $get_user_info_url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true); // 从证书中检查SSL加密算法是否存在
        $res = curl_exec($curl); //返回api的json对象
        //关闭URL请求
        curl_close($res);
        //  $json_obj=json_decode($res,true);
        //   
        ////解析json 
        $user_obj = json_decode($res, true);
        $data = array();
        $data["openid"] = $user_obj['openid'];
        $members = M('members')->where($data)->find();
        if (!empty($members)) {
            $up['uid'] = $members['id'];
            $up['add_time'] = time();
            $up['ip'] = get_client_ip();
            M('member_login')->add($up);
            //$jsons['data']['uid'] = text($members['id']);
            $jsons['data']['uid'] = $this->jwt->getToken(text($members['id']));
            $jsons['data']['user_name'] = text($members['user_name']);
            $jsons['data']['user_type'] = text($members['user_type']);
            $jsons['data']['user_phone'] = $members['user_phone'];
            $jsons['data']["is_changepin"] = $members['pin_pass'] ? 1 : 0;
            $jsons['data']["openid"] = $members['openid'];
            $jsons['data']["userpic"] = $members['userpic'];
            $jsons["status"] = "1";
            $jsons["time"] = time();
            $jsons["openid"] = $user_obj['openid'];
            // $jsons["unionid"]=$user_obj['unionid'];
            session('unionid', $user_obj['unionid']);
            $jsons["msg"] = "登录成功！";
            //outJson($jsons);

        } else {
            session('unionid', $user_obj['unionid']);
            $jsons["status"] = "0";
            $jsons["openid"] = $user_obj['openid'];
            $jsons["txtUser"] = $user_obj['nickname'];
            $jsons["userpic"] = $user_obj['headimgurl'];
            $jsons["unionid"] = $user_obj['unionid'];
            $jsons["time"] = time();
            $jsons["msg"] = "您没有绑定微信！";
            //outJson($jsons);

        }
        $res = urldecode(http_build_query($jsons));
        $res = "https://" . $_SERVER['SERVER_NAME'] . "/m/usercenter/wx_login_result?" . $res;
        //$res="http://houtai.rzmwzc.com:8080/#/login/usercenter/wx_login_result?".$res;
        //$this->redirect('mqqapi://card/show_pslcard?src_type=internal&version=1&uin=1921779545&card_type=person&source=sharecard',302);
        header("location:$res");
        //$this->redirect($res);

    }
    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }
    public function GetOpenid() {
        //通过code获得openid
        if (!isset($_GET['code'])) {
            $appid = "wx275dca4b41d86791";
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=";
            $url.= $appid;
            $url.= "&redirect_uri=";
            $url.= "https://" . $_SERVER['SERVER_NAME'] . "/Wap/Api/GetOpenid";
            $url.= "&response_type=code";
            $url.= "&scope=snsapi_userinfo";
            $url.= "&state=" . "123";
            $url.= "#wechat_redirect";
            header("location:$url");
        } else {
            //获取code码，以获取openid
            $code = $_GET['code'];
            $appid = "wx275dca4b41d86791";
            $secret = "e4da953fffa77005e74ce472330d58f8";
            $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=';
            $get_token_url.= $appid;
            $get_token_url.= '&secret=';
            $get_token_url.= $secret;
            $get_token_url.= '&code=';
            $get_token_url.= $code;
            $get_token_url.= '&grant_type=authorization_code';
            //$res = $this->httpGet($get_token_url);
            $curl = curl_init(); // 启动一个CURL会话
            curl_setopt($curl, CURLOPT_URL, $get_token_url);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true); // 从证书中检查SSL加密算法是否存在
            $res = curl_exec($curl); //返回api的json对象
            //关闭URL请求
            curl_close($res);
            $json_obj = json_decode($res, true);
            $res = urldecode(http_build_query($json_obj));
            $res = "https://" . $_SERVER['SERVER_NAME'] . "/m/getOpenidPage?" . $res;
            header("location:$res");
        }
    }
    public function GetOpenid2() {
        //通过code获得openid
        if (!isset($_GET['code'])) {
            $appid = "wx275dca4b41d86791";
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=";
            $url.= $appid;
            $url.= "&redirect_uri=";
            $url.= "https://" . $_SERVER['SERVER_NAME'] . "/Wap/Api/GetOpenid2";
            $url.= "&response_type=code";
            $url.= "&scope=snsapi_userinfo";
            $url.= "&state=" . "123";
            $url.= "#wechat_redirect";
            header("location:$url");
        } else {
            //获取code码，以获取openid
            $code = $_GET['code'];
            $appid = "wx275dca4b41d86791";
            $secret = "e4da953fffa77005e74ce472330d58f8";
            $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=';
            $get_token_url.= $appid;
            $get_token_url.= '&secret=';
            $get_token_url.= $secret;
            $get_token_url.= '&code=';
            $get_token_url.= $code;
            $get_token_url.= '&grant_type=authorization_code';
            //$res = $this->httpGet($get_token_url);
            $curl = curl_init(); // 启动一个CURL会话
            curl_setopt($curl, CURLOPT_URL, $get_token_url);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true); // 从证书中检查SSL加密算法是否存在
            $res = curl_exec($curl); //返回api的json对象
            //关闭URL请求
            curl_close($res);
            $json_obj = json_decode($res, true);
            $res = urldecode(http_build_query($json_obj));
            $res = "https://" . $_SERVER['SERVER_NAME'] . "/m/getOpenidPage?" . $res;
            //var_dump($res); exit();
            header("location:$res");
        }
    }
    function match_chinese($chars, $encoding = 'utf8') {
        $pattern = ($encoding == 'utf8') ? '/[\x{4e00}-\x{9fa5}a-zA-Z0-9]/u' : '/[\x80-\xFF]/';
        preg_match_all($pattern, $chars, $result);
        $temp = join('', $result[0]);
        return $temp;
    }
    public function weixinregaction() {
        error_reporting(E_ALL);
        //file_put_contents('./logs.txt',print_r($_POST,true),FILE_APPEND);
        $data['user_name'] = trim($this->match_chinese($_REQUEST['txtUser']));
        $data['user_phone'] = trim(text($_REQUEST['txtPhone']));
        $data['openid'] = $_REQUEST['openid'];
        $data['appopenid'] = $_REQUEST['appopenid'];
        // var_dump($data['unionid']);exit;
        if (empty($data["appopenid"])) {
            //$data["unionid"]=$this->union($data['openid']);
            $data['unionid'] = session("unionid");
        } else {
            $data['unionid'] = $_REQUEST['unionid'];
        }
        $data['userpic'] = $_REQUEST['userpic'];
        // echo json_encode($_REQUEST);exit;
        $code = trim(text($_REQUEST['code']));
        // $codeId=$_REQUEST['code'];
        // if($code != $_SESSION['reg_code']){
        //     $json['status'] = "c";
        //     $json['info'] = "手机验证码不正确";
        //     outJson($jsons);
        // }
        $codeId = @$_REQUEST['codeId'];
        $txtCode = @$_REQUEST['code'];
        $user_phone = text($_REQUEST['txtPhone']);
        $verifyRs = M('verify_code')->where("id = '{$codeId}' and content = {$txtCode} and phone='{$user_phone}'")->count('id');
        if ($verifyRs != 1) {
            $jsons['status'] = "0";
            $jsons["msg"] = "手机验证码不正确";
            outJson($jsons);
        }
        $mev = M('members')->where("user_phone = '{$data['user_phone']}' OR user_name='{$data['user_name']}'")->find();
        if (strlen($mev["unionid"]) > 10 && $data["unionid"] != $mev["unionid"]) {
            $jsons['status'] = "0";
            $jsons["msg"] = "请使用统一微信账号绑定";
            outJson($jsons);
        }
        if (!empty($data['appopenid'])) {
            if ($data['appopenid'] != $mev['appopenid']) {
                $jsons['status'] = "0";
                $jsons['msg'] = "操作失败，用户名或者手机号已经有人使用";
                outJson($jsons);
            }
            if ($mev['appopenid'] == $data['appopenid']) {
                $jsons['status'] = "0";
                $jsons['msg'] = "操作失败，您已绑定过微信";
                outJson($jsons);
            }
        } else if (!empty($mev['openid'])) {
            if ($mev['openid'] != $data['openid']) {
                $jsons['status'] = "0";
                $jsons['msg'] = "操作失败，用户名或者手机号已经有人使用";
                outJson($jsons);
            }
            //var_dump($mev['openid']);
            if ($mev['openid'] == $data['openid']) {
                $jsons['status'] = "0";
                $jsons['msg'] = "操作失败，您已绑定过微信";
                outJson($jsons);
            }
        }
        //获取推荐人
        $txtIncode = text($_REQUEST['txtIncode']);
        if (!empty($txtIncode)) {
            $txtRecUserid = M('members')->where("incode='" . $txtIncode . "'")->find();
            if (!empty($txtRecUserid)) {
                $data['recommend_id'] = $txtRecUserid['id'];
                $data['recommend_bid'] = $txtRecUserid['recommend_id'];
                $data['recommend_cid'] = $txtRecUserid['recommend_bid'];
            } else {
                $jsons['status'] = "0";
                $jsons['msg'] = "推荐人不存在，若没有推荐人请留空。";
                outJson($jsons);
            };
        }
        if (!empty($mev) && empty($mev['openid']) && !empty($data['openid'])) {
            $ddc["openid"] = $data['openid'];
            $ddc["unionid"] = $data['unionid'];
            $me = M('members')->where("user_phone = '{$data['user_phone']}' OR user_name='{$data['user_name']}'")->save($ddc);
            //$this->_memberlogin($members['id']);
            if ($me) {
                $me = M('members')->where("openid = '" . $ddc["openid"] . "'")->find();
                $jsons['data']['uid'] = $this->jwt->getToken(text($me['id']));
                $jsons['data']['user_name'] = text($me['user_name']);
                $jsons['data']['userpic'] = text($data['userpic']);
                $jsons['data']['user_type'] = text($me['user_type']);
                $jsons['data']['user_phone'] = $me['user_phone'];
                $jsons['data']["is_changepin"] = $me['pin_pass'] ? 1 : 0;
                $jsons['data']["openid"] = $me['openid'];
                $jsons["status"] = "1";
                $jsons["msg"] = "绑定成功";
                outJson($jsons);
                if ($this->glo['award_reg'] > 0) {
                    pubExperienceMoney($newid, $this->glo['award_reg'], 4, 30);
                }
                //pubBonusByRules($mev["id"],2);

            } else {
                $jsons["status"] = "0";
                $jsons["msg"] = "绑定失败";
                outJson($jsons);
            }
        }
        if (!empty($mev) && empty($mev['appopenid']) && !empty($data['appopenid'])) {
            $ddc["appopenid"] = $data['appopenid'];
            $ddc["unionid"] = $_REQUEST['unionid'];
            $me = M('members')->where("user_phone = '{$data['user_phone']}' OR user_name='{$data['user_name']}'")->save($ddc);
            if ($me) {
                $me = M('members')->where("openid = '" . $ddc["openid"] . "'")->find();
                $jsons['data']['uid'] = $this->jwt->getToken(text($me['id']));
                $jsons['data']['user_name'] = text($me['user_name']);
                $jsons['data']['userpic'] = text($data['userpic']);
                $jsons['data']['user_type'] = text($me['user_type']);
                $jsons['data']['user_phone'] = $me['user_phone'];
                $jsons['data']["is_changepin"] = $me['pin_pass'] ? 1 : 0;
                $jsons['data']["openid"] = $me['openid'];
                $jsons['data']["appopenid"] = $me['appopenid'];
                $jsons["status"] = "1";
                $jsons["msg"] = "绑定成功";
                outJson($jsons);
            } else {
                $jsons["status"] = "0";
                $jsons["msg"] = "绑定失败";
                outJson($jsons);
            }
        }
        $data['reg_time'] = time();
        $data['reg_ip'] = get_client_ip();
        $data['lastlog_time'] = time();
        $data['lastlog_ip'] = get_client_ip();
        $data['incode'] = getincode();
        // if(session("tmp_invite_user"))  $data['recommend_id'] = session("tmp_invite_user");
        if (!empty($mev["id"])) {
            $data['id'] = $mev["id"];
            $newid = M('members')->save($data);
        } else {
            $data['user_pass'] = trim(md5("123456"));
            //$data['pin_pass'] = trim(md5("123456"));
            $newid = M('members')->add($data);
        }
        $vo = M('members_status')->where("uid={$newid}")->find();
        if (!$vo) {
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
        } else {
            $data['phone_status'] = 1;
            M("members_status")->where("uid={$newid}")->save($data);
        }
        if ($newid) {
            //绑定微信头像暂时未开启
            // $ddcm["user_img"]=$data['userpic'];
            // M('member_info')->where("uid=".$me["id"])->save($ddcm);
            // $vo=M('members')->where("openid = '".$data['openid']."'")->find();
            $vo = M('members')->where("id = $newid")->find();
            $jsons['data']['uid'] = $this->jwt->getToken(text($vo['id']));;
            $jsons['data']['user_name'] = text($vo['user_name']);
            $jsons['data']['userpic'] = text($data['userpic']);
            $jsons['data']['user_type'] = text($vo['user_type']);
            $jsons['data']['user_phone'] = $vo['user_phone'];
            $jsons['data']["is_changepin"] = $vo['pin_pass'] ? 1 : 0;
            $jsons['data']["openid"] = $vo['openid'];
            $jsons["status"] = "1";
            $jsons["msg"] = "注册绑定成功";
            if ($this->glo['award_reg'] > 0) {
                pubExperienceMoney($newid, $this->glo['award_reg'], 4, 30);
            }
            outJson($jsons);
        } else {
            $jsons["status"] = "0";
            $jsons["msg"] = "注册绑定失败";
            outJson($jsons);
        }
    }
    public function union($openid) {
        $globalz = M('global')->select();
        foreach ($globalz as $k => $v) {
            $global[$v['code']] = $v['text'];
        }
        //获取access_token
        $appid = $global['weixinappid'];
        $secret = $global['weixinsecret'];
        $access_token = $this->httpGet("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret");
        // var_dump($access_token);
        if ($access_token) {
            $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $access_token['access_token'] . '&openid=' . $openid . '&lang=zh_CN';
            $res = $this->httpGet($url);
            return $res['unionid'];
        } else {
            return false;
        }
    }
    /**
     * 手机登录接口
     * @param txtUsername  用户名
     * @param txtPwd  密码（md5）
     */
    public function phonelogin() {
        $jsons["status"] = "0";
        $pre = C('DB_PREFIX');
        $user_name = text(urldecode($_REQUEST['txtphone']));
        $user_pass = text($_REQUEST['txtPwd']);
        if (empty($user_name) || empty($user_pass)) {
            $jsons["msg"] = "登录失败，用户名或密码不能为空！";
            outJson($jsons);
        }
        $whereStr = "(m.user_phone = '{$user_name}' and ms.phone_status = 1)";
        $vo = M('members m')->field('m.id,m.user_type,m.user_name,m.user_email,m.user_pass,m.pin_pass,m.is_ban,m.user_phone')->join($pre . 'members_status ms on m.id=ms.uid')->where($whereStr)->find();
        if ($vo['is_ban'] == 1) {
            $jsons["msg"] = "您的帐户已被冻结，请联系客服处理！";
            outJson($jsons);
        }
        if ($vo["user_name"] == "" || !is_array($vo)) {
            $jsons["msg"] = "用户名或者密码错误！";
            outJson($jsons);
        }
        if ($vo['user_pass'] == $user_pass) {
            $up['uid'] = $vo['id'];
            $up['add_time'] = time();
            $up['ip'] = get_client_ip();
            M('member_login')->add($up);
            //$jsons['uid'] = text($vo['id']);
            $jsons['uid'] = $this->jwt->getToken(text($vo['id']));
            $jsons['user_name'] = text($vo['user_name']);
            $jsons["is_changepin"] = $vo['pin_pass'] ? 1 : 0;
            $jsons['user_phone'] = $vo['user_phone'];
            $jsons["status"] = "1";
            $jsons["msg"] = "登录成功！";
            outJson($jsons);
        } else {
            $jsons["msg"] = "用户名或者密码错误！";
            outJson($jsons);
        }
    }
    /*



    *   会员注册



    *   @param txtPhone  手机号



    *   txtCode  验证码



    */
    public function reg() {
        $jsons['status'] = "0";
        $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "phoneverify";
        switch ($action) {
            case 'phoneverify':
                $user_phone = text($_REQUEST['txtPhone']);
                if (empty($user_phone)) {
                    $jsons["msg"] = "手机验证失败，手机号不能为空！";
                    outJson($jsons);
                }
                if (preg_match("/^1\d{10}$/", $user_phone)) {
                    $map['user_phone'] = $user_phone;
                    $count = M('members')->where($map)->count('id');
                    if ($count > 0) {
                        $jsons["msg"] = "手机验证失败，当前手机号已经使用！";
                        outJson($jsons);
                    }
                }
                $count = M('members')->where("user_name='{$user_phone}'")->count('id');
                if ($count > 0) {
                    $jsons["msg"] = "手机验证失败，用户名已经有人使用！";
                    outJson($jsons);
                }
                $codeId = @$_REQUEST['codeId'];
                $txtCode = @$_REQUEST['txtCode'];
                $user_phone = $_REQUEST["txtPhone"];
                $verifyRs = M('verify_code')->where("id = '{$codeId}' and content = {$txtCode} and phone='{$user_phone}'")->count('id');
                if (!$verifyRs) {
                    $jsons["msg"] = "验证码错误！";
                    outJson($jsons);
                }
                // if($verifyRs['phone']!=$user_phone){
                //     $jsons["msg"]="注册手机号和发送验证码手机号不符！";
                //     outJson($jsons);
                // }
                $jsons['status'] = "1";
                $jsons['txtPhone'] = $user_phone;
                $jsons["msg"] = "手机验证成功，请设置密码！";
                outJson($jsons);
                break;

            case 'successreg':
                $data['user_name'] = text($_REQUEST['txtPhone']);
                $data['user_phone'] = text($_REQUEST['txtPhone']);
                $data['user_pass'] = md5($_REQUEST['txtPwd']);
                $maps['user_phone'] = $data['user_phone'];
                $infos = M('members')->where($maps)->find();
                if (!empty($infos["user_pass"])) {
                    $jsons["status"] = "0";
                    $jsons["msg"] = "你已注册成功，请不要重复提交！";
                    outJson($jsons);
                }
                //获取推荐人
                $txtIncode = text($_REQUEST['txtIncode']);
                if (!empty($txtIncode)) {
                    $txtRecUserid = M('members')->where("incode='" . $txtIncode . "'")->getField('id');
                    if (!empty($txtRecUserid)) {
                        $data['recommend_id'] = $txtRecUserid;
                    } else {
                        $jsons["msg"] = "推荐人不存在，若没有推荐人请留空。";
                        outJson($jsons);
                    };
                }
                if (session("tmp_invite_user")) $data['recommend_id'] = session("tmp_invite_user");
                $data['reg_time'] = time();
                $data['reg_ip'] = get_client_ip();
                $data['lastlog_time'] = time();
                $data['lastlog_ip'] = get_client_ip();
                $data['incode'] = getincode();
                $newid = M('members')->add($data);
                if ($newid) {
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
                    if ($this->glo['award_reg'] > 0) {
                        pubExperienceMoney($newid, $this->glo['award_reg'], 4, 30);
                    }
                    //pubBonusByRules($newid,2);
                    $jsons['uid'] = text($newid);
                    $jsons["status"] = "1";
                    $jsons["msg"] = "注册成功！";
                    outJson($jsons);
                } else {
                    $jsons["msg"] = "注册失败，请重试！";
                    outJson($jsons);
                }
                break;

            default:
                $jsons["msg"] = "注册失败";
                outJson($jsons);
                break;
        }
    }
    /**
     * 发送验证码
     */
    public function sendcode() {
        $jsons["status"] = "0";
        $code = rand(100000, 999999);
        $txtPhone = $_REQUEST["txtPhone"];
        $act = isset($_REQUEST["act"]) ? text($_REQUEST["act"]) : '';
        switch ($act) {
            case 'reg':
                $content = "验证码已经发送至您的手机！123;";
                break;

            case 'findpass':
                $content = "验证码已经发送至您的手机！123;";
                break;

            default:
                $content = "验证码已经发送至您的手机！123;";
                break;
        }
        if ($txtPhone == "" || !preg_match("/^1\d{10}$/", $txtPhone)) {
            $jsons["status"] = "0";
            $jsons["msg"] = "手机号格式不正确！";
            outJson($jsons);
        }
        $map['user_phone'] = text($txtPhone);
        $count = M('members')->where($map)->count('id');
        if ($act == 'reg' && $count > 0) {
            $jsons["status"] = "0";
            $jsons["msg"] = "手机号已存在！";
            outJson($jsons);
        } elseif ($act == 'findpass' && $count <= 0) {
            $jsons["status"] = "0";
            $jsons["msg"] = "手机号不存在！";
            outJson($jsons);
        }
        $datag = get_global_setting();
        $typed = $datag["dx_type"];
        if ($typed == 1) {
            Notice1(1, 0, array(
                'phone' => $txtPhone,
                "code" => $code
            ));
        } else {
            sendsms($txtPhone, $content);
        }
        $addData = array(
            'content' => $code,
            'add_time' => time() ,
            'phone' => $txtPhone
        );
        $codeId = M('verify_code')->add($addData);
        if ($codeId) {
            $jsons["status"] = "1";
            $jsons["codeId"] = $codeId;
            $jsons["msg"] = '验证码已经发送至您的手机！123';
            //$jsons["msg"]= '验证码已经发送至您的手机！';

        }
        outJson($jsons);
    }
    // public function idcard_apply()
    // {
    //     $uid             = $this->uid=$_REQUEST["uid"];
    //     $jsons["status"] = '0';
    //     $real_name = text($_REQUEST["realname"]);// or die;
    //     $cardid = text($_REQUEST["idcard"]);// or die;
    //     if(empty($real_name)||empty($cardid)){
    //           $jsons["msg"] = "用户名或者身份证为空！";
    //     }else{
    //         $data['real_name'] = $real_name;
    //         $data['idcard'] = $cardid;
    //         $data['up_time'] = time();
    //         /////////////////////////
    //         $data1['idcard'] = text($cardid);
    //         $data1['up_time'] = time();
    //         $data1['uid'] = $uid;
    //         $datag = get_global_setting();
    //         $data1['status'] = 0;
    //         $hasApply = M('name_apply')->where("uid = {$uid}")->count('uid');
    //         if ($hasApply) {
    //             M('name_apply')->where("uid ={$uid}")->save($data1);
    //         } else {
    //             M('name_apply')->add($data1);
    //         }
    //         $hasUid = M('member_info')->getFieldByIdcard($data['idcard'], 'uid');
    //         if ($hasIdcard && $hasUid != $uid) {
    //             $jsons["msg"] = "此身份证号码已使用！";
    //             outJson($jsons);
    //         }
    //         $hasInfo = M('member_info')->where("uid = {$uid}")->count('uid');
    //         if ($hasInfo) {
    //             $newid = M('member_info')->where("uid = {$uid}")->save($data);
    //         } else {
    //             $data['uid'] = $uid;
    //             $newid = M('member_info')->add($data);
    //         }
    //         if (isset($newid) && $newid) {
    //             $realname = $data['real_name'];
    //             $idcard   = $data['idcard'];
    //             $rsVerify = getIdVerify($realname,$idcard);
    //             if($rsVerify){
    //                 addInnerMsg($this->uid,"实名认证通过",'自动认证');
    //                 $ms = M('members_status')->where("uid={$this->uid}")->setField('id_status',1);
    //                 $name_apply_data['status']=1;
    //                 $name_apply_data['deal_info']= '自动认证';
    //                 $new = M("name_apply")->where("uid={$this->uid}")->save($name_apply_data);
    //                 $jsons["status"] = '1';
    //                 $jsons["msg"] =  "实名认证通过";
    //                 outJson($jsons);
    //             }else{
    //                 $ms=M('members_status')->where("uid={$this->uid}")->setField('id_status',3);
    //                 if($ms==1){
    //                     $jsons["status"] = '1';
    //                     $jsons["msg"] =  "实名认证申请审核中……";
    //                     outJson($jsons);
    //                 }else{
    //                     $dt['uid'] = $this->uid;
    //                     $dt['id_status'] = 3;
    //                     $rs = M('members_status')->add($dt);
    //                     if ($rs) {
    //                         $jsons["status"] = '1';
    //                         $jsons["msg"] = "实名认证申请已提交，请耐心等待审核……";
    //                         outJson($jsons);
    //                     }
    //                 }
    //             }
    //         }
    //     }
    //     $jsons["msg"] = "申请失败，请重试！";
    //     outJson($jsons);
    // }
    public function idcard_apply() {
        //{"realname":realname,"idcard":idcard,"user_type":user_type,"riskint":riskint,"phone":phone,"Verification":Verification},
        $isyindao = $_REQUEST["isyindao"];
        $passd = $_REQUEST["pin_pass"];
        if (isset($passd) && !empty($passd)) {
            $chars = "/^\d{6}$/";
            if (!preg_match($chars, $_REQUEST["pin_pass"])) {
                $jsons['status'] = "0";
                $jsons["msg"] = "请输入6位纯属数字的支付码密码";
                outJson($jsons);
            }
        }
        if ($isyindao != "1") {
            $codeId = @$_REQUEST['codeId'];
            $txtCode = @$_REQUEST['txtCode'];
            //$user_phone=$_REQUEST['phone'];
            $verifyRs = M('verify_code')->where("id = '{$codeId}' and content = {$txtCode}")->count('id');
            if ($verifyRs != 1) {
                $jsons["msg"] = "验证码错误！";
                outJson($jsons);
            }
        }
        $uid = $this->uid = $_REQUEST["uid"];
        $minfo = getMinfo($this->uid, true);
        $user_type = $_REQUEST['user_type'] ? $_REQUEST['user_type'] : 1;
        $riskint = intval($_REQUEST['riskint']);
        $phone = $minfo['user_phone'];
        $data['real_name'] = text($_REQUEST['realname']);
        $data['idcard'] = text($_REQUEST['idcard']);
        $data['riskint'] = text($_POST['riskint']);
        $data['company_name'] = text($_POST['company_name']);
        $data['company_idcard'] = text($_POST['company_idcard']);
        $data['company_jd'] = text($_POST['company_jd']);
        if (empty($data['real_name']) || empty($data['idcard'])) {
            $jsons["status"] = '0';
            $jsons["msg"] = "请填写真实姓名和身份证号码";
            outJson($jsons);
        }
        $xuid = M('member_info')->getFieldByIdcard($data['idcard'], 'uid');
        if ($xuid > 0 && $xuid != $this->uid) {
            $jsons["status"] = '0';
            $jsons["msg"] = "此身份证号码已被人使用";
            outJson($jsons);
        }
        $data['up_time'] = time();
        $realname = $data['real_name'];
        $idcard = $data['idcard'];
        include './../RenzhengAction.class.php';
        $renzheng = new RenzhengAction();
        $res = $renzheng->main($realname, $idcard);
        if ($res["status"] == "0") {
            outJson($res);
        } //var_dump($res);exit();
        if ($user_type == 1) {
            $rsVerify = create_agreeperson($realname, $idcard, $phone, $this->uid);
        } else {
            $rsVerify = create_agreecompany($data['company_name'], $data['company_idcard'], $phone, $this->uid);
        }
        $minfo = M('members')->where("id={$this->uid}")->find();
        if (isset($minfo['recommend_id']) && !empty($minfo['recommend_id'])) {
            $recommend_ida = $minfo['recommend_id'];
        } else if (isset($minfo['recommend_bid']) && !empty($minfo['recommend_bid'])) {
            $recommend_ida = $minfo['recommend_bid'];
        } else if (isset($minfo['recommend_cid']) && !empty($minfo['recommend_cid'])) {
            $recommend_ida = $minfo['recommend_cid'];
        }
        if ($rsVerify) {
            if ($user_type == 1) {
                create_personmoulage($this->uid);
            } else {
                create_companymoulage($this->uid);
            }
            /////////////////////////
            $data1['idcard'] = text($_POST['idcard']);
            $data1['up_time'] = time();
            $data1['uid'] = $this->uid;
            $data1['status'] = 0;
            $b = M('name_apply')->where("uid = {$this->uid}")->count('uid');
            if ($b == 1) {
                M('name_apply')->where("uid ={$this->uid}")->save($data1);
            } else {
                M('name_apply')->add($data1);
            }
            //if($isimg!=1) ajaxmsg("请先上传身份证正面图片",0);
            //if($isimg2!=1) ajaxmsg("请先上传身份证反面图片",0);
            $c = M('member_info')->where("uid = {$this->uid}")->count('uid');
            $data['signerid'] = $minfo['signerid'];
            if ($c == 1) {
                $newid = M('member_info')->where("uid = {$this->uid}")->save($data);
            } else {
                $data['uid'] = $this->uid;
                $newid = M('member_info')->add($data);
            }
            if ($newid) {
                //M('members')->where("id ={$this->uid}")->save($data2);
                $ms = M('members_status')->where("uid={$this->uid}")->count();
                if ($ms > 0) {
                    $rs = 1;
                } else {
                    $dt['uid'] = $this->uid;
                    $dt['id_status'] = 0;
                    $rs = M('members_status')->add($dt);
                    $rs = 1;
                }
                if ($rs) {
                    $pubDataa = array(
                        'uid' => $recommend_ida,
                        'money_bonus' => "18.0",
                        'start_time' => date('Y-m-d 00:00:00', time()) ,
                        'end_time' => date('Y-m-d 23:59:59', time() + 30 * 24 * 60 * 60) ,
                        'bonus_invest_min' => '1000'
                    );
                    if (isset($recommend_ida) && !empty($recommend_ida)) {
                        pubBonus($pubDataa);
                    }
                    pubBonusByRules($this->uid, 2);
                    addInnerMsg($this->uid, "实名认证通过", '自动认证');
                    $ms = M('members_status')->where("uid={$this->uid}")->setField('id_status', 1);
                    $name_apply_data['status'] = 1;
                    $name_apply_data['deal_info'] = '自动认证';
                    $new = M("name_apply")->where("uid={$this->uid}")->save($name_apply_data);
                    if (isset($passd) && !empty($passd)) {
                        $pin_pass = md5($_REQUEST["pin_pass"]);
                        M('members')->where("id = {$this->uid}")->save(["user_type" => $user_type, "riskint" => $riskint, "is_guide" => 1, "pin_pass" => $pin_pass]);
                    } else {
                        M('members')->where("id = {$this->uid}")->save(["user_type" => $user_type, "riskint" => $riskint, "is_guide" => 1]);
                    }
                    if (!($ms === false)) {
                        $jsons["status"] = '1';
                        $jsons["msg"] = "实名认证通过";
                        outJson($jsons);
                    }
                    // $jsons["status"] = '0';
                    // $jsons["msg"] = "实名认证通过";
                    // outJson($jsons);

                }
            }
        }
        $jsons["status"] = '0';
        $jsons["msg"] = "此身份证号码已被注册或错误！";
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
    public function findpass() {
        $jsons['status'] = "0";
        $per = C('DB_PREFIX');
        $map['user_phone'] = text($_REQUEST['txtPhone']);
        $txtPassword = text($_REQUEST['txtPassword']);
        //$txtRePassword = text($_REQUEST['txtRePassword']);
        if ($map['user_phone'] == "") {
            $jsons['msg'] = "手机号不能为空";
            outJson($jsons);
        }
        if ($txtPassword == "") {
            $jsons['msg'] = "密码不能为空";
            outJson($jsons);
        }
        // if($txtPassword !=$txtRePassword){
        //     $jsons['msg'] = "新密码与确认密码不同";
        //     outJson($jsons);
        // }
        $user = M('members')->where($map)->find();
        if ($user["id"] == "") {
            $jsons['msg'] = "暂无此手机绑定的用户";
            outJson($jsons);
        }
        $codeId = @$_REQUEST['codeId'];
        $txtCode = @$_REQUEST['txtCode'];
        $user_phone = text($_REQUEST['txtPhone']);
        $verifyRs = M('verify_code')->where("id = '{$codeId}' and content = {$txtCode} and phone='{$user_phone}'")->count('id');
        if ($verifyRs != 1) {
            $jsons['msg'] = "验证码错误！" . M('verify_code')->getlastsql();
            outJson($jsons);
        }
        $oldpass = M("members")->getFieldById($user["id"], 'user_pass');
        if ($oldpass == md5($txtPassword)) {
            $newid = true;
        } else {
            $newid = M()->execute("update {$per}members set `user_pass`='" . md5($txtPassword) . "' where id=" . $user["id"]);
        }
        if ($newid) {
            $jsons['status'] = "1";
            $jsons['msg'] = "修改成功！";
            outJson($jsons);
        } else {
            $jsons['msg'] = "修改失败，请重试！";
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
    public function findpinpass() {
        $jsons['status'] = "0";
        $per = C('DB_PREFIX');
        $map['user_phone'] = text($_REQUEST['txtPhone']);
        $txtPassword = text($_REQUEST['txtPassword']);
        $txtRePassword = text($_REQUEST['txtRePassword']);
        if ($map['user_phone'] == "") {
            $jsons['msg'] = "手机号不能为空";
            outJson($jsons);
        }
        if ($txtPassword == "") {
            $jsons['msg'] = "密码不能为空";
            outJson($jsons);
        }
        if ($txtPassword != $txtRePassword) {
            $jsons['msg'] = "新密码与确认密码不同";
            outJson($jsons);
        }
        $user = M('members')->where($map)->find();
        if ($user["id"] == "") {
            $jsons['msg'] = "暂无此手机绑定的用户";
            outJson($jsons);
        }
        $codeId = @$_REQUEST['codeId'];
        $txtCode = @$_REQUEST['txtCode'];
        $user_phone = text($_REQUEST['txtPhone']);
        $verifyRs = M('verify_code')->where("id = '{$codeId}' and content = {$txtCode} and phone='{$user_phone}'")->count('id');
        if ($verifyRs != 1) {
            $jsons['msg'] = "验证码错误！";
            outJson($jsons);
        }
        $oldpass = M("members")->getFieldById($user["id"], 'pin_pass');
        if ($oldpass == md5($txtPassword)) {
            $newid = true;
        } else {
            $newid = M()->execute("update {$per}members set `pin_pass`='" . md5($txtPassword) . "' where id=" . $user["id"]);
        }
        if ($newid) {
            $jsons['status'] = "1";
            $jsons['msg'] = "修改成功！";
            outJson($jsons);
        } else {
            $jsons['msg'] = "修改失败，请重试！";
            outJson($jsons);
        }
    }
    public function memberindex() {
        $jsons['status'] = "0";
        $this->uid = $uid = $_REQUEST['uid'];
        $minfo = getMinfo($uid, true);
        $this->hehe($uid);
        //var_dump($minfo);
        // 站内消息
        $msgcount = M('inner_msg')->where('uid=' . $this->uid . ' and status = 0')->count();
        //最近30天收益
        $stime = strtotime(date('Y-m-d 00:00:00', strtotime("-30 day")));
        $invest = M('borrow_investor')->where('investor_uid = ' . $this->uid . ' and repayment_time >=' . $stime)->sum('investor_interest');
        $mx = M("borrow_investor  i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where(" i.investor_uid={$this->uid}   and b.borrow_status=6  and i.status !=3")->sum("investor_capital");
        $omap["uid"] = $this->uid;
        $omap["action"] = 3;
        $dd["wfk"] = M("order")->where($omap)->count();
        $omap["action"] = 0;
        $dd["dfh"] = M("order")->where($omap)->count();
        $omap["action"] = 1;
        $dd["dsh"] = M("order")->where($omap)->count();
        $omap["action"] = 2;
        $dd["ywc"] = M("order")->where($omap)->count();
        $jsons["ddzt"] = $dd;
        $ymap = array();
        $ymap['b.borrow_status'] = 6;
        $ymap['b.xs_time'] = array(
            "gt",
            time()
        );
        $ymap['i.investor_uid'] = $this->uid;
        $ymap['b.pid'] = 1;
        $ycdd["ydaishou"] = M('borrow_investor i')->where($ymap)->join("lzh_borrow_info b ON b.id=i.borrow_id")->join("lzh_members m ON m.id=b.borrow_uid")->count();
        $ymap['b.pid'] = 3;
        $ycdd["gdaishou"] = M('borrow_investor i')->where($ymap)->join("lzh_borrow_info b ON b.id=i.borrow_id")->join("lzh_members m ON m.id=b.borrow_uid")->count();
        $ymap['b.xs_time'] = array(
            "lt",
            time()
        );
        $ymap['b.pid'] = 1;
        $ycdd["yshoumai"] = M('borrow_investor i')->where($ymap)->join("lzh_borrow_info b ON b.id=i.borrow_id")->join("lzh_members m ON m.id=b.borrow_uid")->count();
        $ymap['b.pid'] = 3;
        $ycdd["gshoumai"] = M('borrow_investor i')->where($ymap)->join("lzh_borrow_info b ON b.id=i.borrow_id")->join("lzh_members m ON m.id=b.borrow_uid")->count();
        $ymap = array();
        $ymap['i.investor_uid'] = $this->uid;
        $ymap['i.status'] = 5;
        $ymap['b.pid'] = 1;
        $ycdd["ywancheng"] = M('borrow_investor i')->where($ymap)->join("lzh_borrow_info b ON b.id=i.borrow_id")->join("lzh_members m ON m.id=b.borrow_uid")->count();
        $ymap['b.pid'] = 3;
        $ycdd["gwancheng"] = M('borrow_investor i')->where($ymap)->join("lzh_borrow_info b ON b.id=i.borrow_id")->join("lzh_members m ON m.id=b.borrow_uid")->count();
        $jsons["ycdd"] = $ycdd;
        $jsons["minfo"] = array(
            //'mid'=>$uid,
            'mid' => $this->jwt->getToken($uid) ,
            'user_name' => $minfo['user_name'],
            //'userpic'=>$this->domainUrl . get_app_avatar($uid),
            'userpic' => $minfo['user_img'],
            'all_money' => getFloatvalue($minfo['account_money'] + $mx + $minfo['money_freeze'] + $minfo['yubi'] + $minfo['yubi_freeze'], 2) ,
            'account_money' => $minfo['account_money'],
            'ky_money' => $minfo['account_money'] + $minfo['yubi'],
            'freeze_money' => $minfo['money_freeze'] + $minfo['yubi_freeze'],
            //'experience_money'=>$minfo['money_experience'],
            'experience_money' => 0, //为了显示积分
            'msgcount' => $msgcount,
            'invest' => $invest ? $invest : '0.00',
            'credits' => $minfo['credits'],
            'nick_name' => $minfo['nick_name'],
            'yubi' => $minfo['yubi'],
            'openid' => $minfo['openid'],
        );
        // 设置手机验证状态
        $renzheng = M("members_status")->where("uid=$uid")->find();
        $rzsttatus["phone_status"] = $renzheng["phone_status"];
        $rzsttatus["id_status"] = $renzheng["id_status"];
        $rzsttatus["email_status"] = $renzheng["email_status"];
        $rzsttatus["is_vip"] = $renzheng["is_vip"];
        if ($minfo["pin_pass"] != '') {
            $rzsttatus["is_pin_pass"] = 1;
        } else {
            $rzsttatus["is_pin_pass"] = 0;
        }
        $typelist = M('pro_category')->field('id,type_name')->select();
        $jsons['typelist'] = $typelist;
        $jsons['members_status'] = $rzsttatus;
        $jsons['status'] = "1";
        outJson($jsons);
    }
    public function yzfenlei() {
        $typelist = M('pro_category')->field('id,type_name')->select();
        $jsons['typelist'] = $typelist;
        $jsons['status'] = "1";
        outJson($jsons);
    }
    public function zichan() {
        $uid = $_REQUEST['uid'];
        $this->uid = $uid;
        $mx = M("borrow_investor  i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where(" i.investor_uid={$this->uid}   and b.borrow_status=6  and i.status !=3")->sum("investor_capital");
        $mx1 = M("borrow_investor i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where(" i.investor_uid={$this->uid} and b.pid = 1  and b.borrow_status=6 and i.status !=3 ")->sum("investor_capital");
        $mx2 = M("borrow_investor i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where(" i.investor_uid={$this->uid} and b.pid = 2  and b.borrow_status=6 and i.status !=3  ")->sum("investor_capital");
        $mx3 = M("borrow_investor i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where(" i.investor_uid={$this->uid} and b.pid = 3  and b.borrow_status=6 and i.status !=3 ")->sum("investor_capital");
        $ms = M("investor_detail i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where("investor_uid={$this->uid} and b.borrow_status!=5   and i.status !=3 ")->sum("receive_interest");
        $mss1 = M("investor_detail i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where("investor_uid={$this->uid} and b.borrow_status!=5   and i.status !=3 ")->sum("receive_chajia");
        $ms1 = M("investor_detail i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where("investor_uid={$this->uid} and b.pid = 1  and b.borrow_status=7  ")->sum("receive_interest");
        $ms2 = M("investor_detail i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where("investor_uid={$this->uid} and b.pid = 2  and b.borrow_status=7    ")->sum("receive_interest");
        $ms3 = M("investor_detail i")->join("lzh_borrow_info b on i.borrow_id=b.id")->where("investor_uid={$this->uid} and b.pid = 3  and b.borrow_status=7    ")->sum("receive_interest");
        $va = m("borrow_investor")->field($field)->where("investor_uid = {$this->uid}")->group("status")->select();
        ////////////////////////////////////////////////////////////////
        $minfo = getMinfo($this->uid, true);
        $member_withdraw = M("member_withdraw")->where("uid={$this->uid} and (withdraw_status=0 or withdraw_status=1)")->sum("withdraw_money");
        $levename = getLeveName($minfo["credits"]);
        $levetixian = getLeveTixian($minfo["credits"]);
        //$renzheng=M("members_status")->where("uid=$uid")->find();
        $rzsttatus["keyong"] = getFloatValue($minfo['account_money'] + $minfo['back_money'], 2);
        //减去已售出的
        $rzsttatus["zaitou"] = $mx;
        $rzsttatus["tgyue"] = $minfo["yongjin"] == null ? 0 : $minfo["yongjin"];
        $tgdongjie = M("pt_stop")->where("status=0 and tuid=" . $this->uid)->sum('num');
        $rzsttatus["tgdongjie"] = $tgdongjienull == null ? 0 : $tgdongjienull;
        $tgzong = M("pt_stop")->where("status in (0,1) and tuid=" . $this->uid)->sum('num');
        $rzsttatus["tgzong"] = $tgzong == null ? 0 : $tgzong;
        $rzsttatus["tixian"] = $member_withdraw;
        $tian = ceil((time() - $minfo['reg_time']) / 60 / 60 / 24);
        $rzsttatus["zhuce"] = date("Y-m-d", $minfo["reg_time"]);
        $rzsttatus["tian"] = $tian;
        $tgsy = M("pt_stop")->where("status =1 and tuid=" . $this->uid)->sum('num');
        $rzsttatus["tgsy"] = $tgsy == null ? 0 : $tgsy;
        $rzsttatus["ycsy"] = $ms + $mss1;
        $rzsttatus["zongshouyi"] = $rzsttatus["ycsy"] + $rzsttatus["tgsy"];
        $dsbj = M('borrow_investor bi')->join($pre . 'borrow_info b on b.id = bi.borrow_id')->where("investor_uid={$uid} and b.borrow_status =6 and bi.status!=3")->sum('investor_capital');
        // var_dump($zz);
        $all_money = getFloatvalue($minfo['account_money'] + $dsbj + $minfo['money_freeze'] + $minfo['yubi'] + $minfo['yubi_freeze'] + $tgzong, 2);
        $rzsttatus["all_money"] = $all_money;
        $rzsttatus["yubi"] = $minfo['yubi'];
        $rzsttatus["account_money"] = $minfo['account_money'];
        $rzsttatus["money_freeze"] = $minfo['money_freeze'];
        $yall_money = getFloatvalue($minfo['account_money'] + $dsbj + $minfo['money_freeze'] + $minfo['yubi'] + $minfo['yubi_freeze'], 2);
        $rzsttatus["yall_money"] = $yall_money;
        $jsons['zichan'] = $rzsttatus;
        $jsons['status'] = "1";
        outJson($jsons);
    }
    /**
     * 用户基息
     * @param uid  用户id
     */
    public function userinfo() {
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
        $minfo['is_bank'] = $bank_num['bank_num'] > 0 ? 1 : 0;
        $bankname = C('BANK_NAME');
        if ($minfo) {
            $jsons['status'] = "1";
            $jsons['minfo'] = array(
                'mid' => $minfo['id'],
                'userpic' => $minfo['userpic'],
                'user_name' => $minfo['user_name'],
                'real_name' => $membersStatus['id_status'] == 1 ? $minfo['real_name'] : '',
                'idcard' => $membersStatus['id_status'] == 1 ? $minfo['idcard'] : '',
                'user_phone' => $minfo['user_phone'],
                'is_bank' => $minfo['is_bank'],
                'id_status' => $membersStatus['id_status'],
                'bank_num' => substr($bank_num['bank_num'], -4) ,
                'bank_name' => $bankname[$bank_num['bank_name']],
            );
        } else {
            $jsons['status'] = "0";
            $jsons['msg'] = "获取用户信息失败！";
        }
        outJson($jsons);
    }
    /**
     * 修改手机号
     * @param uid  用户id
     */
    public function changephone() {
        $per = C('DB_PREFIX');
        $uid = $_REQUEST['uid'];
        $act = isset($_REQUEST['act']) ? text($_REQUEST['act']) : '';
        switch ($act) {
            case 'firstphone':
                $minfo = getMinfo($uid, "m.id,m.user_phone");
                $jsons['status'] = "1";
                $jsons['minfo'] = array(
                    'mid' => $minfo['id'],
                    'user_phone' => $minfo['user_phone'],
                );
                break;

            case 'firstphoneact':
                $user_phone = text($_REQUEST['txtPhone']);
                if (empty($user_phone)) {
                    $jsons["msg"] = "手机验证失败，手机号不能为空！";
                    outJson($jsons);
                }
                $codeId = @$_REQUEST['codeId'];
                $txtCode = @$_REQUEST['txtCode'];
                //$user_phone = text($_REQUEST['txtPhone']);
                $verifyRs = M('verify_code')->where("id = '{$codeId}' and content = {$txtCode} and phone='{$user_phone}'")->count('id');
                if ($verifyRs != 1) {
                    $jsons["msg"] = "验证码错误！";
                    outJson($jsons);
                }
                $jsons['status'] = "1";
                $jsons['txtPhone'] = $user_phone;
                $jsons["msg"] = "手机验证成功，请设置新的手机号！";
                break;

            case 'successphone':
                $user_phone = text($_REQUEST['newPhone']);
                if (empty($user_phone)) {
                    $jsons["msg"] = "手机验证失败，手机号不能为空！";
                    outJson($jsons);
                }
                $codeId = @$_REQUEST['codeId'];
                $txtCode = @$_REQUEST['txtCode'];
                //$user_phone = text($_REQUEST['txtPhone']);
                $verifyRs = M('verify_code')->where("id = '{$codeId}' and content = {$txtCode} and phone='{$user_phone}'")->count('id');
                if ($verifyRs != 1) {
                    $jsons["msg"] = "验证码错误！";
                    outJson($jsons);
                }
                $newid = M()->execute("update {$per}members set `user_phone`='" . $user_phone . "' where id=" . $uid);
                if ($newid) {
                    $jsons['status'] = "1";
                    $jsons['msg'] = "修改成功！";
                } else {
                    $jsons['msg'] = "修改失败，请重试！";
                }
                break;
        }
        outJson($jsons);
    }
    /*



    *    消息中心



    */
    public function sysmsg() {
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
                foreach ($list as $v) {
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
                    $json["msg"] = "已经是最后一页啦";
                    outJson($json);
                }
                if ($list) {
                    $json['list'] = $str;
                    $json['msg'] = "成功";
                    $json['status'] = "1";
                } else {
                    $json['list'] = array();
                    $json['msg'] = "暂无信息";
                    $json['status'] = "1";
                }
                break;

            case 'personalnews':
                if (empty($uid)) {
                    $json['msg'] = "信息有误，重新填写";
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
                foreach ($list as $v) {
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
                    $json["msg"] = "已经是最后一页啦";
                    outJson($json);
                }
                if ($list) {
                    $json['list'] = $str;
                    $json['msg'] = "成功";
                    $json['status'] = "1";
                } else {
                    $json['list'] = array();
                    $json['msg'] = "暂无信息";
                    $json['status'] = "1";
                }
                break;
        }
        outJson($json);
    }
    /*



    *   查看消息中心



    */
    public function msgview() {
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
    public function article_view() {
        $id = intval($_REQUEST['id']);
        $article = M('article')->where("id={$id}")->find();
        // $jsons["title"] = $article['title'];
        // $jsons["content"] = $article['art_content'];
        // $jsons["add_time"] = date("Y-m-d  H:i:s", $article["art_time"]);
        $jsons['data'] = $article;
        $jsons['status'] = 1;
        outJson($jsons);
    }
    /*



    *   新手指南



    */
    public function category_view() {
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
    public function investlog() {
        $uid = $_REQUEST['uid'];
        $act = isset($_REQUEST['act']) ? text($_REQUEST['act']) : 'income';
        $field = "status,count(id) as num,sum(investor_capital) as investor_capital,sum(reward_money) as reward_money,sum(investor_interest) as investor_interest,sum(receive_capital) as receive_capital,sum(receive_interest) as receive_interest";
        $investNum = m("borrow_investor")->field($field)->where("investor_uid = {$uid}")->group("status")->select();
        foreach ($investNum as $v) {
            $investStatus[$v['status']] = $v;
        }
        $jsons['status'] = 1;
        $jsons['dsbj'] = getfloatvalue(floatval($investStatus[4]['investor_capital']) , 2); //待收本金
        $jsons['dssy'] = getfloatvalue(floatval($investStatus[4]['investor_interest']) , 2); //待收收益
        $jsons['yhsy'] = getfloatvalue(floatval($investStatus[5]['investor_interest']) , 2); //已获收益
        $map['investor_uid'] = $_REQUEST['uid'];
        switch ($act) {
            case 'income':
                //收益中
                $map['status'] = 4;
                $list = get_TenderList($map, 10);
                break;

            case 'haveclosed':
                //已结清
                $map['status'] = array(
                    "in",
                    "5,6"
                );
                $list = get_TenderList($map, 10);
                break;
        }
        $jsons['list'] = $list ? $list : array();
        outJson($jsons);
    }
    /*



    *   待回款



    */
    public function tendout() {
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
                $map['status'] = array(
                    "in",
                    "5,6"
                );
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
    public function investor_details() {
        $uid = $_REQUEST['uid'];
        $id = $_REQUEST['id'];
        $fieldx = "bi.investor_capital,bi.add_time,bi.deadline,bi.investor_interest,bi.member_interest_rate_id,sum(bi.investor_capital+bi.investor_interest) as allcap,b.borrow_name,b.borrow_interest_rate,b.borrow_duration,b.borrow_status";
        $investinfo = M("borrow_investor bi")->field($fieldx)->join("lzh_borrow_info b ON bi.borrow_id = b.id")->where("bi.id={$id} and bi.investor_uid = {$uid}")->order("bi.id DESC")->find();
        $res = array();
        $bstatus = array(
            '0' => '初审中',
            '1' => '预热中',
            '2' => '众筹中',
            '4' => '复审中',
            '6' => '已成功',
            '7' => '已分红',
            '8' => '已结项',
            '9' => '',
            '10' => '',
            '-1' => '初审未通过',
            '3' => '复审未通过',
            '5' => '复审未通过',
        );
        foreach ($investinfo as $key => $value) {
            if ($key == 'borrow_status') {
                $res['list']['borrow_status'] = $bstatus[$value];
            } else if ($key == 'add_time') {
                $res['add_time'] = date('Y-m-d', $value);
            } else if ($key == 'deadline') {
                $res['deadline'] = date('Y-m-d', $value);
            } else {
                $res[$key] = $value;
            }
        }
        $res['list']['hkje'] = $investinfo['allcap'];
        $res['list']['qs'] = '第一期';
        $res['list']['hkbj'] = $investinfo['investor_capital'];
        $res['list']['hklx'] = $investinfo['investor_interest'];
        $res['list']['xtjl'] = '0.00';
        $res['list']['glfy'] = '0.00';
        $res['list']['yqfy'] = '0.00';
        //加息券
        if ($investinfo['member_interest_rate_id'] != 0) {
            $rid = $investinfo['member_interest_rate_id'];
            $canUseInterest = M('member_interest_rate')->where(" id = '{$rid}' and uid='{$uid}'")->find();
            $res['list']['jxjl'] = round($investinfo['investor_capital'] * $canUseInterest['interest_rate'] / 100 / 360 * $investinfo['borrow_duration'], 2);
        } else {
            $res['list']['jxjl'] = 0;
        }
        $res['list']['start_time'] = date('Y-m-d', $investinfo['add_time']);
        $res['list']['end_time'] = date('Y-m-d', $investinfo['deadline']);
        $res['status'] = 1;
        outJson($res);
    }
    /*



    *   滚动时间范围



    */
    public function timeframe() {
        $jsons['start_time'] = '2015-01';
        $jsons['end_time'] = '2035-12';
        $jsons['status'] = 1;
        outJson($jsons);
    }
    /**
     *  银行卡添加修改
     * @param $uid
     */
    public function banksave() {
        include './../RenzhengAction.class.php';
        $this->uid = $uid = $_REQUEST['uid'];
        $isyindao = $_REQUEST["isyindao"];
        if ($isyindao != "1") {
            $codeId = @$_REQUEST['codeId'];
            $txtCode = @$_REQUEST['txtcode'];
            $verifyRs = M('verify_code')->where("id = '{$codeId}' and content = {$txtCode}")->count('id');
            if ($verifyRs != 1) {
                $jsons["msg"] = "验证码错误！";
                outJson($jsons);
            }
        }
        $data['bank_num'] = text($_POST['bank_num']);
        $data['bank_address'] = text($_POST['bank_address']);
        $data['bank_name'] = text($_POST['bank_name']);
        $data['realname'] = text($_POST['real_name']);
        $data['bank_province'] = text($_POST['province']);
        $data['bank_city'] = text($_POST['city']);
        $data['phone'] = text($_POST['phone']);
        $data['add_ip'] = get_client_ip();
        $data['add_time'] = time();
        $data['idcard'] = M('member_info')->where("uid={$uid}")->getField('idcard');
        $data['is_default'] = intval($_POST['is_default']);
        $renzheng = new RenzhengAction();
        $res = $renzheng->mainyhk($data['realname'], trim($data['idcard']) , $data['bank_num'], $data['phone']);
        if ($res["status"] == "0") {
            outJson($res);
        }
        if ($data['is_default'] == 1) {
            $newid = M('member_banks')->where("uid = " . $this->uid)->save(["is_default" => 0]);
        }
        $data['uid'] = $this->uid;
        $real_name = M('member_info')->where("uid={$uid}")->getField('real_name');
        if ($real_name != $data['realname']) {
            $jsons['status'] = '0';
            $jsons['msg'] = '操作失败！非法操作！！！';
            outJson($jsons);
        }
        if (!isset($_REQUEST['id']) || empty($_REQUEST['id'])) {
            $mapp["bank_num"] = $data['bank_num'];
            $rinfo = M('member_banks')->where($mapp)->find();
            if ($rinfo) {
                $json['status'] = "0";
                $json['msg'] = "您已经绑定过此银行卡";
                outJson($json);
            }
            $rs = M('member_banks')->add($data);
        } else {
            $data['id'] = $_POST['id'];
            $rs = M('member_banks')->save($data);
        }
        if ($rs) {
            MTip('chk2', $this->uid);
            $jsons['status'] = '1';
            $jsons['msg'] = '操作成功！';
        } else {
            $jsons['status'] = '0';
            $jsons['msg'] = '操作失败！';
        }
        outJson($jsons);
    }
    //删除银行卡
    public function delbank() {
        $vobank = M("member_banks")->where("id=" . $_REQUEST['id'])->delete();
        if ($vobank) {
            $jsons['status'] = '1';
            $jsons['msg'] = '操作成功！';
        } else {
            $jsons['status'] = '0';
            $jsons['msg'] = '操作失败！';
        }
    }
    /**
     *  银行卡发卡行
     * @param $uid
     */
    public function bankinfo() {
        $jsons['bank_list'] = C('BANK_NAME');
        $jsons['status'] = 1;
        outJson($jsons);
    }
    /*



    *   银行卡列表



    */
    public function banklist() {
        $uid = $_REQUEST['uid'];
        M("member_banks")->where("uid=" . $uid . " and (bank_num ='' or bank_num is null) ")->delete();
        $vobank = M("member_banks")->field("uid,bank_num,bank_name,id,is_default,phone")->where('uid=' . $uid)->order('add_time desc')->select();
        $bankname = C('BANK_NAME');
        foreach ($vobank as $k => $v) {
            $a = $v["bank_name"];
            $trans = array_flip($bankname);
            if (!empty($trans[$a])) {
                $vobank[$k]['bank_ico'] = $trans[$a];
            } else {
                $vobank[$k]['bank_ico'] = "BAC";
            }
        }
        $jsons['banklist'] = $vobank;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    /*



    *   银行卡解除绑定



    */
    public function unbindbank() {
        $uid = $_REQUEST['uid'];
        if (empty($uid)) {
            $jsons['status'] = '0';
            $jsons['msg'] = '填写完整信息！';
            outJson($jsons);
        }
        $rs = M('member_banks')->where(array(
            'uid' => $uid
        ))->delete();
        if (!($rs === false)) {
            $jsons['status'] = '1';
            $jsons['msg'] = '操作成功！';
            outJson($jsons);
        } else {
            $jsons['status'] = '0';
            $jsons['msg'] = '操作失败！';
            outJson($jsons);
        }
    }
    /*



    *   加息券列表



    */
    public function interest_rate_list() {
        $uid = $_REQUEST['uid'];
        $ps = $p = intval($_REQUEST['p']) ? intval($_REQUEST['p']) : "1";
        $irstatus = isset($_REQUEST['irstatus']) ? text($_REQUEST['irstatus']) : '';
        $whereStr = "status not in(2,3) and end_time < UNIX_TIMESTAMP()";
        $whereStr.= " and uid = {$uid}";
        $rs = M('member_interest_rate')->where($whereStr)->save(array(
            'status' => 3
        ));
        if ($irstatus) {
            $map['t1.status'] = $irstatus;
        } else {
            $map['t1.status'] = array(
                'in',
                '0,2,3'
            );
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
        $json['page'] = $pageSet;
        $json['status'] = 1;
        if (ceil($count / 10) < $ps) {
            $json["status"] = 1;
            $json["list"] = array();
            $json["msg"] = "已经是最后一页啦";
            outJson($json);
        }
        $statusArr = array(
            '已禁用',
            '未使用',
            '已使用',
            '已过期'
        );
        if ($memberinterestList) {
            $res = array();
            foreach ($memberinterestList as $key => $value) {
                $res[$key]['interest_rate'] = $value['interest_rate'];
                $res[$key]['start_time'] = date('Y-m-d', $value['start_time']);
                $res[$key]['end_time'] = date('Y-m-d', $value['end_time']);
                $res[$key]['status'] = $statusArr[$value['status']];
            }
            $json['list'] = $res;
            $json['msg'] = "成功";
            $json['status'] = "1";
        } else {
            $json['list'] = array();
            $json['msg'] = "暂无信息";
            $json['status'] = "1";
        }
        outJson($json);
    }
    /*



    *   我的红包



    */
    public function bonus() {
        $uid = $_REQUEST['uid'];
        $ps = $p = intval($_REQUEST['p']) ? intval($_REQUEST['p']) : "1";
        // $bonusstatus = isset($_REQUEST['bonusstatus']) ? text($_REQUEST['bonusstatus']) : '1';
        $bonusstatus = '1';
        $whereStr = "status not in(2,3) and end_time < UNIX_TIMESTAMP()";
        $whereStr.= " and uid = {$uid}";
        $rs = M('member_bonus')->where($whereStr)->save(array(
            'status' => 3
        ));
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
        $json['page'] = $pageSet;
        $json['status'] = 1;
        if (ceil($count / 10) < $ps) {
            $json["status"] = 1;
            $json["list"] = array();
            $json["msg"] = "已经是最后一页啦";
            outJson($json);
        }
        $statusArr = array(
            '已禁用',
            '未使用',
            '已使用',
            '已过期'
        );
        if ($bonusList) {
            $res = array();
            foreach ($bonusList as $key => $value) {
                $res[$key]['money_bonus'] = $value['money_bonus'];
                $res[$key]['start_time'] = date('Y/m/d', $value['start_time']);
                $res[$key]['end_time'] = date('Y/m/d', $value['end_time']);
                // $res[$key]['status']=$statusArr[$value['status']];
                $res[$key]['id'] = $value['id'];
                $res[$key]['bonus_invest_min'] = $value['bonus_invest_min'];
            }
            $json['list'] = $res;
            $json['msg'] = "成功";
            $json['status'] = "1";
        } else {
            $json['list'] = array();
            $json['msg'] = "暂无信息";
            $json['status'] = "1";
        }
        outJson($json);
    }
    /*



    * 删除红包



    */
    public function delbonus() {
        $jsons['status'] = "0";
        $uid = $_REQUEST['uid'];
        $id = intval($_REQUEST['id']) ? intval($_REQUEST['id']) : "";
        if (empty($id)) {
            $jsons['msg'] = '填写完整信息！';
            outJson($jsons);
        }
        $r = M("member_bonus")->where("uid=" . $uid . " and id = " . $id)->find();
        if (empty($r)) {
            $jsons['msg'] = '红包不存在！';
            outJson($jsons);
        }
        $new = M("member_bonus")->where("uid=" . $uid . " and id = " . $id)->delete();
        if ($new) {
            $jsons['status'] = "1";
            $jsons['msg'] = '红包删除成功！';
            outJson($jsons);
        } else {
            $jsons['msg'] = '红包删除失败！';
            outJson($jsons);
        }
    }
    /*



    *   平台数据



    */
    public function platform_data() {
        $common = (time() - strtotime('2016-01-01'));
        $a = floor($common / 86400 / 360); //整数年
        $date = floor(((time() - strtotime('2016-01-01')) / 86400) - $a * 360);
        $hour = floor((time() - strtotime('2016-01-01')) % 86400 / 3600);
        $jsons['operation_time'] = $a . "年" . $date . "天" . $hour . "小时";
        //累计成交金额
        $jsons['ljcjje'] = getDataCount(4);
        //待收总额
        $jsons['ljdsze'] = getDataCount(6);
        //累计收益
        $jsons['ljsy'] = getDataCount(3);
        //交易笔数
        $jsons['jybs'] = intval(getDataCount(2));
        //投资人总数
        $jsons['tzrzs'] = intval(getDataCount(5)) - intval(getDataCount(15));
        //借款人总数
        $jsons['jkrzs'] = intval(getDataCount(15));
        //逾期90天以上的金额
        $jsons['yqje'] = getDataCount(16);
        //逾期90天以上的笔数
        $jsons['yqbs'] = intval(getDataCount(17));
        $jsons['status'] = "1";
        outJson($jsons);
    }
    public function useraterule() {
        $info = '<span>1、加息券可用于加息券项目<br /></span>';
        outWeb($info);
    }
    public function withdrawtip() {
        $info = '1、尊敬的会员，提现操作涉及您的资金变动，请仔细核对您的提现信息<br>2、一般用户单日提现上限为30万元<br>3、涉及到您的资金安全，请仔细操作';
        outWeb($info);
    }
    /*



     * 用户提现



    */
    public function withdraw() {
        $pre = C('DB_PREFIX');
        // $tqfee = getfloatvalue($this->glo['fee_tqtx'],2);
        // $tqfee = explode("|",$this->glo['fee_tqtx']);
        $this->uid = $uid = $_REQUEST['uid'] or die;
        $withdraw_money = floatval($_REQUEST['amount']) or die;
        $pwd = md5(text($_REQUEST['txtPassword'])) or die;
        $bank_num = M('member_banks')->where("uid='{$uid}'")->find();
        if ($bank_num['bank_num'] == '') {
            $jsons["status"] = "0";
            $jsons["msg"] = "请先绑定银行卡";
            outJson($jsons);
        }
        $vm = getMinfo($this->uid, 'm.pin_pass');
        if (empty($vm['pin_pass'])) {
            $vo = M('members m')->field('mm.account_money,mm.back_money,m.user_leve,m.time_limit')->join("{$pre}member_money mm on mm.uid = m.id")->where("m.id={$uid} AND (m.user_pass='{$pwd}')")->find();
        } else {
            $vo = M('members m')->field('mm.account_money,mm.back_money,m.user_leve,m.time_limit')->join("{$pre}member_money mm on mm.uid = m.id")->where("m.id={$uid} AND (m.pin_pass='{$pwd}')")->find();
        }
        if (empty($_REQUEST['txtPassword']) || !is_array($vo)) {
            $jsons["status"] = "0";
            $jsons["msg"] = "支付密码错误";
            outJson($jsons);
        }
        $all_money = $vo['account_money'];
        if ($vo['account_money'] < $withdraw_money) {
            $jsons["status"] = "0";
            $jsons["msg"] = "账户余额不足";
            outJson($jsons);
        }
        $start = strtotime(date("Y-m-d", time()) . " 00:00:00");
        $end = strtotime(date("Y-m-d", time()) . " 23:59:59");
        $bank_id = $_REQUEST['bank_id'];
        $wmap['uid'] = $this->uid;
        $wmap['withdraw_status'] = array(
            "neq",
            3
        );
        $wmap['add_time'] = array(
            "between",
            "{$start},{$end}"
        );
        $wmap['bank_id'] = $bank_id;
        $today_money = M('member_withdraw')->where($wmap)->sum('withdraw_money');
        $today_time = M('member_withdraw')->where($wmap)->count('id');
        $tqfee = explode("|", $this->glo['fee_tqtx']);
        /*



        ：   1、提现次数：每天可以提现10次



            2、提现上下限：1元<=提现金额<=1000000元



            3、提现手续费：2元/笔



            4、未投资手续费：未投资的部分另外扣取千分之3的手续费



        */
        $today_limit = $tqfee[0];
        if (!empty($today_limit) && $today_time >= $today_limit) {
            $message = "一天最多只能提现{$today_limit}次";
            $jsons["status"] = "0";
            $jsons["msg"] = $message;
            outJson($jsons);
        }
        $one_limit_min = $tqfee[1];
        $one_limit_max = $tqfee[2];
        if ($withdraw_money < $one_limit_min || $withdraw_money > $one_limit_max * 10000) {
            $jsons["status"] = "0";
            $jsons["msg"] = "单笔提现金额限制为{$one_limit_min}-{$one_limit_max}万元";
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
        $member_banks = M('member_banks')->where("id = " . $bank_id)->find();
        $moneydata['bank_num'] = $member_banks['bank_num'];
        $moneydata['bank_name'] = $member_banks['bank_name'];
        $moneydata['bank_id'] = $bank_id;
        // $fee += $needFeeMoney * getfloatvalue($tqfee[3],2) / 1000;
        $moneydata['withdraw_money'] = $withdraw_money;
        $moneydata['withdraw_fee'] = 0;
        $moneydata['second_fee'] = $fee;
        $moneydata['second_money'] = $withdraw_money;
        $moneydata['withdraw_status'] = 0;
        $moneydata['uid'] = $this->uid;
        $moneydata['add_time'] = time();
        $moneydata['add_ip'] = get_client_ip();
        $newid = M('member_withdraw')->add($moneydata);
        if ($newid) {
            memberMoneyLog($uid, 4, -$withdraw_money, "提现，手续费" . $fee . "元", '0', '@网站管理员@', -($fee));
            MTip('chk6', $this->uid);
            $jsons["status"] = "1";
            $jsons["msg"] = "提现申请成功";
            outJson($jsons);
        } else {
            $jsons["status"] = "0";
            $jsons["msg"] = "提现申请失败";
            outJson($jsons);
        }
        $jsons['msg'] = '成功';
        $jsons['data'] = [];
        $jsons['statusArr'] = '1';
        outJson($jsons);
    }
    /**
     * 修改密码
     * @param $uid
     */
    public function change_pass() {
        $uid = $_REQUEST['uid'];
        $old = $_REQUEST['oldpassword'];
        $newpwd = $_REQUEST['newpassword'];
        $newpwd1 = $_REQUEST['newpasswordre'];
        if (empty($old) || empty($newpwd) || empty($newpwd1)) {
            $jsons["msg"] = "信息不完整！";
        } else {
            $uid = $_REQUEST['uid'] or die;
            $old = !empty($_REQUEST['oldpassword']) ? md5($_REQUEST['oldpassword']) : die;
            $newpwd = !empty($_REQUEST['newpassword']) ? md5($_REQUEST['newpassword']) : die;
            $newpwd1 = !empty($_REQUEST['newpasswordre']) ? md5($_REQUEST['newpasswordre']) : die;
            if ($newpwd != $newpwd1) {
                $jsons["status"] = '0';
                $jsons["msg"] = "两次密码输入不一样";
                outJson($jsons);
            }
            $user_pass = M('members')->getFieldById($uid, 'user_pass');
            if ($old != $user_pass) {
                $jsons["msg"] = "原密码错误，请重新输入";
                outJson($jsons);
            }
            if ($old == $newpwd) {
                $jsons["msg"] = "设置失败，请勿让新密码与老密码相同";
                outJson($jsons);
            }
            $newid = M('members')->where("id={$uid}")->setField('user_pass', $newpwd1);
            if ($newid) {
                MTip('chk1', $uid);
                $jsons["status"] = '1';
                $jsons["msg"] = '修改成功';
                outJson($jsons);
            }
            $jsons["msg"] = "操作失败，请联系客服！";
        }
        outJson($jsons);
    }
    /**
     * 修改支付密码
     * @param $uid
     */
    public function change_pinpass() {
        $uid = $_REQUEST['uid'];
        $old = $_REQUEST['oldpassword'];
        $newpwd = $_REQUEST['newpassword'];
        $newpwd1 = $_REQUEST['newpasswordre'];
        if (empty($old) || empty($newpwd) || empty($newpwd1)) {
            $jsons["msg"] = "信息不完整！";
        } else {
            $uid = $_REQUEST['uid'] or die;
            $old = !empty($_REQUEST['oldpassword']) ? md5($_REQUEST['oldpassword']) : die;
            $newpwd = !empty($_REQUEST['newpassword']) ? md5($_REQUEST['newpassword']) : die;
            $newpwd1 = !empty($_REQUEST['newpasswordre']) ? md5($_REQUEST['newpasswordre']) : die;
            // if ($newpwd != $newpwd1) {
            //     $jsons["msg"] = "两次密码输入不一样";
            //     outJson($jsons);
            // }
            $members = M('members')->find($uid);
            if ($old == $newpwd1) {
                $jsons["msg"] = "设置失败，请勿让新密码与老密码相同";
                outJson($jsons);
            }
            if (empty($members['pin_pass'])) {
                // if ($members['pin_pass'] == $old) {
                $newid = M('members')->where("id={$uid}")->setField('pin_pass', $newpwd1);
                if ($newid) {
                    $jsons['status'] = "1";
                    $jsons["msg"] = "修改成功！";
                    //outJson($jsons);

                } else {
                    $jsons['status'] = "0";
                    $jsons["msg"] = "设置失败，请重试";
                    // outJson($jsons);

                }
                // } else {
                //     $jsons["msg"] = "原支付密码错误，请重试！";
                //     outJson($jsons);
                // }

            } else {
                if ($members['pin_pass'] == $old) {
                    $newid = M('members')->where("id={$uid}")->setField('pin_pass', $newpwd1);
                    if ($newid) {
                        $jsons["status"] = '1';
                        $jsons["msg"] = '修改成功';
                        // outJson($jsons);

                    }
                } else {
                    $jsons["msg"] = "原支付密码错误，请重试！";
                    //outJson($jsons);

                }
            }
            $jsons["msg"] = "操作失败，请联系客服！";
        }
        outJson($jsons);
    }
    /**
     * 修改支付密码
     * @param $uid
     */
    public function change_pinpass_new() {
        $uid = $_REQUEST['uid'];
        $newpwd = $_REQUEST['newpassword'];
        $newpwd1 = $_REQUEST['newpasswordre'];
        $chars = "/^\d{6}$/";
        if (!preg_match($chars, $newpwd)) {
            $jsons['status'] = "0";
            $jsons["msg"] = "请输入6位纯属数字的支付码密码";
            outJson($jsons);
        }
        $codeId = @$_REQUEST['codeId'];
        $txtCode = @$_REQUEST['txtCode'];
        $user_phone = M('members')->where("id={$uid}")->getField('user_phone');
        $verifyRs = M('verify_code')->where("id = '{$codeId}' and content = {$txtCode} and phone='{$user_phone}'")->count('id');
        if ($verifyRs != 1) {
            $jsons['msg'] = "验证码错误！";
            outJson($jsons);
        }
        if (empty($newpwd) || empty($newpwd1)) {
            $jsons['status'] = "0";
            $jsons["msg"] = "信息不完整！";
        } else {
            $uid = $_REQUEST['uid'] or die;
            $newpwd = !empty($_REQUEST['newpassword']) ? md5($_REQUEST['newpassword']) : die;
            $newpwd1 = !empty($_REQUEST['newpasswordre']) ? md5($_REQUEST['newpasswordre']) : die;
            if ($newpwd != $newpwd1) {
                $jsons['status'] = "0";
                $jsons["msg"] = "两次密码输入不一样";
                outJson($jsons);
            }
            $members = M('members')->find($uid);
            if ($members["pin_pass"] == $newpwd1) {
                $jsons['status'] = "0";
                $jsons["msg"] = "设置失败，请勿让新密码与老密码相同";
                outJson($jsons);
            }
            $newid = M('members')->where("id={$uid}")->setField('pin_pass', $newpwd1);
            if ($newid) {
                $jsons['status'] = "1";
                $jsons["msg"] = "修改成功！";
                //outJson($jsons);

            } else {
                $jsons['status'] = "0";
                $jsons["msg"] = "设置失败，请重试";
                // outJson($jsons);

            }
        }
        outJson($jsons);
    }
    /*



    *   我的邀请



    */
    public function promotion_friend() {
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
            if ($value['reg_time'] >= strtotime("2018-07-18 00:00:00")) {
                $members_list[$key]['reward'] = '1%的加息劵';
            } else {
                $members_list[$key]['reward'] = '';
            }
        }
        $jsons['page'] = $pageSet;
        if (ceil($count / 15) < $_REQUEST["p"]) {
            $jsons["status"] = 1;
            $jsons['members_list'] = array();
            $jsons["msg"] = "已经是最后一页啦";
            outJson($jsons);
        }
        $jsons['members_list'] = (array)$members_list;
        outJson($jsons);
    }
    public function promotion() {
        $uid = $_REQUEST['uid'] or die;
        $jsons['promotion_link'] = $this->domainUrl . '/member/common/register?invite=' . $uid; //邀请链接
        $jsons['incode'] = $uid; //邀请码
        $hy_num = M("members m")->where("m.recommend_id ={$uid}")->count();
        $jsons['innums'] = isset($hy_num) ? $hy_num : 0; //邀请人数
        $jsons['logo'] = $this->domainUrl . '/Public/default/Home/images/logo.png';
        $jsons['status'] = "1";
        //调用查看结果
        $files = C("APP_ROOT") . '../Public/qrcode/member_' . $uid . '.png';
        if (!file_exists($files)) {
            $url = $this->domainUrl . '/member/common/register?invite=' . $uid;
            $httpurl = self::makeQRcode($url);
        }
        $jsons['qrcode'] = $this->domainUrl . '/Public/qrcode/member_' . $uid . '.png';
        outJson($jsons);
    }
    public function promotionmsg() {
        $info = '1、邀请好友注册并投资成功（不限投资时间、金额），双方可获得加息券一张。<br />2、每邀请1人并投标成功，邀请人即可获得奖励，无上限要求，邀请越多则奖励越多';
        outWeb($info);
    }
    function makeQRcode($data, $size = 4, $need_save = false, $file_name = '', $level = 'L') {
        include C("APP_ROOT") . "/Lib/Phpqrcode/qrcode.php";
        //本地文档相对路径
        $url = C("APP_ROOT") . '../Public/qrcode/';
        //引入php QR库文件
        $value = $data;
        $errorCorrentionLevel = 'L'; //容错级别
        $matrixPoinSize = 6; //生成图片大小
        $a = time() . '.png';
        //生成二维码,第二个参数为二维码保存路径
        QRcode::png($value, $url . $a, $errorCorrentionLevel, $matrixPoinSize, 2);
        //如不加logo，下面logo code 注释掉，输出$url.qrcode.png即可。
        $logo = $url . 'logo.png'; //logo
        $QR = $url . $a; //已经生成的二维码
        if ($logo !== FALSE) {
            $QR = imagecreatefromstring(file_get_contents($QR));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR); //二维码图片宽度
            $QR_height = imagesy($QR); //二维码图片高度
            $logo_width = imagesx($logo); //logo图片宽度
            $logo_height = imagesy($logo); //logo图片高度
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width / $logo_qr_width;
            $logo_qr_height = $logo_height / $scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            //重新组合图片并调整大小
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
        }
        //新图片
        $img = $url . 'member_' . $this->uid . '.png';
        //输出图片
        imagepng($QR, $img);
        return $img;
    }
    public function actlogout() {
        $vo = array(
            "id",
            "user_name"
        );
        foreach ($vo as $v) {
            session("u_{$v}", NULL);
        }
        cookie("Ukey", NULL);
        cookie("Ukey2", NULL);
        //uc登陆
        $loginconfig = FS("Webconfig/loginconfig");
        $uc_mcfg = $loginconfig['uc'];
        if ($uc_mcfg['enable'] == 1) {
            require_once C('APP_ROOT') . "Lib/Uc/config.inc.php";
            require C('APP_ROOT') . "Lib/Uc/uc_client/client.php";
            $logout = uc_user_synlogout();
        }
        //uc登陆
        $jsons["status"] = '1';
        $jsons["msg"] = "注销成功";
        outJson($jsons);
    }
    public function about() {
        $jsons["status"] = '1';
        $jsons["version"] = "v1.0.1";
        $jsons["wx"] = "673401805@qq.com";
        $jsons["email"] = "BD@tiannongjinrong.com";
        $jsons["url"] = "www.tiannongjinrong.com";
        $jsons["tel"] = "400-1031-191";
        $jsons["bottom"] = "客服工作时间（周一至周五 09:00~17:00）\n © Copyright(c)2016 天农众筹 .All Rights Reserved 天农众筹 粤ICP备17153534号-1 \n 众筹有风险 请谨慎众筹";
        outJson($jsons);
    }
    public function support() {
        $jsons["status"] = '1';
        $uid = $_REQUEST['uid'] or die;
        $bid = $_REQUEST['bid'] or die;
        $binfo = M('borrow_info')->where('id=' . $bid)->find();
        $bonus_list = M('member_bonus')->where("uid='{$this->uid}' and status = 1 and (" . time() . " > start_time and " . time() . " < end_time )")->field('id,money_bonus,bonus_invest_min')->select();
        $inrate_list = M('member_interest_rate')->where("uid='{$this->uid}' and status = 1 and (" . time() . " > start_time and " . time() . " < end_time )")->field('id,interest_rate')->select();
        $jsons['inrate_list'] = $inrate_list ? $inrate_list : array();
        $jsons['bonus_list'] = $bonus_list ? $bonus_list : array();
        $vm = getMinfo($this->uid, 'm.pin_pass,mm.account_money,mm.back_money,mm.money_experience');
        $jsons['account_money'] = $vm['account_money'];
        $jsons['money_experience'] = $vm['money_experience'];
        $jsons['is_experience'] = $binfo['is_experience'] ? $binfo['is_experience'] : 0;
        $jsons['is_bonus'] = $binfo['is_bonus'] ? $binfo['is_bonus'] : 0;
        $jsons['is_memberinterest'] = $binfo['is_memberinterest'] ? $binfo['is_memberinterest'] : 0;
        $jsons['is_binpass'] = $binfo['password'] ? 1 : 0;
        outJson($jsons);
    }
    public function withshow() {
        $uid = $_REQUEST['uid'] or die;
        $bank_num = M('member_banks')->where("uid='{$uid}'")->find();
        $bankname = C('BANK_NAME');
        $vm = getMinfo($this->uid, 'm.pin_pass,mm.account_money,mm.back_money,mm.money_experience');
        $minfo['account_money'] = $vm['account_money'];
        if ($vm) {
            $jsons['status'] = "1";
            $jsons['account_money'] = $minfo['account_money'];
            $tqfee = explode("|", $this->glo['fee_tqtx']);
            $fee = $tqfee[3] / 1000;
            $jsons['fee_money'] = $fee;
            $today_limit = $tqfee[0];
            $one_limit_min = getfloatvalue($tqfee[1], 2);
            $one_limit_max = getfloatvalue($tqfee[2] * 10000, 2);
            $jsons['feemsg'] = "① 充值的资金经投资回款后，无提现手续费；\r\n② 充值的资金未经投资，在网站时间超过15日的，无提现手续费；\r\n③ 充值的资金未经投资，在网站时间未超过15日(含15日)的，提现金额的" . $fee . "手续费；\r\n④ 平台最低提现金额为{$one_limit_min}元起。";
            if ($bank_num['bank_num'] == '') {
                $jsons['status'] = "2";
                $jsons['msg'] = "请先绑定银行卡";
            } else {
                $jsons['bank_num'] = substr($bank_num['bank_num'], -4);
                $jsons['bank_name'] = $bankname[$bank_num['bank_name']];
                $jsons['bank_ico'] = $this->domainUrl . '/Public/bank/' . $bank_num['bank_name'] . ".png";
            }
        } else {
            $jsons['status'] = "0";
            $jsons['msg'] = "获取用户信息失败！";
        }
        outJson($jsons);
    }
    public function limitbank() {
        $array = array(
            array(
                'bank' => '中国银行',
                'binfo' => '单笔99万，单日99万，单月无限额',
                'bank_ico' => $this->domainUrl . '/Public/bank/BOCSH.png'
            ) ,
            array(
                'bank' => '招商银行',
                'binfo' => '单笔99万，单日99万，单月无限额',
                'bank_ico' => $this->domainUrl . '/Public/bank/CMB.png'
            ) ,
            array(
                'bank' => '工商银行',
                'binfo' => '单笔99万，单日99万，单月无限额',
                'bank_ico' => $this->domainUrl . '/Public/bank/ICBC.png'
            ) ,
            array(
                'bank' => '建设银行',
                'binfo' => '单笔99万，单日99万，单月无限额',
                'bank_ico' => $this->domainUrl . '/Public/bank/CCB.png'
            ) ,
        );
        $jsons['status'] = "1";
        $jsons['limitlist'] = $array;
        outJson($jsons);
    }
    public function artrule() {
        $info = '1、邀请好友注册并投资成功（不限投资时间、金额），双方可获得加息券一张。<br />2、每邀请1人并投标成功，邀请人即可获得奖励，无上限要求，邀请越多则奖励越多';
        outWeb($info);
    }
    public function bill() {
        $log_type = !empty($_REQUEST['log_type']) ? $_REQUEST['log_type'] : '';
        // $p = !empty($_REQUEST['p']) ? $_REQUEST['p'] : '1';
        $map['uid'] = $this->uid;
        if (!empty($_REQUEST['log_type'])) {
            if (intval($_REQUEST['log_type']) == 11) {
                $map['type'] = 9;
            } else {
                $map['type'] = intval($_REQUEST['log_type']);
            }
        } else {
            $map['type'] = array(
                'in',
                '3,29,15,20,11,9'
            );
        }
        $list = getMoneyLog($map, 1000);
        $lst = array();
        $i = 0;
        $array = array(
            3 => '充值',
            29 => '提现',
            15 => '投标',
            20 => '奖励',
            11 => '回款'
        );
        foreach ($list['list'] as $key => $value) {
            $t = date('Y-m', $value['add_time']);
            $lst[$t]['date'] = date('Y年m月', $value['add_time']);
            $lst[$t]['list'][$i]['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
            $lst[$t]['list'][$i]['info'] = $value['type'];
            $lst[$t]['list'][$i]['money'] = $value['affect_money'] > 0 ? '+￥' . $value['affect_money'] : '-￥' . str_replace("-", "", $value['affect_money']);
            $lst[$t]['list'][$i]['typeid'] = $value['typeid'];
            $i++;
        }
        $i = 0;
        foreach ($lst as $k => $val) {
            $lsts[$i]['date'] = $val['date'];
            foreach ($val['list'] as $k1 => $val1) {
                $lsts[$i]['list'][] = $val1;
            }
            $i++;
        }
        $jsons['status'] = "1";
        $jsons['list'] = $lsts ? $lsts : array();
        // $jsons['page'] = $list['_page'];
        outJson($jsons);
    }
    public function bills() {
        $array = array(
            3 => '充值',
            29 => '提现',
            15 => '投标',
            20 => '奖励',
            11 => '回款'
        );
        $log_type = !empty($_REQUEST['log_type']) ? $_REQUEST['log_type'] : '';
        $p = !empty($_REQUEST['p']) ? $_REQUEST['p'] : '1';
        $map['uid'] = $this->uid;
        if (!empty($_REQUEST['log_type'])) {
            $map['type'] = intval($_REQUEST['log_type']);
        } else {
            $map['type'] = array(
                'in',
                '3,29,15,20,11'
            );
        }
        $list = getMoneyLog($map, 20);
        $lst = array();
        $i = 0;
        foreach ($list['list'] as $key => $value) {
            $t = date('Y-m', $value['add_time']);
            $lst[$t]['date'] = date('Y年m月', $value['add_time']);
            $lst[$t]['list'][$i]['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
            $lst[$t]['list'][$i]['info'] = $value['type'];
            $lst[$t]['list'][$i]['money'] = $value['affect_money'] > 0 ? '+￥' . $value['affect_money'] : '-￥' . str_replace("-", "", $value['affect_money']);
            $lst[$t]['list'][$i]['typeid'] = $value['typeid'];
            $i++;
        }
        $i = 0;
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
    function binary_to_file($file) {
        $content = $GLOBALS['HTTP_RAW_POST_DATA']; // 需要php.ini设置
        if (empty($content)) {
            $content = file_get_contents('php://input'); // 不需要php.ini设置，内存压力小

        }
        $ret = file_put_contents($file, $content, true);
        return $ret;
    }
    public function feedback() {
        $jsons["status"] = '0';
        $msg = $_REQUEST['opinion'];
        $user_phone = $_REQUEST['user_phone'];
        $img1 = '';
        $img2 = '';
        if (!empty($_FILES['img1']['name'])) {
            // $now =$_SERVER['REQUEST_TIME'];
            $this->saveRule = 'uniqid';
            $this->savePathNew = 'UF/Uploads/feedback/';
            $info = $this->CUpload();
            $img1 = $info[0]['savepath'] . $info[0]['savename'];
        }
        if (!empty($_FILES['img2']['name'])) {
            // $this->saveRule = time().rand(0,1000);
            // $this->savePathNew = 'UF/Uploads/feedback/';
            // $info = $this->CUpload();
            $img2 = $info[1]['savepath'] . $info[1]['savename'];
        }
        if (empty($user_phone)) {
            $jsons["msg"] = "联系电话必填！";
            outJson($jsons);
        }
        // $uInfo = M('members')->find($this->uid);
        $addData = array(
            'type' => 3,
            'contact' => $user_phone,
            'msg' => $msg,
            'ip' => get_client_ip() ,
            'add_time' => time() ,
            'status' => 0,
            'img1' => $img1,
            'img2' => $img2
        );
        $rs = M('feedback')->add($addData);
        if ($rs) {
            $jsons["status"] = '1';
            $jsons["msg"] = "提交成功，网站工作人员将会及时联系您！";
            outJson($jsons);
        } else {
            $jsons["msg"] = "提交失败，请稍后重试！";
            outJson($jsons);
        }
    }
    public function qqshow() {
        $jsons["status"] = '1';
        $jsons["qq"] = '1870664219';
        outJson($jsons);
    }
    public function uploads() {
        if (!empty($_FILES['img']['name'])) {
            $url = '/Public/uploads/';
            $name = time() . '_' . rand(1000, 9999);
            import('ORG.Net.UploadFile');
            $upload = new UploadFile(); // 实例化上传类
            $upload->allowExts = array(
                'jpg',
                'gif',
                'png',
                'jpeg'
            ); // 设置附件上传类型
            $upload->savePath = $url; // 设置附件上传目录
            $upload->saveRule = $name;
            if ($upload->upload()) { // 上传成功 获取上传文件信息
                $info = $upload->getUploadFileInfo();
                $msg = $url . $info[0]['savename'];
                $jsons["status"] = '1';
                $jsons["msg"] = "上传图片成功！";
                $jsons['url'] = $msg;
            } else { // 上传错误提示错误信息
                $msg = $upload->getErrorMsg();
                $jsons["status"] = '0';
                $jsons["msg"] = $msg;
                $jsons["url"] = "";
            }
        } else {
            $jsons["status"] = '0';
            $jsons["msg"] = "请选择上传内容！";
            $jsons["url"] = "";
        }
        outJson($jsons);
    }
    public function reg_service() {
        $article = M('article_category')->where("id=28")->find();
        $jsons["content"] = $article['type_content'];
        outWeb($jsons['content']);
    }
    public function risk_warning() {
        $info = '在您通过平台进行资金出借的过程中，也许会面临以下可能导致您出借资金及收益受损的各种风险。请您在进行资金出借前仔细阅读以下内容，了解融资项目信贷风险，确保自身具备相应的投资风险意识、风险认知能力、风险识别能力和风险承受能力，拥有非保本类金融产品投资的经历并熟悉互联网，并请您根据自身的风险承受能力选择是否出借资金及出借资金的数额。<br />1. 法律及监管风险：因许多法律和法规相对较新且可能发生变化，相关官方解释和执行可能存在较大不确定性等因素引起的风险。有些新制定的法律、法规或修正案的生效可能被延迟；有些新颁布或生效的法律法规可能具有追溯力从而对您的投资利益造成不利影响。<br />2. 政策风险：因国家宏观政策、财政政策、货币政策、监管导向、行业政策、地区发展政策等因素引起的风险。<br />3. 市场风险：因市场资金面紧张或利率波动、行业不景气、企业效益下滑等因素引起的风险。<br />4. 技术风险：由于无法控制和不可预测的系统故障、设备故障、通讯故障、电力故障、网络故障、黑客或计算机病毒攻击、以及其它因素，可能导致平台出现非正常运行或者瘫痪，由此导致您无法及时进行查询、充值、出借、提现等操作。<br />5. 不可抗力风险：由于战争、动乱、自然灾害等不可抗力因素的出现而可能导致您出借资金损失的风险。';
        outWeb($info);
    }
    public function setavatar() {
        $uid = intval($_REQUEST["uid"]);
        if (!empty($_FILES['img']['name'])) {
            $this->saveRule = 'uniqid';
            $this->savePathNew = '/UF/Uploads/avatar/';
            $info = $this->CUpload();
            $img1 = $info[0]['savepath'] . $info[0]['savename'];
            $data['avatar'] = $img1;
            M('members')->where('id=' . $uid)->save($data);
            writeLog($img1);
        } else {
            $jsons["status"] = '0';
            $jsons["msg"] = "上传头像失败！";
            $jsons["url"] = $this->domainUrl . "/Style/header/images/noavatar_big.gif";
            outJson($jsons);
        }
        $jsons["url"] = $this->domainUrl . $img1;
        $jsons["status"] = '1';
        $jsons["msg"] = "上传头像成功！";
        outJson($jsons);
    }
    public function paybank() {
        $jsons['bank_list'] = array(
            '中国工商银行',
            '中国农业银行',
            '中国银行',
            '中国建设银行',
            '中国邮政储蓄银行',
            '中信银行',
            '中国光大银行',
            '兴业银行'
        );
        $jsons['status'] = 1;
        outJson($jsons);
    }
    public function charge_sign_pay() {
        header('Content-Type: text/html; Charset=UTF-8');
        $uid = $this->uid;
        require_once C('APP_ROOT') . "Lib/AllinpayInter/libs/ArrayXml.class.php";
        require_once C('APP_ROOT') . "Lib/AllinpayInter/libs/cURL.class.php";
        require_once C('APP_ROOT') . "Lib/AllinpayInter/libs/PhpTools.class.php";
        $tools = new PhpTools();
        $minf = M('member_info')->where('uid=' . $uid)->find();
        $bank_name = $_REQUEST['bank_name'];
        $account_no = $_REQUEST['account_no'];
        $account_name = $minf['real_name'];
        $idcard = $minf['idcard'];
        $tel = $_REQUEST['tel'];
        $mi['bank_name'] = $bank_name;
        $mi['account_no'] = $account_no;
        M('member_info')->where('uid=' . $uid)->save($mi);
        $bank_list = array(
            '中国工商银行' => '0102',
            '中国农业银行' => '0103',
            '中国银行' => '0104',
            '中国建设银行' => '0105',
            '中国邮政储蓄银行' => '0403',
            '中信银行' => '0302',
            '中国光大银行' => '0303',
            '兴业银行' => '0309'
        );
        $bank_code = $bank_list[$bank_name];
        $orid = time() . rand(10000, 99999);
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
            ) ,
            'FAGRA' => array(
                'MERCHANT_ID' => $this->merchant_id,
                'BANK_CODE' => $bank_code, //银行代码
                'ACCOUNT_TYPE' => '00', //账号类型
                'ACCOUNT_NO' => $account_no, //账号
                'ACCOUNT_NAME' => $this->characet($account_name) , //账号名
                'ACCOUNT_PROP' => '0', //账号属性  私人
                'ID_TYPE' => '0', //开户证件类型 身份证
                'ID' => $idcard, //证件号
                'TEL' => $tel, //手机号
                'CVV2' => '', //CVV2
                'VAILDDATE' => '', //有效期
                'MERREM' => '', //商户保留信息
                'REMARK' => $orid,
            ) ,
        );
        $result = $tools->send($params);
        writeLog($result);
        if ($result['AIPG']['INFO']['RET_CODE'] == "0000" && $result['AIPG']['FAGRARET']['RET_CODE'] == "0000") {
            $jsons['status'] = 1;
            $jsons['msg'] = "请求成功";
            $jsons['ordid'] = $result['AIPG']['INFO']['REQ_SN'];;
        } else {
            $jsons['status'] = 0;
            $jsons['msg'] = "请求失败";
            $jsons['ordid'] = 0;
        }
        outJson($jsons);
    }
    //2.1.2 协议支付签约
    public function payment_sign() {
        header('Content-Type: text/html; Charset=UTF-8');
        $uid = $this->uid;
        $ordid = $_REQUEST["ordid"];
        $vercode = $_REQUEST["vercode"];
        if ($ordid == 0 || empty($ordid)) {
            $jsons['status'] = 0;
            $jsons['msg'] = "原请求流水号不能为空";
            outJson($jsons);
        }
        require_once C('APP_ROOT') . "Lib/AllinpayInter/libs/ArrayXml.class.php";
        require_once C('APP_ROOT') . "Lib/AllinpayInter/libs/cURL.class.php";
        require_once C('APP_ROOT') . "Lib/AllinpayInter/libs/PhpTools.class.php";
        $tools = new PhpTools();
        $orid = time() . rand(10000, 99999);
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
            ) ,
            'FAGRC' => array(
                'MERCHANT_ID' => $this->merchant_id,
                'SRCREQSN' => $ordid, //原请求流水
                'VERCODE' => $vercode, //账号类型

            ) ,
        );
        $result = $tools->send($params);
        writeLog($result);
        if ($result['AIPG']['INFO']['RET_CODE'] == "0000" && $result['AIPG']['FAGRCRET']['RET_CODE'] == "0000") {
            $data['agrmno'] = $result['AIPG']['FAGRCRET']['AGRMNO'];
            M('members')->where('id=' . $uid)->save($data);
            $jsons['status'] = 1;
            $jsons['msg'] = "请求签约成功";
        } else {
            $jsons['status'] = 0;
            $jsons['msg'] = "请求签约失败";
        }
        outJson($jsons);
    }
    //2.1.4 协议支付
    public function charge() {
        header('Content-Type: text/html; Charset=UTF-8');
        $uid = $this->uid;
        $minf = M('member_info')->where('uid=' . $uid)->find();
        $account_name = $minf["real_name"];
        $amount = $_REQUEST["amount"];
        $orid = time() . rand(10000, 99999);
        $data['money'] = getFloatValue($amount, 2);
        $data['fee'] = 0;
        $data['add_time'] = time();
        $data['add_ip'] = get_client_ip();
        $data['status'] = 0;
        $data['uid'] = $uid;
        $data['nid'] = $orid;
        $data['way'] = "allinpayapp";
        M("member_payonline")->add($data);
        $minfo = m('members')->find($uid);
        require_once C('APP_ROOT') . "Lib/AllinpayInter/libs/ArrayXml.class.php";
        require_once C('APP_ROOT') . "Lib/AllinpayInter/libs/cURL.class.php";
        require_once C('APP_ROOT') . "Lib/AllinpayInter/libs/PhpTools.class.php";
        $tools = new PhpTools();
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
            ) ,
            'FASTTRX' => array(
                'BUSINESS_CODE' => '12301', //业务代码
                'MERCHANT_ID' => $this->merchant_id,
                'SUBMIT_TIME' => date('YmdHis') , //提交时间
                'AGRMNO' => $minfo['agrmno'], //签约时返回的协议号
                'ACCOUNT_NO' => '', //账号
                'ACCOUNT_NAME' => $account_name, //账号名
                'AMOUNT' => $amount * 100, //金额
                'CURRENCY' => 'CNY', //金额
                'ID_TYPE' => '', //开户证件类型
                'ID' => '', //证件号
                'TEL' => '', //手机号
                'CVV2' => '', //手机号
                'VAILDDATE' => '', //手机号
                'CUST_USERID' => '', //手机号
                'SUMMARY' => '', //手机号
                'REMARK' => '', //手机号

            ) ,
        );
        $result = $tools->send($params);
        writeLog($result);
        if ($result['AIPG']['INFO']['RET_CODE'] == "0000") {
            if ($result['AIPG']['FASTTRXRET']['RET_CODE'] == "0000") {
                $ordid = $result['AIPG']['INFO']['REQ_SN'];
                $done = $this->payDone(1, $ordid, $ordid);
                $jsons['status'] = 1;
                $jsons['msg'] = "支付成功";
            } else {
                $jsons['status'] = 0;
                $jsons['msg'] = $result['AIPG']['FASTTRXRET']['ERR_MSG'];
            }
        } else {
            $jsons['status'] = 0;
            $jsons['msg'] = "支付失败";
        }
        outJson($jsons);
    }
    //2.1.3协议支付解约
    public function cancel() {
        header('Content-Type: text/html; Charset=UTF-8');
        $uid = $this->uid;
        $minf = M('member_info')->where('uid=' . $uid)->find();
        $account_no = $minf["account_no"];
        $amount = $_REQUEST["amount"];
        $orid = time() . rand(10000, 99999);
        $minfo = m('members')->find($uid);
        require_once C('APP_ROOT') . "Lib/AllinpayInter/libs/ArrayXml.class.php";
        require_once C('APP_ROOT') . "Lib/AllinpayInter/libs/cURL.class.php";
        require_once C('APP_ROOT') . "Lib/AllinpayInter/libs/PhpTools.class.php";
        $tools = new PhpTools();
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
            ) ,
            'FAGRCNL' => array(
                'MERCHANT_ID' => $this->merchant_id, //业务代码
                'ACCOUNT_NO' => $account_no, //账号
                'AGRMNO' => $minfo['agrmno'], //签约时返回的协议号

            ) ,
        );
        $result = $tools->send($params);
        writeLog($result);
        if ($result['AIPG']['INFO']['RET_CODE'] == "0000") {
            if ($result['AIPG']['FAGRCNLRET']['RET_CODE'] == "0000") {
                $datas['agrmno'] = '';
                M('members')->where('id=' . $uid)->save($datas);
                $mi['bank_name'] = '';
                $mi['account_no'] = '';
                M('member_info')->where('uid=' . $uid)->save($mi);
                $jsons['status'] = 1;
                $jsons['msg'] = "解约成功";
            } else {
                $jsons['status'] = 0;
                $jsons['msg'] = $result['AIPG']['FAGRCNLRET']['ERR_MSG'];
            }
        } else {
            $jsons['status'] = 0;
            $jsons['msg'] = "解约失败";
        }
        outJson($jsons);
    }
    public function characet($data) {
        if (!empty($data)) {
            $fileType = mb_detect_encoding($data, array(
                'UTF-8',
                'GBK',
                'GB2312',
                'LATIN1',
                'BIG5'
            ));
            if ($fileType != 'UTF-8') {
                $data = mb_convert_encoding($data, 'UTF-8', $fileType);
            }
        }
        return $data;
    }
    private function payDone($status, $nid, $oid) {
        $done = false;
        $Moneylog = D('member_payonline');
        if ($this->locked) return false;
        $this->locked = true;
        switch ($status) {
            case 1:
                $updata['status'] = $status;
                $updata['tran_id'] = $oid;
                $vo = M('member_payonline')->field('uid,money,fee,status')->where("nid='{$nid}'")->find();
                if ($vo['status'] != 0 || !is_array($vo)) return;
                $xid = $Moneylog->where("uid={$vo['uid']} AND nid='{$nid}'")->save($updata);
                //print_r($xid);
                $tmoney = floatval($vo['money'] - $vo['fee']);
                if ($xid) $newid = memberMoneyLog($vo['uid'], 3, $tmoney, "充值订单号:" . $oid, 0, '@网站管理员@'); //更新成功才充值,避免重复充值
                if ($newid) {
                    $u = M("member_moneylog")->where("uid=" . $vo['uid'] . " and type=50")->count();
                    if ($u == 0) {
                        $frist_money = getFloatValue($this->glo['frist_money'], 2);
                        $jiangli = getFloatValue($frist_money * $tmoney / 100, 2);
                        if (!empty($jiangli)) {
                            memberMoneyLog($vo['uid'], 50, $jiangli, "首次线上充值奖励", 0, '@网站管理员@'); //首次充值奖励

                        }
                    }
                }
                if (!$newid) {
                    $updata['status'] = 0;
                    $Moneylog->where("uid={$vo['uid']} AND nid='{$nid}'")->save($updata);
                    return false;
                }
                $vx = M("members")->field("user_phone,user_name")->find($vo['uid']);
                SMStip("payonline", $vx['user_phone'], array(
                    "#USERNAME#",
                    "#MONEY#"
                ) , array(
                    $vx['user_name'],
                    $vo['money']
                ));
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
        if ($status > 0) {
            if ($xid) $done = true;
        }
        $this->locked = false;
        return $done;
    }
    public function is_signpay() {
        $uid = $this->uid;
        $minfo = M('members')->where('id=' . $uid)->find();
        $mstatus = M('members_status')->where('uid=' . $uid)->find();
        if ($mstatus['id_status'] == 1) {
            if ($minfo['agrmno']) {
                $jsons['is_sign'] = 1;
                $jsons['status'] = 1;
                $jsons['agrmno'] = $minfo['agrmno'];
            } else {
                $jsons['is_sign'] = 0;
                $jsons['status'] = 1;
                $jsons['agrmno'] = 0;
            }
        } else {
            $jsons['is_sign'] = 0;
            $jsons['status'] = 2;
            $jsons['agrmno'] = 0;
        }
        outJson($jsons);
    }
    public function payminfo() {
        $uid = $this->uid;
        $minfo = M('member_info')->where('uid=' . $uid)->find();
        $mmoney = M('member_money')->where('uid=' . $uid)->find();
        $jsons['status'] = 1;
        $jsons['bank_name'] = $minfo['bank_name'];
        $jsons['bank_msg'] = "单笔限额10万，单日限额20万";
        $jsons['account_no'] = $minfo['account_no'];
        $jsons['amoney'] = $mmoney['account_money'];
        $bank_list = array(
            '中国工商银行' => 'ICBC',
            '中国农业银行' => 'ABC',
            '中国银行' => 'BOCSH',
            '中国建设银行' => 'CCB',
            '中国邮政储蓄银行' => 'PSBC',
            '中信银行' => 'CNCB',
            '中国光大银行' => 'CEB',
            '兴业银行' => 'CIB'
        );
        $bank_code = $bank_list[$minfo['bank_name']];
        $jsons['bank_ico'] = $this->domainUrl . '/Public/bank/' . $bank_code . ".png";
        outJson($jsons);
    }
    //线下充值
    public function pay_offline() {
        $this->uid = $uid = intval($_REQUEST["uid"]);
        $ids = M('members_status')->getFieldByUid($this->uid, 'id_status');
        if ($ids != 1) {
            $jsons['msg'] = "请先通过实名认证后再进行充值。";
            $jsons['status'] = '0';
            outJson($jsons);
        }
        $data['money'] = floatval($_REQUEST['money_off']);
        //本地要保存的信息
        $data['fee'] = 0;
        $data['nid'] = 'offline';
        $data['way'] = 'off';
        $data['uid'] = $uid;
        $data['add_time'] = time();
        // $this->paydetail['tran_id'] = text($_REQUEST['tran_id']);
        // $this->paydetail['off_bank'] = text($_REQUEST['off_bank']);
        // $this->paydetail['off_way'] = text($_REQUEST['off_way']);
        $data['way_img'] = text($_REQUEST['way_img']);
        $newid = M('member_payonline')->add($data);
        if ($newid) {
            notice1("10", $this->uid);
            $jsons['status'] = '1';
            $jsons['msg'] = '线下充值提交成功，请等待管理员审核';
        } else {
            $jsons['status'] = '0';
            $jsons['msg'] = '线下充值提交失败，请重试';
        }
        outJson($jsons);
    }
    //扫码支付
    public function pay_saoma() {
        $uid = intval($_REQUEST["uid"]);
        $ids = M('members_status')->getFieldByUid($this->uid, 'id_status');
        if ($ids != 1) {
            $jsons['msg'] = "请先通过实名认证后再进行充值。";
            $jsons['status'] = '0';
            outJson($jsons);
        }
        $dataz['uid'] = $uid;
        $dataz['nid'] = "SMF-" . date('Ymd') . '-' . substr(microtime() , 2, 6);
        $dataz['money'] = (float)$_REQUEST['money_off'];
        $dataz['fee'] = 0;
        $dataz['way'] = "smf";
        //  $status_arr =array('待审核','审核通过,处理中','已提现','审核未通过');
        $dataz['status'] = 0;
        $dataz['add_time'] = time();
        $dataz['way_img'] = $_REQUEST['way_img'];;
        $dataz['add_ip'] = get_client_ip();
        $dataz['deal_info'] = "扫码付充值";
        $newid = M('member_payonline')->add($dataz);
        if ($newid) {
            notice1("10", $this->uid);
            $jsons['status'] = '1';
            $jsons['msg'] = '鱼币购买成功，等待审核';
        } else {
            $jsons['status'] = '0';
            $jsons['msg'] = '充值失败，请保留存单，联系客服';
        }
        outJson($jsons);
    }
    //签到
    public function sign() {
        $this->uid = $uid = intval($_REQUEST["uid"]);
        $mapa['uid'] = $this->uid;
        $va = strtotime(date("Y-m-1 0:0:0", time()));
        //var_dump(date("Y-m-1 0:0:0",$va));
        $mapa['sign_time'] = ["egt", $va];
        $sustain_day = M("member_sign")->where($mapa)->order("sign_time desc")->find();
        // $mapa2['uid'] = $this->uid;
        // $mapa2['sign_time']=["lt",$va];
        // $sustain_day2=M("member_sign")->where($mapa2)->order("sign_time desc")
        // ->find();
        // //  var_dump($sustain_day2); die;
        // //var_dump($sustain_day['sustain_day'],$sustain_day2['sustain_day']); die;
        // if($sustain_day['sustain_day']>$sustain_day2['sustain_day']){
        //         $sustain_day['sustain_day']=$sustain_day['sustain_day']-$sustain_day2['sustain_day'];
        // }else{
        //         $sustain_day['sustain_day']=$sustain_day['sustain_day'];
        // }
        //if()
        //$list = M("member_sign")->where("uid={$uid}")->order("sign_time desc")->find();
        $addsign['sign_time'] = strtotime(date("Y-m-d 0:0:0", time()));
        //$addsign['sign_time']=time();
        $addsign['uid'] = $uid;
        //php获取今日起始时间戳和结束时间戳
        $endYesterday = mktime(0, 0, 0, date('m') , date('d') + 1, date('Y')) - 1;
        $beginYesterday = mktime(0, 0, 0, date('m') , date('d') , date('Y'));
        if ($beginYesterday <= intval($sustain_day['sign_time'])) {
            if ((intval($list['sign_time']) < $endYesterday)) {
                $jsons['status'] = '0';
                $jsons['msg'] = '已签到';
                outJson($jsons);
                exit();
            }
        }
        $beginYesterday = mktime(0, 0, 0, date('m') , date('d') - 1, date('Y'));
        $endYesterday = mktime(0, 0, 0, date('m') , date('d') , date('Y')) - 1;
        $das = intval(date("d", time()));
        // if($das==1){
        //     $addsign['sustain_day']=1;
        // }else{
        // }
        if ($sustain_day) {
            $addsign['sustain_day'] = $sustain_day['sustain_day'] + 1;
        } else {
            $addsign['sustain_day'] = 1;
        }
        // if($addsign['sustain_day']==1){
        //      //1元优惠券
        //      $addsign['money_type']=2;
        //      $pubData = array(
        //      'uid' => $this->uid,
        //      'money_bonus' =>1,
        //      'start_time' => strtotime(date('Y-m-d 00:00:00')),
        //      'end_time'   => strtotime(date("Y-m-d", strtotime("+1 months", strtotime(date("Y-m-d"))))),
        //      'bonus_invest_min' => 0,
        //      );
        //      $rs = M('member_bonus')->add($pubData);
        //      $infos="累计签到1天奖励1元优惠券";
        // }else if($addsign['sustain_day']==7){
        //     //2000积分
        //     $jaddsign['money_type']=1;
        //     $jaddsign['integral']=2000;
        //     $infos="累计签到7天奖励积分2000";
        // }else if($addsign['sustain_day']==14){
        //     //8元优惠券
        //     $addsign['money_type']=2;
        //     $pubData = array(
        //      'uid' => $this->uid,
        //      'money_bonus' =>8,
        //      'start_time' => strtotime(date('Y-m-d 00:00:00')),
        //      'end_time'   => strtotime(date("Y-m-d", strtotime("+1 months", strtotime(date("Y-m-d"))))),
        //      'bonus_invest_min' => 0,
        //      );
        //      $rs = M('member_bonus')->add($pubData);
        //      $infos="累计签到14天奖励8元优惠券";
        // }else if($addsign['sustain_day']==21){
        //     //10000积分
        //     $addsign['money_type']=1;
        //     $jaddsign['integral']=10000;
        //     $jinfos="累计签到21天奖励积分10000";
        // }else if($addsign['sustain_day']==30){
        //     //20元优惠券
        //     $addsign['money_type']=2;
        //     $pubData = array(
        //      'uid' => $this->uid,
        //      'money_bonus' =>20,
        //      'start_time' => strtotime(date('Y-m-d 00:00:00')),
        //      'end_time'   => strtotime(date("Y-m-d", strtotime("+1 months", strtotime(date("Y-m-d"))))),
        //      'bonus_invest_min' => 0,
        //      );
        //      $rs = M('member_bonus')->add($pubData);
        //      $infos="累计签到30天奖励20元优惠券";
        // }
        $va = strtotime(date("Y-m-1 0:0:0", time()));
        $mapd['sign_time'] = ["egt", $va];
        $mapd['uid'] = $uid;
        //$mapd['affect_credits']=10;
        $list = M("member_sign")->where($mapd)->group('sign_time')->order("sign_time ASC")->select();
        if (!$list) {
            //1元优惠券
            $addsign['money_type'] = 2;
            $pubData = array(
                'uid' => $this->uid,
                'add_time' => strtotime(date('Y-m-d 00:00:00')) ,
                'money_bonus' => 1,
                'start_time' => strtotime(date('Y-m-d 00:00:00')) ,
                'end_time' => strtotime(date("Y-m-d 23:59:59", strtotime("+1 months", strtotime(date("Y-m-d"))))) ,
                'bonus_invest_min' => 2000,
            );
            $rs = M('member_bonus')->add($pubData);
            $infos = "累计签到1天奖励1元优惠券";
        } else if (count($list) == 6) {
            //2000积分
            $jaddsign['money_type'] = 1;
            $jaddsign['integral'] = 2000;
            $jinfo = "累计签到7天奖励积分2000";
        } else if (count($list) == 13) {
            //8元优惠券
            $addsign['money_type'] = 2;
            $pubData = array(
                'uid' => $this->uid,
                'add_time' => strtotime(date('Y-m-d 00:00:00')) ,
                'money_bonus' => 8,
                'start_time' => strtotime(date('Y-m-d 00:00:00')) ,
                'end_time' => strtotime(date("Y-m-d 23:59:59", strtotime("+1 months", strtotime(date("Y-m-d"))))) ,
                'bonus_invest_min' => 5000,
            );
            $rs = M('member_bonus')->add($pubData);
            $infos = "累计签到14天奖励8元优惠券";
        } else if (count($list) == 20) {
            //10000积分
            $jaddsign['money_type'] = 1;
            $jaddsign['integral'] = 10000;
            $jinfo = "累计签到21天奖励积分10000";
        } else if (count($list) == 29) {
            //20元优惠券
            $addsign['money_type'] = 2;
            $pubData = array(
                'uid' => $this->uid,
                'add_time' => strtotime(date('Y-m-d 00:00:00')) ,
                'money_bonus' => 20,
                'start_time' => strtotime(date('Y-m-d 00:00:00')) ,
                'end_time' => strtotime(date("Y-m-d 23:59:59", strtotime("+1 months", strtotime(date("Y-m-d"))))) ,
                'bonus_invest_min' => 10000,
            );
            $rs = M('member_bonus')->add($pubData);
            $infos = "累计签到30天奖励20元优惠券";
        }
        $addsign['integral'] = 10;
        $infos = "签到奖励积分10";
        $addsign['cumulative_day'] = M("member_sign")->where("uid={$uid}")->order("sign_time desc")->limit('0,30')->count;
        $zz = memberCreditsLog($uid, 13, $addsign['integral'], $info = "签到积分");
        $zz = memberCreditsLog($uid, 13, $jaddsign['integral'], $jinfo = "签到积分");
        $addsign['status'] = '1';
        $list = M("member_sign")->where("uid={$this->uid}")->order("sign_time desc")->limit('0,30')->add($addsign);
        $jsons['msg'] = '签到成功';
        $jsons['status'] = '1';
        $jsons['data'] = $infos;
        outJson($jsons);
    }
    public function qiandaobonus() {
        $uid = $this->uid = $_REQUEST["uid"];
        $date = $_REQUEST["date"];
        $va = strtotime(date("Y-m-1 0:0:0", time()));
        $mapd['sign_time'] = ["egt", $va];
        $mapd['uid'] = $uid;
        //$mapd['affect_credits']=10;
        $list = M("member_sign")->where($mapd)->order("sign_time ASC")->select();
        // var_dump(count($list));
        // var_dump($date);
        // var_dump($list[$date-1]);
        if ($date == 1) {
            if (count($list) >= 1 && $list[0]['status'] == 0) {
                //1元优惠券
                $addsign['money_type'] = 2;
                $pubData = array(
                    'uid' => $this->uid,
                    'add_time' => strtotime(date('Y-m-d 00:00:00')) ,
                    'money_bonus' => 1,
                    'start_time' => strtotime(date('Y-m-d 00:00:00')) ,
                    'end_time' => strtotime(date("Y-m-d 23:59:59", strtotime("+1 months", strtotime(date("Y-m-d"))))) ,
                    'bonus_invest_min' => 2000,
                );
                $rs = M('member_bonus')->add($pubData);
                $infos = "累计签到1天奖励1元优惠券";
            }
        }
        if ($date == 7 && $list[6]['status'] == 0) {
            if (count($list) >= 7) {
                //2000积分
                $jaddsign['money_type'] = 1;
                $jaddsign['integral'] = 2000;
                $jinfos = "累计签到7天奖励积分2000";
            }
        }
        if ($date == 14 && $list[13]['status'] == 0) {
            if (count($list) >= 14) {
                //8元优惠券
                $addsign['money_type'] = 2;
                $pubData = array(
                    'uid' => $this->uid,
                    'add_time' => strtotime(date('Y-m-d 00:00:00')) ,
                    'money_bonus' => 8,
                    'start_time' => strtotime(date('Y-m-d 00:00:00')) ,
                    'end_time' => strtotime(date("Y-m-d 23:59:59", strtotime("+1 months", strtotime(date("Y-m-d"))))) ,
                    'bonus_invest_min' => 5000,
                );
                $rs = M('member_bonus')->add($pubData);
                $infos = "累计签到14天奖励8元优惠券";
            }
        }
        if ($date == 21 && $list[20]['status'] == 0) {
            if (count($list) >= 21) {
                //10000积分
                $jaddsign['money_type'] = 1;
                $jaddsign['integral'] = 10000;
                $jinfos = "累计签到21天奖励积分10000";
            }
        }
        if ($date == 30 && $list[29]['status'] == 0) {
            if (count($list) >= 30) {
                //20元优惠券
                $addsign['money_type'] = 2;
                $pubData = array(
                    'uid' => $this->uid,
                    'add_time' => strtotime(date('Y-m-d 00:00:00')) ,
                    'money_bonus' => 20,
                    'start_time' => strtotime(date('Y-m-d 00:00:00')) ,
                    'end_time' => strtotime(date("Y-m-d 23:59:59", strtotime("+1 months", strtotime(date("Y-m-d"))))) ,
                    'bonus_invest_min' => 10000,
                );
                $rs = M('member_bonus')->add($pubData);
                $infos = "累计签到30天奖励20元优惠券";
            }
        }
        //var_dump($jaddsign);
        $zz = memberCreditsLog($this->uid, 13, intval($jaddsign['integral']) , $jinfos);
        if ($zz) {
            $data["status"] = '1';
            $date = $date - 1;
            $wheredata["id"] = $list[$date]["id"];
            $wheredata["uid"] = $this->uid;
            $res = M("member_sign")->where($wheredata)->save($data);
            //var_dump(M("member_sign")->getlastsql());
            if ($res) {
                $jsons['msg'] = '领取成功';
                $jsons['status'] = '1';
            } else {
                $jsons['msg'] = '领取失败';
                $jsons['status'] = '0';
            }
        } else {
            $jsons['msg'] = '领取失败';
            $jsons['status'] = '0';
        }
        outJson($jsons);
    }
    //    public function bufa(){
    //        $maps["id"]=array("in",'1334,1387,1465,1472,1476,1530,1536,1747,1748,1997,2085,2093,2119,2134,2245,2256,2262,2286,2314,2403,123601,123669,123717,123749,123783,123836,123890,124027,124036,124058,124067,124112,124160,124374,124382,124534,124547,124661,124742
    //');
    //        $mlist=M("members")->where($maps)->select();
    //
    ////        $va=strtotime(date("Y-2-1 0:0:0",time()));
    ////        pre($mlist);exit;
    ////        $mapd['sign_time']=["egt",$va];
    //        foreach ($mlist as $k => $v) {
    ////            $mapd['uid'] = $v["id"];
    ////            $list = M("member_sign")->where($mapd)->order("sign_time ASC")->select();
    //            //var_dump($list);exit;
    //            //if (count($list) >= 21) {
    ////            if(count($list)==28){
    ////                var_dump($v["user_name"]);
    //            var_dump(count());
    //                $pubData = array(
    //                    'uid' => $v['id'],
    //                    'add_time'=> strtotime(date('Y-m-d 00:00:00')),
    //                    'money_bonus' =>20,
    //                    'start_time' => strtotime(date('Y-m-d 00:00:00')),
    //                    'end_time'   => strtotime(date("Y-m-d 23:59:59", strtotime("+1 months", strtotime(date("Y-m-d"))))),
    //                    'bonus_invest_min' => 10000,
    //                );
    //                pre($pubData);
    //                //$rs = M('member_bonus')->add($pubData);
    //
    //            //}
    //        }
    //
    //
    //    }
    public function xiugais() {
        $mlist = M("members")->select();
        $va = strtotime(date("Y-m-1 0:0:0", time()));
        $mapd['sign_time'] = ["egt", $va];
        //$mapd['']
        //$mapd['affect_credits']=10;
        foreach ($mlist as $k => $v) {
            $mapd['uid'] = $v["id"];
            $list = M("member_sign")->where($mapd)->order("sign_time ASC")->select();
            if (count($list) >= 21) {
                //select  *  from lzh_member_creditslog s    where add_time > 1575129600 and affect_credits=10000  and type=13
                //var_dump($list[20]["id"]);
                $mdd["uid"] = $v['id'];
                $mdd["add_time"] = ["egt", $va];
                $mdd["affect_credits"] = 10000;
                $mdd['type'] = 13;
                $qdjf = M("member_creditslog")->where($mdd)->find();
                if (!$qdjf) {
                    var_dump($list[20]["id"]);
                    $data["status"] = '0';
                    $wheredata["id"] = $list[20]["id"];
                    $wheredata["uid"] = $v['id'];
                    $res = M("member_sign")->where($wheredata)->save($data);
                    var_dump($res);
                }
            }
        }
        // $mapd['uid']=1472;
        // $list = M("member_sign")->where($mapd)->order("sign_time ASC")->select();
        // if(count($list)>=21){
        //     var_dump($list);
        //    //select  *  from lzh_member_creditslog s    where add_time > 1575129600 and affect_credits=10000  and type=13
        //    //var_dump($list[20]["id"]);
        //    $mdd["uid"]=1472;
        //    $mdd["add_time"]=["egt",$va];
        //    $mdd["affect_credits"]=10000;
        //    $mdd['type']=13;
        //    $qdjf= M("member_creditslog")->where($mdd)->find();
        //    var_dump($qdjf);
        //    if(!$qdjf){
        //         var_dump($list[20]["id"]);
        //         $data["status"]='0';
        //         $wheredata["id"]=$list[20]["id"];
        //         $wheredata["uid"]=1472;
        //         $res=M("member_sign")->where($wheredata)->save($data);
        //         var_dump(M("member_sign")->getlastsql());
        //         //var_dump($res);
        //    }
        // }

    }
    //我的签到
    public function sign_list() {
        $uid = intval($_REQUEST["uid"]);
        $maps['uid'] = $uid;
        $va = getCreditsLog($maps, 1);
        $av = $va['list'][0];
        $va = strtotime(date("Y-m-1 0:0:0", time()));
        $map['sign_time'] = ["egt", $va];
        $map['uid'] = $uid;
        //$map['affect_credits']=10;
        $list = M("member_sign")->where($map)->order("sign_time ASC")->select();
        $jsons['msg'] = '成功';
        $jsons['data'] = $list;
        $jsons['status'] = '1';
        $jsons["count"] = count($list);
        $jsons["jifen"] = $av["account_credits"];
        if (isset($list[0]) && $list[0]['status'] == 0 && count($list) >= 1) {
            $jsons['ling']['ling1'] = '1';
        } else if (isset($list[0]) && $list[0]['status'] == 1 && count($list) >= 1) {
            $jsons['ling']['ling1'] = '2';
        } else {
            $jsons['ling']['ling1'] = '0';
        }
        if (isset($list[6]) && $list[6]['status'] == 0 && count($list) >= 7) {
            $jsons['ling']['ling2'] = '1';
        } else if (isset($list[6]) && $list[6]['status'] == 1 && count($list) >= 1) {
            $jsons['ling']['ling2'] = '2';
        } else {
            $jsons['ling']['ling2'] = '0';
        }
        if (isset($list[13]) && $list[13]['status'] == 0 && count($list) >= 14) {
            $jsons['ling']['ling3'] = '1';
        } else if (isset($list[13]) && $list[13]['status'] == 1 && count($list) >= 1) {
            $jsons['ling']['ling3'] = '2';
        } else {
            $jsons['ling']['ling3'] = '0';
        }
        if (isset($list[20]) && $list[20]['status'] == 0 && count($list) >= 20) {
            $jsons['ling']['ling4'] = '1';
        } else if (isset($list[20]) && $list[20]['status'] == 1 && count($list) >= 1) {
            $jsons['ling']['ling4'] = '2';
        } else {
            $jsons['ling']['ling4'] = '0';
        }
        if (isset($list[27]) && date('m', time()) == 2 && $list[27]['status'] == 0 && count($list) >= 27) {
            $jsons['ling']['ling5'] = '1';
        } else if (isset($list[27]) && date('m', time()) == 2 && $list[27]['status'] == 1 && count($list) >= 1) {
            $jsons['ling']['ling5'] = '2';
        } else if (isset($list[29]) && $list[29]['status'] == 0 && count($list) >= 29) {
            $jsons['ling']['ling5'] = '1';
        } else if (isset($list[29]) && $list[29]['status'] == 1 && count($list) >= 1) {
            $jsons['ling']['ling5'] = '2';
        } else {
            $jsons['ling']['ling5'] = '0';
        }
        outJson($jsons);
    }
    public function sing_goods() {
        $uid = intval($_REQUEST["uid"]);
        $map['uid'] = $uid;
        $field = 'b.uid,b.gid,b.jifen,b.jine,b.num,b.add_time,b.add_ip,b.action,m.id mid,m.user_name,x.*,x.id ma_id,b.id id';
        $zvolist = M('order b')->field($field)->join("lzh_members m ON m.id=b.uid")->join("lzh_market x ON x.id=b.gid")->where($map)->limit(2)->order("b.id DESC")->select();
        foreach ($zvolist as $k => $v) {
            $zvolist[$k]["art_img"] = $this->urls . $v["art_img"];
            if ($v["carid"] != null) {
                $goods = M("car c")->field('c.num,m.id,title,art_info,art_time,art_img,art_jiage,type_id')->where("c.id in (" . $v["carid"] . ")")->join("lzh_market m ON m.id=c.gid")->select();
            } else {
                $goods = M("market")->field('id,title,art_info,art_time,art_img,art_jiage,type_id')->where("id=" . $v["gid"])->select();
                //$goods[0]["num"]=$info["num"];

            }
            $zvolist[$k]["goods"] = $goods;
        }
        $jsons['msg'] = '成功';
        $jsons['data'] = $zvolist;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    //提现记录
    public function withdraw_list() {
        $uid = intval($_REQUEST["uid"]);
        $page = intval($_REQUEST["page"]);
        $size = intval($_REQUEST["size"]);
        $limit = $page * $size . ',' . $size;
        $order = "add_time desc";
        $map["uid"] = $uid;
        $list = M('member_withdraw')->where($map)->limit($limit)->order($order)->select();
        if (count($list) == $size) {
            $limits = ($page + 1) * $size . ',' . $size;
            $countlist = M('member_withdraw')->where($map)->limit($limits)->order($order)->count();
            if ($countlist > 0) {
                $jsons['data']["ispage"] = 1;
            } else {
                $jsons['data']["ispage"] = 0;
            }
        } else {
            $jsons['data']["ispage"] = 0;
        }
        $jsons['data']['size'] = $size;
        $jsons['data']['page'] = $page;
        $jsons['data']["list"] = $list;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    //充值记录
    public function pay_list() {
        $uid = intval($_REQUEST["uid"]);
        $page = intval($_REQUEST["page"]);
        $size = intval($_REQUEST["size"]);
        $limit = $page * $size . ',' . $size;
        $order = "add_time desc";
        $map["uid"] = $uid;
        $map['status'] = 1;
        $list = M('member_payonline')->where($map)->limit($limit)->order($order)->select();
        if (count($list) == $size) {
            $limits = ($page + 1) * $size . ',' . $size;
            $countlist = M('member_payonline')->where($map)->limit($limits)->order($order)->count();
            if ($countlist > 0) {
                $jsons['data']["ispage"] = 1;
            } else {
                $jsons['data']["ispage"] = 0;
            }
        } else {
            $jsons['data']["ispage"] = 0;
        }
        $jsons['data']['size'] = $size;
        $jsons['data']['page'] = $page;
        $jsons['data']["list"] = $list;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    //收支明细
    public function shouzi_list() {
        $uid = intval($_REQUEST["uid"]);
        $page = intval($_REQUEST["page"]);
        $size = intval($_REQUEST["size"]);
        $limit = $page * $size . ',' . $size;
        $order = "id desc";
        $map["uid"] = $uid;
        $list = M('member_moneylog')->where($map)->limit($limit)->order($order)->select();
        $type_arr = c("MONEY_LOG");
        foreach ($list as $key => $v) {
            $list[$key]['typeid'] = $v['type'];
            $list[$key]['type'] = $type_arr[$v['type']];
        }
        if (count($list) == $size) {
            $limits = ($page + 1) * $size . ',' . $size;
            $countlist = M('member_moneylog')->where($map)->limit($limits)->order($order)->count();
            if ($countlist > 0) {
                $jsons['data']["ispage"] = 1;
            } else {
                $jsons['data']["ispage"] = 0;
            }
        } else {
            $jsons['data']["ispage"] = 0;
        }
        $jsons['data']['size'] = $size;
        $jsons['data']['page'] = $page;
        $jsons['data']["list"] = $list;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    //我的邀请
    public function yaoqing() {
        $uid = intval($_REQUEST["uid"]);
        $incode = M('members')->getFieldById($uid, 'incode');
        $value = "https://" . $_SERVER['SERVER_NAME'] . "/m/login?invite=" . $incode; //二维码内容
        Vendor('phpqrcode');
        $errorCorrectionLevel = 'L'; //容错级别
        $matrixPointSize = 6; //生成图片大小
        //生成二维码图片
        QRcode::png($value, "Public/ewm/{$incode}.png", $errorCorrectionLevel, $matrixPointSize, 2);
        //$QR = 'qrcode.png';//已经生成的原始二维码图
        //输出图片
        //imagepng($QR, "Public/ewm/{$incode}.png");
        $jsons['msg'] = '成功';
        $jsons['data'] = $this->urls . "/Public/ewm/{$incode}.png";
        $jsons['incode'] = $incode;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    //邀请列表
    public function yq_list() {
        $uid = intval($_REQUEST["uid"]);
        $page = intval($_REQUEST["page"]);
        $size = intval($_REQUEST["size"]);
        $limit = $page * $size . ',' . $size;
        // $field = " m.id,m.user_name,m.reg_time,sum(ml.affect_money) jiangli ";
        $field1 = " m.user_name,m.reg_time,m.recommend_id,m.recommend_bid,m.recommend_cid,m.id";
        // $vm = M("members m")->field($field)->join(" lzh_member_moneylog ml ON m.id = ml.target_uid ")->where(" m.recommend_id ={$uid} AND ml.type =13")->group("ml.target_uid")->select();
        $vm1 = M("members m")->field($field1)->where(" m.recommend_id ={$uid}")->limit($limit)->order("id desc")->select();
        //var_dump( M("members m")->getlastsql());
        foreach ($vm1 as $k => $v) {
            if ($v["recommend_cid"] > 0) {
                $vm1[$k]["jibie"] = "三级会员";
            } else if ($v["recommend_bid"] > 0) {
                $vm1[$k]["jibie"] = "二级会员";
            } else if ($v["recommend_id"] > 0) {
                $vm1[$k]["jibie"] = "一级会员";
            } else {
                $vm1[$k]["jibie"] = "推销商";
            }
            $vm1[$k]["touzi"] = $this->is_investor($v["id"]);
        }
        if (count($vm1) == $size) {
            $limits = ($page + 1) * $size . ',' . $size;
            $countlist = M("members m")->where(" m.recommend_id ={$uid}")->limit($limits)->order("id desc")->count();
            if ($countlist > 0) {
                $jsons['data']["ispage"] = 1;
            } else {
                $jsons['data']["ispage"] = 0;
            }
        } else {
            $jsons['data']["ispage"] = 0;
        }
        $jsons['data']['size'] = $size;
        $jsons['data']['page'] = $page;
        $jsons['data']["list"] = $vm1;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    public function is_investor($uid) {
        $count = M('borrow_investor')->where('investor_uid = ' . $uid)->count('id');
        return $count > 0 ? '有过投资' : '未投过资';
    }
    //奖金记录
    public function promotionlog() {
        $this->uid = intval($_REQUEST["uid"]);
        $map['uid'] = $this->uid;
        $map['type'] = array(
            "in",
            "221"
        );
        //$list = getMoneyLog($map,15);
        $page = intval($_REQUEST["page"]);
        $size = intval($_REQUEST["size"]);
        $limit = $page * $size . ',' . $size;
        $order = "id desc";
        $list = M('member_moneylog')->where($map)->limit($limit)->order($order)->select();
        $type_arr = c("MONEY_LOG");
        foreach ($list as $key => $v) {
            $list[$key]['typeid'] = $v['type'];
            $list[$key]['type'] = $type_arr[$v['type']];
        }
        if (count($list) == $size) {
            $limits = ($page + 1) * $size . ',' . $size;
            $countlist = M('member_moneylog')->where($map)->limit($limits)->order($order)->count();
            if ($countlist > 0) {
                $jsons['data']["ispage"] = 1;
            } else {
                $jsons['data']["ispage"] = 0;
            }
        } else {
            $jsons['data']["ispage"] = 0;
        }
        $jsons['data']['size'] = $size;
        $jsons['data']['page'] = $page;
        $jsons['data']["list"] = $list;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    //新手体验标
    public function new_invest() {
        $uid = intval($_REQUEST["uid"]);
        $borrowinfo = M("borrow_info")->where("id=1")->find();
        $tyb["borrow_interest_rate"] = $borrowinfo["borrow_interest_rate"];
        $member_money = M("member_money")->where("uid=" . $uid)->find();
        $money_experience = $member_money["money_experience"];
        $tyb["money_experience"] = $money_experience;
        $jsons['msg'] = '成功';
        $jsons['data'] = $tyb;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    public function investmoney() {
        $this->uid = intval($_REQUEST["uid"]);
        $_POST = $_REQUEST;
        //红包
        if (!empty($_POST["bonus_id"])) {
            $borrow = M("member_bonus")->where("id=" . $_POST["bonus_id"])->find();
            $_POST["money"] = $_POST["money"] - $borrow["money_bonus"];
        }
        //var_dump($_POST);exit;
        if (!$this->uid) {
            $jsons['msg'] = '用户未登录';
            $jsons['status'] = '0';
            outJson($jsons);
        }
        // /****** 防止模拟表单提交 *********/
        // $cookieKeyS = cookie(strtolower(MODULE_NAME).'-invest');
        // //echo cookie(strtolower(MODULE_NAME)."-invest");
        // //echo $_REQUEST['cookieKey'];
        // //exit;
        // if ($cookieKeyS != $_REQUEST['cookieKey']) {
        //     //$this->error("数据校验有误");
        // }
        // // 表单令牌验证
        // if(!M("members")->autoCheckToken($_POST)) {
        //     echo "<script type='text/javascript'>";
        //     echo "alert('禁止重复提交表单');";
        //     echo "window.location.href='" . getenv("HTTP_REFERER") . "';";
        //     echo "</script>";
        //     exit();
        // }
        $zt = $_POST["zt"];
        $sg = $_POST["sg"];
        if ($zt < 0 || $sg < 0) {
            $jsons['msg'] = '数量不能为负数';
            $jsons['status'] = '0';
            outJson($jsons);
        }
        /****** 防止模拟表单提交 *********/
        $money = $_POST['money'];
        writeLog($_POST);
        $borrow_id = (int)($_POST['borrow_id']);
        $is_experience = isset($_POST['is_experience']) && 1 == $_POST['is_experience'] ? 1 : 0;
        $bonus_id = isset($_POST['bonus_id']) ? (int)($_POST['bonus_id']) : 0;
        $memberinterest_id = isset($_POST['memberinterest_id']) ? (int)($_POST['memberinterest_id']) : 0;
        if ($bonus_id > 0) {
            $bs = M('member_bonus')->where("id='{$bonus_id}'")->find();
            $canInvestMoneys = canInvestMoney($this->uid, $borrow_id, $money + $bs['money_bonus'], 0, $is_experience, '0', $bonus_id, text($_POST['borrow_pass']));
            if (0 == $canInvestMoneys['status']) {
                $jsons['msg'] = $canInvestMoneys['tips'];
                $jsons['status'] = '0';
                outJson($jsons);
            }
            $money_bonuss = $canInvestMoneys['money_bonus'];
            $money = (float)($money + $money_bonuss);
        }
        $m = M('member_money')->field('account_money,back_money,yubi')->find($this->uid);
        $amoney = $m['account_money'] + $m['back_money'] + $m['yubi'];
        $uname = session('u_user_name');
        $vm = getMinfo($this->uid, 'm.pin_pass,mm.account_money,mm.back_money,mm.money_experience');
        $pin_pass = $vm['pin_pass'];
        $pin = md5($_POST['pin']);
        if ($pin_pass == '') {
            $jsons['msg'] = "支付密码为空，请您设置密码";
            $jsons['status'] = '0';
            outJson($jsons);
        } else if ($pin <> $pin_pass) {
            $jsons['msg'] = "支付密码错误，请重试";
            $jsons['status'] = '0';
            outJson($jsons);
        }
        $binfo = M('borrow_info')->field('shoujia,borrow_uid,borrow_money,has_borrow,has_vouch,borrow_max,borrow_min,borrow_type,password,pause,max_limit,start_time,new_user_only,bespeak_able,bespeak_money,bespeak_days')->find($borrow_id);
        $binfo['borrow_max'] = $binfo['borrow_min'] * $binfo['max_limit'];
        $minfo = getMinfo($this->uid, true);
        if ($this->uid == $binfo["borrow_uid"]) {
            $jsons['msg'] = '不能对自己的项目投标';
            $jsons['status'] = '0';
            outJson($jsons);
        }
        $ids = M('members_status')->getFieldByUid($this->uid, 'id_status');
        if ($ids != 1) {
            $jsons['msg'] = "请先通过实名认证后再进行投标。";
            $jsons['status'] = '0';
            outJson($jsons);
        }
        $phones = M('members_status')->getFieldByUid($this->uid, 'phone_status');
        if ($phones != 1) {
            $jsons['msg'] = "请先通过手机认证后再进行投标。";
            $jsons['status'] = '0';
            outJson($jsons);
        }
        if ($binfo['pause'] == 1) {
            $jsons['msg'] = "此标当前已经截止，请等待管理员开启。";
            $jsons['status'] = '0';
            outJson($jsons);
        }
        // 50 > 10
        if ($money < $binfo['borrow_min']) {
            $jsons['msg'] = '此项目最小投资金额为' . $binfo['borrow_min'] . '元';
            $jsons['status'] = '0';
            outJson($jsons);
        }
        if ($binfo['has_vouch'] < $binfo['borrow_money'] && 2 == $binfo['borrow_type']) {
            $jsons['msg'] = '此标担保还未完成，您可以担保此标或者等担保完成再投投资';
            $jsons['status'] = '0';
            outJson($jsons);
        }
        if (!empty($binfo['password'])) {
            if (empty($_POST['borrow_pass'])) {
                $jsons['msg'] = '此标是定向项目，必须验证项目密码';
                $jsons['status'] = '0';
                outJson($jsons);
            } elseif ($binfo['password'] != $_POST['borrow_pass']) {
                $jsons['msg'] = '投标密码不正确';
                $jsons['status'] = '0';
                outJson($jsons);
            }
        }
        if ($binfo["new_user_only"] == 1) {
            $new_user_time = time() - $this->glo['new_user_time'] * 24 * 60 * 60;
            $where = null;
            $where['investor_uid'] = array(
                'eq',
                "{$this->uid}"
            );
            if ($this->glo['new_user_time'] > 0) {
                $where['add_time'] = array(
                    array(
                        'egt',
                        "{$new_user_time}"
                    ) ,
                    array(
                        'elt',
                        time()
                    )
                );
            }
            $where['borrow_id'] = array(
                'neq',
                1
            );
            $newUser = count(M('borrow_investor')->where($where)->find());
            $where = null;
            $where['bespeak_uid'] = array(
                'eq',
                "{$this->uid}"
            );
            if ($this->glo['new_user_time'] > 0) {
                $where['add_time'] = array(
                    array(
                        'egt',
                        "{$new_user_time}"
                    ) ,
                    array(
                        'elt',
                        time()
                    )
                );
            }
            $where['borrow_id'] = array(
                'neq',
                1
            );
            $newUser = max($newUser, count(M('bespeak')->where($where)->find()));
            if ($newUser > 0) {
                $jsons['msg'] = '此标是新手专享标';
                $jsons['status'] = '0';
                outJson($jsons);
            }
        }
        //投标总数检测
        $capital = M('borrow_investor')->where("borrow_id={$borrow_id} AND investor_uid={$this->uid}")->sum('investor_capital');
        if (($capital + $money) > $binfo['borrow_max'] && $binfo['borrow_max'] > 0) {
            $xtee = $binfo['borrow_max'] - $capital;
            $jsons['msg'] = "您已投标{$capital}元，上限为{$binfo['borrow_max']}元，你最多只能再投{$xtee}元";
            $jsons['status'] = '0';
            outJson($jsons);
        }
        if ($binfo['bespeak_able'] == 1 && $binfo['start_time'] + $binfo["bespeak_days"] * 60 * 60 * 24 > time()) {
            $bespeak_money = M('bespeak')->where("borrow_id={$borrow_id} AND bespeak_uid={$this->uid} AND bespeak_status=0")->sum('bespeak_money');
            if (!$bespeak_money || $bespeak_money == 0) {
                //预约已投金额
                $bespeak_invest_money = M('bespeak')->where("borrow_id={$borrow_id} and bespeak_status = 1")->sum('bespeak_money');
                //非预约已投金额
                $bespeak_not_invest_money = bcsub($binfo["has_borrow"], $bespeak_invest_money, 2);
                //非预约金额
                $not_invest_money = bcsub($binfo["borrow_money"], $binfo["bespeak_money"], 2);
                //可投金额
                $can_invest_money = bcsub($not_invest_money, $bespeak_not_invest_money, 2);
                if ($can_invest_money == 0) {
                    $jsons['msg'] = '此标为预约标，当前预约已满，您未参与预约，请等待';
                    $jsons['status'] = '0';
                    outJson($jsons);
                } elseif ($money > $can_invest_money) {
                    $jsons['msg'] = '此标为预约标，当前最多可投{$can_invest_money}元';
                    $jsons['status'] = '0';
                    outJson($jsons);
                } elseif (bcsub($can_invest_money, $binfo['borrow_min'], 2) > 0 && bcsub($can_invest_money, $binfo['borrow_min'], 2) < $binfo["borrow_min"]) {
                    $jsons['msg'] = "此标为预约标，如果您投{$money}元，将导致最后一次投标最多只能投" . bcsub($can_invest_money, $binfo['borrow_min'], 2) . "元，小于最小投标金额{$binfo['borrow_min']}元,请重新选择投标金额";
                    $jsons['status'] = '0';
                    outJson($jsons);
                }
            } elseif ($bespeak_money != $money) {
                $jsons['msg'] = '此标为预约标，您已参与预约，此次支持金额必须与预约金额相等';
                $jsons['status'] = '0';
                outJson($jsons);
            }
            if ($borrow_id == 1 || $is_experience == 1) {
                $jsons['msg'] = '新手标不支持预约';
                $jsons['status'] = '0';
                outJson($jsons);
            }
            if ($bonus_id > 0) {
                $jsons['msg'] = '预约标不支持使用红包';
                $jsons['status'] = '0';
                outJson($jsons);
            }
        }
        $need = bcsub($binfo['borrow_money'], $binfo['has_borrow'], 2);
        $caninvest = bcsub($need, $binfo['borrow_min'], 2);
        //exit;
        if ($money > $caninvest && 0 != ($need - $money)) {
            if (0 == (int)$need or '0.00' == $need) {
                $jsons['msg'] = "尊敬的{$uname}，此项目已经投满";
                $jsons['status'] = '0';
                outJson($jsons);
                //
                //

            }
            if ($money > $need) {
                $jsons['msg'] = "尊敬的客户，此项目还差{$need}元满标,您最多只能再投{$need}元"; //'投资金额错误.';
                $jsons['status'] = '0';
                outJson($jsons);
                $money = $need;
            }
        }
        //var_dump($money);var_dump($need);exit;
        if (($need - $money) < 0) {
            $jsons['msg'] = "尊敬的客户，此项目还差{$need}元满标,您最多只能再投{$need}元"; //'投资金额错误.';
            $jsons['status'] = '0';
            outJson($jsons);
            $money = $need;
        } else {
            $capital = M('borrow_investor')->where("borrow_id={$borrow_id}")->sum('investor_capital');
            if ($capital > $binfo['borrow_money']) {
                $jsons['msg'] = '投资金额错误.';
                $jsons['status'] = '0';
                outJson($jsons);
            }
            $capital = M('investor_detail')->where("borrow_id={$borrow_id}")->sum('capital');
            if ($capital > $binfo['borrow_money']) {
                $jsons['msg'] = '投资金额错误.';
                $jsons['status'] = '0';
                outJson($jsons);
            }
            // $done = investMoney($this->uid,$borrow_id,$money);
            writeLog($memberinterest_id);
            if ($memberinterest_id > 0) {
                //加息券校验
                $canUseInterest = canUseInterest($this->uid, $memberinterest_id);
                writeLog($canUseInterest);
                if (0 == $canUseInterest['status']) {
                    $jsons['msg'] = '加息券不可用';
                    $jsons['status'] = '0';
                    outJson($jsons);
                }
                $interest_rate = $canUseInterest['interest_rate'];
            } else {
                $interest_rate = 0;
            }
            //体验金校验
            $canInvestMoney = canInvestMoney($this->uid, $borrow_id, $money, 0, $is_experience, '0', $bonus_id, text(@$_POST['borrow_pass']));
            if (0 == $canInvestMoney['status']) {
                $jsons['msg'] = $canInvestMoney['tips'];
                $jsons['status'] = '0';
                outJson($jsons);
            }
            $money_bonus = $canInvestMoney['money_bonus'];
            if (1 == $canInvestMoney['money_type'] && $amoney < $money) {
                $jsons['msg'] = "尊敬的{$uname}，您准备投标{$money}元，但您的账户可用余额为{$amoney}元，请先去充值再投项目.";
                $jsons['status'] = '0';
                outJson($jsons);
            }
            //          $done = investMoney($this->uid, $borrow_id, $money, '0', '1', $is_experience, $memberinterest_id, $bonus_id, $money_bonus, text(@$_POST['borrow_pass']));
            $ktfs = $money / $binfo["borrow_min"];
            $zfs = $zt + $sg;
            if ($binfo["shoujia"] == 0) {
                if ($ktfs >= $zfs) {
                    $sg = $zfs;
                } else {
                    $sg = $ktfs;
                }
                $zt = 0;
            } else {
                if ($ktfs < $zfs) {
                    if ($sg >= $ktfs) {
                        $sg = $ktfs;
                        $zt = 0;
                    } else {
                        $zt = $ktfs - $sg;
                    }
                }
            }
            $xsinvestor_capital = $sg * $binfo["borrow_min"];
            if (($zt + $sg) <= 0) {
                $jsons['msg'] = '项目份额错误！';
                $jsons['status'] = '0';
                outJson($jsons);
            }
            if ($is_experience == 1) {
                $money_experience = M("member_money")->where("uid=" . $this->uid)->find();
                $done = investMoney($this->uid, $borrow_id, $money, '0', '1', 1, $memberinterest_id, $bonus_id, $money_bonus, text(@$_POST['borrow_pass']) , $zt, $sg, $xsinvestor_capital);
                if ($done === true) {
                    $zrm = $binfo["borrow_money"] + $money;
                    M('borrow_info')->where("id={$borrow_id}")->save(['borrow_money' => $zrm]);
                }
            } else {
                $done = investMoney($this->uid, $borrow_id, $money, '0', '1', 0, $memberinterest_id, $bonus_id, $money_bonus, text(@$_POST['borrow_pass']) , $zt, $sg, $xsinvestor_capital);
            }
        }
        if (true === $done) {
            $jsons['msg'] = "恭喜成功投资{$money}元（其中使用红包{$money_bonus}元）!";
            $jsons['status'] = '1';
            outJson($jsons);
        } elseif ($done) {
            $jsons['msg'] = $done;
            $jsons['status'] = '0';
            outJson($jsons);
        } else {
            $jsons['msg'] = '对不起，投资失败，请重试!';
            $jsons['status'] = '0';
            outJson($jsons);
        }
    }
    //项目列表
    public function invest_lists() {
        $searchMap = array();
        $searchMap['borrow_status'] = array(
            "in",
            '1,2'
        );
        $searchMap['b.id'] = array(
            'neq',
            '1'
        );
        if (!empty($_REQUEST["pid"])) {
            $searchMap["b.pid"] = $_REQUEST["pid"];
        }
        $parm['map'] = $searchMap;
        $parm['orderby'] = "b.pid,b.borrow_status ASC,b.id DESC";
        $parm['map'] = $searchMap;
        $listBorrow = getBorrowList($parm);
        $lists = $listBorrow['list'];
        //$listBorrow       = getBorrowList($parm);
        foreach ($lists as $key => $val) {
            //var_dump($val);
            $fieldx = "bi.investor_capital,bi.add_time,m.user_name,bi.is_auto,bi.investor_interest";
            $investinfo = M("borrow_investor bi")->field($fieldx)->join("lzh_members m ON bi.investor_uid = m.id")->where("bi.borrow_id={$val['id']}")->order("bi.id DESC")->select();
            $borrow_img = explode(',', $val['content_img']);
            if (!empty($val["sg_time"])) {
                $a = $val["sg_time"];
            } else {
                if ($val["total"] > 1) {
                    if (!empty($val["hkday"])) {
                        $yue = $val["hkday"];
                        $dt = date('Y-n-j', $val['lead_time']);
                        $a = strtotime("$dt+" . $yue * $val["total"] . "days");
                    } else {
                        $dt = date('Y-n-j', $val['lead_time']);
                        $a = strtotime("$dt+" . $val["borrow_duration"] . "month");
                    }
                } else {
                    $a = $val['lead_time'];
                }
            }
            //$val['yj_time'] =$a;
            //var_dump($val);
            $status = "";
            if ($val['borrow_status'] == 2) {
                $status = "销售中";
            }
            if ($val['borrow_status'] == 6) {
                if (!empty($val["shoujia"]) && $val["shoujia"] != 0) {
                    if ($val["xs_time"] > time()) {
                        $status = "养殖中";
                    }
                    if ($val["xs_time"] < time()) {
                        $status = "售卖中";
                    }
                } else {
                    $status = "养殖中";
                }
            }
            if ($val['borrow_status'] == 7) {
                $status = "已完成";
            }
            if ($val["pid"] == '3') {
                $borrow_img[0] = '/Public/images/hsyc.jpg';
            }
            $list[] = array(
                'bid' => $val['id'],
                'borrow_name' => $val['borrow_name'], //项目名称
                'borrow_money' => getMoneyFormt($val['borrow_money']) ,
                'borrow_times' => $val['borrow_times'],
                'borrow_duration' => $val['borrow_duration'], //项目期限
                'borrow_interest_rate' => $val['borrow_interest_rate'], //项目回收溢价
                'progress' => $val['progress'], //项目进度
                'borrow_num' => count($investinfo) ,
                'borrow_status' => $val['borrow_status'],
                'borrow_img' => $borrow_img[0] ? $borrow_img[0] : '/Public/upload/1542157585_48.jpg',
                'borrow_min' => $val["borrow_min"],
                'type_name' => $val["type_name"],
                'kg_num' => ($val['borrow_money'] - $val["has_borrow"]) / $val["borrow_min"],
                'sy_time' => ($val['start_time'] + ($val['collect_day'] * 60 * 60 * 24)) ,
                'sg_time' => $a,
                'borrow_status' => $val['borrow_status'],
                'start_time' => $val['start_time'],
                'zhuangtai' => $status,
                'pid' => $val['pid'],
            );
        }
        $jsons['data']['list'] = is_array($list) ? $list : array();
        $jsons["status"] = "1";
        $searchMaps = array();
        $searchMaps['borrow_status'] = array(
            "in",
            '6,7'
        );
        $searchMaps['id'] = array(
            'neq',
            '1'
        );
        $parms = "id DESC";
        $listBorrows = M("borrow_info")->field('id,borrow_name,borrow_img')->where($searchMaps)->order($parms)->limit('0,6')->select();
        $jsons['wqlist'] = $listBorrows; //M("borrow_info")->getLastSql();
        outJson($jsons);
    }
    //代售项目
    public function xsinvest_list() {
        $page = intval($_REQUEST["page"]);
        $size = intval($_REQUEST["size"]);
        $limit = $page * $size . ',' . $size;
        $searchMap = array();
        $searchMap['b.id'] = array(
            'neq',
            '1'
        );
        if (!empty($_REQUEST["pid"])) {
            $searchMap["b.pid"] = $_REQUEST["pid"];
        }
        //6代售中8销售中7已完成
        $borrow_status = $_REQUEST["borrow_status"];
        $searchMap["b.shoujia"] = array(
            'neq',
            0
        );
        if (!empty($borrow_status)) {
            if ($borrow_status == '6') {
                $searchMap["b.borrow_status"] = $borrow_status;
                $searchMap["b.xs_time"] = array(
                    "gt",
                    time()
                );
            }
            if ($borrow_status == '7') {
                $searchMap["b.borrow_status"] = $borrow_status;
                $searchMap["b.sg_time"] = array(
                    "lt",
                    time()
                );
            }
            if ($borrow_status == '8') {
                $searchMap["b.borrow_status"] = '6';
                $searchMap["b.xs_time"] = array(
                    "lt",
                    time()
                );
                $searchMap["b.xj_time"] = array(
                    "gt",
                    time()
                );
            }
        } else {
            $searchMap['b.borrow_status'] = array(
                "in",
                '6,7'
            );
        }
        //模糊搜索
        $name = $_REQUEST['name'];
        if (!empty($name)) {
            $searchMap['b.borrow_name'] = array(
                'like',
                '%' . $name . '%'
            );
        }
        // $searchMap['is_experience']= 0;
        //$searchMap        = array_merge($searchMap, $this->investFilterSearch($_REQUEST));
        $parm = array();
        $parm['map'] = $searchMap;
        // $parm['pagesize'] = $size;
        $parm['limit'] = $limit;
        $parm['orderby'] = "b.borrow_status ASC,b.id DESC";
        $parm['map'] = $searchMap;
        $listBorrow = getBorrowList($parm);
        //var_dump($parm);exit();
        $lists = $listBorrow['list'];
        if (count($lists) == $size) {
            $limits = ($page + 1) * $size . ',' . $size;
            $parm['limit'] = $limits;
            $listBorrow = getBorrowList($parm);
            $countlist = count($listBorrow['list']);
            if ($countlist > 0) {
                $jsons['data']["ispage"] = 1;
            } else {
                $jsons['data']["ispage"] = 0;
            }
        } else {
            $jsons['data']["ispage"] = 0;
        }
        foreach ($lists as $key => $val) {
            //var_dump($val);
            $fieldx = "bi.investor_capital,bi.add_time,m.user_name,bi.is_auto,bi.investor_interest";
            $investinfo = M("borrow_investor bi")->field($fieldx)->join("lzh_members m ON bi.investor_uid = m.id")->where("bi.borrow_id={$val['id']}")->order("bi.id DESC")->select();
            $aa = 0;
            //            foreach ($investinfo as $k => $v) {
            //                $syfs=$v['fenshu']-$v["ztfenshu"]-$v["xsfenshu"];
            //                $aa=$aa+$syfs;
            //            }
            if (!empty($val["sg_time"])) {
                $a = $val["sg_time"];
            } else {
                if ($val["total"] > 1) {
                    if (!empty($val["hkday"])) {
                        $yue = $val["hkday"];
                        $dt = date('Y-n-j', $val['lead_time']);
                        $a = strtotime("$dt+" . $yue * $val["total"] . "days");
                    } else {
                        $dt = date('Y-n-j', $val['lead_time']);
                        $a = strtotime("$dt+" . $val["borrow_duration"] . "month");
                    }
                } else {
                    $a = $val['lead_time'];
                }
            }
            $borrow_img = explode(',', $val['content_img']);
            $list[] = array(
                'bid' => $val['id'],
                'num' => $val['art_writer'],
                'borrow_name' => $val['borrow_name'], //项目名称
                'shoujia' => $val['shoujia'], //项目名称
                'borrow_money' => getMoneyFormt($val['borrow_money']) ,
                'borrow_times' => $val['borrow_times'],
                'borrow_duration' => $val['borrow_duration'], //项目期限
                'borrow_interest_rate' => $val['borrow_interest_rate'], //项目回收溢价
                'progress' => $val['progress'], //项目进度
                'borrow_num' => count($investinfo) ,
                'borrow_status' => $val['borrow_status'],
                'borrow_img' => $val['borrow_img'],
                'borrow_min' => $val["borrow_min"],
                'type_name' => $val["type_name"],
                'kg_num' => ($val['borrow_money'] - $val["has_borrow"]) / $val["borrow_min"],
                'sy_time' => ($val['start_time'] + ($val['collect_day'] * 60 * 60 * 24)) ,
                'sg_time' => $a,
                'borrow_status' => $val['borrow_status'],
                'start_time' => $val['start_time'],
            );
        }
        $jsons['data']['list'] = is_array($list) ? $list : array();
        $jsons['data']['page'] = $page;
        $jsons['data']["size"] = $size;
        $jsons["status"] = "1";
        outJson($jsons);
    }
    //项目列表
    public function invest_list() {
        $page = intval($_REQUEST["page"]);
        $size = intval($_REQUEST["size"]);
        //$size=50;
        $limit = $page * $size . ',' . $size;
        $searchMap = array();
        $searchMap['b.id'] = array(
            'neq',
            '1'
        );
        if (!empty($_REQUEST["pid"])) {
            $searchMap["b.pid"] = $_REQUEST["pid"];
        }
        $borrow_status = $_REQUEST["borrow_status"];
        if (!empty($borrow_status)) {
            if ($borrow_status == '6') {
                $searchMap["b.borrow_status"] = $borrow_status;
                $searchMap['_complex'] = array(
                    'b.shoujia' => "0",
                    "b.xs_time" => array(
                        "gt",
                        time()
                    ) ,
                    '_logic' => 'or'
                );
            }
            if ($borrow_status == '8') {
                $searchMap["b.borrow_status"] = 6;
                $searchMap["b.shoujia"] = array(
                    "neq",
                    0
                );
                $searchMap["b.xs_time"] = array(
                    "lt",
                    time()
                );
            }
            if ($borrow_status == '2') {
                $searchMap["b.borrow_status"] = 2;
            }
            if ($borrow_status == '7') {
                $searchMap["b.borrow_status"] = 7;
            }
        } else {
            $searchMap['b.borrow_status'] = array(
                "in",
                '1,2,6,7'
            );
        }
        //exit;
        //模糊搜索
        $name = $_REQUEST['name'];
        if (!empty($name)) {
            $searchMap['b.borrow_name'] = array(
                'like',
                '%' . $name . '%'
            );
        }
        // $searchMap['is_experience']= 0;
        // $searchMap        = array_merge($searchMap, $this->investFilterSearch($_REQUEST));
        $parm = array();
        $parm['map'] = $searchMap;
        // $parm['pagesize'] = $size;
        $parm['limit'] = $limit;
        $parm['orderby'] = "b.borrow_status ASC,b.id DESC";
        $parm['map'] = $searchMap;
        $listBorrow = getBorrowList($parm);
        //var_dump($parm);exit();
        $lists = $listBorrow['list'];
        if (count($lists) == $size) {
            $limits = ($page + 1) * $size . ',' . $size;
            $parm['limit'] = $limits;
            $listBorrow = getBorrowList($parm);
            $countlist = count($listBorrow['list']);
            if ($countlist > 0) {
                $jsons['data']["ispage"] = 1;
            } else {
                $jsons['data']["ispage"] = 0;
            }
        } else {
            $jsons['data']["ispage"] = 0;
        }
        $ntime = time();
        foreach ($lists as $key => $val) {
            //var_dump($val);
            $fieldx = "bi.investor_capital,bi.add_time,m.user_name,bi.is_auto,bi.investor_interest";
            $investinfo = M("borrow_investor bi")->field($fieldx)->join("lzh_members m ON bi.investor_uid = m.id")->where("bi.borrow_id={$val['id']}")->order("bi.id DESC")->select();
            $status = "";
            if ($val['borrow_status'] == 2) {
                $status = "销售中";
            }
            if ($val['borrow_status'] == 6) {
                if (!empty($val["shoujia"]) && $val['shoujia'] != 0) {
                    if ($val["xs_time"] > $ntime) {
                        $status = "养殖中";
                    }
                    if ($val["xs_time"] < $ntime && $val["xj_time"] > $ntime) {
                        $status = "售卖中";
                    }
                    if ($val["xj_time"] < $ntime) {
                        $status = "已下架";
                    }
                } else {
                    $status = "养殖中";
                }
            }
            if ($val['borrow_status'] == 7) {
                $status = "已完成";
            }
            if ($status == '养殖中' && $val["pid"] == '3') {
                $status = "建造中";
            }
            /* <?php $borrow_img=explode(',',$vb['content_img']); ?>



                <img class="xm_l_img" src="{$borrow_img[0]|default='/Public/upload/1542157585_48.jpg'}"/>*/
            $borrow_img = explode(',', $val['content_img']);
            $list[] = array(
                'bid' => $val['id'],
                'borrow_name' => $val['borrow_name'], //项目名称
                'borrow_money' => getMoneyFormt($val['borrow_money']) ,
                'borrow_times' => $val['borrow_times'],
                'borrow_duration' => $val['borrow_duration'], //项目期限
                'borrow_interest_rate' => $val['borrow_interest_rate'], //项目回收溢价
                'progress' => $val['progress'], //项目进度
                'borrow_num' => count($investinfo) ,
                'borrow_status' => $val['borrow_status'],
                'borrow_img' => $borrow_img[0] ? $borrow_img[0] : '/Public/upload/1542157585_48.jpg',
                'borrow_min' => $val["borrow_min"],
                'type_name' => $val["type_name"],
                'kg_num' => ($val['borrow_money'] - $val["has_borrow"]) / $val["borrow_min"],
                //'sy_time'=>getLeftTime( $val['start_time'] + ($val['collect_day']*60*60*24),2),
                'sy_time' => $val['start_time'] + ($val['collect_day'] * 60 * 60 * 24) ,
                'sg_time' => $a,
                'borrow_status' => $val['borrow_status'],
                'start_time' => $val['start_time'],
                'zhuangtai' => $status,
            );
        }
        $jsons['data']['list'] = is_array($list) ? $list : array();
        $jsons['data']['page'] = $page;
        $jsons['data']["size"] = $size;
        $jsons["status"] = "1";
        outJson($jsons);
    }
    public function invest_infos() {
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 1;
        $pre = C('DB_PREFIX');
        $field = "i.*,b.borrow_name,b.sg_time,b.borrow_duration,b.borrow_min,b.total,i.investor_capital,b.hkday,b.borrow_img,b.lead_time,mb.money_bonus,i.fenshu";
        $map["i.id"] = $_REQUEST["id"];
        $map["i.investor_uid"] = $this->uid;
        $borrowinfo = M("borrow_investor i")->field($field)->where($map)->join("lzh_borrow_info b on i.borrow_id=b.id")->join("lzh_member_bonus mb on mb.id=i.bonus_id")->find();
        //var_dump(M("borrow_investor i")->getlastsql());
        if (empty($borrowinfo["fenshu"])) {
            $borrowinfo["fenshu"] = $borrowinfo["investor_capital"] / $borrowinfo["borrow_min"];
        }
        if ($borrowinfo["total"] > 1) {
            if (!empty($borrowinfo["hkday"])) {
                $yue = $borrowinfo["hkday"];
                $dt = date('Y-n-j', $borrowinfo['lead_time']);
                $a = strtotime("$dt+" . $yue * ($borrowinfo["total"] - 1) . "days");
            } else {
                $dt = date('Y-n-j', $borrowinfo['lead_time']);
                $a = strtotime("$dt+" . ($borrowinfo["borrow_duration"] - 1) . "month");
            }
        } else {
            $a = $borrowinfo['lead_time'];
        }
        $borrowinfo['yj_time'] = $a;
        $jsons["data"] = $borrowinfo;
        $jsons["status"] = "1";
        outJson($jsons);
    }
    //项目详情
    public function invest_info() {
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 1;
        $pre = C('DB_PREFIX');
        $Bconfig = C("BORROW");
        $this->uid = $_REQUEST["uid"];
        if ($this->uid) {
            $new_user_time = time() - $this->glo['new_user_time'] * 24 * 60 * 60;
            $where = null;
            $where['investor_uid'] = array(
                'eq',
                "{$this->uid}"
            );
            if ($this->glo['new_user_time'] > 0) {
                $where['add_time'] = array(
                    array(
                        'egt',
                        "{$new_user_time}"
                    ) ,
                    array(
                        'elt',
                        time()
                    )
                );
            }
            $where['borrow_id'] = array(
                'neq',
                1
            );
            $newUser = count(M('borrow_investor')->where($where)->find());
            $where = null;
            $where['bespeak_uid'] = array(
                'eq',
                "{$this->uid}"
            );
            if ($this->glo['new_user_time'] > 0) {
                $where['add_time'] = array(
                    array(
                        'egt',
                        "{$new_user_time}"
                    ) ,
                    array(
                        'elt',
                        time()
                    )
                );
            }
            $where['borrow_id'] = array(
                'neq',
                1
            );
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
        $borrowinfo["hetong_content"] = M("article")->where("title='" . $borrowinfo["templateid"] . "'")->getField("art_content");
        $borrowinfo["fenshu"] = ($borrowinfo["borrow_money"] - $borrowinfo["has_borrow"]) / $borrowinfo["borrow_min"];
        // echo M("borrow_info")->getlastsql();exit;
        $shortcut_c = explode(",", $borrowinfo['shortcut']);
        $this->assign("shortcut_c", $shortcut_c);
        //点击
        $ip = get_client_ip();
        $now_time = strtotime(date('Y-m-d'));
        $hit_array = M("pro_hits")->field(true)->where('add_ip="' . $ip . '" and bid=' . $id . " and add_time>" . $now_time)->find();
        if (!$hit_array) {
            $save['bid'] = $id;
            $save['add_time'] = time();
            $save['add_ip'] = $ip;
            M("pro_hits")->data($save)->add();
            M("borrow_info")->where('id=' . $id)->setField('hits', $borrowinfo["hits"] + 1);
        }
        if (bcsub($borrowinfo['borrow_money'], $borrowinfo['has_borrow'], 2) == 0 and $borrowinfo["borrow_status"] == 2) {
            //echo bcsub($borrowinfo['borrow_money'],$borrowinfo['has_borrow'],2);
            borrowfull($id, $borrowinfo["borrow_type"]);
        }
        //      var_dump(is_array($borrowinfo),$borrowinfo['borrow_status']);
        //      die;
        if ($id != 1) {
            if (!is_array($borrowinfo) || ($borrowinfo['borrow_status'] == 0 && $this->uid != $borrowinfo['borrow_uid'])) {
                $jsons['msg'] = '数据有误！';
                $jsons['status'] = '0';
            }
        }
        $borrowinfo['biao'] = $borrowinfo['borrow_times'];
        $borrowinfo['need'] = bcsub($borrowinfo['borrow_money'], $borrowinfo['has_borrow'], 2);
        $status = "";
        if ($borrowinfo['borrow_status'] == 2) {
            $status = "销售中";
            $statu = 1;
        }
        $ntime = time();
        if ($borrowinfo['borrow_status'] == 6) {
            $status = "养殖中";
            $statu = 2;
            if (!empty($borrowinfo["shoujia"]) && $borrowinfo['shoujia'] != 0) {
                if ($borrowinfo["xs_time"] > $ntime) {
                    $status = "养殖中";
                }
                if ($borrowinfo["xs_time"] < $ntime) {
                    $status = "售卖中";
                    $statu = 3;
                }
            } else {
                $status = "养殖中";
            }
            if ($borrowinfo['pid'] == '3') {
                $status = "建造中";
            }
        }
        if ($borrowinfo['borrow_status'] == 7) {
            $status = "已完成";
            $statu = 4;
        }
        $borrowinfo['zhuangtai'] = $status;
        $borrowinfo['zhuangtaiwz'] = $statu;
        $borrowinfo['leftdays'] = getLeftTime($borrowinfo['collect_time'], 2);
        $borrowinfo['lefttime'] = $borrowinfo['start_time'] - time();
        if ($borrowinfo["borrow_status"] == 1) {
            $borrowinfo['endtimes'] = $borrowinfo['start_time'];
        } else {
            $borrowinfo['endtimes'] = $borrowinfo['start_time'] + ($borrowinfo['collect_day'] * 60 * 60 * 24);
        }
        if ($borrowinfo["total"] > 1) {
            if (!empty($borrowinfo["hkday"])) {
                $yue = $borrowinfo["hkday"];
                $dt = date('Y-n-j', $borrowinfo['lead_time']);
                $a = strtotime("$dt+" . $yue * ($borrowinfo["total"] - 1) . "days");
            } else {
                $dt = date('Y-n-j', $borrowinfo['lead_time']);
                $a = strtotime("$dt+" . ($borrowinfo["borrow_duration"] - 1) . "month");
            }
        } else {
            $a = $borrowinfo['lead_time'];
        }
        $borrowinfo['yj_time'] = $a;
        $ms = "预计销售价";
        $xszt = "1";
        if (empty($borrowinfo["shoujia"]) || $borrowinfo["shoujia"] == 0) {
            $shoujia = 0;
            if (floatval($borrowinfo["borrow_duration"]) > 12) {
                $shoujia = $borrowinfo["borrow_min"] * (100 + floatval($borrowinfo['borrow_interest_rate']) * floatval($borrowinfo["borrow_duration"]) / 12) / 100;
            } else {
                $shoujia = $borrowinfo["borrow_min"] * (100 + floatval($borrowinfo['borrow_interest_rate'])) / 100;
            }
            $borrowinfo["shoujia"] = $shoujia;
            $ms = "预计利润";
            $xszt = "0";
        }
        $borrowinfo['xszt'] = $xszt;
        $borrowinfo['ms'] = $ms;
        $borrowinfo['progress'] = getFloatValue($borrowinfo['has_borrow'] / $borrowinfo['borrow_money'] * 100, 0);
        if ($borrowinfo['bespeak_able'] == 1) {
            $borrowinfo['bespeak_progress'] = getFloatValue($borrowinfo['bespeak_money'] / $borrowinfo['borrow_money'] * 100, 0);
        }
        if (substr($borrowinfo['progress'], -1) == ".") $borrowinfo['progress'] = substr($borrowinfo['progress'], 0, strlen($borrowinfo['progress']) - 1);
        $borrowinfo['vouch_progress'] = getFloatValue($borrowinfo['has_vouch'] / $borrowinfo['borrow_money'] * 100, 2);
        $borrowinfo['vouch_need'] = $borrowinfo['borrow_money'] - $borrowinfo['has_vouch'];
        $borrowinfo["borrow_img"] = str_replace("'", "", $borrowinfo["borrow_img"]);
        if ($borrowinfo['borrow_img'] == "") {
            $borrowinfo['borrow_img'] = "UF/Uploads/borrowimg/nopic.png";
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
        $borrowinfo['vote0'] = M('borrow_vote')->where('status = 0 and borrow_id = ' . $id)->count('id');
        $borrowinfo['vote1'] = M('borrow_vote')->where('status = 1 and borrow_id = ' . $id)->count('id');
        //$this->assign("binfo", $borrowinfo);
        //此标借款利息还款相关情况
        //memberinfo
        $memberinfo = M("members m")->field("m.id,m.customer_name,m.customer_id,m.pin_pass,m.user_name,m.reg_time,m.credits,fi.*,mi.*")->join("{$pre}member_financial_info fi ON fi.uid = m.id")->join("{$pre}member_info mi ON mi.uid = m.id")->where("m.id={$borrowinfo['borrow_uid']}")->find();
        $areaList = getArea();
        $memberinfo['location'] = $areaList[$memberinfo['province']] . $areaList[$memberinfo['city']];
        $memberinfo['location_now'] = $areaList[$memberinfo['province_now']] . $areaList[$memberinfo['city_now']];
        $this->assign("minfo", $memberinfo);
        $data_list = M("member_data_info")->field('type,add_time,data_url,count(status) as num,sum(deal_credits) as credits')->where("uid={$borrowinfo['borrow_uid']} AND status=1")->group('id')->select();
        $this->assign("data_list", $data_list);
        //红包信息
        $bonus_list = M('member_bonus')->where("uid='{$this->uid}' and status = 1 and (" . time() . " > start_time and " . time() . " < end_time )")->field('id,money_bonus,end_time,bonus_invest_min')->select();
        $this->assign("bonus_list", $bonus_list);
        //加息券信息
        $inrate_list = M('member_interest_rate')->where("uid='{$this->uid}' and status = 1 and (" . time() . " > start_time and " . time() . " < end_time )")->field('id,interest_rate,end_time')->select();
        $this->assign("inrate_list", $inrate_list);
        $vm = getMinfo($this->uid, 'm.pin_pass,mm.account_money,mm.back_money,mm.money_experience');
        $amoney = $vm['account_money'] + $vm['back_money'];
        $pin_pass = $vm['pin_pass'];
        $has_pin = (empty($pin_pass)) ? "no" : "yes";
        $this->assign("has_pin", $has_pin);
        $this->assign("account_money", $amoney);
        $this->assign("money_experience", $vm['money_experience']);
        if ($borrowinfo['need'] <= $vm['account_money']) {
            $allinvest = $borrowinfo['need'];
        } else {
            $allinvest = $vm['account_money'];
        }
        $this->assign("allinvest", $allinvest);
        $vo = M('borrow_info')->find($id);
        //$this->assign("vo",$vo);
        //合同模板
        $article_category = M('article')->where('type_id = 525 AND title  LIKE "' . $vo['templateid'] . '"')->find();
        $this->assign("article_category", $article_category);
        $dynamiclist = m("dynamic")->where("tid={$id}")->order("id desc")->select();
        // $this->assign('dynamiclist', $dynamiclist);
        // $fieldx     = "bi.investor_capital,bi.add_time,m.user_name,bi.investor_uid,bi.is_auto,bi.investor_interest,bi.investor_way,bi.member_interest_rate_id,bi.bonus_id,bi.is_experience";
        // $investinfo = M("borrow_investor bi")->field($fieldx)->join("{$pre}members m ON bi.investor_uid = m.id")->where("bi.borrow_id={$id}")->order("bi.id DESC")->group("bi.investor_uid")->select();
        // echo M("borrow_investor bi")->getlastsql();exit;
        $fieldx = "bi.investor_capital,bi.add_time,m.user_name,bi.is_auto,bi.investor_interest,bi.investor_way,bi.member_interest_rate_id,bi.bonus_id,bi.is_experience,mi.user_img";
        $investinfo = M("borrow_investor bi")->field($fieldx)->join("{$pre}members m ON bi.investor_uid = m.id")->join("{$pre}member_info mi ON bi.investor_uid = mi.uid")->where("bi.borrow_id={$id}")->order("bi.id DESC")->select();
        $aa = $bb = 0;
        foreach ($investinfo as $k => $v) {
            $syfs = $v['fenshu'] - $v["ztfenshu"] - $v["xsfenshu"];
            $aa = $aa + $syfs;
            $bb = $bb + $v["xsfenshu"];
        }
        //$this->assign("investinfo", $investinfo);
        $borrowinfo["kucun"] = $aa;
        $borrowinfo["yishou"] = $bb;
        $glo = array(
            'web_title' => $borrowinfo['borrow_name'] . ' - 我要投资'
        );
        $this->assign($glo);
        if ($this->uid) {
        }
        if (!$this->uid) {
            $this->assign('uid', 0);
        } else {
            $this->assign('uid', $this->uid);
        }
        $borrowinfo["borrow_info"] = $this->pipei($vo["borrow_info"], $this->domainUrl);
        $jsons['msg'] = '成功';
        $jsons['data']["borrow"] = $borrowinfo;
        $jsons['data']["article_category"] = $article_category;
        $jsons['data']["investinfo"] = $investinfo;
        $jsons['data']["dynamiclist"] = $dynamiclist;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    //智投项目
    public function zhitou_invest() {
        $bespeak = M("bespeak i");
        //分页处理
        $uid = intval($_REQUEST["uid"]);
        $page = intval($_REQUEST["page"]);
        $size = intval($_REQUEST["size"]);
        $limit = $page * $size . ',' . $size;
        $order = "id desc";
        $map['bespeak_uid'] = $uid;
        $map['bespeak_point'] = '1.00';
        $field = "i.*,b.borrow_name,b.borrow_img,b.borrow_status";
        $list = $bespeak->where($map)->field($field)->join("lzh_borrow_info b ON b.id=i.borrow_id")->order('i.add_time DESC')->limit($limit)->select();
        //var_dump($bespeak->getlastsql());exit();
        $arr = array(
            "智投中",
            "已成功",
            "智投失败"
        );
        foreach ($list as $key => $v) {
            $list[$key]["status"] = $arr[$v["bespeak_status"]];
        }
        if (count($list) == $size) {
            $limits = ($page + 1) * $size . ',' . $size;
            $countlist = $bespeak->where($map)->limit($limits)->order($order)->count();
            if ($countlist > 0) {
                $jsons['data']["ispage"] = 1;
            } else {
                $jsons['data']["ispage"] = 0;
            }
        } else {
            $jsons['data']["ispage"] = 0;
        }
        $jsons['data']['size'] = $size;
        $jsons['data']['page'] = $page;
        $jsons['data']["list"] = $list;
        $jsons['status'] = '1';
        // var_dump($list);
        outJson($jsons);
    }
    public function kztlist() {
        $this->uid = $uid = intval($_REQUEST["uid"]);
        $half_a_year_before = time() - $this->glo["new_user_time"] * 24 * 60 * 60;
        $where = null;
        $where['investor_uid'] = array(
            'eq',
            "{$this->uid}"
        );
        $where['add_time'] = array(
            array(
                'egt',
                "{$half_a_year_before}"
            ) ,
            array(
                'elt',
                time()
            )
        );
        $where['borrow_id'] = array(
            'neq',
            1
        );
        $newUser = count(M('borrow_investor')->where($where)->find());
        $where = null;
        $where['bespeak_uid'] = array(
            'eq',
            "{$this->uid}"
        );
        $where['add_time'] = array(
            array(
                'egt',
                "{$half_a_year_before}"
            ) ,
            array(
                'elt',
                time()
            )
        );
        $where['borrow_id'] = array(
            'neq',
            1
        );
        $bespeakUser = count(M('bespeak')->where($where)->find());
        $newUser = max($newUser, $bespeakUser);
        if ($newUser > 0) { //老用户
            $map["new_user_only"] = array(
                "neq",
                "1"
            );
            $mapc["zhitou_able"] = 0;
        } else { //新用户
            $mapc["zhitou_able"] = array(
                "neq",
                "1"
            );
        }
        $mapc["borrow_status"] = 1;
        $mapc["start_time"] = array(
            "gt",
            time()
        );
        $mapc["borrow_uid"] = array(
            "neq",
            $this->uid
        );
        $mapc['id'] = array(
            'neq',
            1
        );
        $zdlist = M('borrow_info')->where($mapc)->select();
        $dzdlist = array();
        foreach ($zdlist as $k => $v) {
            //预约已投金额
            $bespeak_invest_money = M('bespeak')->where("borrow_id={$v['id']} and bespeak_status = 1")->sum('bespeak_money');
            //非预约已投金额
            $bespeak_not_invest_money = bcsub($v["has_borrow"], $bespeak_invest_money, 2);
            //非预约金额
            $not_invest_money = bcsub($v["borrow_money"], $v["bespeak_money"], 2);
            //可投金额
            $can_invest_money = bcsub($not_invest_money, $bespeak_not_invest_money, 2);
            $dzdlist[$k]["shengyu"] = $can_invest_money;
            $dzdlist[$k]["id"] = $v["id"];
            $dzdlist[$k]["borrow_name"] = $v["borrow_name"];
            $dzdlist[$k]["borrow_min"] = $v["borrow_min"];
        }
        if (count($dzdlist) > 0) {
            $jsons['list'] = $dzdlist;
            $jsons['status'] = '1';
        } else {
            $jsons['list'] = $dzdlist;
            $jsons['status'] = '0';
            $jsons['msg'] = '没有可以智投的项目';
        }
        outJson($jsons);
    }
    public function dozhitou() {
        $this->uid = $uid = intval($_REQUEST["uid"]);
        $pre = C('DB_PREFIX');
        if (!$this->uid) {
            $jsons['status'] = '0';
            $jsons['msg'] = '用户信息不存在！';
            outJson($jsons);
        }
        $pin = md5($_POST['pin']);
        $borrow_id = (int)($_POST['borrow_id']);
        $money = (float)($_POST['money']);
        $is_experience = isset($_POST['is_experience']) && 1 == $_POST['is_experience'] ? 1 : 0;
        $member_interest_rate_id = 0;
        $bonus_id = isset($_POST['bonus_id']) ? (int)($_POST['bonus_id']) : 0;
        if ($bonus_id > 0) {
            $bs = M('member_bonus')->where("id='{$bonus_id}'")->find();
            $canInvestMoneys = canInvestMoney($this->uid, $borrow_id, $money + $bs['money_bonus'], 0, $is_experience, '0', $bonus_id, text($_POST['borrow_pass']));
            if (0 == $canInvestMoneys['status']) {
                ajaxmsg($canInvestMoneys['tips'], $canInvestMoneys['status']);
            }
            $money_bonuss = $canInvestMoneys['money_bonus'];
            $money = (float)($money + $money_bonuss);
        }
        $memberinterest_id = isset($_POST['memberinterest_id']) ? (int)($_POST['memberinterest_id']) : 0;
        $vm = getMinfo($this->uid, 'm.pin_pass,mm.account_money,mm.back_money,mm.money_experience,mm.yubi');
        if ($vm['pin_pass'] == '') {
            $jsons['status'] = '0';
            $jsons['msg'] = '支付密码为空，请您设置密码！';
            outJson($jsons);
        }
        $pin_pass = $vm['pin_pass'];
        if ($pin != $pin_pass) {
            $jsons['status'] = '0';
            $jsons['msg'] = '支付密码错误，请重试！';
            outJson($jsons);
        }
        $amoney = $vm['account_money'] + $vm['back_money'] + $vm['yubi'];
        $uname = session('u_user_name');
        $amoney = (float)$amoney;
        $binfo = M('borrow_info')->field('id,borrow_money,borrow_status,has_borrow,has_vouch,borrow_min,borrow_type,password,pause,max_limit,bespeak_money,start_time')->find($borrow_id);
        $binfo['borrow_max'] = $binfo['borrow_min'] * $binfo['max_limit'];
        $minfo = getMinfo($this->uid, true);
        $levename = getLeveId($minfo['credits']);
        //项目开标后不能自动投
        if ($binfo['start_time'] < time()) {
            $jsons['status'] = '0';
            $jsons['msg'] = '此标已经开始众筹，请到项目列表里面投资！';
            outJson($jsons);
        }
        $ids = M('members_status')->getFieldByUid($this->uid, 'id_status');
        if ($ids != 1) {
            $jsons['status'] = '0';
            $jsons['msg'] = '请先通过实名认证后再进行投标。';
            outJson($jsons);
        }
        $phones = M('members_status')->getFieldByUid($this->uid, 'phone_status');
        if ($phones != 1) {
            $jsons['status'] = '0';
            $jsons['msg'] = '请先通过手机认证后再进行投标。';
            outJson($jsons);
        }
        if ($binfo['pause'] == 1) {
            $jsons['status'] = '0';
            $jsons['msg'] = '此标当前已经截标，请等待管理员开启。';
            outJson($jsons);
        }
        // 50 > 10
        if ($money < $binfo['borrow_min']) {
            $jsons['status'] = '0';
            $jsons['msg'] = '此标最小投标金额为' . $binfo['borrow_min'] . '元';
            outJson($jsons);
        }
        if ($money > $binfo['borrow_max'] and 0 != $binfo['borrow_max']) {
            $jsons['status'] = '0';
            $jsons['msg'] = "此标最大投标金额为" . $binfo['borrow_max'] . "元";
            outJson($jsons);
        }
        if ($binfo['has_vouch'] < $binfo['borrow_money'] && 2 == $binfo['borrow_type']) {
            $jsons['status'] = '0';
            $jsons['msg'] = '此标担保还未完成，您可以担保此标或者等担保完成再投标';
            outJson($jsons);
        }
        $half_a_year_before = time() - $this->glo["new_user_time"] * 24 * 60 * 60;
        $where = null;
        $where['investor_uid'] = array(
            'eq',
            "{$this->uid}"
        );
        $where['add_time'] = array(
            array(
                'egt',
                "{$half_a_year_before}"
            ) ,
            array(
                'elt',
                time()
            )
        );
        $where['borrow_id'] = array(
            'neq',
            1
        );
        $newUser = count(M('borrow_investor')->where($where)->find());
        $where = null;
        $where['bespeak_uid'] = array(
            'eq',
            "{$this->uid}"
        );
        $where['add_time'] = array(
            array(
                'egt',
                "{$half_a_year_before}"
            ) ,
            array(
                'elt',
                time()
            )
        );
        $where['borrow_id'] = array(
            'neq',
            1
        );
        $bespeakUser = count(M('bespeak')->where($where)->find());
        $newUser = max($newUser, $bespeakUser);
        $new_user_only = M('borrow_info')->field('new_user_only')->find($borrow_id) ['new_user_only'];
        if ($new_user_only == 1 && $newUser > 0) {
            $jsons['status'] = '0';
            $jsons['msg'] = '此标是新手专享标';
            outJson($jsons);
        }
        //投标总数检测
        $bespeak = M("bespeak");
        $capital = $bespeak->where("borrow_id={$borrow_id} AND bespeak_uid={$this->uid}")->sum('bespeak_money');
        if (($capital + $money) > $binfo['borrow_max'] && $binfo['borrow_max'] > 0) {
            $xtee = $binfo['borrow_max'] - $capital;
            $jsons['status'] = '0';
            $jsons['msg'] = "您已预约或自动投总金额{$capital}元，上限为{$binfo['borrow_max']}元，你最多只能再投{$xtee}元";
            outJson($jsons);
        }
        //预约已投金额
        $bespeak_invest_money = M('bespeak')->where("borrow_id={$binfo['id']} and bespeak_status = 1")->sum('bespeak_money');
        //非预约已投金额
        $bespeak_not_invest_money = bcsub($binfo["has_borrow"], $bespeak_invest_money, 2);
        //非预约金额
        $not_invest_money = bcsub($binfo["borrow_money"], $binfo["bespeak_money"], 2);
        //可投金额
        $need = bcsub($not_invest_money, $bespeak_not_invest_money, 2);
        $caninvest = bcsub($need, $binfo['borrow_min'], 2);
        if ($money > $caninvest && 0 != ($need - $money)) {
            if (0 == (int)$need or '0.00' == $need) {
                $jsons['status'] = '0';
                $jsons['msg'] = "尊敬的{$uname}，该项目自投投资已完成，请选择其他项目！";
                outJson($jsons);
            }
        }
        if ($money > $need) {
            $money = $need;
        }
        $canInvestMoney = canInvestMoney($this->uid, $borrow_id, $money, 0, $is_experience, '0', $bonus_id, text($_POST['borrow_pass']));
        if (0 == $canInvestMoney['status']) {
            ajaxmsg($canInvestMoney['tips'], $canInvestMoney['tips_type']);
            exit();
        }
        $money_bonus = $canInvestMoney['money_bonus'];
        $yubis = 0;
        if (2 == $canInvestMoney['money_type']) {
            if ($memberinterest_id > 0) {
                $msg = "尊敬的{$uname}，您的体验金账户余额为{$vm['money_experience']}元，您确认投标{$money}元吗？";
            } else {
                $msg = "尊敬的{$uname}，您的体验金账户余额为{$vm['money_experience']}元，您确认投标{$money}元吗？";
            }
            $jsons['status'] = '0';
            $jsons['msg'] = $msg;
            outJson($jsons);
        } elseif (3 == $canInvestMoney['money_type']) {
            if ($memberinterest_id > 0) {
                $msg = "尊敬的{$uname}，您确认投标{$money}元（使用{$money_bonus}元红包），投资吗？";
            } else {
                $msg = "尊敬的{$uname}，您确认投标{$money}元（使用{$money_bonus}元红包），投资吗？";
            }
            $jsons['status'] = '0';
            $jsons['msg'] = $msg;
            outJson($jsons);
        } elseif ($money <= $amoney) {
            $data["borrow_id"] = $borrow_id;
            $data["bespeak_uid"] = $this->uid;
            $data["bespeak_money"] = $money;
            $data["add_time"] = time();
            $data["bespeak_status"] = 0;
            $data["bespeak_point"] = 1;
            if ($vm["yubi"] >= $money) {
                $yubis = $money;
            } else {
                $yubis = $vm["yubi"];
            }
            $data["yubi"] = $yubis;
            $borrowInfo = M("borrow_info");
            $borrowInfo->startTrans();
            $borrow_info = M("borrow_info");
            $bmap["id"] = $borrow_id;
            $bwinfo = $borrow_info->where($bmap)->find();
            //自动投记录
            $res = $bespeak->add($data);
            if ($bwinfo && $res) {
                $bespeak_money = $bwinfo["bespeak_money"];
                if ($bespeak_money == null) {
                    $bespeak_money = 0;
                }
                $bespeak_money = ($bespeak_money + $money);
                $bdata["bespeak_money"] = $bespeak_money;
                //修改标可投金额
                $res1 = $borrow_info->where($bmap)->save($bdata);
                if ($res1) {
                    if (($minfo["account_money"] + $minfo['yubi']) >= $money) {
                        //writeLog($investinfo);
                        $res2 = memberMoneyLog($this->uid, 222, 0 - $money, "对（{$bwinfo["borrow_name"]}）进行自动投标", $bwinfo['borrow_uid'], "", false, $yubis);
                        if ($res2 === true && $res1) {
                            $borrowInfo->commit();
                            $msg = "尊敬的{$uname}，您预约的自动投{$money}元，已经成功！";
                            $jsons['status'] = '1';
                            $jsons['msg'] = $msg;
                            outJson($jsons);
                        } else {
                            $borrowInfo->rollback();
                            $msg = "尊敬的{$uname}，您预约的自动投{$money}元，未成功！";
                            $jsons['status'] = '0';
                            $jsons['msg'] = $msg;
                            outJson($jsons);
                        }
                    } else {
                        $msg = "您的可用余额不足！";
                        $jsons['status'] = '0';
                        $jsons['msg'] = $msg;
                        outJson($jsons);
                    }
                }
            } else {
                $msg = "尊敬的{$uname}，您预约的自动投{$money}元，未成功！";
                $jsons['status'] = '0';
                $jsons['msg'] = $msg;
                outJson($jsons);
            }
        } else {
            $jsons['status'] = '0';
            $jsons['msg'] = $msg;
            outJson($jsons);
        }
    }
    //认证信息
    public function renzheng() {
        $uid = intval($_REQUEST["uid"]);
        $info = M('member_info')->where("uid=" . $uid)->find();
        if ($info) {
            $jsons['msg'] = '获取成功！';
            $jsons['data']["cell_phone"] = $info["cell_phone"];
            $jsons['data']["sex"] = $info["sex"];
            $jsons['data']["real_name"] = $info["real_name"];
            $jsons['data']["up_time"] = $info["up_time"];
            $jsons['data']["nick_name"] = $info["nick_name"];
            $jsons['data']["user_img"] = $info["user_img"];
            $jsons['data']["idcard"] = $info["idcard"];
            $jsons['status'] = '1';
        } else {
            $jsons['msg'] = '用户信息不存在！';
            $jsons['data'] = $info;
            $jsons['status'] = '0';
        }
        outJson($jsons);
    }
    //活动中心
    public function activity() {
        //分页处理
        $page = intval($_REQUEST["page"]);
        $size = intval($_REQUEST["size"]);
        $type_id = intval($_REQUEST["type_id"]);
        $limit = $page * $size . ',' . $size;
        $order = "art_time desc";
        $map['type_id'] = $type_id;
        $map['is_fabu'] = '1';
        $list = M("article")->where($map)->order($order)->limit($limit)->select();
        if (count($list) == $size) {
            $limits = ($page + 1) * $size . ',' . $size;
            $countlist = M("article")->where($map)->limit($limits)->order($order)->count();
            if ($countlist > 0) {
                $jsons['data']["ispage"] = 1;
            } else {
                $jsons['data']["ispage"] = 0;
            }
        } else {
            $jsons['data']["ispage"] = 0;
        }
        $jsons['data']['size'] = $size;
        $jsons['data']['page'] = $page;
        $jsons['data']["list"] = $list;
        $jsons['status'] = '1';;
        outJson($jsons);
    }
    //直播中心
    public function zhibo() {
        $jsons['msg'] = '成功';
        $jsons['data'] = [];
        $jsons['status'] = '1';
        outJson($jsons);
    }
    //收货地址
    public function address_list() {
        $uid = intval($_REQUEST["uid"]);
        $list = M('member_address')->where("uid=" . $uid)->select();
        if ($list) {
            $jsons['msg'] = '获取成功！';
            $jsons['status'] = '1';
        } else {
            $jsons['msg'] = '收货地址不存在！';
            $jsons['status'] = '0';
        }
        $jsons['data'] = $list;
        outJson($jsons);
    }
    //添加修改地址
    public function doaddress() {
        $uid = intval($_REQUEST["uid"]);
        if ($_REQUEST['default'] == 1) {
            $va = M("member_address")->where("uid={$uid}")->save(["default" => 0]);
        }
        $_REQUEST['uid'] = $uid;
        if ($_REQUEST["id"]) {
            if (M("member_address")->where("id=" . $_REQUEST['id'] . " and uid=" . $uid)->find()) {
                $va = M("member_address")->where("id=" . $_REQUEST["id"])->save($_REQUEST);
            } else {
                $jsons['msg'] = '信息错误！';
                $jsons['status'] = '0';
                outJson($jsons);
            }
        } else {
            $va = M("member_address")->add($_REQUEST);
        }
        if ($va) {
            $jsons['msg'] = '设置成功！';
            $jsons['status'] = '1';
        } else {
            $jsons['msg'] = '设置失败！';
            $jsons['status'] = '0';
        }
        outJson($jsons);
    }
    //删除地址
    public function deladdress() {
        $uid = intval($_REQUEST["uid"]);
        $va = M("member_address")->where("id=" . $_REQUEST['id'] . " and uid=" . $uid)->delete();
        if ($va) {
            $jsons['msg'] = '删除成功！';
            $jsons['status'] = '1';
        } else {
            $jsons['msg'] = '删除失败！';
            $jsons['status'] = '0';
        }
        outJson($jsons);
    }
    //获取广告位
    public function getad() {
        $id = $_REQUEST["id"];
        $list = array();
        $condition['start_time'] = array(
            "lt",
            time()
        );
        $condition['end_time'] = array(
            "gt",
            time()
        );
        $condition['id'] = array(
            "eq",
            $id
        );
        $_list = m("ad")->field("ad_type,content")->where($condition)->find();
        if ($_list['ad_type'] == 1) {
            $_list['content'] = unserialize($_list['content']);
        }
        $list = $_list;
        if ($list['ad_type'] == 0 || !$list['content']) {
            if (!$list['content']) {
                $adimg = "广告未上传或已过期";
                $jsons['msg'] = $adimg;
                $jsons['status'] = '0';
                outJson($jsons);
            }
            $adimg['content'] = $list['content'];
            $adimg['ad_type'] = $list['ad_type'];
        } else {
            $adimg['content'] = $list['content'];
            $adimg['ad_type'] = $list['ad_type'];
        }
        $jsons['msg'] = "成功";
        $jsons['data'] = $adimg;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    //获取手机项目
    public function getads() {
        $id = 52;
        $list = array();
        $condition['start_time'] = array(
            "lt",
            time()
        );
        $condition['end_time'] = array(
            "gt",
            time()
        );
        $condition['id'] = array(
            "eq",
            $id
        );
        $_list = m("ad")->field("ad_type,content")->where($condition)->find();
        if ($_list['ad_type'] == 1) {
            $_list['content'] = unserialize($_list['content']);
        }
        foreach ($_list['content'] as $k => $v) {
            $_list['content'][$k]["picurl"] = "https://" . $_SERVER['SERVER_NAME'] . '/' . $v["img"];
            //$_list['content'][$k]["picurl"]='/'.$v["img"];
            $_list['content'][$k]["linkurl"] = $v["url"];
        }
        $list = $_list;
        if ($list['ad_type'] == 0 || !$list['content']) {
            if (!$list['content']) {
                $adimg = "广告未上传或已过期";
                $jsons['msg'] = $adimg;
                $jsons['status'] = '0';
                outJson($jsons);
            }
            $adimg['content'] = $list['content'];
            $adimg['ad_type'] = $list['ad_type'];
        } else {
            $adimg['content'] = $list['content'];
            $adimg['ad_type'] = $list['ad_type'];
        }
        $jsons['msg'] = "成功";
        $jsons['data'] = $adimg;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    public function getfenlei() {
        $tfield = "id,type_name";
        $tylist = M("article_category")->field($tfield)->where("parent_id=513")->select();
        foreach ($tylist as $k => $v) {
            $tylist[$k]["zlist"] = M("article_category")->field($tfield)->where("parent_id=" . $v["id"])->select();
        }
        $jsons['data'] = $tylist;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    public function shoplist() {
        $page = intval($_REQUEST["page"]) == '' ? 0 : (intval($_REQUEST["page"]) - 1);
        $size = intval($_REQUEST["size"]) == '' ? 10 : intval($_REQUEST["size"]);
        $type_id = intval($_REQUEST["type_id"]);
        $limit = $page * $size . ',' . $size;
        $order = "art_time DESC";
        $map['art_set'] = 0;
        if ($_REQUEST["type_id"] != 0) {
            $map["type_id"] = $_REQUEST["type_id"];
        }
        //模糊查询商品名称
        $name = text($_REQUEST["name"]);
        if (!empty($name)) {
            $map["title"] = array(
                'like',
                '%' . $name . '%'
            );
        }
        $list = M("market")->where($map)->order($order)->limit($limit)->select();
        if (count($list) == $size) {
            $limits = ($page + 1) * $size . ',' . $size;
            $countlist = M("market")->where($map)->limit($limits)->order($order)->count();
            if ($countlist > 0) {
                $jsons['data']["ispage"] = 1;
            } else {
                $jsons['data']["ispage"] = 0;
            }
        } else {
            $jsons['data']["ispage"] = 0;
        }
        $jsons['data']['size'] = $size;
        $jsons['data']['page'] = $page;
        $jsons['data']["list"] = $list;
        $jsons['status'] = '1';;
        outJson($jsons);
    }
    public function getfenleis() {
        $tfield = "id,type_name,type_content";
        $tylist = M("article_category")->field($tfield)->where("parent_id=513")->select();
        foreach ($tylist as $k => $v) {
            $tpp = $v['id'];
            $tp = M("article_category")->field('id')->where("parent_id=" . $v["id"])->select();
            foreach ($tp as $ke => $va) {
                $tpp = $tpp . ',' . $va["id"];
            }
            $plist = M("market")->where(array(
                "type_id" => array(
                    'in',
                    $tpp
                )
            ))->limit('0,4')->select();
            $tylist[$k]["plist"] = $plist;
        }
        $jsons['data'] = $tylist;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    public function shoplists() {
        $page = intval($_REQUEST["page"]) == '' ? 0 : (intval($_REQUEST["page"]) - 1);
        $size = intval($_REQUEST["size"]) == '' ? 10 : intval($_REQUEST["size"]);
        $type_id = intval($_REQUEST["type_id"]);
        $aa = 0;
        if ($_REQUEST["type_id"] != 0) {
            $tpp = $_REQUEST["type_id"];
            $tp = M("article_category")->field('id')->where("parent_id=" . $tpp)->select();
            foreach ($tp as $ke => $va) {
                $tpp = $tpp . ',' . $va["id"];
            }
            $map["type_id"] = array(
                "in",
                $tpp
            );
            $aa = 4;
        }
        $limit = ($page * $size + $aa) . ',' . $size;
        $order = "art_time DESC";
        $map['art_set'] = 0;
        //模糊查询商品名称
        $name = text($_REQUEST["name"]);
        if (!empty($name)) {
            $map["title"] = array(
                'like',
                '%' . $name . '%'
            );
        }
        $list = M("market")->where($map)->order($order)->limit($limit)->select();
        if (count($list) == $size) {
            $limits = (($page + 1) * $size + $aa) . ',' . $size;
            $countlist = M("market")->where($map)->limit($limits)->order($order)->count();
            if ($countlist > 0) {
                $jsons['data']["ispage"] = 1;
            } else {
                $jsons['data']["ispage"] = 0;
            }
        } else {
            $jsons['data']["ispage"] = 0;
        }
        $jsons['data']['size'] = $size;
        $jsons['data']['page'] = $page;
        $jsons['data']["list"] = $list;
        $jsons['status'] = '1';;
        outJson($jsons);
    }
    public function shopinfo() {
        $id = intval($_REQUEST['id']);
        $vo = M('market')->find($id);
        //http://ccxx.com//wap/api/shopinfo/id/46
        $listType = D('Acategory')->getField('id,type_name');
        $vo["type_name"] = $listType[$vo['type_id']];
        $vo["buy"] = M("order")->where("gid=" . $id)->count("id");
        $vo["buy"] = $vo["buy"] == "" ? 0 : $vo["buy"];
        // $buylist= M('order o')->field("o.*,m.user_name")->join("lzh_members m on m.id=o.uid")->where("o.gid=".$id)->order("o.id desc")->select();
        // $this->assign("buylist",$buylist);
        //$this->assign("vo",$vo);
        $vo["art_content"] = $this->pipei($vo["art_content"], $this->domainUrl);
        $jsons['data'] = $vo;
        $jsons['status'] = '1';;
        outJson($jsons);
    }
    public function shop_is_order() {
        $this->uid = $_REQUEST["uid"];
        if (!isset($_REQUEST['addid']) || empty($_REQUEST['addid'])) {
            $va = M("member_address as a")->where("a.uid={$this->uid} and a.default=1")->find();
        } else {
            $va = M("member_address as a")->where("a.id={$_REQUEST['addid']}")->find();
        }
        $jsons['address'] = $va;
        //      var_dump($va);
        //      die;
        $id = intval($_REQUEST['id']);
        $vo = M('market')->find($id); //积分商品信息
        $vminfo = getMinfo($this->uid, true);
        $model = M('member_info');
        $vou = $model->find($this->uid);
        if (!is_array($vou)) $model->add(array(
            'uid' => $this->uid
        ));
        else $this->assign('vou', $vou);
        $memberinfo = M('members')->find($this->uid);
        //$this->assign("memberinfo",$memberinfo);
        $num = $_REQUEST['num'];
        $jifen = $vo['art_jifen'] * $num;
        $yue = 0;
        if ($jifen > $memberinfo["credits"]) {
            $yue = ($jifen - intval($memberinfo["credits"])) / $this->glo["market_bl"];
            $jsons['dhjifen'] = $memberinfo["credits"];
        } else {
            $jsons['dhjifen'] = $jifen;
        }
        //$this->assign("yue",round($yue,2));
        $jsons['jifen'] = $jifen;
        $jsons['kyjifen'] = intval($memberinfo["credits"]);
        $jsons['market_bl'] = $this->glo["market_bl"];
        $jsons['yue'] = round($yue, 2);
        $jsons['num'] = $num;
        $jsons['vo'] = $vo;
        $jsons['status'] = '1';;
        outJson($jsons);
    }
    // public function post_order(){
    //     $savedata = textPost($_REQUEST);
    //     $uid=$this->uid=$_REQUEST["uid"];
    //     $vminfo =getMinfo($this->uid,true);
    //     //$carid=explode(',', $savedata["carid"]);
    //     $savedata['uid'] = $this->uid;
    //     $savedata['jine'] = $savedata["jine"]; //$jine;
    //     $savedata['jifen'] = $savedata["jifen"]; //$jifen;
    //     $savedata['pay_way'] = $savedata["pay_way"];
    //     $savedata['add_time'] = time();
    //     $savedata['add_ip'] = get_client_ip();
    //     $savedata['ordernums']=sprintf('%s-%s-%s', 'SDD', $uid,time());
    //     if(empty($savedata["address"])){
    //         $jsons['msg'] ="地址信息未填写！";
    //         $jsons['status'] = '0';
    //         outJson($jsons);
    //     }
    //     $newid = M('order')->add($savedata);
    //     if(!$newid>0){
    //         $jsons['msg'] ="提交失败，请确认填入数据正确！";
    //         $jsons['status'] = '0';
    //         outJson($jsons);
    //     }
    //     if($savedata["jifen"]>0){
    //         memberCreditsLog($this->uid,8,-intval($jifen),"购买商品减去积分".$jifen);
    //     }
    //     //减少库存
    //     $vo["art_writer"] -= $savedata["num"];
    //     $Market = M("market");
    //     $Market->where("id={$vo['id']}")->save($vo);
    //     $cardata["status"]=2;
    //     M("car")->where("id in (".$savedata["carid"].")")->save($cardata);
    //     $jsons['msg'] ="订单生成成功！";
    //     $jsons['newid'] =$newid;
    //     $jsons['ordernums']=$savedata['ordernums'];
    //     $jsons['status'] = '1';
    //     outJson($jsons);
    // }
    public function post_order() {
        $savedata = textPost($_REQUEST);
        $uid = $this->uid = $_REQUEST["uid"];
        $vminfo = getMinfo($this->uid, true);
        //$carid=explode(',', $savedata["carid"]);
        //var_dump($vminfo);
        if ($_REQUEST["jine"] == 0) {
            $pin_pass = $vminfo["pin_pass"];
            $pin = md5($_POST['pin_pass']);
            if (empty($pin_pass)) {
                $jsons['msg'] = "支付密码为空，请您设置密码";
                $jsons['status'] = '0';
                outJson($jsons);
            } else if ($pin <> $pin_pass) {
                $jsons['msg'] = "支付密码错误，请重试";
                $jsons['status'] = '0';
                outJson($jsons);
            }
        }
        $savedata['uid'] = $this->uid;
        $savedata['jine'] = $savedata["jine"]; //$jine;
        $jifen = $savedata['jifen'] = $savedata["jifen"]; //$jifen;
        $mklist = M("car c")->field("c.num as cnum,m.id as mid,m.art_writer,m.art_jiage,m.isyhq,c.gid,c.uid")->join("lzh_market m ON m.id=c.gid")->where("c.id in (" . $savedata["carid"] . ")")->select();
        if ($savedata["jine"] == 0) {
            // $mklist= M("car c")->field("c.num as cnum,m.id as mid,m.art_writer,m.art_jiage,m.isyhq,c.gid,c.uid")->join("lzh_market m ON m.id=c.gid")->where("c.id in (".$savedata["carid"].")")->select();
            foreach ($mklist as $ke => $va) {
                $alljifens+= $va["art_jiage"] * $va["cnum"];
            }
            $alljifens = $alljifens * 2000; //   writeLog($alljifens);
            if ($alljifens != $savedata['jifen']) {
                // $cardata["status"]=2;
                // M("car")->where("id in (".$savedata["carid"].")")->save($cardata);
                $jsons['msg'] = "请先选择数量，再选择积分！";
                $jsons['status'] = '0';
                outJson($jsons);
            }
            $savedata['action'] = 0;
            $savedata['pay_time'] = time();
            $alljifen = 1;
            $savedata['pay_way'] = 3;
        } else {
            $alljifen = 0;
            $savedata['pay_way'] = $savedata["pay_way"];
        }
        $savedata['add_time'] = time();
        $savedata['add_ip'] = get_client_ip();
        $savedata['ordernums'] = sprintf('%s-%s-%s', 'SDD', $uid, time());
        if (empty($savedata["address"])) {
            $jsons['msg'] = "地址信息未填写！";
            $jsons['status'] = '0';
            outJson($jsons);
        }
        $carinfo = M("car")->where("id in (" . $savedata["carid"] . ") and status='1'")->select();
        if (!$carinfo) {
            $jsons['msg'] = "购物车为空！";
            $jsons['status'] = '0';
            outJson($jsons);
        }
        $rjifen = true;
        if ($savedata["jifen"] > 0) {
            $rjifen = memberCreditsLog($this->uid, 8, -intval($jifen) , "购买商品减去积分" . $jifen);
        }
        if ($rjifen) {
            $newid = M('order')->add($savedata);
            if (!$newid > 0) {
                $jsons['msg'] = "提交失败，请确认填入数据正确！";
                $jsons['status'] = '0';
                outJson($jsons);
            }
            //减少库存
            $cardata["status"] = 2;
            M("car")->where("id in (" . $savedata["carid"] . ")")->save($cardata);
            //$mklist= M("car c")->field("c.num as cnum,m.id as mid,m.art_writer,m.isyhq,c.gid,c.uid")->join("lzh_market m ON m.id=c.gid")->where("c.id in (".$savedata["carid"].")")->select();
            //var_dump($mklist);
            $Market = M("market");
            foreach ($mklist as $k => $v) {
                $dat["art_writer"] = $v["art_writer"] - $v["cnum"];
                $Market->where("id=" . $v['mid'])->save($dat);
                $mkinfo = M('market')->where("id=" . $v["gid"])->find();
                //  var_dump($mkinfo);exit();
                if ($mkinfo["isyhq"] == 1 && $savedata['pay_way'] == 3) {
                    //$addsign['money_type']=4;
                    for ($i = 0; $i < $v['cnum']; $i++) {
                        $pubData = array(
                            'uid' => $v["uid"],
                            'add_time' => strtotime(date('Y-m-d 00:00:00')) ,
                            'money_bonus' => $mkinfo["jine"],
                            'type' => 4,
                            'start_time' => strtotime(date('Y-m-d 00:00:00')) ,
                            'end_time' => strtotime(date("Y-m-d 23:59:59", strtotime("+" . $mkinfo["youxiaoqi"] . " months", strtotime(date("Y-m-d"))))) ,
                            'bonus_invest_min' => $mkinfo["bonus_invest_min"],
                        );
                        $rs = M('member_bonus')->add($pubData);
                    }
                    M('order')->where("id=" . $newid)->save(array(
                        "action" => 1
                    ));
                }
            }
            $jsons['msg'] = "订单生成成功！";
            $jsons['newid'] = $newid;
            $jsons['ordernums'] = $savedata['ordernums'];
            $jsons['status'] = '1';
            $jsons['alljifen'] = $alljifen;
            outJson($jsons);
        } else {
            $jsons['msg'] = "抵现积分不足！";
            $jsons['status'] = '0';
            outJson($jsons);
        }
    }
    public function post_order111() {
        $savedata = textPost($_POST);
        $this->uid = $_REQUEST["uid"];
        $vminfo = getMinfo($this->uid, true);
        if ($vminfo['pin_pass'] != md5($savedata["zhifu"])) {
            $jsons['msg'] = "支付密码错误！";
            $jsons['status'] = '0';
            outJson($jsons);
        }
        // $vo = M('market')->where("id=".$savedata["gid"])->find();  //积分商品信息
        // //var_dump($vo);exit();
        // if($vo["art_writer"] <= 0){
        //     $jsons['msg'] ="商品库存不足，请选择其他商品！";
        //     $jsons['status'] = '0';
        //     outJson($jsons);
        // }
        $carid = explode(',', $savedata["carid"]);
        $jine = 0;
        foreach ($carid as $k => $v) {
            $cars = M("car")->where('id=' . $v)->find();
            $mkinfo = M('market')->where("id=" . $cars["gid"])->find();
            $jine+= intval($cars["num"]) * intval($mkinfo["art_jifen"]);
        }
        //$jine=$savedata["num"]*$vo["art_jiage"];
        $jifen = intval($savedata["num"]) * intval($vo["art_jifen"]);
        //if(($vminfo["account_money"]+$vminfo["back_money"])<$jine) $this->error("您的余额不足，请选择其他商品或充值！",__APP__."/maket/detail?id=".$savedata["gid"]);
        if ($vminfo["credits"] < $jifen) {
            $jine = round(($jifen - $vminfo["credits"]) / $this->glo["market_bl"], 2);
            $jifen = $vminfo["credits"];
            if (($vminfo["account_money"] + $vminfo["back_money"]) < $jine) {
                $jsons['msg'] = "您的余额不足，请选择其他商品或充值！";
                $jsons['status'] = '0';
                outJson($jsons);
            }
        }
        $savedata['uid'] = $this->uid;
        $savedata['jine'] = $jine;
        $savedata['jifen'] = $jifen;
        $savedata['add_time'] = time();
        $savedata['add_ip'] = get_client_ip();
        if (empty($savedata["address"])) {
            $jsons['msg'] = "地址信息未填写！";
            $jsons['status'] = '0';
            outJson($jsons);
        }
        $newid = M('order')->add($savedata);
        if (!$newid > 0) {
            $jsons['msg'] = "提交失败，请确认填入数据正确！";
            $jsons['status'] = '0';
            outJson($jsons);
        }
        if ($jine > 0) {
            memberMoneyLog($this->uid, 37, -($jine) , "购买商品减去金额" . $jine, '0', ''); //  商品购买  人民币消费记录

        }
        if ($jifen > 0) {
            memberCreditsLog($this->uid, 8, -intval($jifen) , "购买商品减去积分" . $jifen);
        }
        //减少库存
        $vo["art_writer"]-= $savedata["num"];
        $Market = M("market");
        $Market->where("id={$vo['id']}")->save($vo);
        $jsons['msg'] = "兑换成功！";
        $jsons['status'] = '1';
        outJson($jsons);
    }
    //查看订单
    public function infoorder() {
        $this->uid = $_REQUEST["uid"];
        $map['b.uid'] = $this->uid;
        $map['b.id'] = $_REQUEST['id'];
        $field = 'm.id mid,m.user_name,x.*,b.*';
        $list = M('order b')->field($field)->join("lzh_members m ON m.id=b.uid")->join("lzh_market x ON x.id=b.gid")->where($map)->find();
        $jsons['data'] = $list;
        $jsons['status'] = '1';;
        outJson($jsons);
    }
    public function tuikuan() {
        $ordernums = $_REQUEST["ordernums"];
        $orderinfo = M("order")->where("ordernums='" . $ordernums . "'")->find();
        if ($orderinfo['type'] == "2") {
            $mks = M('borrow_info')->where("id=" . $orderinfo["gid"])->find();
            if ($mks["borrow_status"] == 7) {
                $jsons['msg'] = "项目已完结不可以退款！";
                $jsons['status'] = '0';
                outJson($jsons);
            }
        }
        //var_dump($orderinfo);
        $status = array(
            0,
            1,
            2
        );
        if (!in_array($orderinfo["action"], $status)) {
            $jsons['msg'] = "订单状态错误！";
            $jsons['status'] = '0';
            outJson($jsons);
        } //var_dump($orderinfo);
        if ($_REQUEST["money"] > $orderinfo["jine"]) {
            $jsons['msg'] = "退款金额不能大于支付金额！";
            $jsons['status'] = '0';
            outJson($jsons);
        }
        $data["ordernums"] = $ordernums;
        $data["money"] = $_REQUEST["money"];
        $data["images"] = $_REQUEST["images"];
        $data["uid"] = $this->uid;
        $data["shuoming"] = $_REQUEST["shuoming"];
        $data["yuanyin"] = $_REQUEST["yuanyin"];
        $data["payway"] = $orderinfo["pay_way"];
        $data["jifen"] = $orderinfo["jifen"];
        $data["fangshi"] = $_REQUEST["fangshi"];
        $data["add_time"] = time();
        if ($orderinfo['action'] == 0) {
            $data["status"] = 4;
            $data["tk_time"] = time();
        }
        M()->startTrans();
        $res = M("order_refund")->add($data);
        $odata["action"] = 5;
        if ($orderinfo['action'] == 0) {
            $odata["action"] = 4;
            $odata["wc_time"] = time();
            if ($orderinfo['type'] == "1") {
                $glist = M("car")->where("id in ('" . $orderinfo['carid'] . "')")->select();
                $mk = true;
                foreach ($glist as $k => $v) {
                    $mk = M('market')->where("id=" . $v["gid"])->setInc('art_writer', $v['num']);
                }
            }
            if ($orderinfo['type'] == "2") {
                if (!empty($orderinfo["investor_id"])) {
                    $num = $orderinfo["num"];
                    //$bi_info = M("borrow_investor")->field('fenshu,ztfenshu,xsfenshu')->where("id=" . $orderinfo["investor_id"])->find();
                    // $fenshu = $bi_info["fenshu"] - $bi_info["ztfenshu"] - $bi_info['xsfenshu'];
                    $mk = M("borrow_investor")->where("id=" . $orderinfo["investor_id"])->setDec('xsfenshu', $num);
                } else {
                    $mk = M('borrow_info')->where("id=" . $orderinfo["gid"])->setInc('art_writer', $orderinfo['num']);
                }
            }
        }
        $res1 = M("order")->where("ordernums='" . $ordernums . "'")->save($odata);
        if ($orderinfo['action'] == 0) {
            if ($data["money"] != 0) {
                $rdata = tuikuanapi($orderinfo["pay_way"], $ordernums, $data["money"]);
                if ($rdata['status'] == 0) {
                    M()->rollback();
                    $jsons['msg'] = $rdata['msg'];
                    $jsons['status'] = '0';
                    outJson($jsons);
                }
            }
        }
        if ($res && $res1) {
            M()->commit();
            if (!empty($orderinfo["jifen"])) {
                membercreditslog($orderinfo['uid'], 20, $orderinfo["jifen"], "退货退款积分退回！");
            }
            $jsons['msg'] = "退款申请提交成功！";
            $jsons['status'] = '1';
            outJson($jsons);
        } else {
            M()->rollback();
            $jsons['msg'] = "退款申请提交失败！";
            $jsons['status'] = '0';
            outJson($jsons);
        }
    }
    public function dolist($db, $map, $order, $page = 0, $size = 10) {
        $limit = $page * $size . ',' . $size;
        $list = $db->where($map)->order($order)->limit($limit)->select();
        //var_dump($db->getlastsql());exit();
        if (count($list) == $size) {
            $limits = ($page + 1) * $size . ',' . $size;
            $countlist = $db->where($map)->limit($limits)->order($order)->count();
            if ($countlist > 0) {
                $jsons['data']["ispage"] = 1;
            } else {
                $jsons['data']["ispage"] = 0;
            }
        } else {
            $jsons['data']["ispage"] = 0;
        }
        $jsons['data']['size'] = $size;
        $jsons['data']['page'] = $page;
        $jsons['data']["list"] = $list;
        $jsons['status'] = '1';;
        outJson($jsons);
    }
    public function getbonus() {
        $this->uid = $_REQUEST["uid"];
        $bonus_list = M('member_bonus')->where("uid='{$this->uid}' and status = 1 and (" . time() . " > start_time and " . time() . " < end_time )")->field('id,money_bonus,end_time,bonus_invest_min')->select();
        $jsons["list"] = $bonus_list;
        $jsons['status'] = '1';;
        outJson($jsons);
    }
    //发起项目
    public function fqsave() {
        $this->uid = $_POST["uid"];
        $_POST["borrow_type"] = 1;
        $_POST["repayment_type"] = 1;
        $_POST["borrow_model"] = 1;
        // redirect(__APP__."/member/common/login/");
        $pre = C('DB_PREFIX');
        //相关的判断参数
        $rate_lixt = explode("|", $this->glo['rate_lixi']);
        $borrow_duration = explode("|", $this->glo['borrow_duration']);
        $borrow_duration_day = explode("|", $this->glo['borrow_duration_day']);
        $fee_borrow_manage = explode("|", $this->glo['fee_borrow_manage']);
        $vminfo = M('members m')->join("{$pre}member_info mf ON m.id=mf.uid")->field("m.user_leve,m.time_limit,mf.province_now,mf.city_now,mf.area_now")->where("m.id={$this->uid}")->find();
        //相关的判断参数
        $borrow['borrow_type'] = $_POST['borrow_type'];
        //  if($borrow['borrow_type']==0) $this->error("校验数据有误，请重新发布");
        if (floatval($_POST['borrow_interest_rate']) > $rate_lixt[1] || floatval($_POST['borrow_interest_rate']) < $rate_lixt[0]) {
            $jsons["msg"] = "提交的借款利率有误，请重试！";
            $jsons['status'] = '0';;
            outJson($jsons);
            //$this->error("提交的借款利率有误，请重试",0);

        }
        $borrow['borrow_money'] = intval($_POST['borrow_money']);
        $borrow['borrow_cat'] = intval($_POST['borrow_cat']);
        $borrow['borrow_model'] = intval($_POST['borrow_model']);
        $_minfo = getMinfo($this->uid, true);
        $_capitalinfo = getMemberBorrowScan($this->uid);
        ///////////////////////////////////////////////////////
        $borrowNum = M('borrow_info')->field("borrow_type,count(id) as num,sum(borrow_money) as money,sum(repayment_money) as repayment_money")->where("borrow_uid = {$this->uid} AND borrow_status=6 ")->group("borrow_type")->select();
        $borrowDe = array();
        foreach ($borrowNum as $k => $v) {
            $borrowDe[$v['borrow_type']] = $v['money'] - $v['repayment_money'];
        }
        ///////////////////////////////////////////////////
        //echo $borrow['borrow_type'];
        //exit;
        $borrow['borrow_uid'] = $this->uid;
        $borrow['borrow_name'] = text($_POST['borrow_name']);
        $borrow['borrow_duration'] = 1; //秒标固定为一月
        $borrow['shouyilv'] = text($_POST['shouyilv']);
        $borrow['hkday'] = intval($_POST['hkday']);
        $borrow['borrow_interest_rate'] = floatval($_POST['borrow_interest_rate']);
        if (strtolower($_POST['is_day']) == 'yes') $borrow['repayment_type'] = 1;
        elseif ($borrow['borrow_type'] == 3) $borrow['repayment_type'] = 2; //秒标按月还
        else $borrow['repayment_type'] = intval($_POST['repayment_type']);
        if ($_POST['show_tbzj'] == 1) $borrow['is_show_invest'] = 1; //共几期(分几次还)
        $borrow['total'] = ($borrow['repayment_type'] == 1) ? 1 : $borrow['borrow_duration']; //共几期(分几次还)
        if ($borrow['repayment_type'] == 5) {
            $borrow['total'] = 1;
        }
        //echo $borrow['total'];
        //exit;
        $borrow['borrow_video'] = $_POST["borrow_video"];
        $borrow['pid'] = $_POST["pid"];
        $borrow['new_user_only'] = $_POST["new_user_only"];
        $borrow['bespeak_able'] = $_POST["bespeak_able"];
        $borrow['bespeak_days'] = $this->glo["bespeak_days"];
        $borrow['borrow_status'] = 0;
        $borrow['borrow_duration'] = intval($_POST['borrow_duration']);
        $borrow['total'] = intval($_POST['total']);
        $borrow['borrow_use'] = intval($_POST['borrow_use']);
        $borrow['add_time'] = time();
        $borrow['collect_day'] = intval($_POST['borrow_time']);
        $borrow['add_ip'] = get_client_ip();
        $borrow['borrow_info'] = stripslashes(htmlspecialchars_decode($_POST['borrow_info']));
        $borrow['borrow_con'] = stripslashes(htmlspecialchars_decode($_POST['borrow_con']));
        $borrow['reward_type'] = intval($_POST['reward_type']);
        $borrow['reward_num'] = floatval($_POST["reward_type_{$borrow['reward_type']}_value"]);
        $borrow['borrow_min'] = 50;
        $borrow['borrow_max'] = intval($_POST['borrow_max']);
        $borrow['province'] = "22";
        $borrow['city'] = $_POST["city"];
        $borrow['area'] = "";
        $oldtime = $_POST['lead_time'];
        $borrow['lead_time'] = strtotime($oldtime);
        if ($_POST['is_pass'] && intval($_POST['is_pass']) == 1) $borrow['password'] = $_POST['password'];
        //  $borrow['repayment_type']=$borrow['repayment_type']==5?4:$borrow['repayment_type'];
        //借款费和利息
        $borrow['borrow_interest'] = getBorrowInterest($borrow['repayment_type'], $borrow['borrow_money'], $borrow['borrow_duration'], $borrow['borrow_interest_rate']);
        if ($borrow['repayment_type'] == 1) { //按天还
            $fee_rate = (is_numeric($fee_borrow_manage[0])) ? ($fee_borrow_manage[0] / 1000) : 0;
            $borrow['borrow_fee'] = getFloatValue($fee_rate * $borrow['borrow_money'] * $borrow['collect_day'], 2);
        } else {
            $fee_rate_1 = (is_numeric($fee_borrow_manage[1])) ? ($fee_borrow_manage[1] / 1000) : 0;
            $borrow['borrow_fee'] = getFloatValue($borrow['borrow_money'] * $fee_rate_1 * $borrow['collect_day'], 2);
        }
        if ($borrow['repayment_type'] == 5) {
            $fee_rate = (is_numeric($fee_borrow_manage[0])) ? ($fee_borrow_manage[0] / 100) : 0.001;
            $borrow['borrow_fee'] = getFloatValue($fee_rate * $borrow['borrow_money'] * $borrow['collect_day'], 2);
        }
        if ($borrow['borrow_type'] == 3) { //秒还标
            if ($borrow['reward_type'] > 0) {
                if ($borrow['reward_type'] == 1) $_reward_money = getFloatValue($borrow['borrow_money'] * $borrow['reward_num'] / 100, 2);
                elseif ($borrow['reward_type'] == 2) $_reward_money = getFloatValue($borrow['reward_num'], 2);
            }
            $_reward_money = floatval($_reward_money);
            $__reward_money = 0;
            $borrow['borrow_fee'] = 0;
            if (($_minfo['account_money']) < ($borrow['borrow_fee'] + $_reward_money)) {
                $jsons["msg"] = "发布此标您最少需保证您的帐户余额大于等于" . ($borrow['borrow_fee'] + $_reward_money) . "元，以确保可以支付投标奖励费用！";
                $jsons['status'] = '0';;
                outJson($jsons);
            }
            //$this->error("发布此标您最少需保证您的帐户余额大于等于".($borrow['borrow_fee']+$_reward_money)."元，以确保可以支付投标奖励费用");

        }
        $borrow['borrow_img'] = $_POST['borrow_img'];
        //投标上传图片资料（暂隐）
        foreach ($_POST['swfimglist'] as $key => $v) {
            if ($key > 10) break;

            $row[$key]['img'] = substr($v, 1);
            $row[$key]['info'] = $_POST['picinfo'][$key];
        }
        $borrow['updata'] = serialize($row);
        $borrow['auto_info'] = $this->glo['ttxf_auto_all'];
        $borrow['p_auto_info'] = $this->glo['ttxf_auto_p'];
        if ($borrow['borrow_name'] == "") {
            $smasvae = "请输入项目标题";
        }
        if ($borrow['borrow_money'] == "") {
            $smasvae = "请输入您需要募集的金额";
        }
        if ($borrow['borrow_money'] < 1000) {
            $smasvae = "筹款金额不能小于1000";
        }
        if ($borrow['collect_day'] == "") {
            $smasvae = "请设置项目需要募集的时间";
        }
        if ($borrow['borrow_model'] == "") {
            $smasvae = "请填写项目类型";
        }
        if ($borrow['pid'] == "") {
            $smasvae = "请选择所属行业";
        } //var_dump($_POST);die;
        if ($borrow['borrow_img'] == "") {
            $smasvae = "请上传项目封面";
        }
        if ($borrow['borrow_info'] == "") {
            $smasvae = "请填写项目介绍";
        }
        if (isset($smasvae) && !empty($smasvae)) {
            $jsons["msg"] = $smasvae;
            $jsons['status'] = '0';;
            outJson($jsons);
        } else {
            //  var_dump($borrow);
            // die;
            //直接传递拼接字符串
            //$borrow['content_img']=implode(",",$_POST['content_img']);
            $borrow['content_img'] = $_POST['content_img'];
            // var_dump($borrow);die;
            $newid = M("borrow_info")->add($borrow);
            //   echo M("borrow_info")->getlastsql();
            // die;
            if ($newid) {
                $jsons["newid"] = $newid;
                $jsons['status'] = '1';;
                outJson($jsons);
            } else {
                $jsons["msg"] = "发布失败";
                $jsons['status'] = '0';;
                outJson($jsons);
            }
        }
    }
    public function fqsave1() {
        // var_dump($_POST);die;
        //$_POST['return_info']="<pre>".$_POST['return_info']."</pre>";
        $newid = M("borrow_info")->save($_POST);
        if ($newid) {
            $jsons["msg"] = "项目发布成功，网站会尽快初审";
            $jsons['status'] = '1';;
            outJson($jsons);
        } else {
            $jsons["msg"] = "发布失败";
            $jsons['status'] = '0';;
            outJson($jsons);
        }
    }
    public function qianming() {
        $url = $_REQUEST["url"];
        Vendor('JSSDK');
        $jssdk = new JSSDK("wx0a01a5ed7857bad7", "4d5be00eaa31ca639f5078b04f20a177");
        //$jssdk = new JSSDK("wx275dca4b41d86791","2f46140df07853ac09098638e435b0cc");
        $signPackage = $jssdk->GetSignPackage1($url);
        $jsons["date"] = $signPackage;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    public function pipei($str, $url) {
        $str = str_replace("src=\"/", "src=\"$url/", $str);
        $str = str_replace("src='/", "src='$url/", $str);
        return $str;
    }
    //回款日历
    public function hkrl() {
        // recoverBonus($this->uid);
        // $ucLoing = de_xie($_COOKIE['LoginCookie']);
        // setcookie('LoginCookie','',time()-10*60,"/");
        $this->uid = $_REQUEST["uid"];
        $uid = $this->uid;
        $minfo = getMinfo($uid, true);
        $all_money = getFloatvalue($minfo['account_money'] + $minfo['money_collect'] + $minfo['money_freeze'], 2);
        // 累计收益
        $minfo['receive_interests'] = M('borrow_investor')->where('investor_uid = ' . $uid . ' and status = 5')->sum('receive_interest');
        $pre = C('DB_PREFIX');
        //$this->assign("uid",$uid);
        // 资料完善度
        $mVerify = m("members m")->field("m.credits,m.id,m.user_leve,m.time_limit,s.id_status,s.phone_status,s.email_status,s.video_status,s.face_status")->join("{$pre}members_status s ON s.uid=m.id")->where("m.id={$uid} ")->find();
        $map['uid'] = $this->uid;
        $start = strtotime(date("Y-m-1 0:0:0", time()));
        $end = strtotime(date("Y-m-t 23:59:59", time()));
        //$va2=strtotime(date("Y-m-1 0:0:0",time()));
        //var_dump(date("Y-m-1 0:0:0",$va));
        $ids = $this->getid($uid); //var_dump($ids);
        $idsd = $ids['a'][date("Y-n", time()) ];
        //var_dump($ids['b']);
        $fqhk = $ids['b'][date("Y-n", time()) ]['bx'];
        $fqhkd = $ids['b'][date("Y-n", time()) ]['lx'];
        //$fqhkdl=$ids['b'][date("Y-n",time())]['al'];
        $yhbj["bi.id"] = ["in", $fqhk];
        $yhbj["bi.investor_uid"] = $uid;
        $yhbj["b.borrow_status"] = 7;
        $yhbj["bi.status"] = ["neq", 3];
        $tpl_var['yf_benjin'] = M('borrow_investor bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where($yhbj)->sum('investor_capital');
        $dyhbj["bi.id"] = ["in", $fqhk];
        $dyhbj["bi.investor_uid"] = $uid;
        $dyhbj["b.borrow_status"] = 6;
        $dyhbj["bi.status"] = ["neq", 3];
        $tpl_var['df_benjin'] = M('borrow_investor bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where($dyhbj)->sum('investor_capital');
        $ldyhbj["id"] = ["in", $fqhkd];
        // $ldyhbj["investor_uid"]=$uid;
        $ldyhbj["status"] = 1;
        //已返利润
        $tpl_var['shouyi'] = M('investor_detail')->where($ldyhbj)->sum('receive_interest'); //+$bxlx;
        $tpl_var['chajia'] = M('investor_detail')->where($ldyhbj)->sum('receive_chajia'); //+$bxlx;
        //投资项目
        $mapx['investor_uid'] = $this->uid;
        $mapx['i.status'] = ["neq", 3];
        //var_dump($mapx);exit();
        $mapx['i.id'] = ["in", $idsd];
        //." and i.status!=3"
        $field = "b.xsinvestor_capital,b.hkday,b.total,b.borrow_duration,b.borrow_name,b.borrow_interest_rate,b.full_time,b.borrow_status as bstatus,b.id,i.investor_capital,i.add_time,i.status,i.borrow_id,i.deadline,i.id as inid,i.investor_way,i.member_interest_rate_id,i.bonus_id,b.borrow_img,i.is_experience,i.contractId,i.is_sign,i.step,b.lead_time";
        $tenlist = M('borrow_investor i')->field($field)->join('lzh_borrow_info b on b.id = i.borrow_id')->where($mapx)->order('b.id desc')->limit($Lsql)->select();
        //var_dump(count( $tenlist), M('borrow_investor i')->getlastsql());die;
        $hkid = $ids['b'][date("Y-n", time()) ]['hk'];
        foreach ($tenlist as $k => $v) {
            $sort_order = 1;
            if ($v["total"] > 1) {
                if (!empty($v["hkday"])) {
                    $yue = $v["hkday"];
                    $dt = date('Y-n-j', $v['lead_time']);
                    for ($i = 0; $i < $v["total"]; $i++) {
                        if (date('Y-n', strtotime("$dt+" . $yue * $i . " days")) == date("Y-n", time())) {
                            $a = strtotime("$dt+" . $yue * $i . "days");
                            $sort_order = $i + 1;
                        }
                    }
                } else {
                    $yue = $v["borrow_duration"] / $v["total"];
                    $dt = date('Y-n-j', $v['lead_time']);
                    for ($i = 0; $i < $v["total"]; $i++) {
                        if (date('Y-n', strtotime("$dt+" . $yue * $i . "month")) == date("Y-n", time())) {
                            $a = strtotime("$dt+" . $yue * $i . "month");
                            $sort_order = $i + 1;
                        }
                    }
                }
            } else {
                $a = $v['lead_time'];
            }
            $mpp["borrow_id"] = $v["id"];
            $mpp["status"] = 1;
            $mpp["sort_order"] = $sort_order;
            $mpp["investor_uid"] = $this->uid;
            $minfo = M("investor_detail")->where($mpp)->find();
            if ($minfo) {
                $tenlist[$k]["hkzt"] = 1;
                $tenlist[$k]["shouyi"] = $minfo["receive_interest"] + $minfo["receive_capital"] + $minfo["receive_chajia"];
            } else {
                $tenlist[$k]["hkzt"] = 0;
                //$tenlist[$k]["hklx"]="未回款";

            }
            if ($sort_order == $v["total"]) {
                $tenlist[$k]["hklx"] = "本金+收益";
            } else {
                $tenlist[$k]["hklx"] = "第" . $sort_order . "次收益";
            }
            $tenlist[$k]["lead_time_c"] = date("Y-m-d", $a);
            if ($sort_order == $v["total"]) {
                $tenlist[$k]["bj"] = 1;
            } else {
                $tenlist[$k]["bj"] = 0;
            }
        }
        //var_dump($tenlist);
        $tenlist = $this->paixu($tenlist);
        $jsons["tenlist"] = $tenlist;
        $jsons["sum"] = $tpl_var;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    public function hksj() {
        //date_default_timezone_set('Asia/Shanghai');
        $this->uid = $_REQUEST["uid"];
        $tzmap['b.investor_uid'] = $this->uid;
        $tzmap['a.borrow_status'] = array(
            "in",
            "6,7"
        );
        $tzlist = m("borrow_info")->alias('a')->field("a.hkday,a.lead_time,a.borrow_duration,a.total,a.id,b.id as inid")->join('lzh_borrow_investor b on a.id = b.borrow_id')->where($tzmap)->select();
        //        pre($tzlist);
        //        var_dump($tzmap);die;
        $aa = array();
        foreach ($tzlist as $k => $val) {
            if ($val["total"] > 1) {
                // var_dump($val["hkday"]);
                if (!empty($val["hkday"])) {
                    $yue = $val["hkday"];
                    $dt = date('Y-n-j', $val['lead_time']);
                    for ($i = 1; $i < $val["total"]; $i++) {
                        $mpp["invest_id"] = $val["inid"];
                        $mpp["status"] = 1;
                        $mpp["sort_order"] = $i + 1;
                        $mpp["investor_uid"] = $this->uid;
                        $minfo = M("investor_detail")->where($mpp)->find();
                        if ($minfo) {
                            //var_dump(M("investor_detail")->getlastsql());
                            $hktime[] = date('Y-n-j', strtotime("$dt+" . $yue * $i . "days"));
                            $hkt[date('Y-n-j', strtotime("$dt+" . $yue * $i . "days")) ][] = $val["id"];
                            $hktd[date('Y-n', strtotime("$dt+" . $yue * $i . "days")) ][] = $val["id"];
                        } else {
                            $dhktime[] = date('Y-n-j', strtotime("$dt+" . $yue * $i . "days"));
                            $dhk[date('Y-n-j', strtotime("$dt+" . $yue * $i . "days")) ][] = $val["id"];
                            $dhkd[date('Y-n', strtotime("$dt+" . $yue * $i . "days")) ][] = $val["id"];
                        }
                        //var_dump($mpp);

                    }
                } else {
                    //  var_dump('12');
                    $times = time();
                    $yue = $val["borrow_duration"] / $val["total"];
                    $dt = date('Y-n-j', $val['lead_time']);
                    for ($i = 1; $i < $val["total"]; $i++) {
                        $mpp["invest_id"] = $val["inid"];
                        $mpp["status"] = 1;
                        $mpp["sort_order"] = $i + 1;
                        $mpp["investor_uid"] = $this->uid;
                        $minfo = M("investor_detail")->where($mpp)->find();
                        $stime = strtotime("$dt+" . $yue * $i . "month");
                        if ($stime > 1612579704 && in_array($val["id"], array(
                                1613,
                                1704,
                                1872,
                                1962
                            )) && $stime < 1614649147) {
                            $stime = strtotime("last day of +" . $yue * $i . " month", strtotime($dt)); //strtotime("$dt+".$i."month");

                        }
                        if ($val["id"] == 2102) {
                            $stime = strtotime("last day of +" . $yue * $i . " month", strtotime($dt)); //strtotime("$dt+".$i."month");

                        }
                        // var_dump($stime);
                        if ($minfo) {
                            //var_dump(M("investor_detail")->getlastsql());
                            $hktime[] = date('Y-n-j', $stime); //date('Y-n-j',strtotime("$dt+".$yue*$i."month"));
                            //                            $hkt[date('Y-n-j',strtotime("$dt+".$yue*$i."month"))][]=$val["id"];
                            //                            $hktd[date('Y-n',strtotime("$dt+".$yue*$i."month"))][]=$val["id"];
                            $hkt[date('Y-n-j', $stime) ][] = $val["id"];
                            $hktd[date('Y-n', $stime) ][] = $val["id"];
                        } else {
                            //                            $dhktime[]=date('Y-n-j',strtotime("$dt+".$yue*$i."month"));
                            //                            $dhk[date('Y-n-j',strtotime("$dt+".$yue*$i."month"))][]=$val["id"];
                            //                            $dhkd[date('Y-n',strtotime("$dt+".$yue*$i."month"))][]=$val["id"];
                            if ($stime < $times) {
                                $hktime[] = date('Y-n-j', $stime);
                                $hkt[date('Y-n-j', $stime) ][] = $val["id"];
                                $hktd[date('Y-n', $stime) ][] = $val["id"];
                            } else {
                                $dhktime[] = date('Y-n-j', $stime);
                                $dhk[date('Y-n-j', $stime) ][] = $val["id"];
                                $dhkd[date('Y-n', $stime) ][] = $val["id"];
                            }
                        }
                    }
                }
            }
            //else{
            // var_dump($val["id"]);
            //$mpp["borrow_id"]=$val["id"];
            $mpp["invest_id"] = $val["inid"];
            $mpp["status"] = 1;
            $mpp["sort_order"] = 1;
            $mpp["investor_uid"] = $this->uid;
            $minfo = M("investor_detail")->where($mpp)->find();
            if ($minfo) {
                //var_dump($minfo);
                //var_dump(M("investor_detail")->getlastsql());
                $hktime[] = date('Y-n-j', $val['lead_time']); //$val['borrow_duration']*30*60*60*24
                $hkt[date('Y-n-j', $val['lead_time']) ][] = $val["id"];
                $hktd[date('Y-n', $val['lead_time']) ][] = $val["id"];
                // $dhktime[]=date('Y-n-j',strtotime("$dt+".$yue*$i."month"));
                // $dhk[date('Y-n-j',strtotime("$dt+".$yue*$i."month"))][]=$val["id"];

            } else {
                // $hktime["time"][]=date('Y-n-j',strtotime("$dt+".$yue*$i."month"));
                //        $hkt[date('Y-n-j',strtotime("$dt+".$yue*$i."month"))][]=$val["id"];
                //                        $dhktime[]=date('Y-n-j',$val['lead_time']);
                //                        $dhk[date('Y-n-j',$val['lead_time'])][]=$val["id"];
                //                        $dhkd[date('Y-n',$val['lead_time'])][]=$val["id"];
                if ($val['lead_time'] < $times) {
                    //                            $hktime[]=date('Y-n-j',$stime);
                    //                            $hkt[date('Y-n-j',$stime)][]=$val["id"];
                    //                            $hktd[date('Y-n',$stime)][]=$val["id"];
                    $hktime[] = date('Y-n-j', $val['lead_time']);
                    $hkt[date('Y-n-j', $val['lead_time']) ][] = $val["id"];
                    $hktd[date('Y-n', $val['lead_time']) ][] = $val["id"];
                } else {
                    //                            $dhktime[]=date('Y-n-j',$stime);
                    //                            $dhk[date('Y-n-j',$stime)][]=$val["id"];
                    //                            $dhkd[date('Y-n',$stime)][]=$val["id"];
                    $dhktime[] = date('Y-n-j', $val['lead_time']);
                    $dhk[date('Y-n-j', $val['lead_time']) ][] = $val["id"];
                    $dhkd[date('Y-n', $val['lead_time']) ][] = $val["id"];
                }
            }
            // $dhktime[]=date('Y-n-j',$val['lead_time']);
            // $dhk[date('Y-n-j',$val['lead_time'])][]=$val["id"];
            //}

        } //exit();
        $dhktime = array_unique($dhktime);
        $hktime = array_unique($hktime);
        $jsons["dhktime"] = $dhktime;
        $jsons["dhk"] = $dhk;
        $jsons["hktime"] = $hktime;
        $jsons["hkt"] = $hkt;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    public function showhuikuan() {
        $this->uid = $_REQUEST["uid"];
        $uid = $this->uid;
        //投资项目
        //$_POST["date"]="2019-1-4";
        $oldtime = $_REQUEST["date"] . " 00:00:00";
        $emp_time = $_REQUEST["date"] . ' 23:59:59';
        $stime = strtotime($oldtime);
        //  var_dump($stime);
        $etime = strtotime($emp_time);
        // $map['b.lead_time'] =array('gt',$stime1);
        // $map['b.lead_time'] = array('lt',$etime);
        // $map['investor_uid'] = $this->uid;
        $ids = $_REQUEST["ids"];
        //  $map="b.lead_time >=".$stime." and b.lead_time <=".$etime." and investor_uid=".$this->uid." and i.status!=3"." and b.id != 1";
        $map = "b.id in ($ids) and investor_uid=" . $this->uid . " and i.status!=3" . " and b.id != 1";
        $field = "b.shoujia,i.xsinvestor_capital,b.hkday,b.total,b.borrow_duration,b.borrow_name,b.borrow_interest_rate,b.full_time,b.borrow_status as bstatus,b.id,i.investor_capital,i.add_time,i.status,i.borrow_id,i.deadline,i.id as inid,i.investor_way,i.member_interest_rate_id,i.bonus_id,b.borrow_img,i.is_experience,i.contractId,i.is_sign,i.step,b.lead_time";
        //分页处理
        $tenlist = M('borrow_investor i')->join('lzh_borrow_info b on b.id = i.borrow_id')->field($field)->where($map)->order('b.id desc')->select();
        //var_dump(M('borrow_investor i')->getLastSql());
        $benjine = 0;
        $shouyie = 0;
        $yhk = 0;
        foreach ($tenlist as $a => $v) {
            //var_dump($v);
            $vtime = (intval($v["lead_time"]));
            $tenlist[$a]["borrowname"] = cnsubstr($v["borrow_name"], 10);
            $tenlist[$a]["hk_time_c"] = date("Y-m-d", $vtime);
            $tenlist[$a]["getFloatvalue_c"] = getFloatvalue($v['investor_capital'] - (bounsmoney($v['bonus_id'])) , 2);
            $sort_order = 1;
            if ($v["total"] > 1) {
                if (!empty($v["hkday"])) {
                    $yue = $v["hkday"];
                    $dt = date('Y-n-j', $v['lead_time']);
                    for ($i = 0; $i < $v["total"]; $i++) {
                        $tenlist[$a]["hkzt2"] = date('Y-n-j', strtotime("$dt+" . $yue * $i . "days"));
                        if (date('Y-n-j', strtotime("$dt+" . $yue * $i . "days")) == $_POST["date"]) {
                            $sort_order = $i + 1;
                            $b = strtotime("$dt+" . $yue * $i . "days");
                        }
                    }
                } else {
                    $yue = $v["borrow_duration"] / $v["total"];
                    $dt = date('Y-n-j', $v['lead_time']);
                    for ($i = 0; $i < $v["total"]; $i++) {
                        $stime = strtotime("$dt+" . $yue * $i . "month");
                        if ($stime > 1612579704 && in_array($v["id"], array(
                                1613,
                                1704,
                                1872,
                                1962
                            )) && $stime < 1614649147) {
                            $stime = strtotime("last day of +" . $yue * $i . " month", strtotime($dt)); //strtotime("$dt+".$i."month");

                        }
                        if ($v["id"] == 2102) {
                            $stime = strtotime("last day of +" . $yue * $i . " month", strtotime($dt)); //strtotime("$dt+".$i."month");

                        }
                        $tenlist[$a]["hkzt2"] = date('Y-n-j', $stime); //date('Y-n-j',strtotime("$dt+".$yue*$i."month"));
                        if (date('Y-n-j', $stime) == $_POST["date"]) {
                            $sort_order = $i + 1;
                            //$b=strtotime("$dt+".$yue*$i."month");
                            $b = $stime;
                        }
                    }
                }
            } else {
                $b = $v["lead_time"];
            }
            $mpp["invest_id"] = $v["inid"];
            $mpp["status"] = 1;
            $mpp["sort_order"] = $sort_order;
            $mpp["investor_uid"] = $this->uid;
            $minfo = M("investor_detail")->where($mpp)->find();
            // if($minfo){
            //  $tenlist[$a]["hkzt"]=1;
            // }else{
            //  $tenlist[$a]["hkzt"]=0;
            // }
            // if($v["total"]>12){
            //  $lixi=getFloatvalue(($v["borrow_interest_rate"]/100*($v["total"]/12)*$v["investor_capital"]/$v["total"]),2);
            // }else{
            //  $lixi=getFloatvalue(($v["borrow_interest_rate"]/100*$v["investor_capital"]/$v["total"]),2);
            // }
            //var_dump($lixi);
            if ($minfo) {
                $tenlist[$a]["hkzt"] = 1;
                $tenlist[$a]["shouyi"] = $minfo["receive_interest"] + $minfo["receive_capital"] + $minfo["receive_chajia"];
                $yhk+= 1;
                $shouyie+= $minfo["receive_interest"] + $minfo["receive_chajia"];
            } else {
                $tenlist[$a]["hkzt"] = 0;
                //$tenlist[$a]["hklx"]="未回款";
                if ($sort_order == $v["total"]) {
                    if ($v["shoujia"] == 0) {
                        $tenlist[$a]["shouyi"] = $v["investor_capital"];
                    } else {
                        $tenlist[$a]["shouyi"] = $v["xsinvestor_capital"];
                    }
                } else {
                    $tenlist[$a]["shouyi"] = '';
                }
            }
            if ($sort_order == $v["total"]) {
                if ($v["shoujia"] == 0) {
                    $benjine+= $v["investor_capital"];
                } else {
                    $benjine+= $v["xsinvestor_capital"];
                }
                // $benjine+=$v["xsinvestor_capital"];
                if (empty($minfo)) {
                    $tenlist[$a]["hklx"] = "本金";
                } else {
                    $tenlist[$a]["hklx"] = "本金+收益";
                }
            } else {
                $tenlist[$a]["hklx"] = "第" . $sort_order . "次收益";
            }
            $tenlist[$a]["hk_time_c"] = date("Y-m-d", $b);
            if ($sort_order == $v["total"]) {
                $tenlist[$a]["bj"] = 1;
            } else {
                $tenlist[$a]["bj"] = 0;
            }
        }
        if (is_array($tenlist) && !empty($tenlist)) {
            $data["state"] = "1";
            $data["list"] = $tenlist;
            $hao = explode('-', $_POST["date"]);
            if (count($tenlist) == $yhk) {
                $data["hks"] = "<p  style='border: 0; border-bottom: 10px solid #fff; border-top: 10px solid #fff;'>" . $hao[2] . "号总回款:" . ($benjine + $shouyie) . "(本金" . $benjine . "+收益" . $shouyie . ")</p>";
            }
        } else {
            $data["state"] = "0";
            $data["list"] = $tenlist;
        }
        $jsons["data"] = $data;
        outJson($jsons);
    }
    public function zindexapi() {
        $this->uid = $_REQUEST["uid"];
        $uid = $this->uid;
        $map['uid'] = $this->uid;
        $start = strtotime(date("Y-m-1 0:0:0", strtotime($_REQUEST["date"] . "-01  0:0:1")));
        $end = strtotime(date("Y-m-t 23:59:59", strtotime($_REQUEST["date"] . "-01  0:0:1")));
        $ids = $this->getid($uid); //var_dump($ids);
        $idsd = $ids['a'][$_REQUEST["date"]];
        //var_dump($mapx);exit();
        $mapd['i.id'] = ["in", $idsd];
        //$mapx['i.id'] = ["in",$ids];
        $field = "b.hkday,b.total,b.borrow_duration,b.borrow_name,b.borrow_interest_rate,b.full_time,b.borrow_status as bstatus,b.id,i.investor_capital,i.add_time,i.status,i.borrow_id,i.deadline,i.id as inid,i.investor_way,i.member_interest_rate_id,i.bonus_id,b.borrow_img,i.is_experience,i.contractId,i.is_sign,i.step,b.lead_time";
        $tenlist = M('borrow_investor i')->join('lzh_borrow_info b on b.id = i.borrow_id')->field($field)->where($mapd)->order('b.id desc')->select();
        // $data["a"]=M('borrow_investor i')->getlastsql();
        //$tpl_var['dd']=$ids['b'][$_REQUEST["date"]];
        $fqhk = $ids['b'][$_REQUEST["date"]]['bx'];
        $fqhkd = $ids['b'][$_REQUEST["date"]]['lx'];
        //$fqhkdl=$ids['b'][$_POST["date"]]['al'];
        $yhbj["bi.id"] = ["in", $fqhk];
        $yhbj["bi.investor_uid"] = $uid;
        $yhbj["b.borrow_status"] = 7;
        $yhbj["bi.status"] = ["neq", 3];
        $tpl_var['yf_benjin'] = M('borrow_investor bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where($yhbj)->sum('investor_capital');
        $dyhbj["bi.id"] = ["in", $fqhk];
        $dyhbj["bi.investor_uid"] = $uid;
        $dyhbj["b.borrow_status"] = 6;
        $dyhbj["bi.status"] = ["neq", 3];
        $tpl_var['df_benjin'] = M('borrow_investor bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where($dyhbj)->sum('investor_capital');
        $ldyhbj["id"] = ["in", $fqhkd];
        // $ldyhbj["investor_uid"]=$uid;
        $ldyhbj["status"] = 1;
        //已返利润
        $tpl_var['shouyi'] = M('investor_detail')->where($ldyhbj)->sum('receive_interest'); //+$bxlx;
        //$hkid=$ids['b'][date("Y-n",time())]['hk'];
        foreach ($tenlist as $a => $v) {
            $tenlist[$a]["borrowname"] = cnsubstr($v["borrow_name"], 10);
            // $tenlist[$a]["hk_time_c"]=date("Y-m-d",$vtime);
            $tenlist[$a]["getFloatvalue_c"] = getFloatvalue($v['investor_capital'] - (bounsmoney($v['bonus_id'])) , 2);
            $sort_order = 1;
            if ($v["total"] > 1) {
                if (!empty($v["hkday"])) {
                    $yue = $v["hkday"];
                    $dt = date('Y-n-j', $v['lead_time']);
                    for ($i = 0; $i < $v["total"]; $i++) {
                        if (date('Y-n', strtotime("$dt+" . $yue * $i . " days")) == $_REQUEST["date"]) {
                            $ab = strtotime("$dt+" . $yue * $i . "days");
                            $sort_order = $i + 1;
                        }
                    }
                } else {
                    $yue = $v["borrow_duration"] / $v["total"];
                    if ($yue > 1) {
                        $dt = date('Y-n-j', $v['lead_time']);
                        for ($i = 0; $i < $v["total"]; $i++) {
                            if (date('Y-n', strtotime("$dt+" . $yue * $i . "month")) == $_REQUEST["date"]) {
                                $ab = strtotime("$dt+" . $yue * $i . "month");
                                $sort_order = $i + 1;
                            }
                        }
                    }
                }
            } else {
                $ab = $v['lead_time'];
            }
            $mpp["borrow_id"] = $v["id"];
            $mpp["status"] = 1;
            $mpp["sort_order"] = $sort_order;
            $mpp["investor_uid"] = $this->uid;
            $minfo = M("investor_detail")->where($mpp)->find();
            if ($minfo) {
                $tenlist[$a]["hkzt"] = 1;
                $tenlist[$a]["shouyi"] = $minfo["receive_interest"] + $minfo["receive_capital"];
            } else {
                $tenlist[$a]["hkzt"] = 0;
                //$tenlist[$k]["hklx"]="未回款";

            }
            if ($sort_order == $v["total"]) {
                $tenlist[$a]["hklx"] = "本金+收益";
            } else {
                $tenlist[$a]["hklx"] = "第" . $sort_order . "次收益";
            }
            $tenlist[$a]["hk_time_c"] = date("Y-m-d", $ab);
            if ($sort_order == $v["total"]) {
                $tenlist[$a]["bj"] = 1;
            } else {
                $tenlist[$a]["bj"] = 0;
            }
        }
        //$tpl_var["ad"]=$idsd;
        if (is_array($tenlist) && !empty($tenlist)) {
            $data["state"] = "1";
            $data["list"] = $tenlist;
        } else {
            $data["state"] = "0";
            $data["list"] = $tenlist;
        }
        $tpl_var['d'] = $_REQUEST["date"];
        // $this->assign('sum',$tpl_var);
        //  var_dump($tpl_var);
        $data["list"] = $this->paixu1($data["list"]);
        $tpl_var['tenlist'] = $data["list"];
        $jsons["data"] = $tpl_var;
        $jsons['status'] = 1;
        outJson($jsons);
    }
    public function zindexapi1() {
        // $_POST["date"]="2019-1";
        $this->uid = $_REQUEST["uid"];
        $uid = $this->uid;
        //recoverBonus($this->uid);
        // $ucLoing = de_xie($_COOKIE['LoginCookie']);
        // setcookie('LoginCookie','',time()-10*60,"/");
        //$uid = $this->uid;
        $minfo = getMinfo($uid, true);
        $all_money = getFloatvalue($minfo['account_money'] + $minfo['money_collect'] + $minfo['money_freeze'], 2);
        // 累计收益
        $minfo['receive_interests'] = M('borrow_investor')->where('investor_uid = ' . $uid . ' and status = 5')->sum('receive_interest');
        $pre = C('DB_PREFIX');
        $this->assign("uid", $uid);
        // 资料完善度
        $mVerify = m("members m")->field("m.credits,m.id,m.user_leve,m.time_limit,s.id_status,s.phone_status,s.email_status,s.video_status,s.face_status")->join("{$pre}members_status s ON s.uid=m.id")->where("m.id={$uid} ")->find();
        // echo m("members m")->getlastsql();exit;
        $mInfoProgress = 10;
        $mVerify['id_status'] and $mInfoProgress+= 20;
        $mVerify['phone_status'] and $mInfoProgress+= 20;
        $mVerify['email_status'] and $mInfoProgress+= 20;
        $mDataInfo = M('member_data_info')->where("uid={$this->uid} and status = 1")->count('id');
        $mDataInfo > 1 and $mInfoProgress+= 5;
        $mDataInfo > 3 and $mInfoProgress+= 5;
        $mDataInfo > 5 and $mInfoProgress+= 10; //60
        $pinpass = M('members')->where("id={$this->uid}")->find();
        $pinpass['pin_pass'] and $mInfoProgress+= 10;
        if ($mInfoProgress > 100) $mInfoProgress = 100;
        $this->assign('mInfoProgress', $mInfoProgress);
        // 投资进行中总额
        $map['uid'] = $this->uid;
        $start = strtotime(date("Y-m-1 0:0:0", strtotime($_REQUEST["date"] . "-01  0:0:1")));
        $end = strtotime(date("Y-m-t 23:59:59", strtotime($_REQUEST["date"] . "-01  0:0:1")));
        $ids = $this->getid($uid); //var_dump($ids);
        $idsd = $ids['a'][$_REQUEST["date"]];
        //var_dump($mapx);exit();
        $mapd['i.id'] = ["in", $idsd];
        //$mapx['i.id'] = ["in",$ids];
        $field = "b.hkday,b.total,b.borrow_duration,b.borrow_name,b.borrow_interest_rate,b.full_time,b.borrow_status as bstatus,b.id,i.investor_capital,i.add_time,i.status,i.borrow_id,i.deadline,i.id as inid,i.investor_way,i.member_interest_rate_id,i.bonus_id,b.borrow_img,i.is_experience,i.contractId,i.is_sign,i.step,b.lead_time";
        $tenlist = M('borrow_investor i')->join('lzh_borrow_info b on b.id = i.borrow_id')->field($field)->where($mapd)->order('b.id desc')->select();
        // $data["a"]=M('borrow_investor i')->getlastsql();
        $tpl_var['dd'] = $ids['b'][$_POST["date"]];
        $fqhk = $ids['b'][$_REQUEST["date"]]['bx'];
        $fqhkd = $ids['b'][$_REQUEST["date"]]['lx'];
        //$fqhkdl=$ids['b'][$_POST["date"]]['al'];
        $yhbj["bi.id"] = ["in", $fqhk];
        $yhbj["bi.investor_uid"] = $uid;
        $yhbj["b.borrow_status"] = 7;
        $yhbj["bi.status"] = ["neq", 3];
        $tpl_var['sum']['receive_interests'] = M('borrow_investor bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where($yhbj)->sum('investor_capital');
        // $xyhbj["investor_uid"]=$uid;
        // $xyhbj["invest_id"]=["in",$fqhk];
        // $xyhbj["status"]=1;
        // $bxlx= M('investor_detail')->where($xyhbj)->sum('receive_interest');
        $dyhbj["bi.id"] = ["in", $fqhk];
        $dyhbj["bi.investor_uid"] = $uid;
        $dyhbj["b.borrow_status"] = 6;
        $dyhbj["bi.status"] = ["neq", 3];
        $tpl_var['sum']['investor_capital'] = M('borrow_investor bi')->join('lzh_borrow_info b on b.id = bi.borrow_id')->where($dyhbj)->sum('investor_capital');
        $ldyhbj["id"] = ["in", $fqhkd];
        // $ldyhbj["investor_uid"]=$uid;
        $ldyhbj["status"] = 1;
        //已返利润
        $tpl_var['sum']['shouyi'] = M('investor_detail')->where($ldyhbj)->sum('receive_interest'); //+$bxlx;
        //$hkid=$ids['b'][date("Y-n",time())]['hk'];
        foreach ($tenlist as $a => $v) {
            $tenlist[$a]["borrowname"] = cnsubstr($v["borrow_name"], 10);
            // $tenlist[$a]["hk_time_c"]=date("Y-m-d",$vtime);
            $tenlist[$a]["getFloatvalue_c"] = getFloatvalue($v['investor_capital'] - (bounsmoney($v['bonus_id'])) , 2);
            $sort_order = 1;
            if ($v["total"] > 1) {
                if (!empty($v["hkday"])) {
                    $yue = $v["hkday"];
                    $dt = date('Y-n-j', $v['lead_time']);
                    for ($i = 0; $i < $v["total"]; $i++) {
                        if (date('Y-n', strtotime("$dt+" . $yue * $i . " days")) == $_REQUEST["date"]) {
                            $ab = strtotime("$dt+" . $yue * $i . "days");
                            $sort_order = $i + 1;
                        }
                    }
                } else {
                    $yue = $v["borrow_duration"] / $v["total"];
                    $dt = date('Y-n-j', $v['lead_time']);
                    for ($i = 0; $i < $v["total"]; $i++) {
                        if (date('Y-n', strtotime("$dt+" . $yue * $i . "month")) == $_REQUEST["date"]) {
                            $ab = strtotime("$dt+" . $yue * $i . "month");
                            $sort_order = $i + 1;
                        }
                    }
                }
            } else {
                $ab = $v['lead_time'];
            }
            $mpp["invest_id"] = $v["inid"];
            $mpp["status"] = 1;
            $mpp["sort_order"] = $sort_order;
            $mpp["investor_uid"] = $this->uid;
            $minfo = M("investor_detail")->where($mpp)->find();
            // if($minfo){
            //  $tenlist[$a]["hkzt"]=1;
            // }else{
            //  $tenlist[$a]["hkzt"]=0;
            // }
            $tenlist[$a]["hk_time_c"] = date("Y-m-d", $ab);
            if ($minfo) {
                $tenlist[$a]["hkzt"] = 1;
                $tenlist[$a]["shouyi"] = $minfo["receive_interest"] + $minfo["receive_capital"];
            } else {
                $tenlist[$a]["hkzt"] = 0;
                if (strtotime($tenlist[$a]['hk_time_c']) < time()) {
                    $tenlist[$a]["hkzt"] = 1;
                }
                //$tenlist[$a]["hklx"]="未回款";
                if ($sort_order == $v["total"]) {
                    $tenlist[$a]["shouyi"] = $v["investor_capital"];
                } else {
                    $tenlist[$a]["shouyi"] = '';
                }
            }
            if ($sort_order == $v["total"]) {
                if (empty($minfo)) {
                    $tenlist[$a]["hklx"] = "本金";
                } else {
                    $tenlist[$a]["hklx"] = "本金+收益";
                }
            } else {
                $tenlist[$a]["hklx"] = "第" . $sort_order . "次收益";
            }
            //$tenlist[$a]["hk_time_c"]=date("Y-m-d",$ab);
            if ($sort_order == $v["total"]) {
                $tenlist[$a]["bj"] = 1;
            } else {
                $tenlist[$a]["bj"] = 0;
            }
        }
        // $tpl_var["ad"]=$idsd;
        if (is_array($tenlist) && !empty($tenlist)) {
            $data["state"] = "1";
            $data["list"] = $tenlist;
        } else {
            $data["state"] = "0";
            $data["list"] = $tenlist;
        }
        $tpl_var['d'] = $_REQUEST["date"];
        // $this->assign('sum',$tpl_var);
        //  var_dump($tpl_var);
        $tpl_var['tenlist'] = $this->paixu1($data["list"]);
        $jsons["data"] = $tpl_var;
        $jsons['status'] = 1;
        outJson($jsons);
    }
    public function paixu($list) {
        //$list = [6, 2, 4, 8, 5, 9];
        $len = count($list);
        $n = count($list) - 1;
        for ($i = 0; $i < $len; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($list[$j]['lead_time_c'] > $list[$j + 1]['lead_time_c']) {
                    $tmp = $list[$j];
                    $list[$j] = $list[$j + 1];
                    $list[$j + 1] = $tmp;
                }
            }
        }
        return $list;
    }
    public function paixu1($list) {
        //$list = [6, 2, 4, 8, 5, 9];
        $len = count($list);
        $n = count($list) - 1;
        for ($i = 0; $i < $len; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($list[$j]['hk_time_c'] > $list[$j + 1]['hk_time_c']) {
                    $tmp = $list[$j];
                    $list[$j] = $list[$j + 1];
                    $list[$j + 1] = $tmp;
                }
            }
        }
        return $list;
    }
    public function getid($uid) {
        $tzmap['b.investor_uid'] = $this->uid = $uid;
        $tzmap['a.borrow_status'] = array(
            "in",
            "6,7"
        );
        //$tzmap['a.id']  =array("in","1364,1365,1366,1367");
        $tzlist = m("borrow_info")->alias('a')->field("a.hkday,a.lead_time,a.borrow_duration,a.total,a.id,b.id as idss")->join('lzh_borrow_investor b on a.id = b.borrow_id')->where($tzmap)->select();;
        $aa = array();
        foreach ($tzlist as $k => $val) {
            if ($val["total"] > 1) {
                if (!empty($val["hkday"])) {
                    $yue = $val["hkday"];
                    $dt = date('Y-n-j', $val['lead_time']);
                    for ($i = 0; $i < $val["total"]; $i++) {
                        $stime = strtotime("$dt+" . $yue * $i . "days");
                        $hktime[] = date('Y-n-j', $stime);
                        $hkt[date('Y-n-j', $stime) ][] = $val["id"];
                        $hktd[date('Y-n', $stime) ][] = $val["idss"];
                        if (($i + 1) == $val["total"]) {
                            $hktdb[date('Y-n', $stime) ]['bx'][] = $val["idss"];
                        } else {
                            // $vid=m("investor_detail")->where("sort_order='".($i+1)."' and invest_id=".$val["idss"])->getField("id");
                            // $hktdb[date('Y-n',$stime)]['lx'][]=$vid;

                        }
                        $vid = m("investor_detail")->where("sort_order='" . ($i + 1) . "' and invest_id=" . $val["idss"])->getField("id");
                        if (!empty($vid)) {
                            $hktdb[date('Y-n', $stime) ]['lx'][] = $vid;
                            //$hktdb[date('Y-n',$stime)]['hk'][]=$vid["invest_id"];

                        }
                    }
                } else {
                    $yue = $val["borrow_duration"] / $val["total"];
                    $dt = date('Y-n-j', $val['lead_time']);
                    for ($i = 0; $i < $val["total"]; $i++) {
                        $stime = strtotime("$dt+" . $yue * $i . "month");
                        $hktime[] = date('Y-n-j', $stime);
                        $hkt[date('Y-n-j', $stime) ][] = $val["id"];
                        $hktd[date('Y-n', $stime) ][] = $val["idss"];
                        if (($i + 1) == $val["total"]) {
                            $hktdb[date('Y-n', $stime) ]['bx'][] = $val["idss"];
                        } else {
                            // $vid=m("investor_detail")->where("sort_order='".($i+1)."' and invest_id=".$val["idss"])->getField("id");
                            // $hktdb[date('Y-n',$stime)]['lx'][]=$vid;

                        }
                        $vid = m("investor_detail")->where("sort_order='" . ($i + 1) . "' and invest_id=" . $val["idss"])->getField("id");
                        if (!empty($vid)) {
                            $hktdb[date('Y-n', $stime) ]['lx'][] = $vid;
                            //$hktdb[date('Y-n',$stime)]['hk'][]=$vid["invest_id"];

                        }
                    }
                }
            }
            $hktime[] = date('Y-n-j', $val['lead_time']);
            $hkt[date('Y-n-j', $val['lead_time']) ][] = $val["id"];
            $hktd[date('Y-n', $val['lead_time']) ][] = $val["idss"];
            $hktd[date('Y-n', $val['lead_time']) ][] = $val["idss"];
            if ($val["total"] == 1) {
                $hktdb[date('Y-n', $val['lead_time']) ]['bx'][] = $val["idss"];
                $vid = m("investor_detail")->where("sort_order='1' and invest_id=" . $val["idss"])->getField("id");
                if (!empty($vid)) {
                    $hktdb[date('Y-n', $val['lead_time']) ]['lx'][] = $vid;
                    //$hktdb[date('Y-n',$val['lead_time'])]['hk'][]=$vid["invest_id"];

                }
            }
        }
        $data = array(
            "a" => $hktd,
            "b" => $hktdb
        );
        return $data;
    }
    public function getids() {
        //$tzmap['b.investor_uid'] = $this->uid=$uid;
        $tzmap['a.borrow_status'] = array(
            "in",
            "6,7"
        );
        //$tzmap['a.id']  =array("in","1364,1365,1366,1367");
        $tzlist = m("borrow_info")->alias('a')->field("a.hkday,a.lead_time,a.borrow_duration,a.total,a.id,b.id as idss")->join('lzh_borrow_investor b on a.id = b.borrow_id')->where($tzmap)->group("b.borrow_id")->select();;
        //$tzlist = m("borrow_info")->field("hkday,lead_time,borrow_duration,total,id")->where($tzmap)->select();;
        $aa = array();
        foreach ($tzlist as $k => $val) {
            if ($val["total"] > 1) {
                if (!empty($val["hkday"])) {
                    $yue = $val["hkday"];
                    $dt = date('Y-n-j', $val['lead_time']);
                    for ($i = 0; $i < $val["total"]; $i++) {
                        $stime = strtotime("$dt+" . $yue * $i . "days");
                        $stime = date('Y-n-j', $stime);
                        $tday = date('Y-n-j', time());
                        if ($stime == $tday) {
                            $hktime[] = $val['id'];
                        }
                        // $hktime[]=date('Y-n-j',$stime);
                        // $hkt[date('Y-n-j',$stime)][]=$val["id"];
                        // $hktd[date('Y-n',$stime)][]=$val["idss"];
                        // if(($i+1)==$val["total"]){
                        //     $hktdb[date('Y-n',$stime)]['bx'][]=$val["idss"];
                        // }else{
                        //     // $vid=m("investor_detail")->where("sort_order='".($i+1)."' and invest_id=".$val["idss"])->getField("id");
                        //     // $hktdb[date('Y-n',$stime)]['lx'][]=$vid;
                        // }
                        // $vid=m("investor_detail")->where("sort_order='".($i+1)."' and invest_id=".$val["idss"])->getField("id");
                        // if(!empty($vid)){
                        //     $hktdb[date('Y-n',$stime)]['lx'][]=$vid;
                        //     //$hktdb[date('Y-n',$stime)]['hk'][]=$vid["invest_id"];
                        // }

                    }
                } else {
                    $yue = $val["borrow_duration"] / $val["total"];
                    $dt = date('Y-n-j', $val['lead_time']);
                    for ($i = 0; $i < $val["total"]; $i++) {
                        $stime = strtotime("$dt+" . $yue * $i . "month");
                        $stime = date('Y-n-j', $stime);
                        $tday = date('Y-n-j', time());
                        if ($stime == $tday) {
                            $hktime[] = $val['id'];
                        }
                        // if($stime==)
                        // $hktime[]=date('Y-n-j',$stime);
                        // $hkt[date('Y-n-j',$stime)][]=$val["id"];
                        // $hktd[date('Y-n',$stime)][]=$val["idss"];
                        // if(($i+1)==$val["total"]){
                        //     $hktdb[date('Y-n',$stime)]['bx'][]=$val["idss"];
                        // }else{
                        //     // $vid=m("investor_detail")->where("sort_order='".($i+1)."' and invest_id=".$val["idss"])->getField("id");
                        //     // $hktdb[date('Y-n',$stime)]['lx'][]=$vid;
                        // }
                        // $vid=m("investor_detail")->where("sort_order='".($i+1)."' and invest_id=".$val["idss"])->getField("id");
                        // if(!empty($vid)){
                        //     $hktdb[date('Y-n',$stime)]['lx'][]=$vid;
                        //     //$hktdb[date('Y-n',$stime)]['hk'][]=$vid["invest_id"];
                        // }

                    }
                }
            }
            $stime = date('Y-n-j', $val['lead_time']);
            $tday = date('Y-n-j', time());
            if ($stime == $tday) {
                $hktime[] = $val['id'];
            }
            // $hktime[]=date('Y-n-j',$val['lead_time']);
            // $hkt[date('Y-n-j',$val['lead_time'])][]=$val["id"];
            // $hktd[date('Y-n',$val['lead_time'])][]=$val["idss"];
            // $hktd[date('Y-n',$val['lead_time'])][]=$val["idss"];
            // if($val["total"]==1){
            //     $hktdb[date('Y-n',$val['lead_time'])]['bx'][]=$val["idss"];
            //     $vid=m("investor_detail")->where("sort_order='1' and invest_id=".$val["idss"])->getField("id");
            //     if(!empty($vid)){
            //         $hktdb[date('Y-n',$val['lead_time'])]['lx'][]=$vid;
            //         //$hktdb[date('Y-n',$val['lead_time'])]['hk'][]=$vid["invest_id"];
            //     }
            // }

        }
        $mmp["id"] = array(
            "in",
            $hktime
        );
        $tzlist = m("borrow_info")->field("borrow_name,hkday,lead_time,borrow_duration,total,id")->where($mmp)->select();;
        print_r("<pre>");
        print_r($tzlist);
    }
    //合同管理
    /* 获取长效令牌 */
    function yunhetong_login($url, $appId, $appKey) { // 模拟提交数据函数
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HEADER => true, //false时，取得code
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n\"appId\":\"$appId\",\n\"appKey\":\"$appKey\"\n}",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json"
            ) ,
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err . die;
        } else {
            //echo $response;
            $headArr = explode("\r\n", $response);
            foreach ($headArr as $loop) {
                if (strpos($loop, "token") !== false) {
                    $token = trim(substr($loop, 6));
                    //$token = trim($loop);

                }
                if (strpos($loop, "code") !== false) {
                    //$code = trim(substr($loop, 6));
                    $rp = trim($loop);
                }
            }
        }
        $arr = json_decode($rp, true);
        $code = $arr['code']; //code=200 说明成功
        $msg = $arr['msg'];
        //不成功
        if ($code != "200" || !$token) {
            return (0);
            die;
            print_r("获取长效令牌,原因：" . $msg) . die;
        } else {
            return ($token);
        }
        //
        //return $response; // 返回数据，json格式

    }
    //合同下载/download/contract
    function download_contract($url, $data, $token) {
        $idType = $data['idType'];
        $contractId = str_replace(",", "", number_format($data["idContent"]));
        //var_dump($contractId);
        //合同下载
        //$contractId="1804241101415029";
        $curl7 = curl_init();
        curl_setopt_array($curl7, array(
            CURLOPT_URL => "https://api.yunhetong.com/api/contract/download/0/$contractId",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            //CURLOPT_POSTFIELDS => "{\n\"idType\": \"$idType\",\n\"idContent\": \"$contractId\"\n}",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "token: $token"
            ) ,
        ));
        $response7 = curl_exec($curl7);
        $err7 = curl_error($curl7);
        curl_close($curl7);
        //var_dump("https://api.yunhetong.com/api/contract/download/0/$contractId");
        if ($err7) {
            echo "cURL Error #:" . $err7 . die;
        } else {
            echo $response7;
            $arr7 = json_decode($response7, true);
            $code7 = $arr7['code']; //code=200 说明成功
            $msg7 = $arr7['msg'];
            $data7 = $arr7['data'];
            //不成功
            if ($code7 != 200) {
                return (0);
                die;
                print_r("合同下载失败,原因：" . $msg7) . die;
            } else {
                print_r("合同下载成功!");
                header("Location: https://api.yunhetong.com/api/auth/download/$data7");
            }
        }
    }
    //添加签署者
    function contract_signer($url, $data, $token) {
        $idType = $data["idType"]; //参数类型：0 合同 ID，1 合同自定义编号
        //ID 内容
        $contractId = str_replace(",", "", number_format($data["contractId"]));
        $signerId = $data["signerId"]; //签署者 id
        $signPositionType = $data["signPositionType"]; ////签署的定位方式：0=关键字定位，1=签 名占位符定位，2=签署坐标
        $positionContent = $data["positionContent"]; ////对应定位方式的内容，如果用签名占位符 定位可以传多个签名占位符，并以分号隔开,最多 20 个;如果用签署坐标定位， 则该参数包含三个信息：“页面,x 轴坐标,y 轴坐标”（如 20,30,49）
        $signValidateType = $data["signValidateType"]; ////签署验证方式：0=不校验，1=短信验证
        $signMode = $data["signMode"]; ////印章使用类型（针对页面签署）：0=指定印章，1=每次绘制
        //return $positionContent;
        $curl5 = curl_init();
        //return $contractId;
        curl_setopt_array($curl5, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            //CURLOPT_POSTFIELDS => "{\n\"contractTitle\": \"$contractTitle\",\n\"templateId\": \"$templateId\"\n}",
            CURLOPT_POSTFIELDS => "{\"idType\": \"$idType\",\n\"idContent\": \"$contractId\",\n\"signers\": [{\"signerId\": \"$signerId\",\n\"signPositionType\": \"1\",\n\"positionContent\": \"$positionContent\",\n\"signValidateType\": \"0\"}\n]\n}",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "token: $token"
            ) ,
        ));
        $response5 = curl_exec($curl5);
        $err5 = curl_error($curl5);
        curl_close($curl5);
        if ($err5) {
            //       return(0); die; echo "cURL Error #:" . $err5.die;

        } else {
            //echo $response5;
            $arr5 = json_decode($response5, true);
            $code5 = $arr5['code']; //code=200 说明成功
            $msg5 = $arr5['msg'];
        }
        //不成功
        if ($code5 != 200) {
            if ($code5 == 20105) {
                return (1);
                die;
            }
            return (0);
            die;
            return "添加签署者,原因：" . $msg5 . $code5['code'];
            die;
            print_r("添加签署者,原因：" . $msg5 . $code5['code']);
            die;
        } else {
            return (1);
            die;
            print_r(1);
        }
        // print_r($code5);

    }
    //合同签署
    function contract_sign($url, $data, $token) {
        $idType = $data['idType'];
        $contractId = str_replace(",", "", number_format($data["idContent"]));
        //var_dump($contractId);
        $signerId = intval($data['signerId']);
        //print_r($contractId);
        $curl6 = curl_init();
        curl_setopt_array($curl6, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n\"idType\": \"$idType\",\n\"idContent\": \"$contractId\",\n\"signerId\": \"$signerId\"\n}",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "token: $token"
            ) ,
        ));
        $response6 = curl_exec($curl6);
        $err6 = curl_error($curl6);
        curl_close($curl6);
        if ($err6) {
            // echo "cURL Error #:" . $err6.die;

        } else {
            //  echo $response6;
            $arr6 = json_decode($response6, true);
            $code6 = $arr6['code']; //code=200 说明成功
            $msg6 = $arr6['msg'];
        }
        //不成功
        if ($code6 != 200) {
            if ($code6 == 20105) {
                return (1);
                die;
            }
            return (0);
            die;
            print_r("合同签署失败,原因：" . $msg6);
            die;
            return (0);
            die;
            //          print_r( "合同签署失败,原因：".$msg6).die;

        } else {
            return (1);
            die;
            print_r($response6);
        }
    }
    public function htlist() {
        $uid = $this->uid = $_REQUEST["uid"];
        $Model = new Model(); // 实例化一个model对象 没有对应任何数据表
        $is_hetong = $_REQUEST['is_hetong'];
        if (!isset($is_hetong) & empty($is_hetong)) {
            $is_hetong = 0;
        }
        $sql = "SELECT t.art_content,B.borrow_id,B.id as id,A.borrow_name,B.is_hetong  FROM lzh_borrow_info A JOIN lzh_borrow_investor B  on A.id = B.borrow_id JOIN lzh_article t  on t.title = A.templateid WHERE (A.borrow_status in (6,7)) and A.loan_certificate != '2' and ";
        if ($is_hetong == 1) {
            $sql.= "(B.borrow_uid=" . $this->uid . ") or ";
        } else if ($is_hetong == 0) {
            $sql.= "(B.add_time>1548664779) and ";
        }
        $sql.= " (B.investor_uid =" . $this->uid . ")";
        $sql.= "  and (B.is_hetong <>-1)";
        if ($is_hetong != 3) {
            $sql.= " and (B.is_hetong=" . $is_hetong . ')';
        }
        $sql.= " ORDER BY  update_time DESC ,  B.id DESC ";
        $page = intval($_REQUEST["page"]);
        $size = intval($_REQUEST["size"]);
        $sql1 = $sql;
        $sql.= "limit " . $page * $size . "," . $size;
        $list = $Model->query($sql);
        if (count($list) == $size) {
            $sql1.= "limit " . ($page + 1) * $size . "," . $size;
            $countlist = count($Model->query($sql1));
            if ($countlist > 0) {
                $jsons['data']["ispage"] = 1;
            } else {
                $jsons['data']["ispage"] = 0;
            }
        } else {
            $jsons['data']["ispage"] = 0;
        }
        $jsons['data']['size'] = $size;
        $jsons['data']['page'] = $page;
        $jsons['data']["list"] = $list;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    public function qianshu() {
        $uid = $this->uid = $_REQUEST["uid"];
        $Model = new Model(); // 实例化一个model对象 没有对应任何数据表
        $id = $_REQUEST['id'];
        $borrow_investor = M('borrow_investor')->find($id);
        //var_dump($borrow_investor);//exit();
        if ($borrow_investor['borrow_uid'] != $uid and $borrow_investor['investor_uid'] != $uid) {
            $jsons['msg'] = "数据错误！";
            $jsons['status'] = '0';
            outJson($jsons);
        }
        $sql = "SELECT B.*,A.borrow_name,A.borrow_interest_rate,A.templateid,A.start_time,A.lead_time,A.yifang_company_name,A.yifang_name,A.borrow_money,A.yifang_xinyongdaima FROM lzh_borrow_info A JOIN lzh_borrow_investor B  on A.id = B.borrow_id WHERE  B.id=" . $id . "  limit  1";
        $vm = $Model->query($sql);
        $me = M('member_info')->where("uid=" . $borrow_investor["investor_uid"])->find();
        $meyi = M('member_info')->where("uid=" . $borrow_investor["borrow_uid"])->find();
        header("Content-Type: text/html;charset=utf-8");
        //获取token
        $globalz = M('global')->select();
        foreach ($globalz as $k => $v) {
            $global[$v['code']] = $v['text'];
        }
        //获取token
        //$datass['appId']='2018050916380600068';
        //$datass['appKey']='JM34AbbcRI9VzQ';
        $datass['appId'] = $global['appid'];
        $datass['appKey'] = $global['appkey'];
        /* 获取长效令牌 */
        $token = $this->yunhetong_login("https://api.yunhetong.com/api/auth/login", $datass['appId'], $datass['appKey']);
        $zz = round((int)$vm[0]['investor_capital'] / (int)$vm[0]['borrow_money'] * 100, 2);
        $lead_time = date("Y-m-d H:i:s", $vm[0]["start_time"]);
        if (!empty($borrow_investor["contractid"]) && $borrow_investor["contractid"] != '0') {
            $contractId = $borrow_investor["contractid"];
        } else {
            /***************************************************************************/
            $murl = "https://api.yunhetong.com/api/contract/templateContract";
            $mdata['contractTitle'] = $vm[0]['borrow_name'];
            $mdata['templateId'] = empty($vm[0]['templateid']) ? "TEM1005147" : $vm[0]['templateid']; //TEM1001739
            $mdata['contractNo'] = "t" . time();
            ////乙方公司 : $(yifang_company_name)
            //乙方代表人 : $(yifang_name)
            //乙方信用代码 : $(yifang_xinyongdaima) -->
            $cont = ['${deal_name}' => $vm[0]["borrow_name"], '${bianhao}' => $vm[0]["id"], '${jiafang}' => $me["real_name"], '${idno}' => $me["idcard"], '${bingfang}' => $global["bingfang"], '${yifang_company_name}' => $meyi["company_name"], '${yifang_name}' => $meyi["real_name"], '${yifang_xinyongdaima}' => $meyi["company_idcard"], '${riqi}' => date("Y-m-d H:i:s", time()) , '${bingfang_xinyongdaima}' => $global["bingfang_xinyongdaima"], '${total_money}' => $vm[0]["borrow_money"], '${load_money}' => $vm[0]["investor_capital"], '${baifenbi}' => "$zz", '${end_time}' => "$lead_time"];
            $mdata['contractData'] = $cont;
            $mres = url_request_json($mdata, $murl, $token);
            writeLog($mres);
            $mrs = json_decode($mres, true);
            if ($mrs['code'] == '200') {
                $contractId = numberchange($mrs['data']['contractId']);
            }
            /***************************************************************************/
            M('borrow_investor')->where("id=" . $borrow_investor["id"])->save(['contractid' => $contractId]);
        }
        $vmv = M('borrow_investor')->where("borrow_id=" . $vm[0]["id"])->find();
        $sign = M('members')->where("id=" . $vm[0]["borrow_uid"])->find();
        //添加甲方签署者
        $datacs1["idType"] = 0;
        $datacs1["contractId"] = $contractId;
        $datacs1["signerId"] = $me['signerid']; //$signerId;
        $datacs1["signPositionType"] = 1; //签署的定位方式：0=关键字定位，1=签名占位符定位，2=签署坐标
        $datacs1["positionContent"] = "jia_sign"; //坐标位置为第 20 页（34，57）;//对应定位方式的内容，如果用签名占位符 定位可以传多个签名占位符，并以分号隔开,最多 20 个;如果用签署坐标定位， 则该参数包含三个信息：“页面,x 轴坐标,y 轴坐标”（如 20,30,49）
        $vaz = $this->contract_signer("https://api.yunhetong.com/api/contract/signer", $datacs1, $token);
        //添加丙方签署者
        $datacs2["idType"] = 0;
        $datacs2["contractId"] = $contractId;
        $datacs2["signerId"] = $global['signerid'];
        $datacs2["signPositionType"] = 1; //签署的定位方式：0=关键字定位，1=签名占位符定位，2=签署坐标
        $datacs2["positionContent"] = "bing_sign"; //坐标位置为第 20 页（34，57）;//对应定位方式的内容，如果用签名占位符 定位可以传多个签名占位符，并以分号隔开,最多 20 个;如果用签署坐标定位， 则该参数包含三个信息：“页面,x 轴坐标,y 轴坐标”（如 20,30,49）
        $datacs2["signValidateType"] = 0; //签署验证方式：0=不校验，1=短信验证;
        $datacs2["signMode"] = 1; //印章使用类型（针对页面签署）：0=指定印章， 1=每次绘制
        $signva = $this->contract_signer("https://api.yunhetong.com/api/contract/signer", $datacs2, $token);
        //添加乙方签署者
        ////乙方公司 : $(yifang_company_name)
        //乙方代表人 : $(yifang_name)
        //乙方信用代码 : $(yifang_xinyongdaima) -->
        $datacs3["idType"] = 0;
        $datacs3["contractId"] = $contractId;
        $datacs3["signerId"] = $sign['signerid'];
        $datacs3["signPositionType"] = 1; //签署的定位方式：0=关键字定位，1=签名占位符定位，2=签署坐标
        $datacs3["positionContent"] = "yi_sign"; //坐标位置为第 20 页（34，57）;//对应定位方式的内容，如果用签名占位符
        $datacs3["signValidateType"] = 0; //签署验证方式：0=不校验，1=短信验证;
        $datacs3["signMode"] = 1; //印章使用类型（针对页面签署）：0=指定印章， 1=每次绘制
        $yic = $this->contract_signer("https://api.yunhetong.com/api/contract/signer", $datacs3, $token);
        ///甲合同签署
        $datacsz2['idType'] = 0; //参数类型：0 合同 ID，1 合同自定义编号
        $datacsz2['idContent'] = $contractId; //ID 内容
        $datacsz2['signerId'] = $me['signerid']; //签署者 ID，可选参数，使用指定签署者的令牌调用 接口时可不传该参数
        $datacsz2['sealClass'] = 0; //印模 ID，可选参数，不传时使用用户最新印模//签章样式，0=常规样式，2=含摘要样式，3=含签 署时间样式，可选参数，不传时使用常规样式
        $contract_signapi2 = $this->contract_sign("https://api.yunhetong.com/api/contract/sign", $datacsz2, $token);
        // 丙合同签署
        $contract_sign1['idType'] = 0; //参数类型：0 合同 ID，1 合同自定义编号
        $contract_sign1['idContent'] = $contractId; //ID 内容
        $contract_sign1['signerId'] = $global['signerid']; //签署者 ID，可选参数，使用指定签署者的令牌调用 接口时可不传该参数
        $contract_sign1['sealClass'] = 0; //印模 ID，可选参数，不传时使用用户最新印模//签章样式，0=常规样式，2=含摘要样式，3=含签 署时间样式，可选参数，不传时使用常规样式
        $contract_signapi1 = $this->contract_sign("https://api.yunhetong.com/api/contract/sign", $contract_sign1, $token);
        //  乙合同签署
        $contract_sign2['idType'] = 0; //参数类型：0 合同 ID，1 合同自定义编号
        $contract_sign2['idContent'] = $contractId; //ID 内容
        $contract_sign2['signerId'] = $sign['signerid']; //签署者 ID，可选参数，使用指定签署者的令牌调用 接口时可不传该参数
        $contract_sign2['sealClass'] = 0; //印模 ID，可选参数，不传时使用用户最新印模//签章样式，0=常规样式，2=含摘要样式，3=含签 署时间样式，可选参数，不传时使用常规样式
        $contract_signapi3 = $this->contract_sign("https://api.yunhetong.com/api/contract/sign", $contract_sign2, $token);
        if ($contract_signapi1 == 0 || $contract_signapi2 == 0 || $contract_signapi3 == 0) {
            $jsons['msg'] = "签署失败";
            $jsons['status'] = '1';
            outJson($jsons);
        } else {
            $va = M('borrow_investor')->where("id=" . $id)->save(['is_hetong' => 1, 'update_time' => time() ]);
            $jsons['msg'] = "签署成功";
            $jsons['status'] = '1';
            outJson($jsons);
        }
    }
    public function xiazai() {
        $uid = $this->uid = $_GET["uid"];
        $Model = new Model(); // 实例化一个model对象 没有对应任何数据表
        $id = $_GET['id'];
        $sql = 'SELECT B.borrow_id,B.contractid,A.borrow_name,B.investor_uid,B.borrow_uid  FROM lzh_borrow_info A ';
        $sql.= 'JOIN lzh_borrow_investor B  on A.id = B.borrow_id';
        $sql.= " WHERE   B.id=" . $id . " limit 1";
        $vm = $Model->query($sql);
        $me = M('member_info')->where("uid=$uid")->find();
        header("Content-Type: text/html;charset=utf-8");
        $globalz = M('global')->select();
        foreach ($globalz as $k => $v) {
            $global[$v['code']] = $v['text'];
        }
        $datass['appId'] = $global['appid'];
        $datass['appKey'] = $global['appkey'];
        /* 获取长效令牌 */
        $token = $this->yunhetong_login("https://api.yunhetong.com/api/auth/login", $datass['appId'], $datass['appKey']);
        $datadc['idType'] = 0; //参数类型：0 合同 ID，1 合同自定义编号
        $datadc['idContent'] = $vm[0]['contractid']; //ID 内容
        //var_dump($datadc['idContent'])  ;
        $this->download_contract("https://api.yunhetong.com/api/download/contract", $datadc, $token);
    }
    //优惠券列表
    public function youhuijun() {
        $uid = $this->uid = $_REQUEST["uid"];
        recoverBonus($this->uid);
        $experience_moneyArr = C('EXPERIENCE_MONEY');
        $typeArr = $experience_moneyArr['TYPE'];
        $statusArr = $experience_moneyArr['STATUS'];
        $statusArr = array(
            '已禁用',
            '未使用',
            '已使用',
            '已过期'
        );
        $map = array();
        if ($_REQUEST['status']) {
            $map['t1.status'] = $_REQUEST['status'];
        } else {
            $map['t1.status'] = '1';
        }
        $map['t1.uid'] = $this->uid;
        $page = intval($_REQUEST["page"]);
        $size = intval($_REQUEST["size"]);
        $Lsql = $page * $size . ',' . $size;
        $list = M('member_bonus t1')->join('lzh_members t2 on t1.uid = t2.id')->field('t1.*,t2.user_name')->where($map)->order('t1.money_bonus asc,t1.end_time desc')->limit($Lsql)->select();
        if (count($list) == $size) {
            $limits = ($page + 1) * $size . ',' . $size;
            $countlist = M('member_bonus t1')->join('lzh_members t2 on t1.uid = t2.id')->field('t1.*,t2.user_name')->where($map)->order('t1.id desc,t1.end_time asc')->limit($limits)->select();
            if ($countlist > 0) {
                $jsons['data']["ispage"] = 1;
            } else {
                $jsons['data']["ispage"] = 0;
            }
        } else {
            $jsons['data']["ispage"] = 0;
        }
        $jsons['data']['size'] = $size;
        $jsons['data']['page'] = $page;
        $jsons['data']["list"] = $list;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    public function editmem() {
        $uid = $this->uid = $_REQUEST["uid"];
        if (isset($_REQUEST["user_img"]) && !empty($_REQUEST["user_img"])) {
            $data['user_img'] = $_REQUEST["user_img"];
        }
        $model = M('member_info');
        $member_info = $model->find($this->uid);
        $res = false;
        if (!is_array($member_info) || empty($member_info)) {
            $data['uid'] = $this->uid;
            $res = $model->add($data);
        } else {
            $res = $model->where("uid = {$this->uid}")->save($data);
        }
        if ($res) {
            $jsons['msg'] = "操作成功！";
            $jsons['status'] = '1';
        } else {
            $jsons['msg'] = "操作失败！";
            $jsons['status'] = '0';
        }
        outJson($jsons);
    }
    public function edituname() {
        $uid = $this->uid = $_REQUEST["uid"];
        // if(isset($_REQUEST["user_img"]) && !empty($_REQUEST["user_img"])){
        //     $data['user_img'] =$_REQUEST["user_img"];
        // }
        $codeId = @$_REQUEST['codeId'];
        $txtCode = @$_REQUEST['code'];
        $user_phone = M('members')->where("id={$uid}")->getField('user_phone');
        $verifyRs = M('verify_code')->where("id = '{$codeId}' and content = {$txtCode} and phone='{$user_phone}'")->count('id');
        if ($verifyRs != 1) {
            $jsons['status'] = "0";
            $jsons["msg"] = "手机验证码不正确";
            outJson($jsons);
        }
        if (isset($_REQUEST["user_name"]) && !empty($_REQUEST["user_name"])) {
            $data['user_name'] = $_REQUEST["user_name"];
        }
        $model = M('members');
        $member_info = $model->find($this->uid);
        $res = false;
        if (!is_array($member_info) || empty($member_info)) {
            $data['uid'] = $this->uid;
            $res = $model->add($data);
        } else {
            $res = $model->where("id = {$this->uid}")->save($data);
        }
        if ($res) {
            $jsons['msg'] = "操作成功！";
            $jsons['status'] = '1';
        } else {
            $jsons['msg'] = "操作失败！";
            $jsons['status'] = '0';
        }
        outJson($jsons);
    }
    public function editphone() {
        $uid = $this->uid = $_REQUEST["uid"];
        $codeId = @$_REQUEST['codeId'];
        $txtCode = @$_REQUEST['code'];
        $user_phone = M('members')->where("id={$uid}")->field('user_phone,pin_pass')->find();
        if ($user_phone["pin_pass"] != md5($_REQUEST["pin_pass"])) {
            $jsons['status'] = "0";
            $jsons["msg"] = "支付密码错误！";
            outJson($jsons);
        }
        $verifyRs = M('verify_code')->where("id = '{$codeId}' and content = {$txtCode} and phone='" . $_REQUEST['phone'] . "'")->count('id');
        if ($verifyRs != 1) {
            $jsons['status'] = "0";
            $jsons["msg"] = "手机验证码不正确";
            outJson($jsons);
        }
        $rs = M('members')->where('user_phone=' . $_REQUEST['phone'])->find();
        if ($rs) {
            $jsons['status'] = "0";
            $jsons["msg"] = "该手机号已在本站已绑定，不可重复使用！";
            outJson($jsons);
        }
        $model = M('member_info');
        $model2 = M('members');
        $data['cell_phone'] = $_POST['phone'];
        $data2['user_phone'] = $_POST['phone'];
        $member_info = $model->find($this->uid);
        if (!is_array($member_info)) {
            $res = $model2->where("uid = {$this->uid}")->save($data2);
            $data['uid'] = $this->uid;
            $res1 = $model->add($data);
        } else {
            $res = $model->where("uid = {$this->uid}")->save($data);
            $res1 = $model2->where("id = {$this->uid}")->save($data2);
        }
        if ($res && $res1) {
            $jsons['msg'] = "操作成功！";
            $jsons['status'] = '1';
        } else {
            $jsons['msg'] = "操作失败！";
            $jsons['status'] = '0';
        }
        outJson($jsons);
    }
    public function registerxy() {
        $type = $_REQUEST['type'];
        $id = 523;
        if ($type == 1) {
            $id = 538;
        }
        if ($type == 2) {
            $id = 539;
        }
        $article = m("article_category")->field('type_content')->where('id=' . $id)->find();
        $jsons['content'] = $article;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    public function shanchu() {
        $uid = $this->uid = $_REQUEST["uid"];
        if ($uid > 123481) {
            M('member_info')->where("uid=" . $uid)->delete();
            M('members')->where("id=" . $uid)->delete();
            M('members_status')->where("uid=" . $uid)->delete();
            M('member_money')->where("uid=" . $uid)->delete();
            M('member_experience')->where("uid=" . $uid)->delete();
            $jsons['msg'] = "操作成功！";
            $jsons['status'] = '1';
        } else {
            $jsons['msg'] = "操作失败！";
            $jsons['status'] = '0';
        }
        outJson($jsons);
    }
    // public function bftyj(){
    //     $m=M();
    //     $list=$m->query("select * from lzh_members where id not in(select  uid from lzh_member_experience) and id >2110");
    //     var_dump(count($list));
    //     foreach ($list as $k => $v) {
    //         pubExperienceMoney($v["id"],$this->glo['award_reg'],4,30);
    //     }
    // }
    public function wdindex() {
        $uid = $this->uid = $_REQUEST["uid"];
        $map['investor_uid'] = $this->uid;
        $map['status'] = array(
            'in',
            '1,4,5,6'
        );
        $borrow_status = '';
        if (isset($_REQUEST['type']) && !empty($_REQUEST['type']) && 0 != $_REQUEST['type']) {
            $map['pid'] = $_REQUEST['type'];
        }
        if (isset($_REQUEST['status']) && !empty($_REQUEST['status']) && 0 != $_REQUEST['status']) {
            $status = $_REQUEST['status'];
            if ($status == 1) {
                $map['i.status'] = 1;
            }
            if ($status == 2) {
                $map['b.borrow_status'] = 6;
                //$map['b.xs_time'] = array("gt",time());
                $map['_complex'] = array(
                    'b.shoujia' => "0",
                    "b.xs_time" => array(
                        "gt",
                        time()
                    ) ,
                    '_logic' => 'or'
                );
            }
            if ($status == 3) {
                $map['b.borrow_status'] = 6;
                $map['b.shoujia'] = array(
                    "neq",
                    0
                );
                $map['b.xs_time'] = array(
                    "lt",
                    time()
                );
                $map['b.xj_time'] = array(
                    "gt",
                    time()
                );
            }
            if ($status == 4) {
                $map['i.status'] = 5;
            }
        }
        //$map['b.borrow_status'] = 6;
        $page = intval($_REQUEST["page"]);
        $size = intval($_REQUEST["size"]);
        $Lsql = $page * $size . ',' . $size;
        $field = "i.investor_capital,b.shoujia,b.fx_img,b.xs_time,b.xj_time,i.fenshu,i.ztfenshu,i.xsfenshu,i.status,b.borrow_img,i.add_time as invest_time,m.user_name as borrow_user,b.borrow_duration,b.start_time,b.borrow_duration,b.total,b.borrow_name,b.borrow_min,b.borrow_time,b.id as borrow_id,i.id";
        $list = M('borrow_investor i')->field($field)->where($map)->join("lzh_borrow_info b ON b.id=i.borrow_id")->join("lzh_members m ON m.id=b.borrow_uid")->order('i.add_time DESC')->limit($Lsql)->select(); /**/
        //$jsons['status1']=M('borrow_investor i')->getlastsql();
        foreach ($list as $k => $v) {
            if ($v["status"] == 1) {
                $list[$k]["zt"] = "待养殖";
            }
            //if($v["status"]==4&&$v["xsinvestor_capital"]){
            if ($v["status"] == 4) {
                if ($v["xs_time"] > time()) {
                    $list[$k]["zt"] = "待售中";
                } else {
                    if ($v["shoujia"] == 0) {
                        $list[$k]["zt"] = "待售中";
                    } else {
                        $list[$k]["zt"] = "售卖中";
                        if ($v["xj_time"] < time()) {
                            $list[$k]["zt"] = "已下架";
                        }
                    }
                    //                    $map['b.xs_time'] = array("lt",time());
                    //                    $map['b.xj_time'] = array("gt",time());

                }
            }
            if ($v["status"] == 5) {
                $list[$k]["zt"] = "已完成";
            }
            if (empty($v["fx_img"])) {
                $list[$k]["fx_img"] = $v["borrow_img"];
            }
            if ($v["shoujia"] == 0) {
                $list[$k]["fenshu"] = $v['investor_capital'] / $v["borrow_min"];
            }
        }
        if (count($list) == $size) {
            $limits = ($page + 1) * $size . ',' . $size;
            $countlist = M('borrow_investor i')->field($field)->where($map)->join("lzh_borrow_info b ON b.id=i.borrow_id")->join("lzh_members m ON m.id=b.borrow_uid")->order('i.add_time DESC')->limit($limits)->select(); /**/
            if ($countlist > 0) {
                $jsons['data']["ispage"] = 1;
            } else {
                $jsons['data']["ispage"] = 0;
            }
        } else {
            $jsons['data']["ispage"] = 0;
        }
        $jsons['data']['size'] = $size;
        $jsons['data']['page'] = $page;
        $jsons['data']["list"] = $list;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    public function getCreditsLogs() {
        $uid = $this->uid = $_REQUEST["uid"];
        $map['uid'] = $this->uid;
        $page = intval($_REQUEST["page"]);
        $size = intval($_REQUEST["size"]);
        $Lsql = $page * $size . ',' . $size;
        $list = m("member_creditslog")->where($map)->order("id DESC")->limit($Lsql)->select();
        if (count($list) == $size) {
            $limits = ($page + 1) * $size . ',' . $size;
            $countlist = m("member_creditslog")->where($map)->order("id DESC")->limit($limits)->select();
            if ($countlist > 0) {
                $jsons['data']["ispage"] = 1;
            } else {
                $jsons['data']["ispage"] = 0;
            }
        } else {
            $jsons['data']["ispage"] = 0;
        }
        $jsons['data']['size'] = $size;
        $jsons['data']['page'] = $page;
        $jsons['data']["list"] = $list;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    public function isnewuser() {
        $uid = $this->uid = $_REQUEST["uid"];
        $where['investor_uid'] = array(
            'eq',
            "{$this->uid}"
        );
        if ($this->glo['new_user_time'] > 0) {
            $where['add_time'] = array(
                array(
                    'egt',
                    "{$new_user_time}"
                ) ,
                array(
                    'elt',
                    time()
                )
            );
        }
        $where['borrow_id'] = array(
            'neq',
            1
        );
        $newUser = M('borrow_investor')->where($where)->count();
        $where = null;
        $where['bespeak_uid'] = array(
            'eq',
            "{$this->uid}"
        );
        if ($this->glo['new_user_time'] > 0) {
            $where['add_time'] = array(
                array(
                    'egt',
                    "{$new_user_time}"
                ) ,
                array(
                    'elt',
                    time()
                )
            );
        }
        $where['borrow_id'] = array(
            'neq',
            1
        );
        $newUser = max($newUser, M('bespeak')->where($where)->count());
        $jsons['status'] = $newUser;
        outJson($jsons);
    }
    public function zhongzhi() {
        $info = M("zhongzhi")->where("id=2")->find();
        $info["gglist"] = M("zhongzhi_xx")->where("zid=" . $info["id"])->select();
        $jsons['data']["data"] = $info;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    public function add_car() {
        $data["uid"] = $_REQUEST["uid"];
        $data["gid"] = $_REQUEST["gid"];
        $data["num"] = $_REQUEST["num"];
        $data["fangshi"] = $_REQUEST["fangshi"];
        $data["type"] = $_REQUEST["type"];
        $carid = 0;
        if ($data["type"] == 1) {
            $map["uid"] = $_REQUEST["uid"];
            $map["status"] = 1;
            $map["gid"] = $_REQUEST["gid"];
            $map["type"] = 1;
            $map["fangshi"] = 1;
            $resd = M("car")->where($map)->find();
            if ($resd && $_REQUEST["fangshi"] == 1) {
                $datad["num"] = $resd["num"] + $_REQUEST["num"];
                $res = M("car")->where($map)->save($datad);
                $carid = $resd["id"];
            } else {
                $res = M("car")->add($data);
                $carid = $res;
            }
        }
        if ($data["type"] == 2) {
            $map["uid"] = $_REQUEST["uid"];
            $map["type"] = 2;
            $map["status"] = 1;
            $resd = M("car")->where($map)->delete();
            $data["zid"] = 2;
            $res = M("car")->add($data);
            $carid = $res;
        }
        if ($res) {
            $jsons['msg'] = "操作成功！";
            $jsons['status'] = '1';
            $jsons['carid'] = $carid;
        } else {
            $jsons['msg'] = "操作失败！";
            $jsons['status'] = '0';
        }
        outJson($jsons);
    }
    public function car_list() {
        $uid = $_REQUEST["uid"];
        $map["uid"] = $uid;
        $map["type"] = 1;
        $map["status"] = 1;
        $map["fangshi"] = 1;
        $list = M("car")->where($map)->select();
        foreach ($list as $k => $v) {
            $market = M("market")->where("id=" . $v["gid"])->find();
            $list[$k]["good"]["type_name"] = M("article_category")->where("id=" . $market["type_id"])->getField("type_name");
            $list[$k]["good"]["title"] = $market["title"];
            $list[$k]["good"]["id"] = $market["id"];
            $list[$k]["good"]["art_img"] = $market["art_img"];
            $list[$k]["good"]["art_jiage"] = $market["art_jiage"];
            $list[$k]["good"]["art_jifen"] = $market["art_jifen"];
            $list[$k]["good"]["art_writer"] = $market["art_writer"];
        }
        if ($list) {
            $jsons['status'] = '1';
        } else {
            $jsons['status'] = '0';
        }
        $jsons['data'] = $list;
        outJson($jsons);
    }
    public function car_num() {
        $map["uid"] = $_REQUEST["uid"];
        $map["gid"] = $_REQUEST["gid"];
        $map['status'] = 1;
        $data['num'] = $_REQUEST["num"];
        $res = M("car")->where($map)->save($data);
        if ($res) {
            $jsons['msg'] = "操作成功！";
            $jsons['status'] = '1';
        } else {
            $jsons['msg'] = "操作失败！";
            $jsons['status'] = '0';
        }
        outJson($jsons);
    }
    public function car_del() {
        $map["uid"] = $_REQUEST["uid"];
        $ids = explode(',', $_REQUEST["id"]);
        $map["id"] = array(
            "in",
            $ids
        );
        $res = M("car")->where($map)->delete();
        if ($res) {
            $jsons['msg'] = "操作成功！";
            $jsons['status'] = '1';
        } else {
            $jsons['msg'] = "操作失败！";
            $jsons['status'] = '0';
        }
        outJson($jsons);
    }
    public function order_list() {
        $this->uid = $_REQUEST["uid"];
        //分页处理
        $field = 'id,gid,jifen,uid,num,action,real_name,address,jine,add_time,ordernums,carid,pay_way,type';
        $page = intval($_REQUEST["page"]);
        $size = intval($_REQUEST["size"]);
        $action = $_REQUEST["status"];
        if ($action == 1) {
            $map["action"] = 3;
        }
        if ($action == 2) {
            $map["action"] = 0;
        }
        if ($action == 3) {
            $map["action"] = 1;
        }
        if ($action == 4) {
            $map["action"] = array(
                "in",
                array(
                    2,
                    9
                )
            );
        }
        if ($action == 5) {
            $map["action"] = array(
                "in",
                array(
                    5,
                    6,
                    7,
                    8,
                    4
                )
            );
        }
        $limit = $page * $size . ',' . $size;
        $order = "id DESC";
        $map['uid'] = $this->uid;
        $list = M("order")->field($field)->where($map)->order($order)->limit($limit)->select();
        foreach ($list as $k => $v) {
            if ($v['type'] == 1) {
                if ($v["carid"] != null) {
                    $goods = M("car c")->field('c.num,m.id,title,art_info,art_time,art_img,art_jiage,type_id')->where("c.id in (" . $v["carid"] . ")")->join("lzh_market m ON m.id=c.gid")->select();
                } else {
                    $goods = M("market")->field('id,title,art_info,art_time,art_img,art_jiage,type_id')->where("id=" . $v["gid"])->select();
                    $goods[0]["num"] = $v["num"];
                }
                $goods_num = 0;
                foreach ($goods as $ke => $ve) {
                    $goods_num+= $ve["num"];
                }
            }
            if ($v['type'] == 2) {
                $goods = M("borrow_info")->field('id,borrow_name as title,borrow_img as art_img,shoujia as art_jiage')->where("id=" . $v["gid"])->select();
                $goods[0]["num"] = $v['num'];
                $goods_num = $v['num'];
            }
            $list[$k]["goods"] = $goods;
            $list[$k]["jianshu"] = $goods_num;
            $list[$k]["bhyuanyin"] = M("order_refund")->where("ordernums='" . $v["ordernums"] . "'")->getField('bhyuanyin');
        }
        if (count($list) == $size) {
            $limits = ($page + 1) * $size . ',' . $size;
            $countlist = M("order")->field($field)->where($map)->where($map)->limit($limits)->order($order)->count();
            if ($countlist > 0) {
                $jsons['data']["ispage"] = 1;
            } else {
                $jsons['data']["ispage"] = 0;
            }
        } else {
            $jsons['data']["ispage"] = 0;
        }
        $jsons['data']['size'] = $size;
        $jsons['data']['page'] = $page;
        $jsons['data']["list"] = $list;
        $jsons['status'] = '1';;
        outJson($jsons);
    }
    public function thaddress() {
        $data = C('thaddress');
        $jsons['data'] = $data;
        $jsons['status'] = '1';;
        outJson($jsons);
    }
    public function tuihuo() {
        $map["id"] = $_REQUEST['id'];
        $map["uid"] = $_REQUEST['uid'];
        $oinfo = M("order")->where($map)->find();
        if ($oinfo["action"] != '6') {
            $jsons['msg'] = "订单状态错误！";
            $jsons['status'] = 0;
        } else {
            M()->startTrans();
            $data["action"] = "7";
            $newxid = M("order")->where($map)->save($data);
            $rdata["th_time"] = time();
            $rdata["status"] = '3';
            $rdata["wuliu"] = $_REQUEST['wuliu'];
            $rdata["danhao"] = $_REQUEST['danhao'];
            $rmap["ordernums"] = $oinfo["ordernums"];
            $res = M("order_refund")->where($rmap)->save($rdata);
            if ($newxid && $res) {
                M()->commit();
                $jsons['msg'] = "处理成功";
                $jsons['status'] = 1;
            } else {
                M()->rollback();
                $jsons['msg'] = "处理失败";
                $jsons['status'] = 0;
            }
        }
        outJson($jsons);
    }
    //订单详情
    public function orderinfo() {
        $map["id"] = $_REQUEST["id"];
        $info = M("order")->field('id,gid,jifen,uid,num,action,cell_phone,real_name,address,jine,add_time,ordernums,carid,yijian,beizhu,type')->where($map)->find();
        if (!$info) {
            $jsons['msg'] = "数据错误！";
            $jsons['status'] = '0';
            outJson($jsons);
        }
        if ($info['type'] == '1') {
            if ($info["carid"] != null) {
                $info["goods"] = M("car c")->field('c.num,m.id,title,art_info,art_time,art_img,art_jiage,type_id')->where("c.id in (" . $info["carid"] . ")")->join("lzh_market m ON m.id=c.gid")->select();
            } else {
                $goods = M("market")->field('id,title,art_info,art_time,art_img,art_jiage,type_id')->where("id=" . $info["gid"])->select();
                $goods[0]["num"] = $info["num"];
                $info["goods"] = $goods;
            }
        }
        if ($info['type'] == '2') {
            $goods = M("borrow_info")->field('id,borrow_name as title,borrow_img as art_img,shoujia as art_jiage')->where("id=" . $info["gid"])->select();
            $goods[0]["num"] = $info["num"];
            $info["goods"] = $goods;
        }
        if ($info["yijian"]) {
            $wl = explode(":", $info["yijian"]);
            $info["wuliu"] = $wl[0];
            $info["danhao"] = $wl[1];
        }
        $info["refund"] = M("order_refund")->where("ordernums='" . $info['ordernums'] . "'")->find();
        $info["refund"]['images'] = explode(',', $vo["refund"]['images']);
        $jsons['status'] = '1';
        $jsons['data'] = $info;
        outJson($jsons);
    }
    //添加种植订单
    public function zhongzhi_order() {
        $order = M("order");
        $borrow_id = $_REQUEST["borrow_id"];
        $address = $_REQUEST["address"];
        $uid = $this->uid;
        $adinfo = M("member_address")->where("id=" . $address)->find();
        $num = $_REQUEST["num"];
        //$zhongzhi=M("zhongzhi")->where("id=".$cars["zid"])->find();
        $borrow_info = M("borrow_info")->where("id=" . $borrow_id)->find();
        //var_dump($borrow_info);
        if ($borrow_info['shoujia'] == 0 || $borrow_info["borrow_status"] != 6) {
            $jsons['msg'] = "此商品不可销售";
            $jsons['status'] = 0;
            outJson($jsons);
        }
        if ($borrow_info['xs_time'] > time() || $borrow_info["borrow_status"] != 6) {
            $jsons['msg'] = "商品还未开售";
            $jsons['status'] = 0;
            outJson($jsons);
        }
        if ($borrow_info['xj_time'] < time() || $borrow_info["borrow_status"] != 6) {
            $jsons['msg'] = "商品已过销售期";
            $jsons['status'] = 0;
            outJson($jsons);
        }
        //$zhongzhi_xx=M("zhongzhi_xx")->where("id=".$carinfo["gid"])->find();
        $order->startTrans(); //rollback
        $savedata['ordernums'] = sprintf('%s-%s-%s', 'ZDD', $uid, time());
        $savedata['uid'] = $this->uid;
        $savedata['jine'] = $borrow_info["shoujia"] * $num;
        $savedata['gid'] = $borrow_id;
        $savedata['add_time'] = time();
        $savedata['address'] = $adinfo["province"] . $adinfo["city"] . $adinfo["district"] . $adinfo["address"];
        $savedata['num'] = $num;
        $savedata['cell_phone'] = $adinfo["main_phone"];
        $savedata['real_name'] = $adinfo["name"];
        $savedata['type'] = "2";
        $savedata['action'] = 3;
        $savedata['add_ip'] = get_client_ip();
        $savedata['pay_way'] = $_REQUEST["pay_way"];
        if (!empty($_REQUEST["investor_id"])) {
            $savedata['investor_id'] = $_REQUEST["investor_id"];
        }
        $newid = $order->add($savedata);
        if ($newid) {
            $order->commit();
            //减库存
            $dat["art_writer"] = $borrow_info["art_writer"] - $num;
            M("borrow_info")->where("id=" . $borrow_id)->save($dat);
            $jsons['msg'] = "操作成功";
            $jsons['status'] = 1;
            $jsons['ordernums'] = $savedata['ordernums'];
            $jsons['pay_way'] = $savedata['pay_way'];
        } else {
            $order->rollback();
            $jsons['msg'] = "操作失败";
            $jsons['status'] = 0;
        }
        outJson($jsons);
    }
    //种植订单详情
    public function zhongzhi_info() {
        $id = $_REQUEST["id"];
        $order = M('order')->field('id,gid,jifen,uid,num,cell_phone,status,real_name,address,jine,add_time,ordernums,yijian')->where("id='" . $id . "'")->find();
        $goods = M("zhongzhi_xx x")->field('x.*,z.images,z.name')->where("x.id=" . $order["gid"])->join("lzh_zhongzhi z ON z.id=x.zid")->find();
        $goods["imglist"] = explode(",", $goods["images"]);
        $jsons['order'] = $order;
        $jsons['goods'] = $goods;
        $jsons['status'] = 1;
        outJson($jsons);
    }
    public function xinshoulist() {
        $page = intval($_REQUEST["page"]);
        $size = intval($_REQUEST["size"]);
        $Lsql = $page * $size . ',' . $size;
        $map["money_experience"] = '8888.00';
        $list = M('member_experience e')->where($map)->field("e.paystatus,m.user_name")->join("lzh_members m ON m.id=e.uid")->order("e.id DESC")->limit($Lsql)->select();
        if (count($list) == $size) {
            $limits = ($page + 1) * $size . ',' . $size;
            $countlist = M('member_experience e')->where($map)->field("e.paystatus,m.user_name")->join("lzh_members m ON m.id=e.uid")->limit($limits)->select();
            if ($countlist > 0) {
                $jsons['data']["ispage"] = 1;
            } else {
                $jsons['data']["ispage"] = 0;
            }
        } else {
            $jsons['data']["ispage"] = 0;
        }
        $jsons['data']['size'] = $size;
        $jsons['data']['page'] = $page;
        $jsons['data']["list"] = $list;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    public function zhongzhilist() {
        $this->uid = $_REQUEST["uid"];
        $map['uid'] = $this->uid;
        $status = $_REQUEST["status"];
        if ($status == 0) {
            $map["o.status"] = array(
                "in",
                array(
                    1,
                    2,
                    3,
                    4
                )
            );
        } else {
            $map["o.status"] = $status;
        }
        $page = intval($_REQUEST["page"]);
        $size = intval($_REQUEST["size"]);
        $Lsql = $page * $size . ',' . $size;
        $order = "o.add_time desc";
        $list = m('order_zz o')->field('o.ordernums,o.id as oid,o.status,o.pay_way,o.jine,x.*,o.num,z.name,z.images')->where($map)->join("lzh_zhongzhi_xx x ON x.id=o.gid")->join("lzh_zhongzhi z ON z.id=x.zid")->order($order)->limit($Lsql)->select();
        //var_dump(m('order_zz o')->getlastsql());
        foreach ($list as $k => $v) {
            $list[$k]["images"] = explode(",", $v["images"]) [0];
        }
        if (count($list) == $size) {
            $limits = ($page + 1) * $size . ',' . $size;
            $countlist = m('order_zz o')->where($map)->join("lzh_zhongzhi_xx x ON x.id=o.gid")->limit($limits)->select();
            if ($countlist > 0) {
                $jsons['data']["ispage"] = 1;
            } else {
                $jsons['data']["ispage"] = 0;
            }
        } else {
            $jsons['data']["ispage"] = 0;
        }
        $jsons['data']['size'] = $size;
        $jsons['data']['page'] = $page;
        $jsons['data']["list"] = $list;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    public function zhifubao() {
        $uid = $_REQUEST["uid"];
        $id = $_REQUEST["id"];
        $map["uid"] = $uid;
        $map["ordernums"] = $id;
        $order = M('order_zz')->where($map)->find();
        //var_dump($order);exit();
        if ($order["status"] != 1) {
            $jsons['msg'] = "订单状态错误";
            $jsons['status'] = 0;
            outJson($jsons);
        }
        if ($order["pay_way"] != 1) {
            $jsons['msg'] = "请选择微信完成支付";
            $jsons['status'] = 0;
            outJson($jsons);
        }
        $goods = M("zhongzhi_xx x")->field('x.*,z.images,z.name')->where("x.id=" . $order["gid"])->join("lzh_zhongzhi z ON z.id=x.zid")->find();
        $data['WIDout_trade_no'] = $order["ordernums"];
        //订单名称，必填
        $data['WIDsubject'] = $goods["name"];
        //付款金额，必填
        $data['WIDtotal_amount'] = $order["jine"];
        //商品描述，可空
        $data['WIDbody'] = $goods["pinlei"];
        vendor('Alipawap.wappay.pay');
        $wappay = new pay();
        $wappay->post_order($data);
    }
    public function weixin() {
        $uid = $_REQUEST["uid"];
        $id = $_REQUEST["id"];
        $map["uid"] = $uid;
        $map["ordernums"] = $id;
        $order = M('order_zz')->where($map)->find();
        if ($order["status"] != 1) {
            $jsons['msg'] = "订单状态错误";
            $jsons['status'] = 0;
            outJson($jsons);
        }
        if ($order["pay_way"] != 2) {
            $jsons['msg'] = "请选择支付宝完成支付";
            $jsons['status'] = 0;
            outJson($jsons);
        }
        $zhongzhi_xx = M("zhongzhi_xx")->where("id=" . $order["gid"])->find();
        $zhongzhi = M("zhongzhi")->where("id=" . $zhongzhi_xx["zid"])->find();
        $data["name"] = $zhongzhi["name"];
        $data["ordernums"] = $order["ordernums"];
        $data["money"] = intval($order["jine"] * 100);
        $data["pid"] = $order["gid"];
        $data["openid"] = $_REQUEST["openid"]; // "okS1X5-qSQdVZ3FKQFBb8wQz-SQ8";
        // vendor('Weixin.natives');
        vendor('Weixin.jsapi');
        $jsapi = new jsapi();
        $jsdata = $jsapi->wxwap($data);
        $jsons['jsdata'] = $jsdata;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str.= substr($chars, mt_rand(0, strlen($chars) - 1) , 1);
        }
        return $str;
    }
    public function ToUrlParams() {
        $buff = "";
        foreach ($this->values as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff.= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }
    public function sweixin() {
        $uid = $_REQUEST["uid"];
        $id = $_REQUEST["id"];
        $map["uid"] = $uid;
        $map["ordernums"] = $id;
        $order = M('order')->where($map)->find();
        //var_dump($order);exit();
        if ($order["action"] != 3) {
            $jsons['msg'] = "订单状态错误";
            $jsons['status'] = 0;
            outJson($jsons);
        }
        if ($order["pay_way"] != 2) {
            $jsons['msg'] = "请选择支付宝完成支付";
            $jsons['status'] = 0;
            outJson($jsons);
        }
        if ($order["jine"] <= 0) {
            $jsons['msg'] = "支付金额错误！";
            $jsons['status'] = 0;
            outJson($jsons);
        }
        if ($order["type"] == '1') {
            $cars = M("car")->field('num,gid')->where("id in (" . $order["carid"] . ")")->select();
            $goods = "";
            foreach ($cars as $k => $v) {
                $markets = M("market")->where("id=" . $v["gid"])->find();
                $goods.= $markets["title"] . "x" . $v["num"] . "/" . $markets["art_jiage"];
            }
        }
        if ($order["type"] == '2') {
            $binfo = M("borrow_info")->field('borrow_name')->where("id=" . $order['gid'])->find();
            $goods = $binfo["borrow_name"];
        }
        $data["name"] = $goods;
        $data["ordernums"] = $order["ordernums"];
        $data["money"] = $order["jine"] * 100;
        $data["pid"] = $order["id"];
        $data["openid"] = $_REQUEST["openid"]; // "okS1X5-qSQdVZ3FKQFBb8wQz-SQ8";
        //vendor('Weixin.natives');
        vendor('Weixin.jsapi');
        $jsapi = new jsapi();
        $jsdata = $jsapi->wxwap($data);
        $jsons['jsdata'] = $jsdata;
        $jsons['status'] = '1';
        outJson($jsons);
    }
    public function szhifubao() {
        $uid = $_REQUEST["uid"];
        $id = $_REQUEST["id"];
        $map["uid"] = $uid;
        $map["ordernums"] = $id;
        $order = M('order')->where($map)->find();
        //var_dump($order);exit();
        if ($order["action"] != 3) {
            $jsons['msg'] = "订单状态错误";
            $jsons['status'] = 0;
            outJson($jsons);
        }
        if ($order["pay_way"] != 1) {
            $jsons['msg'] = "请选择微信完成支付";
            $jsons['status'] = 0;
            outJson($jsons);
        }
        if ($order["jine"] <= 0) {
            $jsons['msg'] = "支付金额错误！";
            $jsons['status'] = 0;
            outJson($jsons);
        }
        if ($order["type"] == '1') {
            $cars = M("car")->field('num,gid')->where("id in (" . $order["carid"] . ")")->select();
            $goods = "";
            foreach ($cars as $k => $v) {
                $markets = M("market")->where("id=" . $v["gid"])->find();
                $goods.= $markets["title"] . "x" . $v["num"] . "/" . $markets["art_jiage"];
            }
        }
        if ($order["type"] == '2') {
            $binfo = M("borrow_info")->field('borrow_name')->where("id=" . $order['gid'])->find();
            $goods = $binfo["borrow_name"];
        }
        $data['WIDout_trade_no'] = $order["ordernums"];
        //订单名称，必填
        $data['WIDsubject'] = "海鲜商城购买";
        //付款金额，必填
        $data['WIDtotal_amount'] = $order["jine"];
        //商品描述，可空
        $data['WIDbody'] = $goods;
        vendor('Alipawap.wappay.pay');
        $wappay = new pay();
        $wappay->post_order($data);
    }
    public function quxiao() {
        $id = $_REQUEST['id'];
        $Model = M();
        $Model->startTrans();
        $oinfo = M("order")->where("ordernums='{$id}'")->find();
        if ($oinfo["action"] != '3') {
            $jsons['msg'] = "处理失败";
            $jsons['status'] = 0;
        } else {
            if ($oinfo['type'] == "1") {
                $res = memberCreditsLog($this->uid, 8, intval($oinfo['jifen']) , $info = "取消商品购买积分退回");
                $glist = M("car")->where("id in ('" . $oinfo['carid'] . "')")->select();
                $mk = true;
                foreach ($glist as $k => $v) {
                    $mk = M('market')->where("id=" . $v["gid"])->setInc('art_writer', $v['num']);
                }
            }
            if ($oinfo['type'] == "2") {
                $mk = M('borrow_info')->where("id=" . $v["gid"])->setInc('art_writer', $oinfo['num']);
            }
            $data["action"] = "9";
            $newxid = M("order")->where("ordernums='{$id}'")->save($data);
            if ($newxid) {
                $Model->commit();
                $jsons['msg'] = "处理成功";
                $jsons['status'] = 1;
            } else {
                $Model->rollback();
                $jsons['msg'] = "处理失败";
                $jsons['status'] = 0;
            }
        }
        outJson($jsons);
    }
    public function shouhuo() {
        $map["id"] = $_REQUEST['id'];
        $map["uid"] = $_REQUEST['uid'];
        $oinfo = M("order")->where($map)->find();
        if ($oinfo["action"] != '1') {
            $jsons['msg'] = "订单状态错误！";
            $jsons['status'] = 0;
        } else {
            $data["action"] = "2";
            $data["sh_time"] = time();
            $newxid = M("order")->where($map)->save($data);
            if ($newxid) {
                $jsons['msg'] = "处理成功";
                $jsons['status'] = 1;
            } else {
                $jsons['msg'] = "处理失败";
                $jsons['status'] = 0;
            }
        }
        outJson($jsons);
    }
    /*app上线接口*/
    public function appwxlogin() {
        $data["appopenid"] = $user_obj['openid'] = $_REQUEST['openid'];
        $user_obj['nickname'] = $_REQUEST['nickname'];
        $user_obj['headimgurl'] = $_REQUEST['headimgurl'];
        $unionid['unionid'] = $_REQUEST['unionid'];
        $members = M('members')->where($data)->find();
        if (!empty($members)) {
            $up['uid'] = $members['id'];
            $up['add_time'] = time();
            $up['ip'] = get_client_ip();
            M('member_login')->add($up);
            //$jsons['data']['uid'] = text($members['id']);
            $jsons['data']['uid'] = $this->jwt->getToken(text($members['id']));
            $jsons['data']['user_name'] = text($members['user_name']);
            $jsons['data']['user_type'] = text($members['user_type']);
            $jsons['data']['user_phone'] = $members['user_phone'];
            $jsons['data']["is_changepin"] = $members['pin_pass'] ? 1 : 0;
            $jsons['data']["openid"] = $members['openid'];
            $jsons['data']["userpic"] = $members['userpic'];
            $jsons["status"] = "1";
            $jsons["appopenid"] = $user_obj['openid'];
            $jsons["msg"] = "登录成功！";
            // outJson($jsons);

        } else {
            $membersss = M('members')->where($unionid)->find();
            if (!empty($membersss)) {
                $res = M('members')->where($unionid)->save($data);
                // outJson(array('msg'=>$res));exit;
                $up['uid'] = $membersss['id'];
                $up['add_time'] = time();
                $up['ip'] = get_client_ip();
                M('member_login')->add($up);
                //$jsons['data']['uid'] = text($members['id']);
                $jsons['data']['uid'] = $this->jwt->getToken(text($membersss['id']));
                $jsons['data']['user_name'] = text($membersss['user_name']);
                $jsons['data']['user_type'] = text($membersss['user_type']);
                $jsons['data']['user_phone'] = $membersss['user_phone'];
                $jsons['data']["is_changepin"] = $membersss['pin_pass'] ? 1 : 0;
                $jsons['data']["openid"] = $membersss['openid'];
                $jsons['data']["userpic"] = $membersss['userpic'];
                $jsons["status"] = "1";
                $jsons["appopenid"] = $user_obj['openid'];
                $jsons["msg"] = "登录成功！";
            } else {
                $jsons["status"] = "0";
                $jsons["appopenid"] = $user_obj['openid'];
                $jsons["openid"] = '';
                $jsons["txtUser"] = $user_obj['nickname'];
                $jsons["userpic"] = $user_obj['headimgurl'];
                $jsons["msg"] = "您没有绑定微信！";
                session('unionid', $unionid['unionid']);
            }
            // outJson($jsons);

        }
        $jsons['res'] = urldecode(http_build_query($jsons));
        outJson($jsons);
    }
    public function appweixin() {
        $uid = $_REQUEST["uid"];
        $id = $_REQUEST["id"];
        $map["uid"] = $uid;
        $map["ordernums"] = $id;
        $order = M('order_zz')->where($map)->find();
        if ($order["status"] != 1) {
            $jsons['msg'] = "订单状态错误";
            $jsons['status'] = 0;
            outJson($jsons);
        }
        if ($order["pay_way"] != 2) {
            $jsons['msg'] = "请选择支付宝完成支付";
            $jsons['status'] = 0;
            outJson($jsons);
        }
        $zhongzhi_xx = M("zhongzhi_xx")->where("id=" . $order["gid"])->find();
        $zhongzhi = M("zhongzhi")->where("id=" . $zhongzhi_xx["zid"])->find();
        $data["name"] = $zhongzhi["name"];
        $data["ordernums"] = $order["ordernums"];
        $data["money"] = intval($order["jine"] * 100);
        $data["pid"] = $order["gid"];
        $data["openid"] = $_REQUEST["openid"];
        // vendor('Weixin.lib.WxPay#Api');
        // vendor('Weixin.lib.WxPay#Data');
        require_once __DIR__ . "/../../../../CORE/Extend/Vendor/Weixin/lib/WxPay.Api.php";
        require_once __DIR__ . "/../../../../CORE/Extend/Vendor/Weixin/lib/WxPay.Data.php";
        // require_once __DIR__."/../../../../CORE/Extend/Vendor/Weixin/lib/WxPay.Config.php";
        require_once __DIR__ . "/WxPay.Config.php";
        // 商品名称
        $subject = $data["name"];
        // 订单号，示例代码使用时间值作为唯一的订单ID号
        $out_trade_no = $data["ordernums"];
        // 商品金额（单位为分）
        $total = $data["money"];
        $unifiedOrder = new WxPayUnifiedOrder();
        // $unifiedOrder->SetAppid("wx02c8fe894cf913ca");
        // $unifiedOrder->SetMch_id("1547037081");
        $unifiedOrder->SetBody($subject); //商品或支付单简要描述
        $unifiedOrder->SetNonce_str($out_trade_no); //date('YmdHis', time())
        $unifiedOrder->SetOut_trade_no($out_trade_no);
        $unifiedOrder->SetTotal_fee($total);
        $unifiedOrder->SetTrade_type("APP");
        $unifiedOrder->SetSpbill_create_ip(get_client_ip());
        $unifiedOrder->SetNotify_url("https://" . $_SERVER['SERVER_NAME'] . "/Paynew/wnotify");
        $config = new WxPayConfig();
        $result = WxPayApi::unifiedOrder($config, $unifiedOrder);
        if (is_array($result)) {
            // echo json_encode($result); // 生成支付订单数据
            $return = array(
                "appid" => $result['appid'],
                "noncestr" => $result['nonce_str'],
                "package" => "Sign=WXPay",
                "partnerid" => $result['mch_id'],
                "prepayid" => $result['prepay_id'],
                // sign: "2AC49055659D35A21232C49C2FB5A9C4"
                "timestamp" => "1591070457",
            );
            $return["sign"] = $this->getSign($return);
            echo json_encode(array(
                'return' => $return,
                'result' => $result
            ));
        }
    }
    public function appsweixin() {
        $uid = $_REQUEST["uid"];
        $id = $_REQUEST["id"];
        $map["uid"] = $uid;
        $map["ordernums"] = $id;
        $order = M('order')->where($map)->find();
        if ($order["status"] != 1) {
            $jsons['msg'] = "订单状态错误";
            $jsons['status'] = 0;
            outJson($jsons);
        }
        if ($order["pay_way"] != 2) {
            $jsons['msg'] = "请选择支付宝完成支付";
            $jsons['status'] = 0;
            outJson($jsons);
        }
        $cars = M("car")->field('num,gid')->where("id in (" . $order["carid"] . ")")->select();
        $goods = "";
        foreach ($cars as $k => $v) {
            $markets = M("market")->where("id=" . $v["gid"])->find();
            if ($k < 2) {
                $goods.= $markets["title"] . "x" . $v["num"] . "/" . $markets["art_jiage"];
            }
        }
        $data["name"] = $goods;
        $data["ordernums"] = $order["ordernums"];
        $data["money"] = $order["jine"] * 100;
        $data["pid"] = $order["id"];
        $data["openid"] = $_REQUEST["openid"]; // "okS1X5-qSQdVZ3FKQFBb8wQz-SQ8";

        /**
         *
         */
        require_once __DIR__ . "/../../../../CORE/Extend/Vendor/Weixin/lib/WxPay.Api.php";
        require_once __DIR__ . "/../../../../CORE/Extend/Vendor/Weixin/lib/WxPay.Data.php";
        require_once __DIR__ . "/WxPay.Config.php";
        // 商品名称
        $subject = $data["name"];
        // 订单号，示例代码使用时间值作为唯一的订单ID号
        $out_trade_no = $data["ordernums"];
        // 商品金额（单位为分）
        $total = $data["money"];
        $unifiedOrder = new WxPayUnifiedOrder();
        $unifiedOrder->SetBody($subject); //商品或支付单简要描述
        $unifiedOrder->SetNonce_str($out_trade_no); //date('YmdHis', time())
        $unifiedOrder->SetOut_trade_no($out_trade_no);
        $unifiedOrder->SetTotal_fee($total);
        $unifiedOrder->SetTrade_type("APP");
        $unifiedOrder->SetSpbill_create_ip(get_client_ip());
        $unifiedOrder->SetNotify_url("https://" . $_SERVER['SERVER_NAME'] . "/Paynew/wnotify");
        $config = new WxPayConfig();
        $result = WxPayApi::unifiedOrder($config, $unifiedOrder);
        if (is_array($result)) {
            // echo json_encode($result); // 生成支付订单数据
            $return = array(
                "appid" => $result['appid'],
                "noncestr" => $result['nonce_str'],
                "package" => "Sign=WXPay",
                "partnerid" => $result['mch_id'],
                "prepayid" => $result['prepay_id'],
                // sign: "2AC49055659D35A21232C49C2FB5A9C4"
                "timestamp" => "1591070457",
            );
            $return["sign"] = $this->getSign($return);
            echo json_encode(array(
                'return' => $return,
                'result' => $result
            ));
        }
    }
    public function getSign($paramArr) { //print_r($paramArr);
        ksort($paramArr);
        $paramStr = http_build_query($paramArr);
        $paramStr = urldecode($paramStr);
        $param_temp = $paramStr . '&key=2f46140df07853ac09098638e435b0cc'; //echo $param_temp.'<br>';
        $signValue = strtoupper(md5($param_temp)); //echo $signValue.'<br>';
        return $signValue;
    }
    public function ceshidx() {
        vendor('Alisms.sendSms');
        $sendSms = new sendSms();
        $template = "SMS_162545326";
        $phone = "13651076469";
        $param = array(
            "code" => "222115"
        );
        $jsdata = $sendSms->alisendSms($phone, $template, $param);
        $jsdata = json_decode(json_encode($jsdata) , true);
        if ($jsdata['Code'] == 'OK') {
            var_dump("发送成功！");
        } else {
            var_dump($jsdata["Message"]);
        }
    }
    public function dodenglu() {
        $uid = $_REQUEST["uid"];
        $sessionid = $_REQUEST["sessionid"];
        S($sessionid, $uid, 600);
    }
    public function dotuikuan() {
        $id = intval($_REQUEST['id']);
        //$data['yijian'] =  $_POST['yijian'];
        $data['action'] = $_REQUEST['action'];
        $data['th_time'] = time();
        $order = M("order")->where("id={$id}")->find();
        //   var_dump($order);
        if ($_REQUEST["action"] == 4) {
            $data['out_trade_no'] = $order["ordernums"];
            $data['out_refund_no'] = $order["ordernums"];
            $data['total_fee'] = intval($order["jine"] * 100); //支付金额
            $data['refund_fee'] = intval($order["jine"] * 100); //退款金额
            vendor('Weixin.jsapi');
            $jsapi = new jsapi();
            $content = $jsapi->tuikuan($data);
            if ($content["result_code"] == "FAIL") {
                var_dump($content["err_code_des"]);
            }
            if ($content["return_code"] == "FAIL") {
                var_dump($content["return_msg"]);
            }
            if ($content["result_code"] == "SUCCESS") {
                var_dump("12");
            }
            var_dump($content);
            exit;
        }
    }
    public function getwuliu() {
        $host = "https://ali-deliver.showapi.com";
        $path = "/showapi_expInfo";
        $method = "GET";
        $appcode = "9892fc444b1247369f80a499ab1868d9";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        $querys = "com=auto&nu=" . $_REQUEST["num"] . "&receiverPhone=receiverPhone&senderPhone=senderPhone";
        $bodys = "";
        $url = $host . $path . "?" . $querys;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$" . $host, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        $result = curl_exec($curl);
        $data = json_decode($result, true);
        outJson($data);
    }
    //获取手机项目
    public function saomafu() {
        $id = 51;
        $list = array();
        $condition['start_time'] = array(
            "lt",
            time()
        );
        $condition['end_time'] = array(
            "gt",
            time()
        );
        $condition['id'] = array(
            "eq",
            $id
        );
        $_list = m("ad")->field("ad_type,content")->where($condition)->find();
        if ($_list['ad_type'] == 1) {
            $_list['content'] = unserialize($_list['content']);
        }
        $list = $_list;
        if ($list['ad_type'] == 0 || !$list['content']) {
            if (!$list['content']) {
                $adimg = "广告未上传或已过期";
                $jsons['msg'] = $adimg;
                $jsons['status'] = '0';
                outJson($jsons);
            }
            $adimg['content'] = $list['content'];
            $adimg['ad_type'] = $list['ad_type'];
        } else {
            $adimg['content'] = $list['content'];
            $adimg['ad_type'] = $list['ad_type'];
        }
        $jsons['msg'] = "成功";
        $jsons['data'] = $adimg;
        $jsons['status'] = '1';
        outJson($jsons);
    }
}

