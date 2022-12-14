<?php
function src_url(){
	return C('TMPL_PARSE_STRING.__PUBLIC__').'/'.C('DEFAULT_THEME').'/';
}
function site_url(){
	return '/'.'Wap'.'/';
}
function pageSet($_page,$p = 1,$size = 10){			
	$pageSet['nowPage'] = strval(intval($p));
	$pageSet['nowPage'] = strval(intval(@$_page->nowPage));
	$pageSet['totalPages'] = strval(intval(@$_page->totalPages));
	$pageSet['totalRows'] = strval(intval(@$_page->totalRows));
	$pageSet['pageSize'] = strval($size);
	return $pageSet;
}
if(!function_exists(substr_cut)){
/**
 * 只保留字符串首尾字符，隐藏中间用*代替（两个字符时只显示第一个）
 * @param string $user_name 姓名
 * @return string 格式化后的姓名
 */
	function substr_cut($user_name){
	    $strlen     = mb_strlen($user_name, 'utf-8');
	    $firstStr     = mb_substr($user_name, 0, 1, 'utf-8');
	    $lastStr     = mb_substr($user_name, -1, 1, 'utf-8');
	    return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
	}	
}


function user_name($uid){
	$user_name = M('members')->where('id='.$uid)->getField('user_name');
    return $user_name ? $user_name : '无';
}
function getRecCount($id){
	return M('members')->where('recommend_id ='.$id)->count();
}

function areaList(){
	$area_list = M('area')->where('reid in(0,1) and is_open = 1')->order('sort_order asc , id asc')->getField('id,name');
	return $area_list;
}

/**
 * 无限极分类
 */
function getCatTree($cate,$pid=0,$pname='pid',$html='&emsp;',$level=0){
	$arr = array();			
	foreach ($cate as $key => $value) {
		if($value[$pname] == $pid){			
			$value['level'] = $level + 1;
			$value['html'] = str_repeat($html,$level);			
			$child = getCatTree($cate,$value['id'],$pname,$html,$level + 1);
			if($child){
				$value['haveson'] = true ;				
				$arr[] = $value;
				$arr = array_merge($arr,$child);										
			}else{
				$arr[] = $value;
			}
		}
	}
	return $arr;
}
/**
 * 文章列表
 */
function getArtList($type_id,$limit){
	$rs = getArticleList(array('type_id'=>intval($type_id),'limit'=>intval($limit)));	
	return $rs['list'];
}
/**
 * 理财分类
 */
function getProCat($parent_id=0){
	return M('pro_category')->where("parent_id={$parent_id} and is_hiden=0")->select();
}
/**
 * 文章分类
 */
function getCat($parent_id=0){
	return M('article_category')->where("parent_id={$parent_id} and is_hiden=0")->select();
}
/**
 * 格式化在线QQ
 */
function getQq(){
	$datag = get_global_setting();
    $datag = de_xie($datag);
	$ttxf_qq = $datag['ttxf_qq'];
	$ttxf_qqArr = explode(PHP_EOL, $ttxf_qq);	
	$ttxf_qqArr_Tmp = array();
	foreach ($ttxf_qqArr as $key => $value) {		
		$tmp = explode(':', str_replace('：', ':', $value));
		if(!isset($tmp[1])) continue;
		$ttxf_qqArr_Tmp[]=array('qqname'=>$tmp[0],'qq'=>$tmp[1]);
	}
	return $ttxf_qqArr_Tmp;
}
/**
 * 数据统计
 */
