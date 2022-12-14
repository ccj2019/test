<?php
// 本类由系统自动生成，仅供测试用途
class IndexsAction extends WCommonAction {
    public function css(){
       
        $private_key = '-----BEGIN PRIVATE KEY-----
MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAKqa10NeM49Zl85n
KekDNMzTJGnb4EXh4Rrml4ukBPSa6JCJxjDExnmOcJ4YOsahJyrhE5BXGRvRk/6A
oMX+sc6ZRNWZybv0R3qLSg9gtj1WUchpXO2J4EwROZtxBkgouBMnyA3ikzrSFt6b
tO8VRcRLv+DVjjJJPwq9tbjhtqnxAgMBAAECgYBkDlPZatIKbJ0frFlkE0FQLzTX
8OBjm8oNoNeYSiWt33wt6N+XkpBkkilyTvuJqtHxz/dNXAeZzRxi0sV32ZwlfYtO
hT7kbdMGnodku6+3f7WRWLkLBak/i3wcW+s+M4o860FpdgSjnSUd8/TQwV+q72vl
BD3IOLwnWlZpvwv3wQJBANSAOFCDrNZzAu7axF3C/iDD2MKgpScLMGELVLbX1aQH
j/f3FU5F1P+S6Yrbdpr/10K9bGEFPomvBrpdy5Cjp1UCQQDNhx/8lufJ4rezXB2s
ajvvo7jVh21mZpBSFakPwdd4xxn+1VXh3nNsof0NGemA9RfNGagqc9J7kgo0OB3E
qUAtAkEAqEy4zUPMYrgKTaRkO6JQ5SBXj6Xtx2N8OI/h00JUCSqYIprFfA2gqZ3w
a7JvWElicpBVwu2FX0SI/peEvxRuVQJAc0GjnnLB6WVKvzqZKWcp1Wlq7dPUdehu
ZpnfBQcfXovI+C+Kye+FqxXsYrx1RewsPMh2ldf94W40arRNfUuswQJAPerX3Ut6
umCnbezbBD3bLHzpNjqlVBH0rFOy3Ga6tdETMPNuV4nwH9Ci/RLkHaSOqDGoOhvD
vQ0bmeebOcjaBw==
-----END PRIVATE KEY-----';
         
        $public_key = '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCqmtdDXjOPWZfOZynpAzTM0yRp
2+BF4eEa5peLpAT0muiQicYwxMZ5jnCeGDrGoScq4ROQVxkb0ZP+gKDF/rHOmUTV
mcm79Ed6i0oPYLY9VlHIaVztieBMETmbcQZIKLgTJ8gN4pM60hbem7TvFUXES7/g
1Y4yST8KvbW44bap8QIDAQAB
-----END PUBLIC KEY-----';
         
        //echo $private_key;
        $pi_key =  openssl_pkey_get_private($private_key);//这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id
        $pu_key = openssl_pkey_get_public($public_key);//这个函数可用来判断公钥是否是可用的
      
        $data = "123";//原始数据
        $encrypted = "";
        $decrypted = "";
         
        echo "source data:",$data,"\n";
         
        echo "private key encrypt:\n";
         
        openssl_private_encrypt($data,$encrypted,$pi_key);//私钥加密
        
        $encrypted = base64_encode($encrypted);//加密后的内容通常含有特殊字符，需要编码转换下，在网络间通过url传输时要注意base64编码是否是url安全的
        echo $encrypted,"\n";
         
        echo "public key decrypt:\n";
         
        openssl_public_decrypt(base64_decode($encrypted),$decrypted,$pu_key);//私钥加密的内容通过公钥可用解密出来
        echo $decrypted,"\n";
         
        echo "---------------------------------------\n";
        echo "public key encrypt:\n";
         
        openssl_public_encrypt($data,$encrypted,$pu_key);//公钥加密
        $encrypted = base64_encode($encrypted);
        echo $encrypted,"\n";
         
        echo "private key decrypt:\n";
        openssl_private_decrypt(base64_decode($encrypted),$decrypted,$pi_key);//私钥解密
        echo $decrypted,"\n";

    }
    public function yhqtx(){
        //发送
        //$map["b.end_time"]=array("lt",time()+86400);"$timestamp0,$timestamp24"
        $map["b.end_time"]=array("between",time().",".(time()+86400));
        $map["b.tixing"]='1';
        $map["b.status"]="1";
        $gqlist = M('members m')->field('m.id,m.user_name,m.user_phone,b.end_time,b.id as bid')->join('lzh_member_bonus b on m.id=b.uid')->where($map)->select();
        foreach ($gqlist as $k => $v) {
           notice1("12",$v["id"],date("Y-m-d H:i:s",$v["end_time"]));
           $mmp["id"]=$v["bid"];
           $dat["tixing"]="2";
           $list=$member_bonus->where($mmp)->save($dat);
        }
        var_dump(M('members m')->getlastsql());
        var_dump($vo);
        // //更新以前的
        // $member_bonus=M("member_bonus");
        // $dat["tixing"]="2";
        // $mmp["end_time"]=array("lt",time());
        // $mmp["status"]=array("neq","2");
        // $list=$member_bonus->where($mmp)->save($dat);
        // var_dump($member_bonus->getlastsql());
    }
    public function qiaaan(){
        $mmp["i.signerid"]= array('exp','is null');
        $mmp["m.signerid"]= array('exp','is not null');
        $mes = M('member_info i')->field('m.id,m.signerid as ms,i.signerid as iss')->join('lzh_members m on m.id=i.uid')->where($mmp)->order("i.uid desc")->select();
        foreach ($mes as $k => $v) {
            $dat["signerid"]=$v["ms"];
            M("member_info")->where("uid=".$v['id'])->save($dat);
        }
        var_dump($mes);
    }
    public function jisuan($data,$yue){

    }
    public function getrqi($rqi,$ms){
        $dats=strtotime("+{$ms} month ".$rqi);
        $dated=date('d',strtotime($rqi));
        if(date("d",$dats)!=$dated){
            $y=date("Y",$dats);
            $m= date("m",$dats)-1<10 ? "0".(date("m",$dats)-1) : date("m",$dats)-1;
            $lastDay=date('t',strtotime($y."-".$m));
            return strtotime($y."-".$m."-".$lastDay);
        }else{
            return strtotime(date("Y-m-d",$dats));
        }
    }
    public function huikuan(){
       
        if($_REQUEST["time"]==''){
            $time=time();
        }else{
            $time=strtotime($_REQUEST["time"]);
        }
        $this->assign("time",$time);
        $tzmap['a.borrow_status']  =array("in","6,7");
        $tzlist = m("borrow_info")->alias('a')->field("a.borrow_name,a.hkday,a.lead_time,a.borrow_duration,a.total,a.id")->where($tzmap)->group("a.id")->select();;
        $dt2=explode('-',date('y-m-d',$time))[1];
        //var_dump($dt2);
        foreach ($tzlist as $k => $val) {
            if($val["total"]>1){
                if(!empty($val["hkday"])){
                    $yue=$val["hkday"];
                    $dt=date('Y-n-j',$val['lead_time']);
                    for ($i=0; $i < $val["total"]; $i++) { 
                            $stime=strtotime("$dt+".$yue*$i."days");
                            $stime=date('Y-n-j',$stime);
                            $tday=date('Y-n-j',$time);
                            if($stime==$tday){
                                $hktime[]=$val['id'];
                                $val['qi']=$i+1;
                                $list[]=$val;
                            }
                    }
                }else{
                        $yue=$val["borrow_duration"]/$val["total"];
                        $dt=date('Y-n-j',$val['lead_time']);
                        for ($i=0; $i < $val["total"]; $i++) {
                            if (in_array($val["id"], array(2102, 2798, 2933,3459,3463))) {
                                $stime = strtotime("last day of +" . $yue * $i . " month", strtotime($dt));
                            }else if($dt2=='2' && in_array($val["id"], array(1613,1704,1872,1962,2102,2368,2667,2668,2929,2994,2993,3112,3110,3166,3240,3314,3388,3460,3458))){
                                $stime = strtotime("last day of +" . $yue * $i . " month", strtotime($dt));
                            }else if(in_array($val["id"], array(3458))){
                                if($i==0){
                                    $stime = strtotime("last day of +" . $yue * $i . " month", strtotime($dt));
                                }else{
                                    $stime =strtotime("$dt+" . $yue * $i . "month")+86400;
                                }
                            }else{
                                $stime = strtotime("$dt+" . $yue * $i . "month");
                            }


                                $stime=date('Y-n-j',$stime);
                                $tday=date('Y-n-j',$time);
                                if($stime==$tday){
                                    $hktime[]=$val['id'];
                                    $val['qi']=$i+1;
                                    $list[]=$val;
                                }
                        }
                    }
               // }
            }else{
                $stime=date('Y-n-j',$val['lead_time']);
                $tday=date('Y-n-j',$time);
                if($stime==$tday){
                    $hktime[]=$val['id'];
                    $val['qi']=1;
                    $list[]=$val;
                }
            }
           
        }
        $this->assign("tzlist",$list);
        $this->display();
    }
    public function hktu(){
        $id=$_REQUEST['id'];
        $qi=$_REQUEST['qi'];
        $binfo=M("borrow_info")->where('id='.$id)->find();
        $list=M('borrow_investor b')->field('b.add_time,b.investor_capital,m.user_name')->join('lzh_members m on b.investor_uid=m.id')->where(array('b.borrow_id'=>$binfo['id']))->select();
        $qishu=true;
        foreach ($list as $k=> $v) {
            if($binfo['total']>$qi){
                $list[$k]['lixi']= number_format(bcmul($v['investor_capital'],$binfo['borrow_interest_rate']/$binfo['total']/100,2), 2);
                $qishu=false;
            }else{
                $list[$k]['lixi']=number_format(bcmul($v['investor_capital'],$binfo['borrow_interest_rate']/$binfo['total']/100,2)+$v['investor_capital'], 2);
            }
            $stlen=$this->fcnsublen($v['user_name']);
            if($stlen==1){
                $list[$k]['yh']=$v['user_name'].'****';
            }else if($stlen==11){
                $list[$k]['yh']=$this->fcnsubstr($v['user_name'],3,0).$this->fcnsubstr($v['user_name'],2,9,false);
            }else{
                $list[$k]['yh']=$this->fcnsubstr($v['user_name'],1,0).'*';
            }
            $list[$k]['investor_capital']=number_format($v['investor_capital'], 2);
        }

        $this->assign('qi',$qi);
        $this->assign('binfo',$binfo);
        $this->assign('time',time());
        $this->assign('qishu',$qishu);
        $this->assign('list',$list);
        $this->display();
    }
    function fcnsubstr($str, $length, $start = 0, $suffix = true, $charset = "utf-8")
    {
        $str = strip_tags($str);
        if (function_exists("mb_substr")) {
            if (mb_strlen($str, $charset) <= $length) {
                return $str;
            }
            $slice = mb_substr($str, $start, $length, $charset);
        } else {
            $re['utf-8'] = "/[\x01-]|[ - ][ - ]|[ - ][ - ]{2}|[ - ][ - ]{3}/";
            $re['gb2312'] = "/[\x01-]|[ - ][ - ]/";
            $re['gbk'] = "/[\x01-]|[ - ][@- ]/";
            $re['big5'] = "/[\x01-]|[ - ]([@-~]| - ])/";
            preg_match_all($re[$charset], $str, $match);
            if (count($match[0]) <= $length) {
                return $str;
            }
            $slice = join("", array_slice($match[0], $start, $length));
        }
        if ($suffix) {
            return  $slice."***";
        }else{
            return  "***".$slice;
        }
        return $slice;
    }

