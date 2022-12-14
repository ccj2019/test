<?php
function getbuynum($gid){
		return M('order')->where('gid ='.$gid)->count();
}

function guanzhu($id){
		return M('pro_guanzhu')->where("bid=".$id )->count('id');
}

function danye($id=1){
$vox = D('Acategory')->find($id);
return $vox["type_content"];
}
function member_logintiem($id=1){
    return M('member_login')->where('uid ='.$id)->order("id DESC")->find();
}

    function ReStrLen($str, $len=10, $etc='...')
	{
		$restr = '';
		$i = 0;
		$n = 0.0;
	
		//字符串的字节数
		$strlen = strlen($str);
		while(($n < $len) and ($i < $strlen))
		{
		   $temp_str = substr($str, $i, 1);
	
		   //得到字符串中第$i位字符的ASCII码
		   $ascnum = ord($temp_str);
	
		   //如果ASCII位高与252
		   if($ascnum >= 252) 
		   {
				//根据UTF-8编码规范，将6个连续的字符计为单个字符
				$restr = $restr.substr($str, $i, 6); 
				//实际Byte计为6
				$i = $i + 6; 
				//字串长度计1
				$n++; 
		   }
		   else if($ascnum >= 248)
		   {
				$restr = $restr.substr($str, $i, 5);
				$i = $i + 5;
				$n++;
		   }
		   else if($ascnum >= 240)
		   {
				$restr = $restr.substr($str, $i, 4);
				$i = $i + 4;
				$n++;
		   }
		   else if($ascnum >= 224)
		   {
				$restr = $restr.substr($str, $i, 3);
				$i = $i + 3 ;
				$n++;
		   }
		   else if ($ascnum >= 192)
		   {
				$restr = $restr.substr($str, $i, 2);
				$i = $i + 2;
				$n++;
		   }
	
		   //如果是大写字母 I除外
		   else if($ascnum>=65 and $ascnum<=90 and $ascnum!=73)
		   {
				$restr = $restr.substr($str, $i, 1);
				//实际的Byte数仍计1个
				$i = $i + 1; 
				//但考虑整体美观，大写字母计成一个高位字符
				$n++; 
		   }
	
		   //%,&,@,m,w 字符按1个字符宽
		   else if(!(array_search($ascnum, array(37, 38, 64, 109 ,119)) === FALSE))
		   {
				$restr = $restr.substr($str, $i, 1);
				//实际的Byte数仍计1个
				$i = $i + 1;
				//但考虑整体美观，这些字条计成一个高位字符
				$n++; 
		   }
	
		   //其他情况下，包括小写字母和半角标点符号
		   else
		   {
				$restr = $restr.substr($str, $i, 1);
				//实际的Byte数计1个
				$i = $i + 1; 
				//其余的小写字母和半角标点等与半个高位字符宽
				$n = $n + 0.5; 
		   }
		}
	
		//超过长度时在尾处加上省略号
		if($i < $strlen)
		{
		   $restr = $restr.$etc;
		}
	
		return $restr;
	}
	
	
