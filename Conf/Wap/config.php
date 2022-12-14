<?php
return array(
	//'配置项'=>'配置值'
	 
	'LAYOUT_ON'=>true,
    'LAYOUT_NAME'=> 'layout',

    'TMPL_ACTION_ERROR' =>str_replace("\\", '/', substr(str_replace('\\Conf\\', '/', dirname(dirname(__FILE__))),0,-8))."/Style/tip/mtip.html",//操作错误提示
	'TMPL_ACTION_SUCCESS' =>str_replace("\\", '/', substr(str_replace('\\Conf\\', '/', dirname(dirname(__FILE__))),0,-8))."/Style/tip/mtip.html",//操作正确提示

	'WECHAT' => array(
		'APPID' => 'wxf31bf85a31069f00',
		'APPSECRET' => '00a3be482c1e9dd732ad358f2a475726',
		),
	'WECHAT' => array(
		'APPID' => 'wxf31bf85a31069f00',
		'APPSECRET' => '6b1b3f38a3c140e70722e6ffa737e871',
		),
	// //测试
	// 'WECHAT' => array(
	// 	'APPID' => 'wxef3bbdf4ed9439c5',
	// 	'APPSECRET' => '7294422104de6147d818a09f5b4587fa',
	// 	),
);
