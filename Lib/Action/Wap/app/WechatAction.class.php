<?php
// 本类由系统自动生成，仅供测试用途
class WechatAction extends HCommonAction
{
    private $wechatObj = '';
    public function __construct()
    {
        header("Content-type:text/html;charset=utf-8");              
    }
    public function index()
    {
        $wechatObj  = $this->wechatObj  = new Wechat();
        $requestMsg = $wechatObj->valid();
        if (($requestMsg)) {
            switch ($requestMsg->MsgType) {
                // 文本消息 text
                case 'event':
                    $responseArr = $this->recEvent($requestMsg);
                    break;
                default:
                    break;
            }
            return $wechatObj->responseMsg($responseArr);
        }
    }
    private function recEvent($requestMsg)
    {
        switch ($requestMsg->Event) {
            case 'CLICK':
                //预约
                if ($requestMsg->EventKey == 'RESERVE') {
                    return $this->reserve($requestMsg);
                }
                break;
            default:
                break;
        }
    }
    public function newtips(){
        // die;
        $uid = '184,901';
        $rs = Wechat::newtips(238,$uid); 
        dump($rs);

        die;
        $uid = '184,901';
        $uid = 4;
        $data = array(
                    array(
                        'investor_capital' => '100.25',
                        'receive_interest' => '8.86',
                        'investor_uid' => $uid,
                        'borrow_name' => '测试项目回款',
                        'sell_days' => '90',
                        'borrow_interest_rate' => '16.3',
                    ),
                );
        $rs  = Wechat::repaymenttips($data);
        dump($rs);die;
        $rs = Wechat::investtips($uid, '测试项目',1100.87); 
        dump($rs);
        die;
        $rs = Wechat::newtips(228,$uid); 
        dump($rs);
        die;
        $this->wechatObj = new Wechat();
    	$borrow_id = 1;
    	$pre = C('DB_PREFIX');
    	$binfo = M('borrow_info')->find($borrow_id);
    	$mLists = M('members m')->field('m.id,o.openid')->join($pre.'oauth o on m.id = o.bind_uid')->where('notice = 1 and notice_end_time >= UNIX_TIMESTAMP()')->select();
    	foreach ($mLists as $key => $value) {
            $tplConf = array(
                    'id' => '',
                    'openid' => $value['openid'],
                    'data' => array(
                        'first' => '您好，您收到了一条新的众筹项目提醒',
                        'keyword1' => $binfo['borrow_name'],
                        'keyword2' => $binfo['borrow_money'],
                        'keyword3' => $binfo['borrow_money'],
                        'keyword4' => $binfo['borrow_tips'],
                        'remark' => '请及时关注，感谢您对平台的支持！',                           
                        ),
                    );
                $msg_id = M('wechat_tpl_mesage')->add(array('uid' => $value['id'], 'openid' => $tplConf['openid'], 'data' => serialize($tplConf['data'])));
                $tplConf['id'] = $msg_id;
                $rs = $this->wechatObj->sendTplMsg('RESERVE', $tplConf);
                dump($rs);
                if($rs) die;
        }
    }
    public function reserve($requestMsg)
    {
        $responseArr = array(
                'MsgType' => 'text',
                'Content' => '预约提醒开通失败！',
            );
        // $this->wechatObj = new Wechat();
        $openid = $this->wechatObj->openid;
        // $openid = 'oeuHrwYaWNbLtnOX5dIp9jVvcaqQ';
        // $openid  = 'oqhbVv72-M2pndZMXWr-QlPLY7Uk';
        // $openid = 'oqhbVv72-M2pndZMXWr-QlPLY7Uk';
        $oauth = M('oauth')->field('id,bind_uid')->where(array('openid' => $openid))->find();
        
        $uid = @$oauth['bind_uid'] ? $oauth['bind_uid'] : '0';
        $member = M('members')->field('id,user_name,notice')->find($uid);        
        if(isset($member['id'])){        	
            $endTime = strtotime("+5 day", time());
            $rs = M('members')->save(array('id' => $uid, 'notice' => 1, 'notice_end_time' => $endTime));            
            if($rs){
            	$tplConf = array(
            		'id' => '',
            		'openid' => $openid,
            		'data' => array(
                		'first' => '恭喜，您已预约成功！',
                		'keyword1' => '新标及活动提醒',
                		'keyword2' => '截止'.date('Y-m-d H:i:s', $endTime),
                		'remark' => '为正常接收提醒，无论是否接收过消息，请在过期前重新预约！',                			
            			),
            		);
            	$msg_id = M('wechat_tpl_mesage')->add(array('uid' => $uid, 'openid' => $tplConf['openid'], 'data' => serialize($tplConf['data'])));                  	         	
            	$tplConf['id'] = $msg_id;
                $rs = $this->wechatObj->sendTplMsg('RESERVE', $tplConf);
                if($rs) die;
            }            
        }else{
            $responseArr['Content'] = '您尚未绑定平台账号！'.$uid;
        }        
        return $responseArr;
    }
    public function sendTplMsg()
    {
        $wechatObj = new Wechat();
        $rs        = $wechatObj->sendTplMsg();
        dump($rs);
    }
    public function setIndustry()
    {
        $wechatObj = new Wechat();
        $Industry  = [
            'industry_id1' => '1',
            'industry_id2' => '8',
        ];
        $rs = $wechatObj->setIndustry($Industry);
        dump($rs);
    }
    public function getTemplate()
    {
        $wechatObj = new Wechat();
        $template  = [];
        $rs        = $wechatObj->getTemplate($template);
        dump($rs);
    }
    public function createMenu()
    {
        $wechatObj = new Wechat();
        $menuArr   = [
            "button" => [
	                [	
					//"type" => "view",
	                //   "name" => "魔方盒子",
	                //   "url"  => "http://koudaigou.net/f/580eee220cf295c29a3d4aac",
					'name'=> '🔥奖励',
			
	                'sub_button' => [
                            [
                                "type" => "view",
                                "name" => "军团奖励🔥",
                                "url"  => "https://www.mofangche.com/public/default/Home/images/ofw/mflhb/mflhb.html",
                            ],
							[
                                "type" => "view",
                                "name" => "收益计算器",
                                "url"  => "https://www.mofangche.com/public/default/Home/images/ofw/syjsq/syjsq.html",
                            ],
                            [
                                "type" => "view",
                                "name" => "魔方盒子",
                                "url"  => "http://koudaigou.net/f/580eee220cf295c29a3d4aac",
                            ],
							[
                                "type" => "view",
                                "name" => "魔方宝典",
                                "url"  => "http://mp.weixin.qq.com/mp/homepage?__biz=MzIxODQxMzIyNg==&hid=5&sn=da9004a37f01eb1171a9338cae174517#wechat_redirect",
                            ],
							[
                                "type" => "view",
                                "name" => "线下充值渠道",
                                "url"  => "http://mp.weixin.qq.com/s/AvNHTPYVOeZFc5ZuPlN-Yg",
                            ]
	                	],
                	],
                	[
	                    "type" => "view",
	                    "name" => "我要投资",
	                    "url"  => "https://m.mofangche.com/",

	                ],
	                [
						"type" => "click",
	                    "name" => "预约✅",
	                    "key"  => "RESERVE",
                	],
                ],
        	];
        $rs = $wechatObj->createMenu($menuArr);
        dump($rs);
    }
}