    function fcnsublen($str,$charset = "utf-8")
    {
        $str = strip_tags($str);
        if (function_exists("mb_substr")) {
            return mb_strlen($str);
        } else {
            $re['utf-8'] = "/[\x01-]|[ - ][ - ]|[ - ][ - ]{2}|[ - ][ - ]{3}/";
            $re['gb2312'] = "/[\x01-]|[ - ][ - ]/";
            $re['gbk'] = "/[\x01-]|[ - ][@- ]/";
            $re['big5'] = "/[\x01-]|[ - ]([@-~]| - ])/";
            preg_match_all($re[$charset], $str, $match);
            return count($match[0]);
        }
    }



    public function huikuantu(){

        if($_REQUEST["time"]==''){
            $time=time();
        }else{
            $time=strtotime($_REQUEST["time"]);
        }
        $this->assign("time",$time);
        $tzmap['a.borrow_status']  =array("in","6,7");
        $tzlist = m("borrow_info")->alias('a')->field("a.borrow_name,a.hkday,a.lead_time,a.borrow_duration,a.total,a.id")->where($tzmap)->group("a.id")->select();;
        foreach ($tzlist as $k => $val) {
            if($val["total"]>1){
                if(!empty($val["hkday"])){
                    $yue=$val["hkday"];
                    $dt=date('Y-n-j',$val['lead_time']);
                    for ($i=0; $i < $val["total"]; $i++) {
                        $stime=strtotime("$dt+".$yue*$i."days");
                        $stime=date('Y-n-j',$stime);
                        $tday=date('Y-n-j',$time);
                        if($stime==$tday){
                            $hktime[]=$val['id'];
                            $val['qi']=$i+1;
                            $list[]=$val;
                        }
                    }

                }else{
                    $yue=$val["borrow_duration"]/$val["total"];
                    $dt=date('Y-n-j',$val['lead_time']);
                    for ($i=0; $i < $val["total"]; $i++) {
                        $stime=strtotime("$dt+".$yue*$i."month");
                        if($stime>1612579704&&in_array($val["id"],array(1613,1704,1872,1962))&&$stime<1614649147){
                            $stime= strtotime("last day of +".$yue*$i." month", strtotime($dt)); //strtotime("$dt+".$i."month");
                        }
                        if (in_array($val["id"], array(2102,2798,2933))) {
                            $stime= strtotime("last day of +".$yue*$i." month", strtotime($dt)); //strtotime("$dt+".$i."month");
                        }

                        if ($stime > 1644979704 && in_array($val["id"], array(2368,2667,2668)) && $stime < 1646275704) {
                            $stime = strtotime("last day of +" . $yue * $i . " month", strtotime($dt)); //strtotime("$dt+".$i."month");
                        }

                        if ($stime > 1677379704 && in_array($val["id"], array(2929)) && $stime < 1677898104) {
                            $stime = strtotime("last day of +" . $yue * $i . " month", strtotime($dt)); //strtotime("$dt+".$i."month");
                        }

                        $stime=date('Y-n-j',$stime);
                        $tday=date('Y-n-j',$time);
                        if($stime==$tday){
                            $hktime[]=$val['id'];
                            $val['qi']=$i+1;
                            $list[]=$val;
                        }
                    }
                }
            }else{
                $stime=date('Y-n-j',$val['lead_time']);
                $tday=date('Y-n-j',$time);
                if($stime==$tday){
                    $hktime[]=$val['id'];
                    $val['qi']=1;
                    $list[]=$val;
                }
            }

        }
        $this->assign("tzlist",$list);
        $this->display();
    }

