<?php
// 本类由系统自动生成，仅供测试用途
class WechatactivityAction extends Action {

    public function index(){
        if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            header("Location:/Wapmember/Wechatactivity/requestOpenId");
        }
    }

    public function requestOpenId(){
        //if(empty($_SESSION['u_user_name'])){
            $globalz=M('global')->select();
            foreach($globalz as $k=>$v){
                $global[$v['code']]=$v['text'];
            }
            $appid=$global['weixinappid'];//"wx0a01a5ed7857bad7";
            $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=";
            $url.=$appid;
            $url.="&redirect_uri=";
            $url.="https://".$_SERVER['SERVER_NAME']."/Wapmember/Wechatactivity/getOpenId";
            $url.="&response_type=code";
            $url.="&scope=snsapi_base";
            $url.="&state="."123";
            $url.="#wechat_redirect";
            header("Location:$url") ;
        //}

        //参数	是否必须	说明
        //appid	是	公众号的唯一标识
        //redirect_uri	是	授权后重定向的回调链接地址， 请使用 urlEncode 对链接进行处理
        //response_type	是	返回类型，请填写code
        //scope	是	应用授权作用域，snsapi_base （不弹出授权页面，直接跳转，只能获取用户openid），snsapi_userinfo （弹出授权页面，可通过openid拿到昵称、性别、所在地。并且， 即使在未关注的情况下，只要用户授权，也能获取其信息 ）
        //state	否	重定向后会带上state参数，开发者可以填写a-zA-Z0-9的参数值，最多128字节
    }

    public function getOpenId(){

        //第二步    
        $globalz=M('global')->select();
        foreach($globalz as $k=>$v){
            $global[$v['code']]=$v['text'];
        }
        //获取token
        $appid=$global['weixinappid'];//"wx0a01a5ed7857bad7";
        $secret=$global['weixinsecret'];//"4d5be00eaa31ca639f5078b04f20a177";
        //参数	是否必须	说明
        //appid	是	公众号的唯一标识
        //secret	是	公众号的appsecret
        //code	是	填写第一步获取的code参数
        //grant_type	是	填写为 authorization_code
        $code = $_GET['code'];
        $get_token_url='https://api.weixin.qq.com/sns/oauth2/access_token?appid=';
        $get_token_url.=$appid;
        $get_token_url.='&secret=';
        $get_token_url.=$secret;
        $get_token_url.='&code=';
        $get_token_url.=$code;
        $get_token_url.='&grant_type=authorization_code';
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $get_token_url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
        $res = curl_exec($curl);     //返回api的json对象

        //关闭URL请求
        curl_close($res);
        $json_obj=json_decode($res,true);
        $access_token=$json_obj['access_token'];
        $openid=$json_obj['openid'];
        $get_user_info_url='https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $get_user_info_url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
        $res = curl_exec($curl);     //返回api的json对象
        //关闭URL请求
        curl_close($res);
        //解析json 
        $user_obj=json_decode($res,true);
        $data=array();
        if(isset($_SESSION['openid']) && !empty($_SESSION['openid'])){
            $data["openid"] = $_SESSION['openid'];
        }else{
            $_SESSION['openid'] =$user_obj['openid'];
            $data["openid"]=$user_obj['openid'];
        }
        $members=M('members')->where($data)->find();
        if(!empty($members["id"])){
            $uid=$members["id"];
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

                if(intval($_POST['Keep'])>0){
                    $time = intval($_POST['Keep'])*3600*24;
                    $loginconfig = FS("Webconfig/loginconfig");
                    $cookie_key = substr(md5($loginconfig['cookie']['key'].$uid),14,10);
                    $cookie_val = $this->_authcode($uid,'ENCODE',$loginconfig['cookie']['key']);
                    cookie("UKey",$cookie_val,$time);
                    cookie("Ukey2",$cookie_key,$time);
                }
            }
//            echo "<script type='text/javascript'>";
//            echo "alert('登录成功！');";
//            echo "window.location.href='/wap';";
//            echo "</script>";die;
        }else{
//            echo "<script type='text/javascript'>";
//            echo "window.location.href='/wapmember/common/weixinregister.html?openid=".$user_obj['openid']."&nickname=".$user_obj['nickname']."'";
//            echo "</script>";die;
        }
        header("Location:/Wapmember/Wechatactivity/wechatActivity");

        //返回
        //参数	描述"oFfm3w74SULva92NNPGfQiOIi3l0
        //openid	用户的唯一标识
        //nickname	用户昵称
        //sex	用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
        //province	用户个人资料填写的省份
        //city	普通用户个人资料填写的城市
        //country	国家，如中国为CN
        //headimgurl	用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。
        //privilege	用户特权信息，json 数组，如微信沃卡用户为（chinaunicom）
        //unionid	只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
    }

    public function giftLog(){
        if(!empty($_SESSION['openid'])) {
            M("wechat_giftlog")->add(array(
                "open_id" => $_SESSION["openid"],
                "gift_type" => $_POST["gift_type"],
                "quantity" => $_POST["quantity"],
                "add_time" => time()
            ));
        }
    }

    public function credits(){
        $time = $_POST["time"];
        $returnArray = Array();
        $returnArray["time"] = $time;
        $type = 2;
        if(!empty($_SESSION['openid'])){
            $where = null;
            $where['openid'] = array('eq',$_SESSION['openid']);
            $list = M('members')->where($where)->find();
            if(empty($list)){
                $returnArray["message"] = "openid不存在";
            }else{
                $l = M('wechat_giftlog')->where($where)->find();
                if(empty($l)){
                    memberCreditsLog($list["id"], 13, 2000, "微信活动积分");
                    M("wechat_giftlog")->add(array(
                        "open_id"=>$_SESSION["openid"],
                        "gift_type"=>"0",
                        "quantity"=>2000,
                        "add_time"=>time()
                    ));
                    $returnArray["message"] = "领取积分成功";
                    $type = 1;
                }else{
                    $returnArray["message"] = "领取积分失败";
                }
            }
        }else{
            $returnArray["message"] = "openid不存在";
        }
        ajaxmsg($returnArray,$type);

    }

    public function wechatActivity(){
        $this->assign("openid",$_SESSION['openid']);
        if(isset($_SESSION['invite']) && !empty($_SESSION['invite'])){
            $this->assign("invite",$_SESSION['invite']);
            session("tmp_invite_user",$_SESSION['invite']);
        }
        $this->display();
    }

    public function canLottery(){
        $time = $_POST["time"];
        $returnArray = Array();
        $returnArray["time"] = $time;
        $returnArray["canLottery"] = 0;
        $returnArray["canLotteryExperience"] = 0;
        $returnArray["openid"] = $_SESSION['openid'];
        $type = 2;
        if(!empty($_SESSION['openid'])){
            $where = null;
            $where['openid'] = array('eq',$_SESSION['openid']);
            $list = M('members')->where($where)->find();
            if(empty($list)){
                $returnArray["canLotteryExperience"] = 1;
            }
            $where = null;
            $where['open_id'] = array('eq',$_SESSION['openid']);
            $list = M('wechat_giftlog')->where($where)->find();
            if(empty($list)){
                $returnArray["message"] = "您未抽奖";
                $returnArray["canLottery"] = 1;
                $type = 1;
            }else{
                $returnArray["message"] = "您已抽奖";
            }
        }else{
            $returnArray["message"] = "openid不存在";
        }
        ajaxmsg($returnArray,$type);
    }

    public function visitStatistics(){
        if(!empty($_SESSION['openid'])){
            $record = M("wechat_statistics")->where("open_id = '" . $_SESSION['openid'] . "' and add_time = " . $_POST["addtime"])->find();

            if(empty($record)) {
                M("wechat_statistics")->add(array(
                    "open_id" => $_SESSION["openid"],
                    "residence_time" => 10,
                    "add_time" => $_POST["addtime"],
                    "update_time" => $_POST["addtime"]
                ));
            }else{
                M("wechat_statistics")->where("open_id = '".$_SESSION['openid'] . "' and add_time = " . $_POST["addtime"])->save(array(
                    "residence_time" => $record["residence_time"] + 10,
                    "update_time" => time()
                ));
            }
        }
    }

    public function  winningList (){
        $time = $_POST["time"];
        $pre     = C('DB_PREFIX');
        $count = M("wechat_giftlog")->where("open_id <> ''")->count();
        $list = M("wechat_giftlog wg")->field("wg.id,wg.open_id,wg.gift_type,wg.quantity,wg.add_time,m.user_phone")->join($pre."members m on wg.open_id = m.openid")->where("wg.open_id <> ''")->order("wg.add_time desc")->limit("3")->select();
        $returnArray["time"] = $time;
        $returnArray["list"] = $list;
        $returnArray["count"] = $count;
        $type = 1;
        ajaxmsg($returnArray,$type);
    }

    public function memberloginout(){
        $vo = array("id","user_name");
        foreach($vo as $v){
            session("u_{$v}",NULL);
        }
        cookie("Ukey",NULL);
        cookie("Ukey2",NULL);
    }
}