function getDataCount($type,$fmoney = true){
	$total = 0;
	$city=$_COOKIE["fz_area"];
	switch ($type) {
		//累计成交总金额
		case '1':
			$map['borrow_status'] = array('gt',5);
			$total = M('borrow_info')->where($map)->sum('borrow_money');
			// $total += M('borrow_info')->where($map)->sum('borrow_interest');			
			break;	
		//累计成交总笔数
		case '2':
			$map['borrow_status'] = array('in',array(2,4,6,7));
			$total = M('borrow_info')->where($map)->count('id');
			return $total;
			break;	
		//为用户累计赚取
		case '3':
			$minfo['receive_interest'] = M('borrow_investor')->sum('investor_interest');        
			$total += $minfo['receive_interest'];
			break;
			$map['type'] = array('in',array(28,21,32));
			$list = M("member_moneylog")->field('type,sum(affect_money) as money')->where($map)->group('type')->select();
			$row=array();
			$name = C('MONEY_LOG');
			foreach($list as $v){
				$row[$v['type']]['money']= ($v['money']>0)?$v['money']:$v['money']*(-1);
				$row[$v['type']]['name']= $name[$v['type']];
				$total+=abs($v['money']);
			}			
			break;
		//用户投资总额
		case '4':
			$total = M('borrow_investor')->sum('investor_capital');
			break;	
		//会员数
		case '5':
			$total = M('members')->count('id');
			return $total;
			break;	
		//平台待收金额
		case '6':
			$map['borrow_status'] = array('eq',6);
			$total = M('borrow_info')->where($map)->sum('borrow_money');
			$total += M('borrow_info')->where($map)->sum('borrow_interest');			
			break;
		//
		case '7':
			$total = M('investor_detail')->where("FROM_UNIXTIME(`deadline`,'%Y-%m-%d') = date_add(curdate(), interval 1 day)")->sum('capital');
			$total += M('investor_detail')->where("FROM_UNIXTIME(`deadline`,'%Y-%m-%d') = date_add(curdate(), interval 1 day)")->sum('interest');
			break;	
		//今日投资
		case '8':			
			$where = ' borrow_status >= 6 and date(FROM_UNIXTIME(`second_verify_time`)) = CURDATE() ';
			$total = M('borrow_info')->where($where)->sum('borrow_money');
			// $total += M('borrow_info')->where($where)->sum('borrow_interest');						
			break;
		//昨日投资
		case '9':			
			$where = " borrow_status >= 6 and FROM_UNIXTIME(`second_verify_time`,'%Y-%m-%d') = date_sub(curdate(), interval 1 day)";
			$total = M('borrow_info')->where($where)->sum('borrow_money');
			// $total += M('borrow_info')->where($where)->sum('borrow_interest');						
			break;
		// 已还收益
		case '10':
			$where = "deadline>0 and repayment_time >0";
			$total = M('investor_detail')->where($where)->sum('interest');			
			break;
		//待还收益
		case '11':			
			$where = "deadline>0 and repayment_time =0";
			$total = M('investor_detail')->where($where)->sum('interest');
			break;
		case '12':			
			$where = ' borrow_status >= 6';
			$total = M('borrow_info')->where($where)->sum('borrow_money');
			break;
		case '13':	
			if($city==1){
				$where = ' borrow_status in (2,4,6,7) and start_time<'.time();
			}else{
				$where = ' borrow_status in (2,4,6,7) and city='.$city.' and start_time<'.time();
			}			
			
			$total = M('borrow_info')->where($where)->count('id');
			break;
		case '14':
			if($city==1){
				$where = ' borrow_status in (2,4,6,7) and start_time>'.time();
			}else{
				$where = ' borrow_status in (2,4,6,7) and city='.$city.' and start_time>'.time();	
			}	
			
			$total = M('borrow_info')->where($where)->count('id');
			break;
		default:			
			break;
	}
	return $fmoney ? getMoneyFormt($total,'') : $total;
}
//获取特定栏目下文章列表
function getAreaArticleList($parm){
	if(empty($parm['type_id'])) return;
	$map['type_id'] = $parm['type_id'];
	$Osql="id DESC";
	$field="id,title,art_set,art_time,art_url,area_id";
	//查询条件 
	if($parm['pagesize']){
		//分页处理
		import("ORG.Util.Page");
		$count = M('article_area')->where($map)->count('id');
		$p = new Page($count, $parm['pagesize']);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}else{
		$page="";
		$Lsql="LIMIT {$parm['limit']}";
	}
	$data = M('article_area')->field($field)->where($map)->order($Osql)->limit($Lsql)->select();
	$suffix=C("URL_HTML_SUFFIX");
	$typefix = get_type_leve_area_nid($map['type_id'],$parm['area_id']);
	$typeu = implode("/",$typefix);
	foreach($data as $key=>$v){
		if($v['art_set']==1) $data[$key]['arturl'] = (stripos($v['art_url'],"http://")===false)?"http://".$v['art_url']:$v['art_url'];
		//elseif(count($typefix)==1) $data[$key]['arturl'] = 
		else $data[$key]['arturl'] = MU("Home/{$typeu}","article",array("id"=>"id-".$v['id'],"suffix"=>$suffix));
	}
	$row=array();
	$row['list'] = $data;
	$row['page'] = $page;
	
	return $row;
}
//获取下级或者同级栏目列表
function getAreaTypeList($parm){
	//if(empty($parm['type_id'])) return;
	$Osql="sort_order DESC";
	$field="id,type_name,type_set,add_time,type_url,type_nid,parent_id,area_id";
	//查询条件 
	$Lsql="{$parm['limit']}";
	$pc = D('Aacategory')->where("parent_id={$parm['type_id']} AND area_id={$parm['area_id']}")->count('id');
	if($pc>0){
		$map['is_hiden'] = 0;
		$map['parent_id'] = $parm['type_id'];
		$map['area_id'] = $parm['area_id'];
		$data = D('Aacategory')->field($field)->where($map)->order($Osql)->limit($Lsql)->select();
	}elseif(!isset($parm['notself'])){
		$map['is_hiden'] = 0;
		$map['parent_id'] = D('Aacategory')->getFieldById($parm['type_id'],'parent_id');
		$map['area_id'] = $parm['area_id'];
		$data = D('Aacategory')->field($field)->where($map)->order($Osql)->limit($Lsql)->select();
	}
	//链接处理
	$typefix = get_type_leve_area_nid($parm['type_id'],$parm['area_id']);
	$typeu = $typefix[0];
	$suffix=C("URL_HTML_SUFFIX");
	foreach($data as $key=>$v){
		if($v['type_set']==2){
			if(empty($v['type_url'])) $data[$key]['turl']="javascript:alert('请在后台添加此栏目链接');";
			else $data[$key]['turl'] = $v['type_url'];
		}
		elseif($v['parent_id']==0&&count($typefix)==1) $data[$key]['turl'] = MU("Home/{$v['type_nid']}/index","typelist",array("id"=>$v['id'],"suffix"=>$suffix));
		else $data[$key]['turl'] = MU("Home/{$typeu}/{$v['type_nid']}","typelist",array("id"=>$v['id'],"suffix"=>$suffix));
	}
	$row=array();
	$row = $data;
	
	return $row;
}
function getsdcity(){
	return M('area')->where("reid=22")->select();
}