    public function infos(){
        $money= M('members m')->field('m.user_name,mi.real_name') ->join("lzh_member_info mi ON m.id=mi.uid")->join("lzh_borrow_investor i ON m.id=i.investor_uid")->where("i.borrow_id !=1")->group("m.id") ->select();
    }
    public function cxxs(){
        $data["name"]="asfasfsdfadf";
        $data["ordernums"]="s11ad123zd131zd4";
        $data["money"]="10";
        $data["pid"]="2";
        $a=$this->curl_post("https://houtai.rzmwzc.com/phpsdk/example/native.php",$data);
       
        var_dump($a);
        $a=substr($a,1,strlen($a)-2);
        
        

        $this->assign("url2",$a);
        $this->display();
    }

    /**
     * 发起HTTPS请求
     */
     function curl_post($url,$data,$header,$post=1)
     {
       //初始化curl
       $ch = curl_init();
       //参数设置  
       $res= curl_setopt ($ch, CURLOPT_URL,$url);
       curl_setopt($ch, CURLOPT_POST, $post);

       curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
       curl_setopt ($ch, CURLOPT_HEADER, 0);
      

       if($post)
       curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
       curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
       $result = curl_exec ($ch);
       curl_close($ch);
       return $result;
    } 
    public function cs()
    {   
        var_dump(intval(5.00));
         $url="https://www.rzmwzc.com/wap/article/index/cid/37.html";

         $url=urlencode($url);
         var_dump($url);
         $url=urldecode($url);
         var_dump($url);
        
         exit();

//        $appid=$global['weixinappid'];//"wx0a01a5ed7857bad7";
//        $secret=$global['weixinsecret'];//"4d5be00eaa31ca639f5078b04f20a177";
        Vendor('JSSDK');
//        $Llpayconfig = new Llpayconfig();
//        $test = new Test();

        $jssdk = new JSSDK("wx0a01a5ed7857bad7","4d5be00eaa31ca639f5078b04f20a177");

        $signPackage = $jssdk->GetSignPackage();
       // var_dump($_SERVER['HTTP_HOST']);
        $this->assign('signPackage',$signPackage);

//         echo $_SERVER['HTTP_HOST'];//当前请求的 Host: 头部的内容 即域名信信息

// echo $_SERVER['PHP_SELF'];//当前正在执行脚本的文件相对网站根目录地址，就算该文件被其他文件引用也可以正确得到地址

 

// echo $_SERVER['SCRIPT_NAME'];//当前正在执行脚本的文件相对网站根目录地址，但当该文件被其他文件引用时，只显示引用文件的相对地址，不显示该被引用脚本的相对地址。

 

// echo $_SERVER['DOCUMENT_ROOT'];//网站相对服务器地址即网站的绝对路径名 #当前运行脚本所在的文档根目录。在服务器配置文件中定义

 

// echo $_SERVER['SCRIPT_FILENAME'];//当前执行脚本的绝对路径名
//echo $_SERVER['DOCUMENT_ROOT'].'/public/uploadsd/'.date('YmdHis').'.jpg';
        $this->display();
    }

