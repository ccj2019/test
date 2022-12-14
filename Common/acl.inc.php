<?php
/*array(菜单名，菜单url参数，是否显示)*/
//error_reporting(E_ALL);
/*
$acl_inc[$i]['low_leve']['global']  global是model
每个action前必须添加eqaction_前缀'eqaction_websetting'  => 'at1','at1'表示唯一标志,可独自命名,eqaction_后面跟的action必须统一小写
*/
$acl_inc =  array();
$i=0;
$acl_inc[$i]['low_title'][] = '全局设置';
$acl_inc[$i]['low_leve']['global']= array( "网站设置" =>array(
												 "列表" 		=> 'at1',
												 "增加" 		=> 'at2',
												 "删除" 		=> 'at3',
												 "修改" 		=> 'at4',
												),
											"友情链接" =>array(
												 "列表" 		=> 'at5',
												 "增加" 		=> 'at6',
												 "删除" 		=> 'at7',
												 "修改" 		=> 'at8',
												 "搜索" 		=> 'att8',
											),
											"前台模板" =>array(
												 "清除" 		=> 'at18',
											),
											"后台模板" =>array(
												 "清除" 		=> 'at19',
											),
											"会员中心" =>array(
												 "清除" 		=> 'at20',
											),
											"数据缓存" =>array(
												 "清除" 		=> 'at21',
											),
											"所有缓存" =>array(
												 "清除" 		=> 'at22',
											),
										   "data" => array(
										   		//网站设置
												'eqaction_websetting'  => 'at1',
												'eqaction_doadd'    => 'at2',
												'eqaction_dodelweb'    => 'at3',
												'eqaction_doedit'   => 'at4',
												//友情链接
												'eqaction_friend'  	   => 'at5',
												'eqaction_dodeletefriend'    => 'at7',
												'eqaction_searchfriend'    => 'att8',
												'eqaction_addfriend'   => array(
																'at6'=>array(
																	'POST'=>array(
																		"fid"=>'G_NOTSET',
																	),
																 ),	
																'at8'=>array(
																	'POST'=>array(
																		"fid"=>'G_ISSET',
																	),
																),
													),
										   		//清除缓存
												'eqaction_cleantemplet'   => array(
																'at18'=>array(
																	'GET'=>array(
																		"acahe"=>'1',
																	),
																 ),	
																'at19'=>array(
																	'GET'=>array(
																		"acahe"=>'2',
																	),
																),
																'at20'=>array(
																	'GET'=>array(
																		"acahe"=>'3',
																	),
																),
													),
												'eqaction_cleandata'    => 'at21',
												'eqaction_cleanall'  => 'at22',
											)
							);
$acl_inc[$i]['low_leve']['area']= array( "地区管理" =>array(
												 "列表" 		=> 'dq1',
												 "增加" 		=> 'dq2',
												 "批量增加" 	=> 'dq5',
												 "删除" 		=> 'dq3',
												 "修改" 		=> 'dq4',
												),
										   "data" => array(
										   		//网站设置
												'eqaction_index'  => 'dq1',
												'eqaction_listtype'  => 'dq1',
												'eqaction_add'    => 'dq2',
												'eqaction_doadd'    => 'dq2',
												'eqaction_edit'    => 'dq4',
												'eqaction_doedit'    => 'dq4',
												'eqaction_dodel'    => 'dq3',
												'eqaction_addmultiple'    => 'dq5',
												'eqaction_doaddmul'    => 'dq5',
											)
							);
$acl_inc[$i]['low_leve']['ad']= array( "广告管理" =>array(
												 "列表" 		=> 'ad1',
												 "增加" 		=> 'ad2',
												 "删除" 		=> 'ad4',
												 "修改" 		=> 'ad3',
												),
										   "data" => array(
										   		//网站设置
												'eqaction_index'  => 'ad1',
												'eqaction_add'    => 'ad2',
												'eqaction_doadd'    => 'ad2',
												'eqaction_edit'    => 'ad3',
												'eqaction_doedit'    => 'ad3',
												'eqaction_swfupload'    => 'ad3',
												'eqaction_dodel'    => 'ad4',
											)
							);
$acl_inc[$i]['low_leve']['leve']= array( "会员级别管理" =>array(
												 "查看" 		=> 'jb1',
												 "修改" 		=> 'jb2',
												),
										   "data" => array(
										   		//网站设置
												'eqaction_index'  => 'jb1',
												'eqaction_save'    => 'jb2',
											)
							);
$acl_inc[$i]['low_leve']['age']= array( "会员年龄别称" =>array(
												 "查看" 		=> 'bc1',
												 "修改" 		=> 'bc2',
												),
										   "data" => array(
										   		//网站设置
												'eqaction_index'  => 'bc1',
												'eqaction_save'    => 'bc2',
											)
							);
$acl_inc[$i]['low_leve']['loginonline']= array( "登陆接口管理" =>array(
												 "查看" 		=> 'dl1',
												 "修改" 		=> 'dl2',
												),
										   "data" => array(
										   		//网站设置
												'eqaction_index'  => 'dl1',
												'eqaction_save'    => 'dl2',
											)
							);
$i++;
$acl_inc[$i]['low_title'][] = '在线通知管理';
$acl_inc[$i]['low_leve']['payonline']= array( "支付接口管理" =>array(
												 "查看" 		=> 'jk1',
												 "修改" 		=> 'jk2',
												),
										   "data" => array(
										   		//网站设置
												'eqaction_index'  => 'jk1',
												'eqaction_save'    => 'jk2',
											)
							);
