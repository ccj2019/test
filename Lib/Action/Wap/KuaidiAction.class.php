<?php

/**
 * 快递100接口
 * Class KuaidiAction
 */
class KuaidiAction extends Action
{
    public function __construct()
    {
        header('Access-Control-Allow-Origin:*');//允许跨域,不限域名

        header("Access-Control-Allow-Methods:POST,GET,OPTIONS");

        header('Access-Control-Allow-Headers: Content-Type'); // 设置允许自定义请求头的字段

        header('Access-Control-Allow-Credentials:true');
    }

    private function api($customer,$key,$com,$num)
    {
        //参数设置
        $post_data = array();
        $post_data["customer"] = $customer;//我方分配给贵司的的公司编号, 请在企业管理后台查看
        $key= $key ;//签名， 用于验证身份， 按param + key + customer 的顺序进行MD5加密（注意加密后字符串一定要转大写）， 不需要加上“+”号， 如{"com": "yuantong", "num": "500306190180", "from": "广东省深圳市", "to": "北京市朝阳区"}xxxxxxxxxxxxyyyyyyyyyyy yyyyyyyyyyyyyyyyyyyyy
        //由其他字段拼接
        //      com:查询的快递公司的编码， 一律用小写字母
        //      num:查询的快递单号， 单号的最大长度是32个字符
        $post_data["param"] = '{"com":"'.$com.'","num":"'.$num.'"}';

        $url='http://poll.kuaidi100.com/poll/query.do';
        $post_data["sign"] = md5($post_data["param"].$key.$post_data["customer"]);
        $post_data["sign"] = strtoupper($post_data["sign"]);
        $o="";
        foreach ($post_data as $k=>$v)
        {
            $o.= "$k=".urlencode($v)."&";		//默认UTF-8编码格式
        }
        $post_data=substr($o,0,-1);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $result = curl_exec($ch);
        $data = str_replace("\"",'"',$result );
        $data = json_decode($data,true);

        return $data;
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

    /**
     * 快递查询
     * @return mixed|string|string[]
     */

    public function select()
    {
        $customer = '84E32BF0E51946626F6C8D017DB8D2C5';
        $key = "vsykpEsY267";

        $com = $this->curl($_REQUEST['num'])['auto'][0]['comCode'];   // com:查询的快递公司的编码， 一律用小写字母
        $num = $_REQUEST['num'];   // num:查询的快递单号， 单号的最大长度是32个字符
		
        $res =  $this->api($customer,$key,$com,$num);

//        var_dump($res);
//        return $res;
        echo json_encode($res);
        #outJson($res);
    }
}
?>