    public function xiazai(){
        Vendor('JSSDK');
        //要生成的图片名字
        $targetName ='/Public/uploadsd/'.date('YmdHis').'.jpg';
        $jssdk = new JSSDK("wx0a01a5ed7857bad7","4d5be00eaa31ca639f5078b04f20a177");
        $accessToken = $jssdk->getAccessToken();

        $serverId = $_REQUEST["serverId"];
        //根据微信JS接口上传了图片,会返回上面写的images.serverId（即media_id），填在下面即可
        $str="http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$accessToken}&media_id={$serverId}";
        $a = file_get_contents($str);

        //以读写方式打开一个文件，若没有，则自动创建
        $resource = fopen( $_SERVER['DOCUMENT_ROOT'].$targetName , 'wb');
        //将图片内容写入上述新建的文件
        fwrite($resource, $a);
        //关闭资源
        fclose($resource);

        $jsons["info"] = $_SERVER['HTTP_HOST'].$targetName;
        $jsons["status"]= "1";
        outJson($jsons);
    }
    public function dorenzheng(){
        import("ORG.Io.Excel");
        include './../RenzhengAction.class.php';
        $list=M("member_info i")->field('i.uid,i.real_name,i.idcard')->join("lzh_members_status s on i.uid = s.uid")->where("s.id_status=1")->select();
         // var_dump(M("member_info i")->getlastsql());exit();
        $renzheng = new RenzhengAction();
        $jgls=array();
        foreach ($list as $k => $v) {
            $res = $renzheng->main($v["real_name"],$v["idcard"]);
            if($res["status"]=="0"){
                $jgls[]=$v;
            }        
        }

        $type = C('MONEY_LOG');
        $row=array();
        $row[0]=array('uid','真实姓名','身份号');
        $i=1;
        foreach($jgls as $v){
                $row[$i]['uid'] = $v["uid"];
                $row[$i]['real_name'] = $v['real_name'];
                $row[$i]['idcard'] = $v['idcard'];
                $i++;
        }
        //$kfinfo["name"].'从'.$_REQUEST['start_time'].'到'.$_REQUEST['end_time']
        $xls = new Excel_XML('UTF-8', false,"datalist");
        $xls->addArray($row);
        $xls->generateXML("未投资");
    }
    // $accessToken = $this->getAccessToken();
    public function index(){
   		include './../RenzhengAction.class.php';
     

        $realname = "晨晨";
        $idcard   ="371121199001090419";
        $renzheng = new RenzhengAction();
        $res = $renzheng->main($realname,$idcard);
        if($res["status"]=="0"){
          outJson($res);

        }
         outJson($res);
        exit();
		$Bconfig = C("BORROW");
		$per = C('DB_PREFIX');
		$curl = $_SERVER['REQUEST_URI'];
 	if(is_mobile()==1){
    				 
	 
			echo "<script type='text/javascript'>";

     	echo "window.location.href='/wap';";
			echo "</script>";die;
			// echo $_SERVER['PHP_SELF']; #/PHP/XX.php
		}
		//预热项目
		$pre = C('DB_PREFIX');
		  
        $searchMaps['borrow_status']= array("in",'1,2');
		// $searchMaps['start_time']= array("gt",time());     
		$searchMaps['pid'] = array('neq',4);
        $parm['limit'] = 3;
        $parm['map'] = $searchMaps;
        $parm['orderby']="b.id desc";
        
        $listProduct_yr = getBorrowList($parm);  
        foreach ($listProduct_yr['list'] as $key => $vo) {
            $listProduct_yr['list'][$key]['progress'] = getFloatValue($vo['has_borrow'] / $vo['borrow_money'] * 100, 4);
        }
        $this->assign("listProduct_yr",$listProduct_yr);

        //成功项目
        unset($searchMaps['start_time']);
        $searchMaps['borrow_status']= array("in",'6');  
        $parm['limit'] = 3;
        $parm['map'] = $searchMaps;
         $parm['orderby']="b.start_time desc"; 
        $listProduct_suc = getBorrowList($parm);  
        $this->assign("listProduct_suc",$listProduct_suc);
		$glo = array('web_title'=>'我要投资');
    	$this->assign($glo);	

		
		//商城
		$_REQUEST["pid"]=$_REQUEST["pid"]==0?"0":$_REQUEST["pid"];
		$_REQUEST["jifen"]=$_REQUEST["jifen"]==0?"0":$_REQUEST["jifen"];
		$map=array();
		$map['art_set'] = 0;
		if($_REQUEST["type_id"]!=0){
			$map["type_id"]=$_REQUEST["type_id"];	
		} 

		//分页处理
		$field= '*';
		$list['list'] = M('market')->where($map)->limit(3)->order("id DESC")->select();
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);


		
		