$acl_inc[$i]['low_leve']['msgonline']= array( "通知信息接口管理" =>array(
												 "查看" 		=> 'jk3',
												 "修改" 		=> 'jk4',
												),
											 "通知信息模板管理" =>array(
												 "查看" 		=> 'jk5',
												 "修改" 		=> 'jk6',
											),
									   "data" => array(
										   		//网站设置
												'eqaction_index'  => 'jk3',
												'eqaction_save'    => 'jk4',
												'eqaction_templet'  => 'jk5',
												'eqaction_templetsave'    => 'jk6',
											)
							);
$i++;
$acl_inc[$i]['low_title'][] = '借款管理';
$acl_inc[$i]['low_leve']['borrow']= array( "初审中" =>array(
												 "列表" 		=> 'br1',
												 "审核" 		=> 'br2',
												),
											"预热中" =>array(
												 "列表" 		=> 'waitstart',
												 "审核" 		=> 'doeditwaitstart',
												),
										   "复审中"                                            => array(
											        "列表"   => 'br3',
											        "投票管理" => 'vote',
											        "审核"   => 'br4',
											    ),
										   "筹资中" =>array(
												 "列表" 		=> 'br5',
												 "审核" 		=> 'br6',
											),
										   "已成功" =>array(
												 "列表" 		=> 'br7',
												 "回报" 		=> 'dorepayment',	
												 "项目跟踪"         =>'doeditrepaymenting',											 
											),
										   "申请分红" =>array(
												 "申请列表" 		=> 'applicationbouns',
												 "回报" 		=> 'doapprepayment',
												 "查看详情" 		=> 'apprepaymentlist',
												 
											),
										   "已分红" =>array(
												 "列表" 		=> 'br9',
												 "项目跟踪"         =>'doeditdone',
											),
										   "已流标借款" =>array(
												 "列表" 		=> 'br11',
											),
										   "初审未通过的借款" =>array(
												 "列表" 		=> 'br13',
											),
										   "复审未通过的借款" =>array(
												 "列表" 		=> 'br14',
											),
										   "查看" =>array(
												 "借款投标详情" => 'br15',												 
											),
											
										   "查看全部" =>array(
												 "查看全部" => 'waitverifyquanbu',												 
											),
									   "data" => array(
										   		//网站设置 
										   		'eqaction_waitverifyquanbu'  => 'waitverifyquanbu',
												'eqaction_waitverify'  => 'br1',
												'eqaction_edit' =>'br2',	
												'eqaction_edit' =>'br4',	
												'eqaction_edit' =>'br6',	
												'eqaction_view' =>'br15',
										   		'eqaction_dozhuanhuan' =>'br15',
												'eqaction_borrowdc' =>'br15',
												'eqaction_doeditwaitverify' =>'br2',	
												'eqaction_waitverify2'  => 'br3',
												'eqaction_doeditwaitverify2'  => 'br4',
												'eqaction_waitstart'  => 'waitstart',
												'eqaction_doeditwaitstart'  => 'doeditwaitstart',
												'eqaction_waitmoney'  => 'br5',
												'eqaction_vote'              => 'vote',
												'eqaction_doeditwaitmoney'  => 'br6',
												'eqaction_repaymenting'    => 'br7',
												'eqaction_dorepayment'    => 'dorepayment',
												'eqaction_doweek'    => 'br7',
												'eqaction_done'    => 'br9',
												'eqaction_unfinish'    => 'br11',
												'eqaction_fail'    => 'br13',
												'eqaction_fail2'    => 'br14',
												'eqaction_swfupload'  => 'br2',												
												'eqaction_doeditrepaymenting'    => 'doeditrepaymenting',
												'eqaction_doeditdone'    => 'doeditdone',
												'eqaction_applicationbouns'    => 'applicationbouns',
												'eqaction_doapprepayment'    => 'doapprepayment',
												'eqaction_apprepaymentlist'    => 'apprepaymentlist',

										   		'eqaction_autohuikuan'    => 'doapprepayment',
										   		'eqaction_generate'    => 'doapprepayment',
										   		'eqaction_subgenerate'    => 'doapprepayment',
											)
							);
							
$acl_inc[$i]['low_leve']['expired']= array( "逾期借款管理" =>array(
												 "查看" 		=> 'yq1',
												 "代还" 		=> 'yq2',
												),
										   "逾期会员列表" =>array(
												 "列表" 		=> 'yq3',
											),
									   "data" => array(
												'eqaction_index'  => 'yq1',
												'eqaction_doexpired'  => 'yq2',
												'eqaction_member'  => 'yq3',
											)
							);
$acl_inc[$i]['low_leve']['release']= array( "发布借款标" =>array(
					 "发布借款标" 		=> 'fbjkb',
					),
		   "data" => array(
					'eqaction_add'  => 'fbjkb',
					'eqaction_save'  => 'fbjkb'
				)
);
$acl_inc[$i]['low_title'][] = '留言管理';
$acl_inc[$i]['low_leve']['comment']= array( "留言管理" =>array(
												 "列表" 		=> 'msg1',
												 "查看" 		=> 'msg2',
												 "删除" 		=> 'msg3',
												),
										   "data" => array(
													//网站设置
													'eqaction_index'  => 'msg1',
													'eqaction_dodel'  => 'msg3',
													'eqaction_edit'  => 'msg2',
													'eqaction_doedit'  => 'msg2',
												)
							);