//获取借款列表
function getBorrowList2($parm=array()){
	if(empty($parm['map'])) return;
	$map= $parm['map'];
	$orderby= $parm['orderby'];
	//$map = array_merge($map,$search);
	
	$row=array();
	if($parm['pagesize']){
		//分页处理
		import("ORG.Util.Page");
		$count = M('borrow_info b')->where($map)->count('b.id');
		$p = new Page($count, $parm['pagesize']);		
		$page = $p->show();
		$row['_page'] = pageSet($p);
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}else{
		$page="";
		$Lsql="{$parm['limit']}";
	}

	$pre = C('DB_PREFIX');
	$suffix=C("URL_HTML_SUFFIX");
	$field = "b.id,b.is_experience,b.borrow_name,b.pause,b.borrow_uid,borrow_img,b.borrow_info,b.borrow_con,b.pid,b.hits,b.borrow_type,b.reward_type,b.borrow_times,b.borrow_status,b.borrow_money,b.borrow_min,b.borrow_max,b.borrow_use,b.repayment_type,b.borrow_interest_rate,b.borrow_duration,b.collect_time,b.add_time,b.province,b.has_borrow,b.has_vouch,b.city,b.area,b.reward_type,b.reward_num,b.password,b.is_tuijian,b.danbao,IFNULL(cat.type_name,'') AS type_name,b.start_time,round(b.has_borrow/b.borrow_money*100,0) as progress,collect_day";
	$list = M('borrow_info b')->field($field)->join("{$pre}pro_category cat ON cat.id=b.pid")->where($map)->order($orderby)->limit($Lsql)->select();
//	var_dump(M('borrow_info b')->getlastsql());die;
	$areaList = getArea();
	$bConfig  = C('BORROW');
	$arraya=array();
	foreach($list as $key=>$v){
		$list[$key]['location'] = $areaList[$v['province']].$areaList[$v['city']];
		$list[$key]['biao'] = $v['borrow_times'];
		$list[$key]['borrow_img'] = str_replace("'","",$v["borrow_img"]);
		$arraya[]=$v['id'];
		if($list[$key]['borrow_img']=="")$list[$key]['borrow_img']="UF/Uploads/borrowimg/nopic.png";
		
		$list[$key]['repayment_type'] = $v["repayment_type"];
		$list[$key]['need'] = $v['borrow_money'] - $v['has_borrow'];
		$list[$key]['leftdays'] = getLeftTime($v['collect_time'],1);
    	//$list[$key]['leftdays'] = getLeftTime($v['collect_time'],1);
		//echo $v["borrow_status"];
		if($v["borrow_status"] >1){
			$list[$key]['leftdays']="已经结束";
		}
		$list[$key]['lefttime'] = $v['start_time'] - time();
		$list[$key]['progress'] = getFloatValue($v['has_borrow']/$v['borrow_money']*100,2);//2
		if(substr($list[$key]['progress'], -1)==".")$list[$key]['progress']=substr($list[$key]['progress'],0,strlen($list[$key]['progress'])-1); ;
		$list[$key]['vouch_progress'] = getFloatValue($v['has_vouch']/$v['borrow_money']*100,2);
		$list[$key]['burl'] = MU("Home/invest","invest",array("id"=>$v['id'],"suffix"=>$suffix));
		$list[$key]['borrow_status_cn']   = @$bConfig['BORROW_STATUS_SHOW'][$v["borrow_status"]];
		$investinfo = M("borrow_investor bi")->field($fieldx)->join("{$pre}members m ON bi.investor_uid = m.id")->where("bi.borrow_id={$v['id']}")->order("bi.id DESC")->select();
		$list[$key]['investinfo'] = count($investinfo);
		// $pro = M("pro_category")->where("id = {$v['pid']}")->find();
		// $list[$key]['pro_name'] = ;
	}
 
	$row['list'] = $list;
	$row['page'] = $page;	
// 	var_dump( M('borrow_info b')->getlastsql());	
// 	 exit;
	return $row;
}
//获取特定栏目下文章列表
function getBorrowList($parm=array()){
	if(empty($parm['map'])) return;
	$map= $parm['map'];
	$orderby= $parm['orderby'];
	//$map = array_merge($map,$search);
	
	$row=array();
	if($parm['pagesize']){
		//分页处理
		import("ORG.Util.Page");
		$count = M('borrow_info b')->where($map)->count('b.id');
		$p = new Page($count, $parm['pagesize']);		
		$page = $p->show();
		$row['_page'] = pageSet($p);
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}else{
		$page="";
		$Lsql="{$parm['limit']}";
	}

	$pre = C('DB_PREFIX');
	$suffix=C("URL_HTML_SUFFIX");
	$field = "b.id,b.borrow_name,b.pause,b.borrow_time,b.borrow_uid,borrow_img,b.borrow_info,b.borrow_con,b.pid,b.hits,b.borrow_type,b.reward_type,b.borrow_times,b.borrow_status,b.borrow_money,b.borrow_min,b.borrow_max,b.borrow_use,b.repayment_type,b.borrow_interest_rate,b.borrow_duration,b.collect_time,b.add_time,b.province,b.content_img,b.has_borrow,b.has_vouch,b.city,b.area,b.reward_type,b.reward_num,b.password,b.is_tuijian,b.danbao,IFNULL(cat.type_name,'') AS type_name,b.start_time,round(b.has_borrow/b.borrow_money*100,0) as progress,collect_day,b.bespeak_able,b.bespeak_money,b.bespeak_days";
	$list = M('borrow_info b')->field($field)->join("{$pre}pro_category cat ON cat.id=b.pid")->where($map)->order($orderby)->limit($Lsql)->select();
//	var_dump(M('borrow_info b')->getlastsql());die;
	$areaList = getArea();
	$bConfig  = C('BORROW');
	foreach($list as $key=>$v){
		$list[$key]['location'] = $areaList[$v['province']].$areaList[$v['city']];
		$list[$key]['biao'] = $v['borrow_times'];
		$list[$key]['borrow_img'] = str_replace("'","",$v["borrow_img"]);
		
		if($list[$key]['borrow_img']=="")$list[$key]['borrow_img']="UF/Uploads/borrowimg/nopic.png";
		
		$list[$key]['repayment_type'] = $v["repayment_type"];
		$list[$key]['need'] = $v['borrow_money'] - $v['has_borrow'];
		$list[$key]['leftdays'] = getLeftTime($v['collect_time'],1);
		
		//echo $v["borrow_status"];
		if($v["borrow_status"] >1){
			$list[$key]['leftdays']="已经结束";
		}
		$list[$key]['lefttime'] = $v['start_time'] - time();
        if($v['bespeak_able'] == 1) {
            //预约已投金额
            $bespeak_invest_money = M('bespeak')->where("borrow_id={$v['id']} and bespeak_status = 1")->sum('bespeak_money');
            $list[$key]['bespeak_progress'] = getFloatValue(($v['bespeak_money']+$v['has_borrow']-$bespeak_invest_money) / $v['borrow_money'] * 100, 2);
            if(substr($list[$key]['bespeak_progress'], -1)==".")$list[$key]['bespeak_progress']=substr($list[$key]['bespeak_progress'],0,strlen($list[$key]['bespeak_progress'])-1);
        }
		$list[$key]['progress'] = getFloatValue($v['has_borrow']/$v['borrow_money']*100,2);//2
		if(substr($list[$key]['progress'], -1)==".")$list[$key]['progress']=substr($list[$key]['progress'],0,strlen($list[$key]['progress'])-1); ;
		$list[$key]['vouch_progress'] = getFloatValue($v['has_vouch']/$v['borrow_money']*100,2);
		$list[$key]['burl'] = MU("Home/invest","invest",array("id"=>$v['id'],"suffix"=>$suffix));
		$list[$key]['borrow_status_cn']   = @$bConfig['BORROW_STATUS_SHOW'][$v["borrow_status"]];
		$investinfo = M("borrow_investor bi")->field($fieldx)->join("{$pre}members m ON bi.investor_uid = m.id")->where("bi.borrow_id={$v['id']}")->order("bi.id DESC")->select();
		$list[$key]['investinfo'] = count($investinfo);
		// $pro = M("pro_category")->where("id = {$v['pid']}")->find();
		// $list[$key]['pro_name'] = ;
	}
	
	$row['list'] = $list;
	$row['page'] = $page;	
// 	var_dump( M('borrow_info b')->getlastsql());	
// 	 exit;
	return $row;
}


