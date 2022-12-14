<?php

class AppAction extends Action
{

    function _initialize(){
        $allow_origin = array(
            'houtai.rzmwzc.com',
            'rzmwzc.com',
        );
        //跨域访问的时候才会存在此字段
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        if (in_array($origin, $allow_origin)) {

            header('Access-Control-Allow-Origin:' . $origin);
        }

        header('Access-Control-Allow-Origin:*');//允许跨域,不限域名

        header("Access-Control-Allow-Methods:POST,GET,OPTIONS");

        header('Access-Control-Allow-Headers: Content-Type'); // 设置允许自定义请求头的字段

        header('Access-Control-Allow-Credentials:true');
    }


    public function __construct()
    {
        parent::__construct();
        
        header("Content-Type:text/html;charset=utf-8");
    }

    public function applogin()
    {
        $reslut ="/index.html#/usercenter/wx_login_result?";// "https://" . $_SERVER['SERVER_NAME'] . 

        $data['access_token'] = $_REQUEST['access_token'];
        $data['openid'] = $_REQUEST['openid'];

        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$data['access_token']."&openid=".$data['openid'];

        $res = $this->curl($url);

        if( !empty($res['unionid']) && !empty($res['openid']) ){
            $unionid = $res['unionid'];
            $openid = $res['openid'];

            $test_user = M('members')->where(array('unionid'=>$unionid))->find();
            if(empty($test_user)){
            /*
                $data['appopenid'] = $openid;
                $data['unionid'] = $unionid;
                $data['reg_time'] = time();
                $data['reg_ip'] = get_client_ip();
                $data['lastlog_time'] = time();
                $data['lastlog_ip'] = get_client_ip();
                $data['incode'] = getincode();

                $newid = M('members')->add($data);
                $vo = M('members_status')->where("uid={$newid}")->find();
                if(!$vo){
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
                }

                M('member_info')->add(array(

                    "uid" => $newid,

                )); 
                */
               // if($newid){
                 
                    $jsons["status"] = "0";

                    $jsons["openid"] = $res['openid'];

                    $jsons["txtUser"] = $res['nickname'];

                    $jsons["userpic"] = $res['headimgurl'];

                    $jsons["msg"] = "您没有绑定微信！";

                    $jsons["reslut"] = $reslut.urldecode(http_build_query($jsons));
                // }else{
                //     $jsons["status"] = "0";

                //     $jsons["openid"] = $res['openid'];

                //     $jsons["txtUser"] = $res['nickname'];

                //     $jsons["userpic"] = $res['headimgurl'];

                //     $jsons["msg"] = "注册失败";

                //     $jsons["reslut"] = $reslut.urldecode(http_build_query($jsons));
                    
                // }
                outJson($jsons);
            }else{
                if(empty($test_user['appopenid'])){
                    M('members')->where(array('unionid'=>$unionid))->save(array('appopenid'=>$openid));
                }

                $up['uid'] = $test_user['id'];

                $up['add_time'] = time();

                $up['ip'] = get_client_ip();

                M('member_login')->add($up);

                $jsons['data']['uid'] = text($test_user['id']);

                $jsons['data']['user_name'] = text($test_user['user_name']);
    
                $jsons['data']['user_type'] = text($test_user['user_type']);
    
                $jsons['data']['user_phone'] = $test_user['user_phone'];
    
                $jsons['data']["is_changepin"] = $test_user['pin_pass'] ? 1 : 0;
    
                $jsons['data']["openid"] = $test_user['appopenid'];
    
                $jsons['data']["userpic"] = $test_user['userpic'];
    
                $jsons["status"] = "1";
    
                $jsons["openid"] = $res['openid'];
    
                $jsons["msg"] = "登录成功！";

                $jsons["reslut"] = $reslut.urldecode(http_build_query($jsons));
            }
           
            outJson($jsons);
        }else{

            $jsons["status"] = $res['errcode'];

            $jsons["openid"] = $res['openid'];

            $jsons["txtUser"] = $res['nickname'];

            $jsons["userpic"] = $res['headimgurl'];

            $jsons["msg"] = $res['errmsg'];

            $jsons["reslut"] = $reslut.urldecode(http_build_query($jsons));

            outJson($jsons);
        }
    }

    public function curl($url)
    {
        $curl = curl_init(); // 启动一个CURL会话

        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_HEADER, 0);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在

        $res = curl_exec($curl);     //返回api的json对象

        //关闭URL请求

        curl_close($res);

        return json_decode($res,true);
    }
}