$i++;
$acl_inc[$i]['low_title'][] = '联合种植';
$acl_inc[$i]['low_leve']['zhongzhi']= array( "联合种植" =>array(
												 "列表" 		=> 'index',
												 "修改" 		=> 'edit',
												 "删除" 		=> 'dodel',
												 "添加" 		=> 'add',
												),
										   "data" => array(
													//网站设置
													'eqaction_index'  => 'index',
													'eqaction_dodel'  => 'dodel',
													'eqaction_edit'  => 'edit',
													'eqaction_add'  => 'add',
													'eqaction_doadd'  => 'add',
													'eqaction_doedit'  => 'edit',
													'eqaction_swfupload'  => 'edit',
													'eqaction_addgg'  => 'edit',
													'eqaction_editgg'  => 'edit',
													'eqaction_doeditgg'  => 'edit',
													'eqaction_gglist'  => 'edit',
													
																											
												)
							);

$i++;
$acl_inc[$i]['low_title'][] = '种植订单';
$acl_inc[$i]['low_leve']['orderz']= array( "种植订单" =>array(
												 "列表" 		=> 'index',
												 "修改" 		=> 'edit',
												),
										   "data" => array(
													//网站设置
													'eqaction_index'  => 'index',
													'eqaction_edit'  => 'edit',
													'eqaction_doedit'  => 'edit',
													
													// 'eqaction_swfupload'  => 'edit',
													// 'eqaction_addgg'  => 'edit',
													// 'eqaction_editgg'  => 'edit',
													// 'eqaction_doeditgg'  => 'edit',
													// 'eqaction_gglist'  => 'edit',
													
																											
												)
							);
$i++;
$acl_inc[$i]['low_title'][] = '预售管理';
							$acl_inc[$i]['low_leve']['ysgood']= array( "预售管理" =>array(
								"列表" 		=> 'index',
								"添加/修改" 		=> 'edit',
							),
								"data" => array(
									//网站设置
									'eqaction_index'  => 'index',
									'eqaction_edit'  => 'edit',
									'eqaction_doedit'  => 'edit',
									'eqaction_add'  => 'edit',
									'eqaction_doadd'  => 'edit',
									'eqaction_swfupload' => 'edit',
									// 'eqaction_addgg'  => 'edit',
									// 'eqaction_editgg'  => 'edit',
									// 'eqaction_doeditgg'  => 'edit',
									// 'eqaction_gglist'  => 'edit',
								)
							);
$i++;
$acl_inc[$i]['low_title'][] = '预售详情';
$acl_inc[$i]['low_leve']['ysorder']= array( "预售详情" =>array(
	"列表" 		=> 'index',

),
	"data" => array(
		//网站设置
		'eqaction_index'  => 'index',
		'eqaction_dobulu'  => 'index',
		'eqaction_delwzf'  => 'index',
		'eqaction_export'  => 'index',
	)
);

$i++;
$acl_inc[$i]['low_title'][] = '明星展示';
$acl_inc[$i]['low_leve']['celebrity']= array( "优秀项目" =>array(
												 "列表" 		=> 'project',
												 "修改" 		=> 'edit',
												 "删除" 		=> 'dodel',
												),
										   "data" => array(
													//网站设置
													'eqaction_project'  => 'project',
													'eqaction_dodel'  => 'dodel',
													'eqaction_edit'  => 'edit',													
												)
							);
$i++;
$acl_inc[$i]['low_leve']['productcategory']= array(
					 "理财分类" =>array(
												 "列表" 		=> 'act1',
												 "添加" 		=> 'act2',
												 "批量添加" 	=> 'act5',
												 "删除" 		=> 'act3',
												 "修改" 		=> 'act4',
											),
		   "data" => array(
					'eqaction_index'  => 'act1',
					'eqaction_listtype'  => 'act1',
					'eqaction_add'  => 'act2',
					'eqaction_doadd'  => 'act2',
					'eqaction_dodel'  => 'act3',
					'eqaction_edit'  => 'act4',
					'eqaction_doedit'  => 'act4',
					'eqaction_addmultiple'  => 'act5',
					'eqaction_doaddmul'  => 'act5',
				)
);
$i++;
$acl_inc[$i]['low_title'][] = '会员管理';
$acl_inc[$i]['low_leve']['members']= array( "会员列表" =>array(
												 "列表" 		=> 'me1',
												 "调整余额" 	=> 'mx2',
												 "调整授信" 	=> 'mx3',
												 "删除会员" 	=> 'mxw',
												 "修改客户类型" 	=> 'xmxw',
												 "自动投标" 	=> 'xmxw',
												 "初始化密码" 	=> 'xmxw',
												 "升级会员" 	=> 'xmxwsj',
												 '搜索会员'     => 'ajaxsearch',
												 "资金日志" 	=> 'zjrz',
												),
										   "会员资料" =>array(
												 "列表" 		=> 'me3',
												 "查看" 		=> 'me4',
											),
										   "额度申请待审核" =>array(
												 "列表" 		=> 'me7',
												 "审核" 		=> 'me6',
											),
										   "推荐人列表" =>array(
												 "查看" 		=> 'mex6',
												),
										   "导出" =>array(
												 "导出" 		=> 'mex6',
												),
									   "data" => array(
										   		//网站设置
												'eqaction_index'  => 'me1',
												'eqaction_auto'  => 'me1',
												'eqaction_initpwd'  => 'xmxw',
												'eqaction_leveedit'  => 'me1',
												'eqaction_info' =>'me3',	
												'eqaction_viewinfom'  => 'me4',
												'eqaction_infowait'  => 'me7',
												'eqaction_viewinfo'  => 'me6',
												'eqaction_doeditcredit'  => 'me6',
												'eqaction_domoneyedit'  => 'mx2',
												'eqaction_moneyedit'  => 'mx2',
												'eqaction_creditedit'  => 'mx3',
												'eqaction_dodel'    => 'mxw',
												'eqaction_edit'    => 'xmxw',
												'eqaction_member_capital'    => 'zjrz',
												'eqaction_doedit'    => 'xmxw',
												'eqaction_docreditedit'  => 'mx3',
												'eqaction_idcardedit'    => 'xmxw',
												'eqaction_doidcardedit'    => 'xmxw',
												'eqaction_doleveedit'    => 'xmxwsj',												
												'eqaction_reclist'  => 'mex6',											
												'eqaction_export'  => 'mex6',
												'eqaction_ajaxsearch'  => 'ajaxsearch',												
											)
							);
