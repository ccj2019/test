<?php
// 本类由系统自动生成，仅供测试用途
class BaomingAction extends WCommonAction {	
	
	public function index(){
		$this->display();
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
