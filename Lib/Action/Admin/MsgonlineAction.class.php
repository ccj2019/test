<?php
// 全局设置
class MsgonlineAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		$msgconfig = FS("Webconfig/msgconfig");	
	    $uid = $msgconfig['sms']['user'];
	    $pwd = $msgconfig['sms']['pass'];    
	    
	    $postData = array(
	        'account' => $msgconfig['sms']['user'],
	        'password' => $msgconfig['sms']['pass'],	        
	        );    
	    $rs =  url_request($postData,'http://www.jianzhou.sh.cn/JianzhouSMSWSServer/http/getUserInfo'); 
	    $rsArr = xmlToArray($rs);
	    $d = intval(@$rsArr['remainFee']).'条';	    

		$this->assign('d',$d);
		$this->assign('stmp_config',$msgconfig['stmp']);
		$this->assign('sms_config',$msgconfig['sms']);
        $this->display();
    }
    public function save()
    {
		FS("msgconfig",$_POST['msg'],"Webconfig/");
		$this->success("操作成功",__URL__."/index/");
    }
	
	
    public function templet()
    {
		$emailTxt = FS("Webconfig/emailtxt");
		$smsTxt = FS("Webconfig/smstxt");
		$msgTxt = FS("Webconfig/msgtxt");
		$this->assign('emailTxt',de_xie($emailTxt));
		$this->assign('smsTxt',de_xie($smsTxt));
		$this->assign('msgTxt',de_xie($msgTxt));
        $this->display();
    }
	
    public function templetsave()
    {
		FS("emailtxt",$_POST['email'],"Webconfig/");
		FS("smstxt",$_POST['sms'],"Webconfig/");
		FS("msgtxt",$_POST['msg'],"Webconfig/");
		$this->success("操作成功",__URL__."/templet/");
    }
}
?>
