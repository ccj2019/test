<?php

/**
 * 快递100接口
 * Class KuaidiAction
 */
class ZhuanyiAction extends Action
{
    public function __construct()
    {
        header('Access-Control-Allow-Origin:*');//允许跨域,不限域名

        header("Access-Control-Allow-Methods:POST,GET,OPTIONS");

        header('Access-Control-Allow-Headers: Content-Type'); // 设置允许自定义请求头的字段

        header('Access-Control-Allow-Credentials:true');

        //var_dump(base64_decode($aa));
    }
    public function huoqu(){
//        Vendor('JSSDK');
//        $jssdk = new JSSDK("wx0a01a5ed7857bad7","4d5be00eaa31ca639f5078b04f20a177");
//        $access_token=$jssdk->getAccessToken();
//
////        $appId="wx275dca4b41d86791";
////        $appSecret="e4da953fffa77005e74ce472330d58f8";
////        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appId."&secret=".$appSecret;
////     //   var_dump($url);
////
////        $res = json_decode($this->httpGet($url));
////        //pre($res);
////        $access_token = $res->access_token;
//
//        $get_token_url="https://api.weixin.qq.com/cgi-bin/user/get?access_token={$access_token}";
//        $res=$this->httpGet($get_token_url);

        $res = json_decode(file_get_contents("opid.txt"),true);
        //var_dump($res);
       // $res=json_decode($res,true);

        $res=$res["data"]["openid"];
        $bb=array();
        $i=0;$j=0;
        foreach ($res as $k=>$v) {
            $bb[$j][]=$v;
            if($i%99==0&&$i!=0){
                $j++;
            }
            $i++;
        }
        //$appId="wx275dca4b41d86791";
//        $appSecret="e4da953fffa77005e74ce472330d58f8";
//        $nurl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appId."&secret=".$appSecret;
//        $nres = json_decode($this->httpCurl($nurl));
//        $naccess_token = $res->access_token;

        $postd["from_appid"]="wx0a01a5ed7857bad7";
        $purl="http://api.weixin.qq.com/cgi-bin/changeopenid?access_token=".$naccess_token;
        $rrb=0;
        foreach ($bb as $kb => $vb) {
            $postd["openid_list"]=$vb;
            $zyres=$this->httpCurl($purl);
            $zyres=json_decode($zyres,true);
            if($zyres['errmsg']=='ok'){
                foreach ($zyres['result_list'] as $zb=>$zv) {
                    if($zv['err_msg']=='ok'){
                       $rrs=M("members")->where(array("openid",$zv['ori_openid']))->save(array("openid",$zv['new_openid']));
                       if($rrs){
                            $rrb++;
                       }
                    }
                }
            }else{
                var_dump($zyres["errmsg"]);
            }
           // pre($vb);
        }
        //var_dump($rrb);
        //pre($bb);

//        $appId="wx275dca4b41d86791";
//        $appSecret="e4da953fffa77005e74ce472330d58f8";
//        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appId."&secret=".$appSecret;
//        $nres = json_decode($this->httpCurl($nres));
//        $access_token = $res->access_token;


       // pre($bb);
//        $list=M("members")->field("id,openid")->select();
//        foreach ($list as $k=>$v) {
//            if(in_array($v["openid"],$res)){
//                M("members")->where(array("openid"=>$v))->find();
//            }
//        }
       // pre($res);
        //$res = $this->httpGet($get_token_url);
    }
    public function xhuoqu()
    {
        Vendor('JSSDK');
        $jssdk = new JSSDK("wx0a01a5ed7857bad7", "4d5be00eaa31ca639f5078b04f20a177");
        $access_token = $jssdk->getAccessToken();

//        $appId="wx275dca4b41d86791";
//        $appSecret="e4da953fffa77005e74ce472330d58f8";
//        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appId."&secret=".$appSecret;
//     //   var_dump($url);
//        $res = json_decode($this->httpGet($url));
//        //pre($res);
//        $access_token = $res->access_token;

        $get_token_url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token={$access_token}";
        $res = $this->httpGet($get_token_url);

        //$res = json_decode(file_get_contents("opid.txt"), true);
        pre($res);
        $res = json_decode($res, true);
        pre($res);

    }
    private function httpCurl($url,$data) {
        //$headers = array('Content-Type: application/x-www-form-urlencoded');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if($data){
            curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data)); // Post提交的数据包
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_URL, $url);
        //curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $res = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);//捕抓异常
        }
        curl_close($curl);
        return $res;
    }


    public function jiami(){
        $aa=array("ab"=>"1212","ad"=>array("aa"=>"1111","bb"=>"123123"));
        $aa=json_encode($aa);
        var_dump($aa);
        $aa=$this->encrypt($aa,"1231");
        var_dump($aa);
        $aa=$this->decrypt($aa,"1231");
        var_dump($aa);
        var_dump(json_decode($aa,true));
    }
    //加密函数
    public function encrypt($data, $key)
    {
        $key    =    md5($key);
        $x        =    0;
        $len    =    strlen($data);
        $l        =    strlen($key);
        for ($i = 0; $i < $len; $i++)
        {
            if ($x == $l)
            {
                $x = 0;
            }
            $char .= $key{$x};
            $x++;
        }
        for ($i = 0; $i < $len; $i++)
        {
            $str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);
        }
        return base64_encode($str);
    }
    //解密
    public  function decrypt($data, $key)
    {
        $key = md5($key);
        $x = 0;
        $data = base64_decode($data);
        $len = strlen($data);
        $l = strlen($key);
        for ($i = 0; $i < $len; $i++)
        {
            if ($x == $l)
            {
                $x = 0;
            }
            $char .= substr($key, $x, 1);
            $x++;
        }
        for ($i = 0; $i < $len; $i++)
        {
            if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1)))
            {
                $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
            }
            else
            {
                $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
            }
        }
        return $str;
    }


    private function curl($num)
    {
//        $num = $_REQUEST['num'];
        $url = "http://www.kuaidi100.com/autonumber/autoComNum?text=".$num;
        $curl = curl_init(); // 启动一个CURL会话

        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_HEADER, 0);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在

        $res = curl_exec($curl);     //返回api的json对象
        //关闭URL请求

        curl_close($curl);

        return json_decode($res,true);
    }
    public function xrwj(){
        $myfile = fopen("m/newfile.txt", "a") or die("Unable to open file!");//w覆盖/a追加
        $txt = "Bill Gates\n";
        fwrite($myfile, $txt);
        $txt = "Steve Jobs\n";
        fwrite($myfile, $txt);
        fclose($myfile);
    }
}
?>