$acl_inc[$i]['low_leve']['common']= array( "会员详细资料" =>array(
												 "查年" 		=> 'mex5',
												),
									   "data" => array(
										   		//网站设置
												'eqaction_member'  => 'mex5',
											)
							);
$acl_inc[$i]['low_leve']['jubao']= array( "举报信息" =>array(
												 "列表" 		=> 'me5',
												),
									   "data" => array(
										   		//网站设置
												'eqaction_index'  => 'me5',
											)
							);
							
$acl_inc[$i]['low_leve']['memberimport']= array( "会员资料导出" =>array(
												 "列表" 		=> 'mex5',
												  "导出" 		=> 'mex5',
												),
									   "data" => array(
										   		//网站设置
												'eqaction_index'  => 'mex5',
												'eqaction_export'  => 'mex5',
											)
							);
$acl_inc[$i]['low_leve']['experience']= array( "会员体验金" =>array(
												 "列表" 		=> 'tiyanjin1',
												 "发放" 		=> 'tiyanjin2',
												 "回收" 		=> 'tiyanjin3',
												),
									   "data" => array(
										   		//网站设置
												'eqaction_index'  => 'tiyanjin1',
												'eqaction_pub'  => 'tiyanjin2',
												'eqaction_recover'  => 'tiyanjin3',
											)
							);
$acl_inc[$i]['low_leve']['memberinterest']= array( "会员加息券" =>array(
												 "列表" 		=> 'index',
												 "发放" 		=> 'add',
												 "修改" 		=> 'edit',
												),
									   "data" => array(
										   		//网站设置
												'eqaction_index'  => 'index',
												'eqaction_add'  => 'add',
												'eqaction_edit'  => 'edit',
											)
							);
$acl_inc[$i]['low_leve']['bonus']= array( "会员红包" =>array(
												 "列表" 		=> 'index',
												 "发放" 		=> 'add',
												 "修改" 		=> 'edit',
												 "红包规则" 		=> 'rules',
												 "红包规则添加" 		=> 'rules_add',
												 "红包规则修改" 		=> 'rules_edit',
												 "红包规则删除" 		=> 'rules_del',
												  "全部发放" 		=> 'rules_addpubBonus',
												  "过期提醒" 		=> 'guoqi',
												),
									   "data" => array(
										   		//网站设置
												'eqaction_index'  => 'index',
												'eqaction_add'  => 'add',
												'eqaction_edit'  => 'edit',
												'eqaction_rules'  => 'rules',
												'eqaction_rules_add'   => 'rules_add',
												'eqaction_rules_edit'  => 'rules_edit',
												'eqaction_rules_del'  => 'rules_del',
												'eqaction_rules_addpubBonus'  => 'rules_addpubBonus',
												'eqaction_guoqi'  => 'guoqi',
												'eqaction_guoqis'  => 'guoqi',													
											)
							);

$i++;
$acl_inc[$i]['low_title'][] = '投资记录';
$acl_inc[$i]['low_leve']['investor']= array( "投资记录" =>array(
												 "日-周-月投资记录" 		=> 'vip1',
												 "总投资记录" 		=> 'vip2',
												 "导出" 		=> 'vip2',
												),
										   "data" => array(
													//网站设置
													'eqaction_investorlist'  => 'vip1',
													'eqaction_index'  => 'vip2',
													'eqaction_export'  => 'vip2',
												)
							);
$i++;
$acl_inc[$i]['low_title'][] = '活动专区';
$acl_inc[$i]['low_leve']['lottery']= array( "活动专区" =>array(
												 "抽奖记录" 		=> 'l1',
												 "编辑审核" 		=> 'l2',
												 "活动设置" 		=> 'l3',
												),
										   "data" => array(
													//网站设置
													'eqaction_index'  => 'l1',													
													"eqaction_edit"   => 'l2',
													"eqaction_setting"   => 'l3'
												)
							);
$i++;
$acl_inc[$i]['low_title'][] = '大转盘管理';
$acl_inc[$i]['low_leve']['zhuanpan']= array( "大转盘管理" =>array(
												 "抽奖记录" 		=> 'winlog',
												 "奖品列表" 		=> 'index',
												 "奖品发布" 		=> 'add',
												 "奖品编辑" 		=> 'edit',
												 "奖品删除" 		=> 'dodel',
												),
										   "data" => array(
													//网站设置
													'eqaction_winlog'  => 'winlog',													
													"eqaction_index"   => 'index',
													"eqaction_add"   => 'add',
													"eqaction_edit"   => 'edit',
													"eqaction_dodel"   => 'dodel'
												)
							);