function getArticleList($parm,$order="DESC"){
	if(empty($parm['type_id'])) return;
	$row=array();
	$map['type_id'] = $parm['type_id'];
	$map['is_fabu'] = 1;
	$Osql="id DESC";
	if(strcasecmp($order, "ASC") == 0){
        	$Osql = "id ASC";
    	}
	$field="id,title,art_set,art_info,art_img,art_time,art_url,art_content,type_id,art_click";
	//查询条件 
	if($parm['pagesize']){
		//分页处理
		import("ORG.Util.Page");
		$count = M('article')->where($map)->count('id');
		$p = new Page($count, $parm['pagesize']);
		$row['_page'] = pageSet($p);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}else{
		$page="";
		$Lsql="{$parm['limit']}";
	}
	$data = M('article')->field($field)->where($map)->order($Osql)->limit($Lsql)->select();
	$suffix=C("URL_HTML_SUFFIX");
	$typefix = get_type_leve_nid($map['type_id']);
	$typeu = implode("/",$typefix);
	foreach($data as $key=>$v){
		if($v['art_set']==1) $data[$key]['arturl'] = (stripos($v['art_url'],"http://")===false)?"http://".$v['art_url']:$v['art_url'];
		//elseif(count($typefix)==1) $data[$key]['arturl'] = 
		else $data[$key]['arturl'] = MU("Home/{$typeu}","article",array("id"=>$v['id'],"suffix"=>$suffix));
		if($v["art_img"]=="") $data[$key]["art_img"] = "/UF/Uploads/Article/nopic.png";		
		if($v["art_info"]=="") $data[$key]["art_info"] = strip_tags($v['art_content']);		
	}	
	$row['list'] = $data;
	$row['page'] = $page;
	
	return $row;
}