		//文章
		
      $parmarticle = array(
        'type_id'=> 21,
        'pagesize'=> 7
        );  
        $article['article']['name'] = $nowCategory['type_name'];
        $article['list'] = getArticleList($parmarticle);
        // var_dump($tpl_var['list']['list']); 
      

        $this->assign('article',$article['list']); 
		$this->assign('articlerightList',$article['rightList']);

	    $this->display(); 
		


		/****************************募集期内标未满,自动流标 新增 2013-03-13****************************/
        //流标返回
        $mapT                  = array();
        $mapT['collect_time']  = array("lt", time());
        $mapT['borrow_status'] = 2;
        $tlist                 = M("borrow_info")->field("id,borrow_uid,borrow_type,borrow_money,first_verify_time,borrow_interest_rate,borrow_duration,repayment_type,collect_day,collect_time")->where($mapT)->select();
        if (empty($tlist)) {
            exit;
        }

        foreach ($tlist as $key => $vbx) {
            $borrow_id = $vbx['id'];
            //流标
            $done           = false;
            $borrowInvestor = D('borrow_investor');
            $binfo          = M("borrow_info")->field("borrow_type,borrow_money,borrow_uid,borrow_duration,repayment_type")->find($borrow_id);
            $investorList   = $borrowInvestor->field('id,investor_uid,investor_capital')->where("borrow_id={$borrow_id}")->select();
            M('investor_detail')->where("borrow_id={$borrow_id}")->delete();
            if ($binfo['borrow_type'] == 1) {
                $limit_credit = memberLimitLog($binfo['borrow_uid'], 12, ($binfo['borrow_money']), $info = "{$binfo['id']}号标流标");
            }
			
            //返回额度
            $borrowInvestor->startTrans();

            $bstatus       = 3;
            $upborrow_info = M('borrow_info')->where("id={$borrow_id}")->setField("borrow_status", $bstatus);
            //处理借款概要
            $buname = M('members')->getFieldById($binfo['borrow_uid'], 'user_name');
            //处理借款概要
            if (is_array($investorList)) {
                $upsummary_res = M('borrow_investor')->where("borrow_id={$borrow_id}")->setField("status", $type);
                foreach ($investorList as $v) {
                    MTip('chk15', $v['investor_uid']); //sss
                    $accountMoney_investor        = M("member_money")->field(true)->find($v['investor_uid']);
                    $datamoney_x['uid']           = $v['investor_uid'];
                    $datamoney_x['type']          = ($type == 3) ? 16 : 8;
                    $datamoney_x['affect_money']  = $v['investor_capital'];
                    $datamoney_x['account_money'] = ($accountMoney_investor['account_money'] + $datamoney_x['affect_money']); //投标不成功返回充值资金池
                    $datamoney_x['collect_money'] = $accountMoney_investor['money_collect'];
                    $datamoney_x['freeze_money']  = $accountMoney_investor['money_freeze'] - $datamoney_x['affect_money'];
                    $datamoney_x['back_money']    = $accountMoney_investor['back_money'];

                    //会员帐户
                    $mmoney_x['money_freeze']  = $datamoney_x['freeze_money'];
                    $mmoney_x['money_collect'] = $datamoney_x['collect_money'];
                    $mmoney_x['account_money'] = $datamoney_x['account_money'];
                    $mmoney_x['back_money']    = $datamoney_x['back_money'];

                    //会员帐户
                    $_xstr                       = ($type == 3) ? "复审未通过" : "募集期内标未满,流标";
                    $datamoney_x['info']         = "第{$borrow_id}号标" . $_xstr . "，返回冻结资金";
                    $datamoney_x['add_time']     = time();
                    $datamoney_x['add_ip']       = get_client_ip();
                    $datamoney_x['target_uid']   = $binfo['borrow_uid'];
                    $datamoney_x['target_uname'] = $buname;
                    $moneynewid_x                = M('member_moneylog')->add($datamoney_x);
                    if ($moneynewid_x) {
                        $bxid = M('member_money')->where("uid={$datamoney_x['uid']}")->save($mmoney_x);
                    }

                }
            } else {
                $moneynewid_x  = true;
                $bxid          = true;
                $upsummary_res = true;
            }

            if ($moneynewid_x && $upsummary_res && $bxid && $upborrow_info) {
                $done = true;
                $borrowInvestor->commit();
            } else {
                $borrowInvestor->rollback();
            }
            if (!$done) {
                continue;
            }
	
            MTip('chk11', $vbx['borrow_uid'], $borrow_id);
            $verify_info['borrow_id']     = $borrow_id;
            $verify_info['deal_info_2']   = text($_POST['deal_info_2']);
            $verify_info['deal_user_2']   = 0;
            $verify_info['deal_time_2']   = time();
            $verify_info['deal_status_2'] = 3;
            if ($vbx['first_verify_time'] > 0) {
                M('borrow_verify')->save($verify_info);
            } else {
                M('borrow_verify')->add($verify_info);
            }

            $vss = M("members")->field("user_phone,user_name")->where("id = {$vbx['borrow_uid']}")->find();
            SMStip("refuse", $vss['user_phone'], array("#USERANEM#", "ID"), array($vss['user_name'], $verify_info['borrow_id']));
            //@SMStip("refuse",$vss['user_phone'],array("#USERANEM#","ID"),array($vss['user_name'],$verify_info['borrow_id']));
            //updateBinfo
            $newBinfo                       = array();
            $newBinfo['id']                 = $borrow_id;
            $newBinfo['borrow_status']      = 3;
            $newBinfo['second_verify_time'] = time();
            $x                              = M("borrow_info")->save($newBinfo);
        }
        /****************************募集期内标未满,自动流标 新增 2013-03-13****************************/
    }
    public function test(){
		if(empty($_GET['p'])){
			$_GET['p']=0;
		}else{
			$_GET['p']=$_GET['p']-1;
		}
       	  $invitation = M('members')->field("id,incode")  ->limit($_GET['p'],100) ->select();
		  foreach($invitation as $k=>$v){
			   $add["incode"]=getincode();
			   var_dump($add["incode"],M('members')->where("id=".$v["id"]) ->save($add));
		  }
    }
 
    public function islogin(){
        $uid = $_SESSION[$session_id];
        if(!empty($uid)){
            writelog($uid);
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
                // session($session_id, '');  
                ajaxmsg(); 
                exit();
            }else{
                ajaxmsg("",0);
            }
        }
    }
    public function chaphone(){
        $p=$_GET['p'];
        $minfo = M('members')->where("user_phone='{$p}'")->field("user_name,user_phone")->find();
        var_dump($minfo);
    }

    //赠品售卖列表
    public function xylist(){
         $yzm=$_REQUEST['yz'];
         if($yzm!='xyj6g6'){
             $this->error('验证失败',__APP__.'/Index');
         }
        $this->assign('yzm',$yzm);

        import('ORG.Util.Page');
        $order = "zp.add_time desc";
        $map['zp.type']='3';
        //$map['zp.smstatus']='1';
        $map['zp.zmnum']=array('neq',0);
        //$map['zp.add_time']=array('gt',time()-C('zxtime')*86400);
        $files="zp.*,z.zxprice,z.price,z.title,z.guige,z.images,z.content,m.yhname,mi.user_img,m.user_name";
        //模糊搜索
        $name = $_REQUEST['name'];
        if (!empty($name)) {
            $map['z.title'] = array(
                'like',
                '%' . $name . '%'
            );
        }

        $count = m('zporder zp')->field($files)->where($map)
            ->join('lzh_members m on zp.uid=m.id')
            ->join('lzh_member_info mi on zp.uid=mi.uid')->
            join("lzh_zeng_pin z ON zp.zpid=z.id")->count();
        $num = 25;
        $page = new Page($count,$num);
        foreach($getval as $key=>$val) {
            $page->parameter .= "$key=".urlencode($val).'&';
        }
        $order = "zp.add_time desc";
        $show = $page->show();
        $limit = $page->firstRow.','.$page->listRows;

        $list = m('zporder zp')->field($files)->where($map)
            ->join('lzh_members m on zp.uid=m.id')
            ->join('lzh_member_info mi on zp.uid=mi.uid')->
            join("lzh_zeng_pin z ON zp.zpid=z.id")->order($order)->limit($limit)->select();

        //$list = m('borrow_info')->where($map)->order($order)->limit($limit)->select();
        foreach ($list as $k => $v) {

        }
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->assign('name',$name);

        $this->display();
    }

}