$i++;
$acl_inc[$i]['low_title'][] = '投资排名';
$acl_inc[$i]['low_leve']['ranking']= array( "投资排名" =>array(
												 "日-周-月投资排名" 		=> 'vip1',
												 "总投资排名" 		=> 'vip2',
												 "导出" 		=> 'vip2',
												),
										   "data" => array(
													//网站设置
													'eqaction_ranklist'  => 'vip1',
													'eqaction_index'  => 'vip2',
													'eqaction_export'  => 'vip2',
													'eqaction_borrowcount'  => 'vip2',
													'eqaction_rankingcount'  => 'vip2',
													'eqaction_rankingcountexport'  => 'vip2',
												)
							);
$i++;
$acl_inc[$i]['low_title'][] = '代收排名';
$acl_inc[$i]['low_leve']['daishou']= array( "代收排名" =>array(
	"代收排名" 		=> 'vip1',

),
	"data" => array(
		//网站设置

		'eqaction_index'  => 'vip1',
//		'eqaction_export'  => 'vip2',
//		'eqaction_borrowcount'  => 'vip2',
//		'eqaction_rankingcount'  => 'vip2',
//		'eqaction_rankingcountexport'  => 'vip2',
	)
);

$i++;
$acl_inc[$i]['low_title'][] = '认证及申请管理';
$acl_inc[$i]['low_leve']['vipapply']= array( "VIP申请列表" =>array(
												 "列表" 		=> 'vip1',
												 "审核" 		=> 'vip2',
												),
										   "data" => array(
													//网站设置
													'eqaction_index'  => 'vip1',
													'eqaction_edit' =>'vip2',	
													'eqaction_doedit'  => 'vip2',
												)
							);
$acl_inc[$i]['low_leve']['memberid']= array( "会员实名认证管理" =>array(
												 "列表" 		=> 'me10',
												 "审核" 		=> 'me9',
												),
									   "data" => array(
										   		//网站设置
												'eqaction_index'  => 'me10',
												'eqaction_edit'  => 'me9',
												'eqaction_doedit'  => 'me9',
											)
							);
$acl_inc[$i]['low_leve']['memberdata']= array( "会员上传资料管理" =>array(
												 "列表" 		=> 'dat1',
												 "审核" 		=> 'dat2',
												),
									   "data" => array(
										   		//网站设置
												'eqaction_index'  => 'dat1',
												'eqaction_edit'  => 'dat2',
												'eqaction_doedit'  => 'dat2',
											)
							);
$acl_inc[$i]['low_leve']['verifyphone']= array( "手机认证会员" =>array(
												 "列表" 		=> 'vphone1',
												 "导出" 		=> 'vphone2',
												),
									   "data" => array(
										   		//网站设置
												'eqaction_index'  => 'vphone1',
												'eqaction_export'  => 'vphone2',
											)
							);
$acl_inc[$i]['low_leve']['verifyvideo']= array( "视频认证申请" =>array(
												 "列表" 		=> 'vpv1',
												 "审核" 		=> 'vpv2',
												),
									   "data" => array(
										   		//网站设置
												'eqaction_index'  => 'vpv1',
												'eqaction_edit'  => 'vpv2',
												'eqaction_doedit'  => 'vpv2',
											)
							);
$acl_inc[$i]['low_leve']['verifyface']= array( "现场认证申请" =>array(
												 "列表" 		=> 'vface1',
												 "审核" 		=> 'vface2',
												),
									   "data" => array(
										   		//网站设置
												'eqaction_index'  => 'vface1',
												'eqaction_edit'  => 'vface2',
												'eqaction_doedit'  => 'vface2',
											)
							);
$i++;
$acl_inc[$i]['low_title'][] = '充值提现管理';
$acl_inc[$i]['low_leve']['paylog']= array( "充值记录" =>array(
												 "列表" 		=> 'cg1',
												 "充值处理" 		=> 'cg33',
												
												),
										   "data" => array(
													//网站设置
													'eqaction_index'  => 'cg1',
													'eqaction_edit'  => 'cg33',
													'eqaction_doedit'  => 'cg33',

													
												)
							);
$acl_inc[$i]['low_leve']['withdrawlog']= array("提现管理" =>array(
												 "列表" 		=> 'cg2',
												 "审核" 		=> 'cg3',
											),
										   "data" => array(
													//网站设置
													'eqaction_index'  => 'cg2',
													'eqaction_edit' =>'cg3',	
													'eqaction_doedit'  => 'cg3',
												)
							);
$i++;
$acl_inc[$i]['low_title'][] = '网站充值导入';
$acl_inc[$i]['low_leve']['pay']= array( "网站充值导入" =>array(
												 "查看" 		=> 'cg1',
												 "导入" 		=> 'cg1',
												),
										   "data" => array(
													//网站设置
													'eqaction_index'  => 'cg1',
													'eqaction_payimport'  => 'cg1',
												)
							);
$i++;
$acl_inc[$i]['low_title'][] = '充值提现';
$acl_inc[$i]['low_leve']['paylog']= array( "充值记录" =>array(
												 "列表" 		=> 'cg1',
												 "充值处理" 		=> 'cg33',
												  "列表导出" 		=> 'cg34',
												),
										   "data" => array(
													//网站设置
													'eqaction_index'  => 'cg1',
													'eqaction_edit'  => 'cg33',
													'eqaction_doedit'  => 'cg33',
													'eqaction_paylogonline'  => 'cg1',
													'eqaction_paylogoffline'  => 'cg1',
													'eqaction_export'  => 'cg34',
													'eqaction_exportall'  => 'cg34',
											        'eqaction_dobulu'  => 'cg33',
												)
							);
