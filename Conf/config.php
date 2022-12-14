<?php
return array(
	//'配置项'=>'配置值'
    'APP_GROUP_LIST'    => 'Home,Admin,Wap,Member,Wapmember,Api',//分组
    'DEFAULT_GROUP'     => DEFAULT_GROUP,//默认分组
    'DEFAULT_THEME'     => 'default',//使用的模板
	/*'LAYOUT_ON'=>true,
    'LAYOUT_NAME'=> 'layout',*/
    'LOAD_EXT_CONFIG' => 'borrow', // 加载扩展配置文件
	'TMPL_DETECT_THEME' => true, // 自动侦测模板主题
	'LANG_SWITCH_ON'	=>true,//开启语言包
    'URL_MODEL'=>1, // 如果你的环境不支持PATHINFO 请设置为3,设置为2时配合放在项目入口文件一起的rewrite规则实现省略index.php/
	'URL_CASE_INSENSITIVE'=>true,//关闭大小写为true.忽略地址大小写
    'TMPL_CACHE_ON'    => false,        // 是否开启模板编译缓存,设为false则每次都会重新编译
    'TMPL_STRIP_SPACE'      => false,       // 是否去除模板文件里面的html空格与换行
	'APP_ROOT'=>str_replace(array('\\','Conf','config.php','//'), array('/','/','','/'), dirname(__FILE__)),//APP目录物理路径
	'WEB_ROOT'=>str_replace("\\", '/', substr(str_replace('\\Conf\\', '/', dirname(__FILE__)),0,-11)),//网站根目录物理路径
	'WEB_URL'=>"http://".$_SERVER['HTTP_HOST'],//网站域名
	'CUR_URI'=>$_SERVER['REQUEST_URI'],//当前地址
	'URL_HTML_SUFFIX'=>".html",//静态文件后缀
	'TMPL_ACTION_ERROR' =>str_replace("\\", '/', substr(str_replace('\\Conf\\', '/', dirname(__FILE__)),0,-11))."/Style/tip/tip.html",//操作错误提示
	'TMPL_ACTION_SUCCESS' =>str_replace("\\", '/', substr(str_replace('\\Conf\\', '/', dirname(__FILE__)),0,-11))."/Style/tip/tip.html",//操作正确提示
	'ERROR_PAGE'	=>'/Public/error.html',
	//cookie
	'COOKIE_PREFIX'	=>'lzh_',
	'COOKIE_PATH'	=>'/',
	'COOKIE_DOMAIN'	=>'tiannongtuan.com',
	//cookie
	//数据库配置
	'DB_TYPE'           => 'mysql',
	'DB_HOST'           => '127.0.0.1',
	'DB_NAME'           =>'houtai_rzmwzc',				//数据库名称
	'DB_USER'           =>'root',				//数据库用户名
	'DB_PWD'            =>'root',				//数据库密码
	'DB_PORT'           =>'3306',
	'DB_PREFIX'         =>'lzh_',


//    'DB_TYPE' => 'pdo',
//    'DB_USER' => 'root',
//    'DB_PWD' => 'root',
//    'DB_DSN' => 'mysql:host=localhost;dbname=houtai_rzmwzc;charset=UTF8',
//    'DB_PREFIX' => 'lzh_',

	//测试站
//	 'DB_TYPE'           => 'mysql',
//	 'DB_HOST'           => 'rm-m5eo916xipx48w74wio.mysql.rds.aliyuncs.com',
//	 'DB_NAME'           =>'ceshi_rzmwzc',				//数据库名称
//	 'DB_USER'           =>'ceshi_rzmwzc',				//数据库用户名
//	 'DB_PWD'            =>'Ab123456',				//数据库密码
//	 'DB_PORT'           =>'3306',
//	 'DB_PREFIX'         =>'lzh_',

     'DB_TYPE'           => 'mysql',
     'DB_HOST'           => 'rm-m5eo916xipx48w74wio.mysql.rds.aliyuncs.com',
     'DB_NAME'           =>'chonggou_rzmwzc',				//数据库名称
 // 'DB_NAME'           =>'ceshi_rzmwzc',
     'DB_USER'           =>'mw2021',				//数据库用户名
     'DB_PWD'            =>'mw2021@xyj',				//数据库密码
     'DB_PORT'           =>'3306',
     'DB_PREFIX'         =>'lzh_',

	//正式站测试
	// 'DB_TYPE'           => 'mysql',
	// 'DB_HOST'           => '127.0.0.1',
	// 'DB_NAME'           =>'zsz_ceshi',				//数据库名称
	// 'DB_USER'           =>'root',				//数据库用户名
	// 'DB_PWD'            =>'root',				//数据库密码
	// 'DB_PORT'           =>'3306',
	// 'DB_PREFIX'         =>'lzh_',

	//数据库配置


	 'DB_TYPE'           => 'mysql',
	 'DB_HOST'           => 'rm-m5eo916xipx48w74wio.mysql.rds.aliyuncs.com',
	 'DB_NAME'           =>'rzmwzc',				//数据库名称
	 'DB_USER'           =>'rzmwzc_2019',				//数据库用户名
	 'DB_PWD'            =>'LIUhao2018',				//数据库密码
	 'DB_PORT'           =>'3306',
	 'DB_PREFIX'         =>'lzh_',



	//'DB_PARAMS'			=>array('persist'=>true),
	//数据库配置
	//子域名配置
	'APP_NAME'=>'互联网金融系统',
	'APP_VERSION'=>'',
	'URL_ROUTER_ON'		=>true,//开启路由规则
	'URL_ROUTE_RULES'	=>array(
		'/^([a|A])dmin\/index\/login(.*)$/' => '/Public/error.html',//屏蔽真实后台路径
		'/^([a|A])dmin(\/?)$/' => '/Public/error.html',//屏蔽真实后台路径
		'/^'.ADMIN_PATH.'(\/?)$/' => 'Admin/index/login',//后台路径
		// '/^aaa\/sss.html$/' => 'Admin/index/login/',//后台路径
		'/^borrow\/post\/([a-zA-z]+)\.html$/' => 'Home/borrow/post?type=:1',//文章栏目页
		// '/^invest\/index.html\?(.*)$/' => 'Home/invest/index?:1',//文章栏目页
		'/^invest\/(\d+).html$/' => 'Home/invest/detail?id=:1',//文章栏目页
		'/^invest\/(\d+).html\?(.*)$/' => 'Home/invest/detail?id=:1:2',//文章栏目页
		/*//'/^shishicaiwu\/finanz.html/' => 'Home/tool/finanz',//实时财务
		'/^tuiguang\/index.html$/' => 'Home/help/tuiguang',//文章栏目页
		'/^ruhejieru\/index.html$/' => 'Home/help/ruhejieru',//文章栏目页
		'/^newbie\/index.html$/' => 'Home/help/newbie',//文章栏目页
		'/^service\/index.html$/' => 'Home/help/kf',//文章栏目页
		'/^borrow\/tool\/index.html$/' => 'Home/tool/index',//文章栏目页
		'/^borrow\/tool\/tool(\d+).html$/' => 'Home/tool/tool:1',//文章栏目页
		'/^borrow\/post\/([a-zA-z]+)\.html$/' => 'Home/borrow/post?type=:1',//文章栏目页
		'/^single\/(\d+).html$/' => 'Home/article/single?id=:1',//文章内容页
		'/^tools\/tool.html?(.*)$/' => 'Home/tool/index:1',//文章栏目页
		'/^tools\/tool(\d+).html?(.*)$/' => 'Home/tool/tool:1:2',//文章栏目页
		'/^([a-zA-z]+)\/([a-zA-z]+).html(.*)$/' => 'Home/help/index:3',//文章栏目页
		'/^([a-zA-z]+)\/(\d+).html$/' => 'Home/help/view?id=:2',//文章内容页
		'/^([a-zA-z]+)\/id\-(\d+).html$/' => 'Home/help/view?id=:2&type=subsite',//文章内容页
		'/^([a-zA-z]+)\/([a-zA-z]+)\/(\d+).html$/' => 'Home/help/view?id=:3',//文章内容页*/
	),
	'SYS_URL'	=>array('admin','borrow','member','Integralmall','invest','donate','tool','feedback','service','bid'),
	'EXC_URL'	=>array('invest/tool/index.html','borrow/tool/index.html','borrow/tool/tool2.html','borrow/tool/tool2.html'),
	//友情链接
    'FRIEND_LINK'=>array(
			1=>'合作伙伴',
			2=>'友情链接',
			3=>'实用工具',
			4=>'底部认证',
		),
	//友情链接
    'TYPE_SET'=>array(
			1=>'列表页',
			2=>'单页',
			3=>'跳转',
		),
	'XMEMBER_TYPE'=>array(
			1=>'普通借款者',
			2=>'优良借款者',
			3=>'风险借款者',
			4=>'黑名单',
	),
	//收费类型
    'MONEY_LOG'=>array(
    		//1=>'注册红包',
			3=>'会员充值',
			4=>'提现冻结',
			5=>'撤消提现',
			6=>'投标冻结',
			7=>'管理员操作',
			8=>'流标返还',
			9=>'投资回报',
			//10=>'网站代还',
			11=>'发放投资回报',
			12=>'提现失败',
			//13=>'推广奖励',
			15=>'投标成功',
			16=>'复审未通过返还',
			17=>'项目完成资金入帐',
			18=>'项目管理费',
			19=>'项目保证金',
			20=>'投标奖励',
			21=>'支付投标奖励',
			// 23=>'利息管理费',
			//24=>'还款完成解冻',
			27=>'鱼币购买',
			//28=>'投标成功待收利息',
			29=>'提现成功',
			//30=>'逾期罚息',
			//31=>'催收费',
			//32=>'线下充值红包',
			//33=>'续投奖励(预奖励)',222=>'智投冻结',
			//34=>'续投奖励',
			//35=>'续投奖励(取消)',
			36=>'提现通过，审核处理中',
			37=>'商品购买',
			50=>'首充值奖励',
			100 => '体验金发放',
	        101 => '体验金回收',
	        102 => '体验金投标冻结',
	        103 => '体验金投标解结',
	        104 => '体验金投标还款',

			110=>'红包投标冻结',
			120=>'加息回收',
			121=>'还款申请',
			221=>'充值返佣奖励',
            303=>'增加佣金',
            304=>'扣除佣金',
            310=>'赠品折现',
            311=>'赠品折现冻结余额',
            312=>'赠品折现解冻余额',

            313=>'(预购)购买商品',
            314=>'(预购)项目方回款',
            315=>'(预购)项目回款',
            316=>'(预购)售出商品',

            317=>'支付宝支付成功',
            327=>'微信支付成功',
            318=>'支付成功红包部分',
            319=>'购买转卖商品扣除',
            320=>'转卖商品卖出',

            321=>'商城购买转入鱼币',

		),
    'MONEY_LOGS'=>array(
    		9=>'投资回报',
    		15=>'投标成功本金解冻',
			100 => '体验金发放',
	        101 => '体验金回收',
	        102 => '体验金投标冻结',
	        103 => '体验金投标解结',
	        104 => '体验金投标还款',

			110=>'红包投标冻结',
			120=>'加息回收',
		),
	// 'BANK_NAME'=>array(
	// 		'招商银行'=>'招商银行',
	// 		'中国银行'=>'中国银行',
	// 		'中国工商银行'=>'中国工商银行',
	// 		'中国建设银行'=>'中国建设银行',
	// 		'中国农业银行'=>'中国农业银行',
	// 		'中国邮政储蓄银行'=>'中国邮政储蓄银行',
	// 		'交通银行'=>'交通银行',
	// 		'上海浦东发展银行'=>'上海浦东发展银行',
	// 		'深圳发展银行'=>'深圳发展银行',
	// 		'中国民生银行'=>'中国民生银行',
	// 		'兴业银行'=>'兴业银行',
	// 		'平安银行'=>'平安银行',
	// 		'北京银行'=>'北京银行',
	// 		'天津银行'=>'天津银行',
	// 		'上海银行'=>'上海银行',
	// 		'华夏银行'=>'华夏银行',
	// 		'光大银行'=>'光大银行',
	// 		'广发银行'=>'广发银行',
	// 		'中信银行'=>'中信银行',
	// 		'上海农商银行'=>'上海农商银行',
	// 	),
	'BANK_NAME'       => array(
            'ICBC'     => '工商银行',
            'ABC'      => '农业银行',
            'BOCSH'    => '中国银行',
            'CCB'      => '建设银行',
            'CMB'      => '招商银行',
            'SPDB'     => '浦发银行',
            'GDB'      => '广发银行',
            'BOCOM'    => '交通银行',
            'PSBC'     => '邮政储蓄银行',
            'CNCB'     => '中信银行',
            'CMBC'     => '民生银行',
            'CEB'      => '光大银行',
            'HXB'      => '华夏银行',
            'CIB'      => '兴业银行',
            'BOS'      => '上海银行',
            'SRCB'     => '上海农商',
            'PAB'      => '平安银行',
            'BCCB'     => '北京银行',
            'BOC'      => '中行（大额）',
            'BAC'      => '其它银行',    
        ),
	'REPAYMENT_TYPE'=>array(
			'1'=>'每月还款',
			'2'=>'一次性还款'
		),
	'PAYLOG_TYPE'=>array(
			'0'=>'充值未完成',
			'1'=>'充值成功',
			'2'=>'签名不符',
			'3'=>'充值失败'
		),
	'WITHDRAW_STATUS'=>array(
			'0'=>'待审核',
			'1'=>'审核通过,处理中',
			'2'=>'已提现',
			'3'=>'审核未通过'
		),
	'FEEDBACK_TYPE'=>array(
			'1'=>'借入借出',
			'2'=>'资金账户',
			'3'=>'推广奖金',
			'4'=>'充值提现',
			'5'=>'注册登录',
			'6'=>'其他问题',
			'7'=>'快速借款通道'
		),
	// 体验金
	'EXPERIENCE_MONEY' => array(
		'TYPE' => array(
	        	'1' => "注册后发放体验金",
		        '2' => "投标成功后发放体验金",
		        '3' => "充值成功后发放体验金",
		        '4' => "管理员发放体验金",
		        '5' => "邀请用户注册发放体验金",
        	),
		'STATUS' => array(
	        	'1' => '使用中',
        		'2' => '回收中',
				'3' => '回收完成',
        	),
		),
	'BONUS_TYPE' => array(
	    	'1' => "系统发放",
        	'2' => "注册成功后发放",    
        	'3' => "投标成功后发放",
'4' => "积分兑换",
		),

    //加息券
    'MEMBER_INTEREST' => array(
        'STATUS' => array(
            '0' => '已禁用',
            '1' => '未使用',
            '2' => '已使用',
            '3' => '已过期',
        ),
    ),
    //连连钱包
    'version'=>'1.1',
    'oidPartner'=>'201907180002049144',
    'signType'=>'RSA',

    'TOKEN_ON'   =>  true, //是否开启令牌验证
    'TOKEN_NAME'  =>  'token',// 令牌验证的表单隐藏字段名称
    'TOKEN_TYPE'  =>  'md5',//令牌验证哈希规则
    'TOKEN_RESET' => 'true',
    'DB_FIELDTYPE_CHECK' => 'true',


    //支付宝配置参数
	'alipay_config'=>array(
    'partner' =>'2088621180325374',   //这里是你在成功申请支付宝接口后获取到的PID；
    'key'=>'7fpbiqsboogj78jex1atmnhjd0m3j7p1',//这里是你在成功申请支付宝接口后获取到的Key
    'sign_type'=>strtoupper('MD5'),
    'input_charset'=> strtolower('utf-8'),
    'cacert'=> getcwd().'\\cacert.pem',
    'transport'=> 'http',
      ),
     //以上配置项，是从接口包中alipay.config.php 文件中复制过来，进行配置；
	    
	 'alipay'   =>array(
	 //这里是卖家的支付宝账号，也就是你申请接口时注册的支付宝账号
	 'seller_email'=>'18953308695@163.com',
	 //这里是异步通知页面url，提交到项目的Pay控制器的notifyurl方法；
	 'notify_url'=>'https://www.rzmwzc.com/Pay/notifyurl', 
	 //这里是页面跳转通知url，提交到项目的Pay控制器的returnurl方法；
	 'return_url'=>'http://www.rzmwzc.com/Pay/returnurl',
	 //支付成功跳转到的页面，我这里跳转到项目的User控制器，myorder方法，并传参payed（已支付列表）
	 'successpage'=>'http://www.rzmwzc.com/User/order?title=&typeid=2',   
	 //支付失败跳转到的页面，我这里跳转到项目的User控制器，myorder方法，并传参unpay（未支付列表）
	 'errorpage'=>'http://www.rzmwzc.com/User/order?title=&typeid=1', 
	 ),
    //积分商城订单状态
    'jforders'=>array(
        0=>'已付款',
        1=>'已发货',
        2=>'已收货',
        3=>'未付款',
        4=>'已退款',
        5=>'申请退款',
        6=>'已同意退款',
        7=>'已退货',
        8=>'驳回退款',
        9=>'已取消',
        10=>'已处理',
    ),
    'thaddress'=>array(
        'name'=>'东盛澜',
        'address'=>'山东省日照市海滨二路156号',
        'phone'=>'18888888888',
    ),
    'ztstatus'=>array(
        '0'=>'全部',
        '1'=>'待发',
        '2'=>'已发待收',
        '3'=>'已完成',
        '4'=>'申请售后',
        '5'=>'已处理',
    ),
    'zpstatus'=>array(
        '0'=>'全部',
        '1'=>'待领取',
    ),
    'shouhuo'=>15,
    'zxtime'=>3,
    'yszffs'=>array(
        '1'=>'余额+鱼币',
        '2'=>'支付宝',
    ),
    'ysostatus'=>array(
        '0'=>'未支付',
        '1'=>'已购买',
        '2'=>'已回款',
    ),
    'yzhetong'=>array(
        'TEM1024252'=>'164152205901000001',
        'TEM1024251'=>'164152205901000001',
        'TEM1024250'=>'164152205901000001',
        'TEM1024249'=>'164152205901000001',
        'TEM1024248'=>'164152205901000001',
        'TEM1024247'=>'164152205901000001',
        'TEM1024246'=>'164152205901000001',


        'TEM1017232'=>'164152555701000001',
        'TEM1020423'=>'164152208001000001',
        'TEM1021105'=>'164152210401000001',

        'TEM10220707'=>'165715647601000001',

        'TEM1024255LQ'=>'166236090101000001',

        'TEM1011406-69801-1'=>'166970030201000001',
        'TEM1011406-69802-1'=>'166970030201000001',
        'TEM1011406-66168-1'=>'166970905401000001',
        'TEM1011406-66268-1'=>'166970905401000001',

        'TEM1019999'=>'166979260101000001'






    ),
    'yundanh'=>array(
        '韵达速递'=>'yunda',
        '韵达快运'=>'ydky',
        '圆通速递'=>'yuantong',
        '顺丰速运'=>'shunfeng',
        'ems'=>'ems',
        '新顺丰（NSF）'=>'nsf',
        'D速快递'=>'dsu',
        'D速物流'=>'dsu'
    ),
     'shouhoufs'=>array(
        '1'=>'退货退款',
        '2'=>'仅退款',
        '3'=>'协商客服处理',
    ),
    'zhengpin_hqfs'=>array(
        '1'=>'常规项目',
        '2'=>'预购商品',
        '3'=>'活动赠送',
    ),

    'hfb'=>array(
        'agent_id'=>2138607,
        'key'=>'550F1E989D7142A28695E5F9',
        'gname'=>array(
            '1'=>'即食香辣海带丝',
            '2'=>'烤虾干自晒对虾干',
            '3'=>'即食海带大板',
            '4'=>'鲜活黑头鱼',
            '5'=>'新鲜龙利鱼',
            '6'=>'大海螺鲜活新鲜海捕野生贝类',
            '7'=>'麻辣油焖大虾真空包装虾零食',
            '8'=>'日照大虾鲜活海鲜南美白对虾',
            '9'=>'现烤鲜香马面鱼片',
        ),
        'sy'=>'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDZBlb73enfn9xtjiIvckyTwGfFI3Bq1H2FOPQjdzSsLKfdtR0hJ8UF7nCFwrr0/r97zWHapTboyGWOg/tbwDcdU49jTCAoLDqjFnvdzaruBJEsaDp6HII8LWB64UPSEmY8QgZYwxj2QYengfH6B5h/9jxqw4h05gLDAEa0BGuISmJM5tu1/ojUcb99a1lOZqTdUybNZcoXdJCApgy0XI4Cb1LsfCf6iD4lDFgdqh1fNuQLYRt2Qb/nvuhqMlxR2gpKgAho8Tp+jR0gtX+1m//H2iSeIHKnWo8ry4LctH7P3WpmF7pGE5O8kjnhximB6d/HaYCFryGywEroz1+O5kDRAgMBAAECggEACavgt9kRB2E90u/H5hiEUTwMf857T7Z0GMoTxqoU+cSEb1LXN0tY+lGAuBRITbtVzBIINaTB0nw+Otx2uNUzO5JGCD876mBktVB+4QC9qJ1hXRLQwtD3DJCa3WUBJ0kgv1gTpXRdzhkX3xMRVhKtNQwjj4Ay2j9HEZq3O+Xbxysw6TNwSHnWzqTIjmUEqNejEwTX3t0e8lHXSCmNV0EX2PeBt3Ia6l2HLtDQdXAckPu13NdbX7We+oedxC+myqHV494r/5oWIT1qKSObpjLwashGGjafASjdx7izCmMbMpJjmT2PRL7dUsVBvjqiOFo8+sYCF4cw+90VLDdZlEeX4QKBgQDiMzgLNdJqLgLKPX11hTtxdvcAgkzP98lhWrx94M+HrjBcV5vCZHGRp6Gy8K3RXZ2wCKFGotfjXpPsyGhdSn9Wt8pgxZR+WQYEsHLOWNSuQpF2ElucrCnzuC04W7YfLqB31+a319TKJOgMFKA1gGtwTckj9Mm+TjYzfi0CDWCzoQKBgQD1na0VrauxNxaJHMuVtX9KX5+PWzijC1iVPNwFQN9iLLRnac35Y/omJsJyAd6vj/PGSlUuVN+8DsASznYsV9OH8RYCd8YvSlJ78unFBPaN0Bohi0tqXcpY50Cs1WGGbb0xWTIs9GCruSywhzHHrQVKOflXsUlx1DdXs4RP8Od/MQKBgQCoRFG8hR0xv2hz6aoacjH2BflYd5WMX1d/BHCTc1juJbXz+3fy86VIJNs0sbWbuXhEKydN1HWkvgZsbei0/WYcrDvwIQqFstO3SUseFRahSwPKQX3E3o2Wr7tz4UVpjNXyULlgqT2x4iJ8WhuTsSQ7NqIaDU7GMog/Ze01SZ5WAQKBgFiNPHXwY1JFFi7g3tVe1kGuMAyzSrwdvxYvFvQd62utymzuTIB5dxqCJuCLpAmP0MfQzN0jZHacqssc/KYIFA+S7+h73kv2c76UWYvaujluqb0mR05V+joLRdoKJFse/XLpQZnLkX7YCDCszVm8G4gjVq4zENOlOz63TiC0LgJBAoGASqneNqXnKFKg9YZYDxOLB+4RfQmITIGP+s0z2ztvG1rrWD3cW4U3Zwi1PYHb6Nm1PA/nIFPI7XkKVPTeaUvwtud5P0OaLVMQbzMc+jSd/YqpAwbz6+I+wYy/H43bYGRv8sdnZECP03RYvwMEOoK6m46tH+fqsxnU0P69dpRaHW4=',
        'gy'=>'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA2QZW+93p35/cbY4iL3JMk8BnxSNwatR9hTj0I3c0rCyn3bUdISfFBe5whcK69P6/e81h2qU26MhljoP7W8A3HVOPY0wgKCw6oxZ73c2q7gSRLGg6ehyCPC1geuFD0hJmPEIGWMMY9kGHp4Hx+geYf/Y8asOIdOYCwwBGtARriEpiTObbtf6I1HG/fWtZTmak3VMmzWXKF3SQgKYMtFyOAm9S7Hwn+og+JQxYHaodXzbkC2EbdkG/577oajJcUdoKSoAIaPE6fo0dILV/tZv/x9okniByp1qPK8uC3LR+z91qZhe6RhOTvJI54cYpgenfx2mAha8hssBK6M9fjuZA0QIDAQAB'
    ),
    'mwkjhfb'=>array(
        'agent_id'=>2139386,
        'key'=>'308E2F2DD84C44FEA1D49DFF',
        'gname'=>array(
            '1'=>'即食香辣海带丝',
            '2'=>'烤虾干自晒对虾干',
            '3'=>'即食海带大板',
            '4'=>'鲜活黑头鱼',
            '5'=>'新鲜龙利鱼',
            '6'=>'大海螺鲜活新鲜海捕野生贝类',
            '7'=>'麻辣油焖大虾真空包装虾零食',
            '8'=>'日照大虾鲜活海鲜南美白对虾',
            '9'=>'现烤鲜香马面鱼片',
        ),
        'shsy'=>'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQC6/ydDqp5o3Qa+bLUqPO+etYn5TT5dTIkQQ/oB+quSgO/R70v9jy6jduFzSGGqTmn5N4dPfKZilDm/3VaeJh/eORDFbqig+JJETDdOIZnkEEn6YGTg8DtwMr/QmfJy+8w0Pr/IAUwbMXgUe+VCFEYRspRvGHJ2XOF1IFw7V+28aeuLWW0PkH945APaHp4L9T2iCSs9I+5bNK8LrIF2DRlx4NYqtMavBuENjDmD79dsRyTkqa4BHC/wg6BrHXLWPH4RxGI2ZjtSvKdbsubkSnLBQ6TNZN9wiqa4eRn8/7rDlODrzb6Z1Spqx4DJJ6dYINWM27Pr8N00Dt2Cl/pGjVelAgMBAAECggEANIPVng47gI2aCD51PlBwpuyqu+Wyfvcwgu3kN0wThQhK0XVXrPTaDzQiqoKIUxDEeCXdDTifbY3dDgH2AmIIjxsNl7S1DMfiI+YXngyXsFHWxMbvbbBpsN+/uLCTQzFtrrp0l5GtsvFYnMASqVUSPIQfZXfDJXR+KKuW21+dN0034ofuTlE6+gpHiraVtuqeaaJhxWKm/KBpOxJ/1YhPS1o8kdq3MjSIgsl37ovaGKjqamvkylBEMk3NolMTcDUvJh8ZoLWNWNbawXr6Jo56iBbU4N4OLkev2EOI3T3Xnwlcw1mWaKMPy2F+QyyqfqjStgPoKYFw4qwHMh/OPatnfQKBgQDAo8l0uzUMBMcrXMw5Q+ou832o22em+jAnOAP2K0HNdIsdvr+FK+q4mAKfbQyJOChh6XmYZpw2qcaLszgSUg6dUdy3FOYqHPnSKg8XltIrVBrFXEApwD+h3UwEC7U6D1zgnqy7w6jnzTwfvYIK+hxgIKDGeRmhwh2FfJ35o7X8nwKBgQD4gDgX9Kr+dYACt9mrUn3KUHOfF/8fHybcUZ/aJLHU1WhqYCUlEZ053nuyUbMEejVyVKJN58PblgV6wULPEC5jOmoOsi6Scw5vbdfHqLSzGqGFsI6Avs1f9me5sw0YYt0bX2otWqbvFYkheIslZZRi96l2RK5x3tbulskwlrCBOwKBgDHZcGU7mIOOrPeEoPhkobIaojbS5+Sms1VCwouuL+35rZI57ReKAMhZ1bvpnSfZF2IW57dPPjdLAaze6LCc+VkueN4Lk2/sZZ1D8vnYtnQt5GuT7qqfLBg3ytb1LKVkmlUp2msQO6IYUumnwYITrMoXR2N0rPRV5gvH7p1OBubjAoGAIE5K/JJKSJpt8eyE18j5oXukDGLKP/mEy8+wwGNU2x6DXJDzQ0Zu8j8CRcRpSYO5vwtRrl8bD0kJnVPSo6iu3yeQ8ign9dIPZl0ZWFOOalpj9UVmwUYM3RTjlzi30xvHMu/MlejbGunp0fgh3tK937/iwAVdyF/4ATyJG0/70lECgYEAnLAgDAjRw5GJG8NK9Zdo37j06Y59/KC+cFjB/770Lxi3x9/S0lEGw3MzVTud/1yMMgwqiOaV+jgUOe75LZPNdvzzvVEUmMgxdB7NMN4lYhRsx9QltGWxMWFz2dR8jOxl2t3Q1ew0hGtO+jHUpK0cmfXbYUEvCmYDgvty1D+0vG8=',
        'shgy'=>'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuv8nQ6qeaN0Gvmy1KjzvnrWJ+U0+XUyJEEP6AfqrkoDv0e9L/Y8uo3bhc0hhqk5p+TeHT3ymYpQ5v91WniYf3jkQxW6ooPiSREw3TiGZ5BBJ+mBk4PA7cDK/0JnycvvMND6/yAFMGzF4FHvlQhRGEbKUbxhydlzhdSBcO1ftvGnri1ltD5B/eOQD2h6eC/U9ogkrPSPuWzSvC6yBdg0ZceDWKrTGrwbhDYw5g+/XbEck5KmuARwv8IOgax1y1jx+EcRiNmY7UrynW7Lm5EpywUOkzWTfcIqmuHkZ/P+6w5Tg682+mdUqaseAySenWCDVjNuz6/DdNA7dgpf6Ro1XpQIDAQAB',
        'hfbgy'=>'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA8NHm2ZHWN/C2Frs3qFyLvXO1dzq6GuXHK0lRzKoIrZWReuJpNUIXY6ZxIk4Tj91W1JLF7D9r4bfj9FW5hS330fsrh3RqXsJpqV3KQay/lj+/n/gumFBVOdi92IXr4lDSxF8pW4ciS25xuazmW208nFvwWp5Ew11C5exjd0GdsUcBXuGhR5LWa4guyjLszli7ucWui4rK3h1zzmim0cUn2weZPq8ofk6qlbUHCaph2EfQFZhF/H+d4tzlsDVrjQg8KbMtpCULF2UgOd+jtycpSA4SlXJOAlbdyMzjXfNCfAAkPqYYFHgV97BhPC5tpYQzFVJo+b7wwZ7fFeikj2s+XwIDAQAB',
        'qmmy'=>"<RSAKeyValue><Modulus>8NHm2ZHWN/C2Frs3qFyLvXO1dzq6GuXHK0lRzKoIrZWReuJpNUIXY6ZxIk4Tj91W1JLF7D9r4bfj9FW5hS330fsrh3RqXsJpqV3KQay/lj+/n/gumFBVOdi92IXr4lDSxF8pW4ciS25xuazmW208nFvwWp5Ew11C5exjd0GdsUcBXuGhR5LWa4guyjLszli7ucWui4rK3h1zzmim0cUn2weZPq8ofk6qlbUHCaph2EfQFZhF/H+d4tzlsDVrjQg8KbMtpCULF2UgOd+jtycpSA4SlXJOAlbdyMzjXfNCfAAkPqYYFHgV97BhPC5tpYQzFVJo+b7wwZ7fFeikj2s+Xw==</Modulus><Exponent>AQAB</Exponent></RSAKeyValue>"
    ),

    'dslhfb'=>array(
        'agent_id'=>2139359,
        'key'=>'1269DF4E00874B8CAC97B6FB',
        'gname'=>array(
            '1'=>'即食香辣海带丝',
            '2'=>'烤虾干自晒对虾干',
            '3'=>'即食海带大板',
            '4'=>'鲜活黑头鱼',
            '5'=>'新鲜龙利鱼',
            '6'=>'大海螺鲜活新鲜海捕野生贝类',
            '7'=>'麻辣油焖大虾真空包装虾零食',
            '8'=>'日照大虾鲜活海鲜南美白对虾',
            '9'=>'现烤鲜香马面鱼片',
        ),
        'sy'=>'MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCoHufUzzRd3Mt3aKzuH+FPwrHEUNqFOuLyOzB/ozvff/hjEIfAiX/KIVVe7kCx32dOCcScapPByOjAvo8B/QqURBTqD2c/1/noStkwkzWSpXau+//P8CVEIb18JhOG1UkHiUoxuGo3acmjdQSWQrc9hYyj5B3WhvfB1ThFGnyEs5QdoIx8ggLhADXmL7RjV5xhusTLCh7/GFd6fVT7s0jvj5fBWq47HVIHhscIT8t8VpjC+XcuKkJglW3BYHf1NR6n01su3XtiM/DlAb1w1Lvm8cFSNz428bdca4WV0XSxn83asokRc6PFJKXqfdBU/VTzO2Ly+ZwIM9+v95BnZcuHAgMBAAECggEAP2lAygzJVKnl+Ij7yOkvY5Us1b4lXeEjHnhix5G7EIbFGiBvA5kZIRVHjQHSVwTLgyy+Ar1UM+Bioeh+KiLWLawsM0hSCkudEBfDKcYsi75rMH8OMtECTVtoi/4UmWOzIuuOl4hWT8ZP7p5Bd6d8U/EeZeaxtmu74VlZ2v6tfqNcKQrmGOMPXs6OW/tEbUxTxQ43+Avz0AksWQn5wKGHhYQPAygfDWK6xzqP1sOfVjjoMVZQRv/Oel1rGCQFVp6raBbetv95JdZYNjBbsceIhIWccNMqDosFo+KpkQN7Wx5cYkLabirqfmJQ3A+4v98M2INhsxQaSGOIU92p2Z6EmQKBgQDhuPbjB1TW3BhNCcuXjYA0xZqOigi5NEv4tYZbsOLpiZS6hHWP9aCbkdo7eJyuScQ7Lp69PZi0h7pkj80m4h7Ypz2gnyDz92EW2CoL/PzxwqxSk+QKitDwZxySyhknkjphgT0EEdceieWRuFHL/n8N2qCJ9R+LsGO9+YpqF/GjRQKBgQC+q/dKyBviauWD++OF9RQyNVFiRrkXNTiMmspEcUEHLpklY4M82UEusq4ZEWC3qjolN6jJW296AKASd5qyI3Xc4HzNi8813iMaYO5AKZB+N+KKJwBUw52UkJ7KqF99wq2fdlSMRLn6QXZT6kBEA2VCc1sPcNn032v1BUUQHRjaWwKBgAsapkqU2b+YJDnl+XYDKANwlSHd+H/j6rjZdTqdgQwsEHz8dywV0UjV6/5w+IQ6bMTcjkKQ/SokYy7/RABdr1bJI2b7bQuTZ9tP1wGv+GGMSh3l2JoMKispZ2ZxOruPnf+d8/p3RjXiYsDAaIW4h1ePIcanoF2m18FIvV5qvyBhAoGAUrdplYCQUD5NiV9qCgATHeDxiG/j9gR5ns2798smCcaWW5j7Ixeg5nBPhMJ8MdFgXiryR0AqcUP6LWjDrIojpImiuszvPJA8rEHmueEONSNrXMGkrCEcqInAyWwX2QfuqGH+NcfO4pkpiyfxb1AFyrkehOlRNKEERYxetKna0UcCgYAk9gizWqqPprrOnXuHEtOR50JHSZvNKkPFmOtXYH/Ev6CZx0grzN5UkJGehg9CxncRqsK0q0FnCrOZBEkdEmCa1/2WrVZkmxCj3uKnqMprSrZ8OHmTzuFAJmt7Oprx9zXW5jUI1POhdA9mDnK+EJS2st9DGJJag37HZebDUNexDA==',
        'gy'=>'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAqB7n1M80XdzLd2is7h/hT8KxxFDahTri8jswf6M733/4YxCHwIl/yiFVXu5Asd9nTgnEnGqTwcjowL6PAf0KlEQU6g9nP9f56ErZMJM1kqV2rvv/z/AlRCG9fCYThtVJB4lKMbhqN2nJo3UElkK3PYWMo+Qd1ob3wdU4RRp8hLOUHaCMfIIC4QA15i+0Y1ecYbrEywoe/xhXen1U+7NI74+XwVquOx1SB4bHCE/LfFaYwvl3LipCYJVtwWB39TUep9NbLt17YjPw5QG9cNS75vHBUjc+NvG3XGuFldF0sZ/N2rKJEXOjxSSl6n3QVP1U8zti8vmcCDPfr/eQZ2XLhwIDAQAB'
    ),

    'paihangbang'=>array(
        '1'=>array('沐雨听风','45832079'),
        '2'=>array('暮歌','42086371'),
        '3'=>array('缘分天注定','36729105'),
        '4'=>array('柒','36594072'),
        '5'=>array('故事人生','30921714'),
        '6'=>array('A00梦想','28975143'),
        '7'=>array('追夢赤子心','26104378'),
        '8'=>array('高波','21905386'),
        '9'=>array('天行剑','21358641'),
        '10'=>array('秋雨','17106984'),
    ),
    'rabbitmq'=>array(
        'host'=>'123.56.184.150',
        'port'=>'5672',
        'user'=>'xunyuji',
        'password'=>'xunyuji',
        'vhost'=>'xunyuji',
        'exchange_name' => 'order',
        'queue_name' => 'order',
        'route_key' => 'order',
        'consumer_tag' => 'consumer',
    )

);