function getArticleListt($parm,$order){
    if(empty($parm['type_id'])) return;
    $row=array();
    $map['type_id'] = $parm['type_id'];
    $map['is_fabu'] = 1;
    $Osql=$order;

    $field="id,title,art_set,art_info,art_img,art_time,art_url,art_content,type_id,art_click";
    //查询条件
    if($parm['pagesize']){
        //分页处理
        import("ORG.Util.Page");
        $count = M('article')->where($map)->count('id');
        $p = new Page($count, $parm['pagesize']);
        $row['_page'] = pageSet($p);
        $page = $p->show();
        $Lsql = "{$p->firstRow},{$p->listRows}";
        //分页处理
    }else{
        $page="";
        $Lsql="{$parm['limit']}";
    }
    $data = M('article')->field($field)->where($map)->order($Osql)->limit($Lsql)->select();
    $suffix=C("URL_HTML_SUFFIX");
    $typefix = get_type_leve_nid($map['type_id']);
    $typeu = implode("/",$typefix);
    foreach($data as $key=>$v){
        if($v['art_set']==1) $data[$key]['arturl'] = (stripos($v['art_url'],"http://")===false)?"http://".$v['art_url']:$v['art_url'];
        //elseif(count($typefix)==1) $data[$key]['arturl'] =
        else $data[$key]['arturl'] = MU("Home/{$typeu}","article",array("id"=>$v['id'],"suffix"=>$suffix));
        if($v["art_img"]=="") $data[$key]["art_img"] = "/UF/Uploads/Article/nopic.png";
        if($v["art_info"]=="") $data[$key]["art_info"] = strip_tags($v['art_content']);
    }
    $row['list'] = $data;
    $row['page'] = $page;

    return $row;
}

function getCommentList($map,$size){
	$Osql="id DESC";
	$field=true;
	//查询条件 
	if($size){
		//分页处理
		import("ORG.Util.Page");
		$count = M('comment')->where($map)->count('id');
		$p = new Page($count, $size);
		$p->parameter .= "type=commentlist&";
		$p->parameter .= "id={$map['tid']}&";
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}
	$data = M('comment')->field($field)->where($map)->order($Osql)->limit($Lsql)->select();
	foreach($data as $key=>$v){
	}
	$row=array();
	$row['list'] = $data;
	$row['page'] = $page;
	$row['count'] = $count;
	
	return $row;
}
//排行榜
function getRankList($map,$size)
{
	$field = "investor_uid,sum(investor_capital) as total";
	$list = M("borrow_investor")->field($field)->where($map)->group("investor_uid")->order("total DESC")->limit($size)->select();
	foreach($list as $k=>$v )
	{
		$list[$k]['user_name'] = M("members")->getFieldById($v['investor_uid'],"user_name");
	}
	return $list;
}