$acl_inc[$i]['low_leve']['withdrawlog']= array("提现管理" =>array(
												 "列表" 		=> 'cg2',
												 "审核" 		=> 'cg3',
											),
										   "data" => array(
													//网站设置
													'eqaction_index'  => 'cg2',
													'eqaction_edit' =>'cg3',	
													'eqaction_doedit'  => 'cg3',
													'eqaction_withdraw0'  => 'cg2',//待提现      新增加2012-12-02 fanyelei
													'eqaction_withdraw1'  => 'cg2',//提现处理中	新增加2012-12-02 fanyelei
													'eqaction_withdraw2'  => 'cg2',//提现成功		新增加2012-12-02 fanyelei
													'eqaction_withdraw3'  => 'cg2',//提现失败		新增加2012-12-02 fanyelei
													
												)
							);
$acl_inc[$i]['low_title'][] = '待提现列表';
$acl_inc[$i]['low_leve']['withdrawlogwait']= array( "待提现列表" =>array(
												 "列表" 		=> 'cg4',
												 "审核" 		=> 'cg5',
												),
									   "data" => array(
										   		//网站设置
												'eqaction_index'  => 'cg4',
													'eqaction_edit' =>'cg5',	
													'eqaction_doedit'  => 'cg5',
											)
							);
$acl_inc[$i]['low_title'][] = '提现处理中列表';					
$acl_inc[$i]['low_leve']['withdrawloging']= array( "提现处理中列表" =>array(
												 "列表" 		=> 'cg6',
												 "审核" 		=> 'cg7',
												  "导出" 		=> 'cg7',
												),
									   "data" => array(
										   		//网站设置
												'eqaction_index'  => 'cg6',
													'eqaction_edit' =>'cg7',	
													'eqaction_doedit'  => 'cg7',
													'eqaction_export'  => 'cg7',
											)
							);
							
$i++;
$acl_inc[$i]['low_title'][] = '文章管理';
$acl_inc[$i]['low_leve']['article']= array( "文章管理" =>array(
												 "列表" 		=> 'at1',
												 "添加" 		=> 'at2',
												 "删除" 		=> 'at3',
												 "修改" 		=> 'at4',
												 "内容" 		=> 'at4',
												),
										   "data" => array(
													//网站设置
													'eqaction_index'  => 'at1',
													'eqaction_add'  => 'at2',
													'eqaction_doadd'  => 'at2',
													'eqaction_dodel'  => 'at3',
													'eqaction_edit'  => 'at4',
													'eqaction_doedit'  => 'at4',
													'eqaction_control'  => 'at4',
												)
							);
$acl_inc[$i]['low_leve']['acategory']= array("文章分类" =>array(
												 "列表" 		=> 'act1',
												 "添加" 		=> 'act2',
												 "批量添加" 	=> 'act5',
												 "删除" 		=> 'act3',
												 "修改" 		=> 'act4',
											),
										   "data" => array(
													//网站设置
													'eqaction_index'  => 'act1',
													'eqaction_listtype'  => 'act1',
													'eqaction_add'  => 'act2',
													'eqaction_doadd'  => 'act2',
													'eqaction_dodel'  => 'act3',
													'eqaction_edit'  => 'act4',
													'eqaction_doedit'  => 'act4',
													'eqaction_addmultiple'  => 'act5',
													'eqaction_doaddmul'  => 'act5',
												)
							);
$acl_inc[$i]['low_title'][] = '资金统计';
$acl_inc[$i]['low_leve']['capitalaccount']= array( "会员帐户" =>array(
												 "列表" 		=> 'capital_1',
												 "导出" 		=> 'capital_2',
												),
										   "data" => array(
													//网站设置
													'eqaction_index'  => 'capital_1',
													'eqaction_export'  => 'capital_2',
												)
							);
$acl_inc[$i]['low_leve']['capitalonline']= array("充值记录" =>array(
												 "列表" 		=> 'capital_3',
												 "导出" 		=> 'capital_4',
												),
											   "提现记录" =>array(
													 "列表" 		=> 'capital_5',
													 "导出" 		=> 'capital_6',
												),
											   "data" => array(
													//网站设置
													'eqaction_charge'  => 'capital_3',
													'eqaction_withdraw'  => 'capital_5',
													'eqaction_chargeexport'  => 'capital_4',
													'eqaction_withdrawexport'  => 'capital_6',
												)
							);
$acl_inc[$i]['low_leve']['capitaldetail']= array("会员资金记录" =>array(
												 "列表" 		=> 'capital_7',
												 "导出" 		=> 'capital_8',
												),
											   "data" => array(
													//网站设置
													'eqaction_index'  => 'capital_7',
													'eqaction_export'  => 'capital_8',
												)
							);
$acl_inc[$i]['low_leve']['capitalall']= array("网站资金统计" =>array(
												 "查看" 		=> 'capital_9',
												),
											   "data" => array(
													//网站设置
													'eqaction_index'  => 'capital_9',
												)
							);
