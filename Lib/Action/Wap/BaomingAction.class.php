<?php
// 本类由系统自动生成，仅供测试用途
class BaomingAction extends WCommonAction {	
	
	public function index(){
		$this->display();
	}
	public function dopiliang(){
	    $this->dodabao($_REQUEST['p']);
    }
	public function dodabao($page){
        include './ApiAction.class.php';
        $api=new ApiAction();
        $globalz = M('global')->select();
        foreach ($globalz as $k => $v) {
            $global[$v['code']] = $v['text'];
        }
        $datass['appId'] = $global['appid'];
        $datass['appKey'] = $global['appkey'];
        $size = 500;
        $Lsql = $page * $size . ',' . $size;
        /* 获取长效令牌 */
        $token = $api->yunhetong_login("https://api.yunhetong.com/api/auth/login", $datass['appId'], $datass['appKey']);
        $url='https://api.yunhetong.com/api/download/batch/contract/api';
        $newlist=M('borrow_investor')->field('contractid')->where('contractid!=0')->limit($Lsql)->select();
//        var_dump(M('borrow_investor')->getLastSql());
//    var_dump($Lsql);exit;
//        $narr='[';
//        foreach ($newlist as $k=>$item) {
//
//            if($k==0){
//                $narr.=$item['contractid'];
//            }else{
//                $narr=$narr.','.$item['contractid'];
//            }
//        }
//        $narr.=']';
        $narr=array();
        foreach ($newlist as $k=>$item) {
            $narr[]=$item['contractid'];
        }

        $data['appId']= $datass['appId'];
        $data['token']=$token;
        $data['contractIdList']=$narr;
        $data=json_encode($data);
       //var_dump($data);exit;
        writeLog($data);
        $curl5 = curl_init();
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
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "token: $token"
            ),
        ));
        $response5 = curl_exec($curl5);
        $err5 = curl_error($curl5);
        curl_close($curl5);
        var_dump($response5);
    }
    public function dodbcx(){
        include './ApiAction.class.php';
        $api=new ApiAction();
        $globalz = M('global')->select();
        foreach ($globalz as $k => $v) {
            $global[$v['code']] = $v['text'];
        }
        $datass['appId'] = $global['appid'];
        $datass['appKey'] = $global['appkey'];
        /* 获取长效令牌 */
        $token = $api->yunhetong_login("https://api.yunhetong.com/api/auth/login", $datass['appId'], $datass['appKey']);
        $url='https://api.yunhetong.com/api/download/batch/check/'.$_REQUEST['id'];
//        $newlist=M('borrow_investor')->field('contractid')->where('contractid!=0')->select();
//        $narr=array();
//        foreach ($newlist as $item) {
//            $narr[]=$item['contractid'];
//        }
//        $data['appId']= $datass['appId'];
//        $data['token']=$token;
         $data['id']=$_REQUEST['id'];
         $data=json_encode($data);
//        var_dump($_REQUEST);
        $curl5 = curl_init();
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
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "token: $token"
            ),
        ));
        $response5 = curl_exec($curl5);
        $err5 = curl_error($curl5);
        curl_close($curl5);
        var_dump($response5);
    }

    public function save(){
    	$_POST["bid"]=1;

    	$map["bid"]=$_POST["bid"];
    	$map["name"]=$_POST["name"];
    	$map["phone"]=$_POST["phone"];

        //var_dump($_POST);die;

        if($_POST['name']==''){
            echo "<script language='javascript'type='text/javascript'>"; 
            echo "alert('请填写您的真实姓名！');"; 
            echo "window.location.href='/wap/baoming';";
            echo "</script>"; die;
        }
        if($_POST['phone']==''){
            echo "<script language='javascript'type='text/javascript'>"; 
            echo "alert('联系方式不能为空！');"; 
            echo "window.location.href='/wap/baoming';";
            echo "</script>"; die;
        }
        if($_POST['wxid']==''){
            echo "<script language='javascript'type='text/javascript'>"; 
            echo "alert('微信id不能为空！');"; 
            echo "window.location.href='/wap/baoming';";
            echo "</script>"; die;
        }
        if($_POST['idcard']==''){
            echo "<script language='javascript'type='text/javascript'>"; 
            echo "alert('身份证号不能空！');"; 
            echo "window.location.href='/wap/baoming';";
            echo "</script>"; die;
        }

    	$bm=M("baomingx")->where($map)->find();
    	// var_dump( $_POST);
    	// var_dump( M("baomingx")->getlastsql());exit();
    	$url="https://www.rzmwzc.com";
    	if($bm){
    			echo "<script language='javascript'type='text/javascript'>"; 
				echo "alert('您已经报名过了！');"; 
				echo "window.location.href='$url'"; 
				echo "</script>"; die;
    	}else{
    		$_POST["time"]=time();
	    	$newid = M("baomingx")->add($_POST);
	    	//var_dump( M("baomingx")->getlastsql());exit();
	    	
	    	if($newid){
	    		echo "<script language='javascript'type='text/javascript'>"; 
				echo "alert('报名成功！');"; 
				echo "window.location.href='$url'"; 
				echo "</script>"; die;
	    	}else{
	    		echo "<script language='javascript'type='text/javascript'>"; 
				echo "alert('报名失败！');"; 
				echo "window.location.href='$url'"; 
				echo "</script>"; die;
	    	}
    	}

    }
    public function test(){
       $permoney  = 118.33 / 10000;
       $permoney += 1;
       $affect_money = 30068.00 * $permoney;
       echo $affect_money."<br />";
        echo bcsub($affect_money, 30068.00, 2);
    }
}
