<?php
class WeixinAction extends HCommonAction {
	public function login(){
		$AppID = 'wx0a01a5ed7857bad7';
        $AppSecret = '4d5be00eaa31ca639f5078b04f20a177';
        $callback  =  'http://'.$_SERVER['HTTP_HOST'].'/weixin/loginbk.html'; //回调地址
        //微信登录
        session_start();
        //-------生成唯一随机串防CSRF攻击
        $state  = md5(uniqid(rand(), TRUE));
        $callback = urlencode($callback);
        $wxurl = "https://open.weixin.qq.com/connect/qrconnect?appid=".$AppID."&redirect_uri={$callback}&response_type=code&scope=snsapi_login&state={$state}#wechat_redirect";
        header("Location: $wxurl");
	}
	
}
?>