$acl_inc[$i]['low_leve']['kefu']= array("客服管理" =>array(
												 "列表" 		=> 'capital_111',
												 "添加/修改" 		=> 'capital_112',
												 "绑定" 		=> 'capital_113',
												 "客服统计" 		=> 'capital_114',
												 "删除客服绑定" 		=> 'capital_115'
												),
											   "data" => array(
													//客服列表
													'eqaction_index'  => 'capital_111',
													//添加客服
													'eqaction_edit'  => 'capital_112',
													'eqaction_doedits'  => 'capital_112',
													'eqaction_dodel'  => 'capital_112',
													//绑定客服
													'eqaction_guanlian'  => 'capital_113',
													//绑定客服
													'eqaction_khindex'  => 'capital_114',
													//绑定客户
													'eqaction_khadd'  => 'capital_113',
													'eqaction_doadd'  => 'capital_113',
													'eqaction_khlist'  => 'capital_114',
													'eqaction_wkhlist'  => 'capital_114',
													'eqaction_khbangding'  => 'capital_113',
													'eqaction_dobangding'  => 'capital_113',
													'eqaction_export'  => 'capital_114',
													//删除客服绑定
													'eqaction_khdel'  => 'capital_115',
													'eqaction_khlist'  => 'capital_114',
													
												),
							);

$acl_inc[$i]['low_leve']['customer']= array("客服客户统计" =>array(
												 "查看" 		=> 'capital_10',
												),
											   "data" => array(
													//客服客户统计查看
													'eqaction_index'  => 'capital_10',
												)
							);
//权限管理
$i++;
$acl_inc[$i]['low_title'][] = '权限管理';
$acl_inc[$i]['low_leve']['acl']= array( "权限管理" =>array(
												 "列表" 		=> 'at73',
												 "增加" 		=> 'at74',
												 "删除" 		=> 'at75',
												 "修改" 		=> 'at76',
												),
										   "data" => array(
										   		//权限管理
												'eqaction_index'  => 'at73',
												'eqaction_doadd'    => 'at74',
												'eqaction_add'    => 'at74',
												'eqaction_dodelete'    => 'at75',
												'eqaction_doedit'   => 'at76',
												'eqaction_edit'   	=> 'at76',
											)
							);
//管理员管理
$i++;
$acl_inc[$i]['low_title'][] = '管理员管理';
$acl_inc[$i]['low_leve']['adminuser']= array( "管理员管理" =>array(
												 "列表" 		=> 'at77',
												 "增加" 		=> 'at78',
												 "删除" 		=> 'at79',
												 "上传头像"	=> 'at99',
												 "修改" 		=> 'at80',
												 "日志" 		=> 'at80',
												),
										   	  "data" => array(
										   		//权限管理
												'eqaction_index'  => 'at77',
												'eqaction_dodelete'    => 'at79',
												'eqaction_header'    => 'at99',
												'eqaction_memberheaderuplad'    => 'at99',
												'eqaction_loginlog'    => 'at99',
												'eqaction_addadmin' =>array(
																'at78'=>array(//增加
																	'POST'=>array(
																		"uid"=>'G_NOTSET',
																	),
																 ),	
																'at80'=>array(//修改
																	'POST'=>array(
																		"uid"=>'G_ISSET',
																	),
																 ),	
												),
											   'eqaction_loglist'    => 'at80',
											   'eqaction_logedit'    => 'at80',
											)
							);
//权限管理
$i++;
$acl_inc[$i]['low_title'][] = '数据库管理';
$acl_inc[$i]['low_leve']['db']= array( "数据库信息" =>array(
												 "查看" 		=> 'db1',
												 "备份" 		=> 'db2',
												 "查看表结构" => 'db3',
												),
									   "数据库备份管理" =>array(
											 "备份列表" 		=> 'db4',
											 "删除备份" 		=> 'db5',
											 "恢复备份" 		=> 'db6',
											 "打包下载" 		=> 'db7',
										),
									   "清空数据" =>array(
											 "清空数据" 		=> 'db8',
										),
										   "data" => array(
										   		//权限管理
												'eqaction_index'  => 'db1',
												'eqaction_set'  => 'db2',
												'eqaction_backup'  => 'db2',
												'eqaction_showtable'  => 'db3',
												'eqaction_baklist'  => 'db4',
												'eqaction_delbak'  => 'db5',
												'eqaction_restore'  => 'db6',
												'eqaction_dozip'  => 'db7',
												'eqaction_downzip'  => 'db7',
												'eqaction_truncate'  => 'db8',
											)
							);
