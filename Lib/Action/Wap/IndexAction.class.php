<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends Action {	
    function __construct(){
        parent::__construct();
    }

    public function chaxun(){
        if(!empty($_REQUEST['phone'])){
            $map['phone'] =$_REQUEST['phone'];
            $this->assign("phone", $_REQUEST['phone']);
        }else{
            $map['id']='abc';
        }
        $field="y.*,z.images,z.title";
        $list=M("yundan y")->join('lzh_zeng_pin z on y.zpid =z.id')->field($field)->where($map)->order('fhtime desc')->select();
        foreach ($list as $k => $v) {
            $list[$k]["images"] = unserialize($v["images"])[0]['img'];
        }
        $this->assign("list",$list);
	    $this->display();
    }
    public function getwuliu(){
        $n=$_REQUEST['n'];
        $p=$_REQUEST['p'];
        $map["y.phone"]=$p;
        $map["y.yundan"]=$n;
        $info=M("yundan y")->join('lzh_zeng_pin z on y.zpid =z.id')->where($map)->order('fhtime desc')->find();//M("yundan")->where($map)->find();
        $info["images"] = unserialize($info["images"])[0]['img'];
        $info["zhuangtai"] =C('ztstatus')[$info["status"]];
        $this->assign("info",$info);

        $res=$this->getwl($n,$p,$info['wuliu']);
        if($res['showapi_res_body']['ret_code']==0){
            $list=$res['showapi_res_body']['data'];
        }else{
            $list=array();
        }
        $this->assign("expTextName",$res['showapi_res_body']['expTextName']);
        $this->assign("mailNo",$res['showapi_res_body']['mailNo']);
        $this->assign("list",$list);
        if(empty($res['showapi_res_body']['mailNo'])){
                 echo "<script type='text/javascript'>";
                 echo "alert('信息错误请联系客服');";
                 echo "window.location.href='" . getenv("HTTP_REFERER") . "';";
                 echo "</script>";
                 exit();
        }
        $this->display();
    }
    public function getwl($n,$p,$name) {
        $host = "https://ali-deliver.showapi.com";
        $path = "/showapi_expInfo";
        $method = "GET";
        $appcode = "9892fc444b1247369f80a499ab1868d9";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        //$com=$this->getSimpleName($name);
        //$com=$this->getkdbh($name);
        $com= c('yundanh')[$name];
        if(empty($com)){
            $com='auto';
        }
        $querys = "com=".$com."&nu=" . $n . "&receiverPhone=receiverPhone&senderPhone=senderPhone&phone=".$p;
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
        return $data;
    }
    public function getSimpleName($name) {
        $host = "https://ali-deliver.showapi.com";
        $path = "/showapi_expressList";
        $method = "GET";
        $appcode = "9892fc444b1247369f80a499ab1868d9";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        //expName=%E9%9F%B5%E8%BE%BE%E9%80%9F%E9%80%92&maxSize=20&page=1
        $querys ="expName=".urlencode($name);//"com=auto&nu=" . $num . "&receiverPhone=receiverPhone&senderPhone=senderPhone&phone=".$phone;
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
        $name=$data['showapi_res_body']['expressList'][0]['simpleName'];
        return $name;
    }

   
}