$i++;
$acl_inc[$i]['low_title'][] = '积分管理';
$acl_inc[$i]['low_leve']['market'] = array("投资积分管理" => array(
        "投资积分操作记录" => 'mk0',
        "获取列表" => 'mk1',
        "获取操作" => 'mk2',
        "商城商品列表" => 'mk3',
        "商品操作" => 'mk4',
        "上传商品图片" => 'mk5',
        "商品删除" => 'mk6',
    ),
    "data" => array(
        //网站设置
        'eqaction_index' => 'mk0',
        'eqaction_getlog' => 'mk1',
        'eqaction_getlog_edit' => 'mk2',
        'eqaction_dologedit' => 'mk2',
        'eqaction_goods' => 'mk3',
        'eqaction_good_edit' => 'mk4',
        'eqaction_dogoodedit' => 'mk4',
        'eqaction_good_del' => 'mk4',
        'eqaction_lottery' => 'mk6',
		'eqaction_add' =>'mk4',
		'eqaction_edit' =>'mk4',
		'eqaction_order' =>'mk4',
		'eqaction_doadd' =>'mk4',
		'eqaction_doedit' =>'mk4',
        'eqaction_lottery_edit' => 'mk7',
        'eqaction_dolotteryedit' => 'mk7',
        'eqaction_lottery_del' => 'mk8',
        'eqaction_upload_shop_pic' => 'mk5',
        'eqaction_dodel' => 'mk6',
    )
);
$acl_inc[$i]['low_leve']['order'] = array("商品兑换" => array(
        "商品兑换" => 'mk0',
        "商品兑换订单删除" => 'mk1',
    ),
    "data" => array(
        //网站设置
         'eqaction_index' => 'mk0',
		 'eqaction_order' => 'mk0',
		  'eqaction_edit' => 'mk0',
		  'eqaction_orerupdate' => 'mk0',
		  'eqaction_dodel' => 'mk1',
		  'eqaction_thorder' => 'mk0',
		  'eqaction_thedit' => 'mk0',
		  'eqaction_thorerupdate' => 'mk0',
    )
);
$i++;
$acl_inc[$i]['low_title'][] = '赠品管理';
$acl_inc[$i]['low_leve']['zengpin'] = array("赠品管理" => array(
	"赠品管理" => 'mk0',
),
	"data" => array(
		//网站设置
		'eqaction_index' => 'mk0',
		'eqaction_order' => 'mk0',
		'eqaction_add' =>'mk0',
		'eqaction_edit' => 'mk0',
		'eqaction_doedit' =>'mk0',
		'eqaction_doadd' =>'mk0',
		'eqaction_swfupload'    => 'mk0',
		'eqaction_orerupdate' => 'mk0',
		'eqaction_getzp' => 'mk0',
		'eqaction_dodel' => 'mk1',


		'eqaction_htadd' =>'mk0',
		'eqaction_htdoadd' =>'mk0',
		'eqaction_htedit' => 'mk0',
		'eqaction_htdoedit' =>'mk0',
		'eqaction_ajaxsearch' =>'mk0',
		'eqaction_dohtadd' =>'mk0',


	)
);
$acl_inc[$i]['low_leve']['zporder'] = array("赠品发货" => array(
	"赠品发货" => 'mk1',
),
	"data" => array(
		//网站设置
		'eqaction_index' => 'mk1',
		'eqaction_zindex' => 'mk1',
		'eqaction_edit' => 'mk1',
		'eqaction_doedit' =>'mk1',
		'eqaction_export' => 'mk1',
		'eqaction_export1' => 'mk1',

	)
);
$acl_inc[$i]['low_leve']['myzengpin'] = array("赠品待处理" => array(
	"赠品待处理" => 'mk2',
),
	"data" => array(
		//网站设置
		'eqaction_htindex'    => 'mk2',
		'eqaction_index' => 'mk2',
	)
);

$acl_inc[$i]['low_leve']['yundan'] = array("礼品管理" => array(
	"礼品管理" => 'mk0',
),
	"data" => array(
		//网站设置
		'eqaction_index' => 'mk0',
		'eqaction_add' =>'mk0',
		'eqaction_doadd' =>'mk0',
		'eqaction_edit' => 'mk0',
		'eqaction_doedit' =>'mk0',
		'eqaction_swfupload'    => 'mk0',
		'eqaction_dodel' => 'mk1',
	)
);

$i++;
$acl_inc[$i]['low_title'][] = '图片上传';
$acl_inc[$i]['low_leve']['kissy']= array( "图片上传" =>array(
												 "图片上传" 		=> 'at81',
												),
										   	  "data" => array(
										   		//权限管理
												'eqaction_index'  => 'at81',
											  )
							);
$i++;
$acl_inc[$i]['low_title'][] = '产品管理';
$acl_inc[$i]['low_leve']['goods']= array( "产品管理" =>array(
												 "产品管理" 		=> 'cp1',
												),
										   	  "data" => array(
										   		//权限管理
												'eqaction_index'  => 'cp1',
												'eqaction_goodedit'  => 'cp1',
												'eqaction_goodadd'  => 'cp1',
												'eqaction_dogoodedit'  => 'cp1',
												'eqaction_dodel'  => 'cp1',
												'eqaction_edit'  => 'cp1',
												'eqaction_orerupdate'=>'cp1',
												'eqaction_partner'=>'cp1',
												'eqaction_audit'=>'cp1',


												'eqaction_typelist'  => 'cp1',
												'eqaction_typeedit'  => 'cp1',
												'eqaction_typeadd'  => 'cp1',
												'eqaction_dotypeedit'  => 'cp1',
												'eqaction_order'  => 'cp1',

												  'eqaction_tuikuan'  => 'cp1',
												  'eqaction_tongyi'  => 'cp1',
												  'eqaction_refund'  => 'cp1',
												  'eqaction_refunds'  => 'cp1',
												  'eqaction_cause'=>'cp1',
												  'eqaction_refundscause'=>'cp1',
												   'eqaction_dodell'=>'cp1',



											  )
							);
//	$i++;
//	$acl_inc[$i]['low_title'][] = '拼团提现';
//	$acl_inc[$i]['low_leve']['ptti']= array( "拼团提现" =>array(
//		"拼团提现" 		=> 'pt1',
//	),
//		"data" => array(
//			//权限管理
//			'eqaction_index'  => 'pt1'
//		)
//	);

	$i++;
	$acl_inc[$i]['low_title'][] = '拼团提现';
	$acl_inc[$i]['low_leve']['ptwithdrawal']= array( "拼团提现" =>array(
		"拼团提现" 		=> 'ptt1',
	),
		"data" => array(
			//权限管理
			'eqaction_index'  => 'ptt1',
			'eqaction_edit'  => 'ptt1',
			'eqaction_eidtupdate' => 'ptt1'
		)
	);

?>
