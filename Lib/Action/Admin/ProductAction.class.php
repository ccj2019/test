<?php
/**
 * 理财产品主控制器
 */

class ProductAction extends ACommonAction
{

    /**
     * 理财列表添加
     */
    public function add()
    {	
    	$action = isset($_POST['action']) ? $_POST['action'] : '';
    	// 保存理财    	
    	if($action == 'adddo'){

			$pre = C('DB_PREFIX');
			//相关的判断参数
			
			$rate_lixt = explode("|",$this->glo['rate_lixi']);
			$borrow_duration = explode("|",$this->glo['borrow_duration']);		
			$fee_borrow_manage = explode("|",$this->glo['fee_borrow_manage']);
			
			//相关的判断参数			
			
		  // if(empty($_POST['borrow_interest_rate']) || floatval($_POST['borrow_interest_rate'])>$rate_lixt[1] || floatval($_POST['borrow_interest_rate'])<$rate_lixt[0]) $this->error("提交的众筹利率有误，请重试",0);
			
			$borrow['borrow_money'] = $borrow_money = intval($_POST['borrow_money']);
			if($borrow['borrow_money']<=0) $this->error("众筹金额必须大于0 !");

			
			$borrow_username = empty($_POST['borrow_username']) ? '' : $_POST['borrow_username'];		

			$borrow_member = M('members')->where(array('user_name'=>$borrow_username))->find();
			
			if(!is_array($borrow_member)||count($borrow_member)<1) {$this->error("众筹用户不存在 !");die;}
			$borrow['borrow_uid'] = $borrow_member['id'];

			$_minfo = getMinfo($borrow['borrow_uid'],true);
			$vminfo = M('members')->field("user_leve,time_limit")->find($borrow['borrow_uid']);	
			
			
			$borrow['borrow_duration'] = intval($_POST['borrow_duration']);//秒标固定为一月
			if(empty($borrow['borrow_duration'])) $this->error("请正确填写众筹期限 !");
			$borrow['borrow_interest_rate'] = floatval($_POST['borrow_interest_rate']);
			

			$borrow['repayment_type'] = intval($_POST['repayment_type']);
			$borrow['total'] = ($borrow['repayment_type']==1)?1:$borrow['borrow_duration'];//共几期(分几次还)
			if($borrow['repayment_type']==5){
				$borrow['total']=1;	
			}

			$borrow['borrow_status'] = 0;			
			$borrow['borrow_type'] = intval($_POST['borrow_type']);
			if(empty($borrow['borrow_type'])) $this->error("请正确选择所属分类 !");
			$borrow['add_time'] = time();
			$borrow['collect_day'] = intval($_POST['borrow_time']);
			if(empty($borrow['collect_day'])) $this->error("请正确填写有效时间 !");
			$borrow['add_ip'] = get_client_ip();

			// 每份金额
			$borrow['invest_method'] = isset($_POST['invest_method']) ? $_POST['invest_method'] : 0;
			if(isset($_POST['invest_method']) && $_POST['invest_method']==1){
				$each_money = $_POST['each_money'];
				$each_number = intval($_POST['each_number']);
				if($borrow_money%$each_money!=0){
					$this->error("请正确填写每份金额,确保众筹总额能够平均等分 !");
				}
				$each_number_all = intval($borrow_money/$each_money);
				if($each_number>$each_number_all){
					$this->error("单人可投份数不可大于可投总份数 !");
				}
				$borrow['each_number'] = intval($_POST['each_number']);
				$borrow['each_money'] = intval($_POST['each_money']);
			}else{
				$borrow['borrow_min'] = intval($_POST['borrow_min']);
				$borrow['borrow_max'] = empty($_POST['borrow_max']) ? 50 : $_POST['borrow_max'];

				if($borrow['borrow_max']>0 && $borrow['borrow_max']<$borrow['borrow_min']){
					$this->error("最多投标总额不可小于最低投标总额");	
				}
			}
			$borrow['borrow_name'] = text($_POST['borrow_name']);
			if(empty($borrow['borrow_name'])) $this->error("请正确填写项目标题 !");
			$borrow['borrow_video'] = text($_POST['borrow_video']);
			$borrow['borrow_info'] = stripslashes(htmlspecialchars_decode($_POST['borrow_info']));
			if(empty($borrow['borrow_info'])) $this->error("请正确填写项目说明 !");
			
			$borrow['reward_type'] = '';
			$borrow['reward_num'] = 0;			
			$borrow['province'] = empty($vminfo['province_now']) ? '' : $vminfo['province_now'];
			$borrow['city'] = empty($vminfo['city_now']) ? '' : $vminfo['city_now'];
			$borrow['area'] = empty($vminfo['area_now']) ? '' : $vminfo['area_now'];
			$borrow['password'] = 0;	
			//借款费和利息
			$borrow['borrow_interest'] = getBorrowInterest($borrow['repayment_type'],$borrow['borrow_money'],$borrow['borrow_duration'],$borrow['borrow_interest_rate']);
			
					
			$borrow['borrow_fee'] = 0;
			
			$img="";
			$bigimg="";
			if(!empty($_FILES['borrow_files']['name'])){
				$this->fix = false;
				$this->saveRule = 'my_filename';
				$this->savePathNew = 'UF/Uploads/borrowimg/';
				$info = $this->CUpload();
				$img = $info[0]['savepath'].$info[0]['savename'];
				$bigimg = $info[1]['savepath'].$info[1]['savename'];
			}
			
			$borrow['borrow_img']=$img;
			$borrow['borrow_img_big']=$bigimg;
			
			//投标上传图片资料（暂隐）
			foreach($_POST['swfimglist'] as $key=>$v){
				if($key>10) break;
				$row[$key]['img'] = substr($v,1);
				$row[$key]['info'] = $_POST['picinfo'][$key];
			}
			$borrow['updata']=serialize($row);			
			$borrow['auto_info'] = '';			
			$borrow['p_auto_info'] = '';			
			$newid = M("pro_borrow_info")->add($borrow);
			
			if($newid){
				 $this->success("众筹发布成功，网站会尽快初审",U('product/waitverify'));die;
			}else {
				$this->error("发布失败");die;
			}
    	}		
    	$borrow_duration = explode("|",$this->glo['borrow_duration']);
		$month = range($borrow_duration[0],$borrow_duration[1]);
		$month_time=array();
		foreach($month as $v){
			$month_time[$v] = $v."个月";
		}	
		$rate_lixt = explode("|",$this->glo['rate_lixi']);
		$borrow_config = require C("APP_ROOT")."Conf/borrow_config.php";
		$this->assign("borrow_use",$borrow_config['BORROW_USE']);	
		$this->assign("borrow_min",$borrow_config['BORROW_MIN']);
		$this->assign("borrow_max",$borrow_config['BORROW_MAX']);	
		$this->assign("borrow_time",$borrow_config['BORROW_TIME']);						
		$this->assign("borrow_month_time",$month_time);		
		$this->assign("repayment_type",$borrow_config['REPAYMENT_TYPE_PRO']);
		$this->assign("vkey",$vkey);
		$this->assign("rate_lixt",$rate_lixt);
		$typelist = get_type_leve_list('0','Productcategory');//分级栏目
		$this->assign('type_list',$typelist);
		$this->display();
    }    
    /**
     * 理财列表
     */
    public function waitverify()
    {
    	$map=array();
		$map['b.borrow_status'] = 0;
		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){
			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');
			$map['b.borrow_uid'] = $uid;
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}
		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){
			$map['b.borrow_uid'] = intval($_REQUEST['uid']);
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['b.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		

		if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){
			$map['m.customer_id'] = $_REQUEST['customer_id'];
			$search['customer_id'] = $map['m.customer_id'];	
			$search['customer_name'] = urldecode($_REQUEST['customer_name']);	
		}
		
		if($_REQUEST['customer_name'] && !$search['customer_id']){
			$cusname = urldecode($_REQUEST['customer_name']);
			$kfid = M('ausers')->getFieldByUserName($cusname,'id');
			$map['m.customer_id'] = $kfid;
			$search['customer_name'] = $cusname;	
			$search['customer_id'] = $kfid;	
		}


		//分页处理
		import("ORG.Util.Page");
		$count = M('pro_borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";

		//分页处理
		
		$field= 'b.id,b.borrow_name,b.borrow_uid,b.borrow_duration,b.borrow_type,b.updata,b.borrow_money,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.add_time,m.user_name,m.id mid,b.is_tuijian';
		$list = M('pro_borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();
		$list = $this->_listFilter($list);
		
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
		$this->assign("xaction",ACTION_NAME);
        $this->assign("query", http_build_query($search));		
        
    	$this->display();
    }
        public function waitmoney()
    {
		$map=array();
		$map['b.borrow_status'] = 2;
		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){
			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');
			$map['b.borrow_uid'] = $uid;
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}
		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){
			$map['b.borrow_uid'] = intval($_REQUEST['uid']);
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['b.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		
		if(session('admin_is_kf')==1){
				$map['m.customer_id'] = session('admin_id');
		}else{
			if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){
				$map['m.customer_id'] = $_REQUEST['customer_id'];
				$search['customer_id'] = $map['m.customer_id'];	
				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	
			}
			
			if($_REQUEST['customer_name'] && !$search['customer_id']){
				$cusname = urldecode($_REQUEST['customer_name']);
				$kfid = M('ausers')->getFieldByUserName($cusname,'id');
				$map['m.customer_id'] = $kfid;
				$search['customer_name'] = $cusname;	
				$search['customer_id'] = $kfid;	
			}
		}
		//分页处理
		import("ORG.Util.Page");
		$count = M('pro_borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$field= 'b.id,b.borrow_name,b.borrow_uid,b.borrow_duration,b.has_borrow,b.borrow_type,b.borrow_money,b.updata,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.add_time,m.user_name,m.id mid,b.is_tuijian';
		$list = M('pro_borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();
		$list = $this->_listFilter($list);
		
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
		$this->assign("xaction",ACTION_NAME);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }
	
    public function repaymenting()
    {
		
		
		$map=array();
		$map['b.borrow_status'] = 6;//还款中
		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){
			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');
			$map['b.borrow_uid'] = $uid;
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}
		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){
			$map['b.borrow_uid'] = intval($_REQUEST['uid']);
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['b.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		
		if(session('admin_is_kf')==1){
				$map['m.customer_id'] = session('admin_id');
		}else{
			if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){
				$map['m.customer_id'] = $_REQUEST['customer_id'];
				$search['customer_id'] = $map['m.customer_id'];	
				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	
			}
			
			if($_REQUEST['customer_name'] && !$search['customer_id']){
				$cusname = urldecode($_REQUEST['customer_name']);
				$kfid = M('ausers')->getFieldByUserName($cusname,'id');
				$map['m.customer_id'] = $kfid;
				$search['customer_name'] = $cusname;	
				$search['customer_id'] = $kfid;	
			}
		}
		//分页处理
		import("ORG.Util.Page");
		$count = M('pro_borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$field= 'b.id,b.borrow_name,b.borrow_uid,b.borrow_duration,b.borrow_type,b.borrow_money,b.borrow_interest,b.updata,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.repayment_money,b.total,b.has_pay,b.deadline,m.user_name,m.id mid,b.is_tuijian';
		$list = M('pro_borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();
		$list = $this->_listFilter($list);
		
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
		$this->assign("xaction",ACTION_NAME);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }
        public function done()
    {
		$map=array();
		$map['b.borrow_status'] = array("in","7,9");
		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){
			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');
			$map['b.borrow_uid'] = $uid;
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}
		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){
			$map['b.borrow_uid'] = intval($_REQUEST['uid']);
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['b.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		
		if(session('admin_is_kf')==1){
				$map['m.customer_id'] = session('admin_id');
		}else{
			if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){
				$map['m.customer_id'] = $_REQUEST['customer_id'];
				$search['customer_id'] = $map['m.customer_id'];	
				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	
			}
			
			if($_REQUEST['customer_name'] && !$search['customer_id']){
				$cusname = urldecode($_REQUEST['customer_name']);
				$kfid = M('ausers')->getFieldByUserName($cusname,'id');
				$map['m.customer_id'] = $kfid;
				$search['customer_name'] = $cusname;	
				$search['customer_id'] = $kfid;	
			}
		}
		//分页处理
		import("ORG.Util.Page");
		$count = M('pro_borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$field= 'b.id,b.borrow_name,b.borrow_uid,b.borrow_duration,b.borrow_type,b.borrow_money,b.updata,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.repayment_money,b.deadline,m.id mid,m.user_name';
		$list = M('pro_borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();
		$list = $this->_listFilter($list);
	
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
		$this->assign("xaction",ACTION_NAME);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }

    public function editproduct($id)
    {
		
		$model = M('pro_borrow_info');

        $id = intval($_REQUEST['id']);
		$Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";
		$borrow_status = $Bconfig['BORROW_STATUS'];
		switch(strtolower(session('listaction'))){
			case "waitverify":
				for($i=0;$i<=10;$i++){
					if(in_array($i,array("1","2")) ) continue;
					unset($borrow_status[$i]);
				}
			break;
			case "waitverify2":
				for($i=0;$i<=10;$i++){
					if(in_array($i,array("5","6")) ) continue;
					unset($borrow_status[$i]);
				}
			break;
			case "waitmoney":
				for($i=0;$i<=10;$i++){
					//if(in_array($i,array("2","3")) ) continue;
					if(in_array($i,array("2")) ) continue;
					unset($borrow_status[$i]);
				}
			break;
			case "fail":
				unset($borrow_status['3'],$borrow_status['4'],$borrow_status['5']);
			break;
		}
		///////////////////////////////////////////////////////////////////////////////////
		$danbao = M('article_category')->field('id,type_name')->where('parent_id=354')->select();
		$dblist = array();
		if(is_array($danbao)){
			foreach($danbao as $key => $v){
				$dblist[$v['id']]=$v['type_name'];
			}
		}		
		
		//$this->assign('xact',session('listaction'));
		if(strtolower(session('listaction')) != 'waitverify'){
			$this->assign('xact',"Product");
		}else{
			$this->assign('xact',"Waitverify");	
		}

		
		$btype = $Bconfig['REPAYMENT_TYPE_PRO'];
		$this->assign("vv",M("borrow_verify")->find($id));
		$this->assign('borrow_status',$borrow_status);
		$this->assign('type_list',$btype);

		$typelist = get_type_leve_list('0','Productcategory');//分级栏目

		$this->assign('borrow_type',$typelist);

		$Productcategory = D('Productcategory')->getField('id,type_name');
		
		$this->assign('Productcategory',$Productcategory);		
		
		//setBackUrl(session('listaction'));	
		$vo = $model->find($id);

		
		
        $this->assign('vo', $vo);        
        $this->display();
    }
    public function doEditWaitverify(){
        $m = D(ucfirst($this->getActionName()));
        if (false === $m->create()) {
            $this->error($m->getError());
        }        

		$vm = M('pro_borrow_info')->field('borrow_money,has_borrow,borrow_uid,borrow_status,borrow_type,first_verify_time,password,updata,danbao,vouch_money,borrow_img,borrow_img_big')->find($m->id);
		
		if($vm['borrow_status']==2 && $m->borrow_status<>2){
			$this->error('已通过初审通过的借款不能改为别的状态');
			exit;
		}
		
		$img="";
		$bigimg="";
		
		if(!empty($_FILES['borrow_files']['name'][0]) or !empty($_FILES['borrow_files']['name'][1])){
			$this->fix = false;
			$this->saveRule = 'my_filename';
			$this->savePathNew = 'UF/Uploads/borrowimg/';
			$info = $this->CUpload();
			if($_FILES['borrow_files']['name'][0]!=""){
				$img = $info[0]['savepath'].$info[0]['savename'];
				$vm['borrow_img']=$img;	
			}
			if($_FILES['borrow_files']['name'][1]!=""){
				$bigimg = $info[1]['savepath'].$info[1]['savename'];
				$vm['borrow_img_big']=$bigimg;
			}
		}
		
		////////////////////图片编辑///////////////////////
		if(!empty($_POST['swfimglist'])){
			foreach($_POST['swfimglist'] as $key=>$v){
				$row[$key]['img'] = substr($v,1);
				$row[$key]['info'] = $_POST['picinfo'][$key];
			}
			$m->updata=serialize($row);
		}
		////////////////////图片编辑///////////////////////
		
		if($vm['borrow_status']<>2 && $m->borrow_status==2){
		  //新标提醒
			//newTip($m->id);
			MTip('chk8',$vm['borrow_uid'],$m->id);
		  //自动投标
			if($m->borrow_type==1){
				memberLimitLog($vm['borrow_uid'],1,-($m->borrow_money),$info="{$m->id}号标初审通过");
			}elseif($m->borrow_type==2){
				memberLimitLog($vm['borrow_uid'],2,-($m->borrow_money),$info="{$m->id}号标初审通过");
			}
			$vss = M("members")->field("user_phone,user_name")->where("id = {$vm['borrow_uid']}")->find();
			SMStip("firstV",$vss['user_phone'],array("#USERANEM#","ID"),array($vss['user_name'],$m->id));
		}
		//if($m->borrow_status==2) $m->collect_time = strtotime("+ {$m->collect_day} days");
		if($m->borrow_status==2){ 
			$m->collect_time = strtotime("+ {$m->collect_day} days");
			//$m->is_tuijian = 1;
		}
		//$m->borrow_interest = getBorrowInterest($m->repayment_type,$m->borrow_money,$m->borrow_duration,$m->borrow_interest_rate);
		
		// 每份金额
		if($vm['invest_method']==1){			
			$each_money = $m->each_money;
			$each_number = $m->each_number;		
			$need_money = bcsub($vm['borrow_money'],$vm['has_borrow'],2);		
			if($need_money%$each_money!=0){
				$this->error("请正确填写每份金额,确保借贷总额能够平均等分 !");die;
			}		
			$each_number_all = intval($need_money/$each_money);
			if($each_number>$each_number_all){
				$this->error("单人可投份数不可大于可投总份数 !");die;
			}
		}else{						
			if($m->borrow_max>0 && $m->borrow_max<$vm['borrow_min']){
				$this->error("最多投标总额不可小于最低投标总额");	
			}
		}
		$m->add_time=time();//修改 
		
		
        //保存当前数据对象
		if($m->borrow_status==2 || $m->borrow_status==1) $m->first_verify_time = time();
		else unset($m->first_verify_time);
		unset($m->borrow_uid);
		$bs = intval($_POST['borrow_status']);
        if ($result = $m->save()) { //保存成功
			if($bs==2 || $bs==1){
				$verify_info['borrow_id'] = intval($_POST['id']);
				$verify_info['deal_info'] = text($_POST['deal_info']);
				$verify_info['deal_user'] = $this->admin_id;
				$verify_info['deal_time'] = time();
				$verify_info['deal_status'] = $bs;
				if($vm['first_verify_time']>0) M('pro_borrow_verify')->save($verify_info);
				else  M('pro_borrow_verify')->add($verify_info);
			}						
            //成功提示
            $this->assign('jumpUrl', __URL__."/".session('listaction'));
            $this->success(L('修改成功'));
        } else {
            //失败提示
            $this->error(L('你未做任何修改或修改失败！'));
		}	
	}
	public function doeditProduct(){
		$m = D(ucfirst($this->getActionName()));
        if (false === $m->create()) {
            $this->error($m->getError());
        }        

		$vm = M('pro_borrow_info')->field('borrow_money,has_borrow,borrow_uid,borrow_status,borrow_type,first_verify_time,password,updata,danbao,vouch_money')->find($m->id);

		////////////////////图片编辑///////////////////////
		if(!empty($_POST['swfimglist'])){
			foreach($_POST['swfimglist'] as $key=>$v){
				$row[$key]['img'] = substr($v,1);
				$row[$key]['info'] = $_POST['picinfo'][$key];
			}
			$m->updata=serialize($row);
		}
		////////////////////图片编辑///////////////////////

		//if($m->borrow_status==2) $m->collect_time = strtotime("+ {$m->collect_day} days");
		if($m->borrow_status==2){ 
			$m->collect_time = strtotime("+ {$m->collect_day} days");
			//$m->is_tuijian = 1;
		}
		//$m->borrow_interest = getBorrowInterest($m->repayment_type,$m->borrow_money,$m->borrow_duration,$m->borrow_interest_rate);
		
		// 每份金额
		$each_money = $m->each_money;
		$each_number = $m->each_number;
		
		$need_money = bcsub($vm['borrow_money'],$vm['has_borrow'],2);		
		
		if($need_money%$each_money!=0){
			$this->error("请正确填写每份金额,确保借贷总额能够平均等分 !");die;
		}		
		$each_number_all = intval($need_money/$each_money);
		if($each_number>$each_number_all){
			$this->error("单人可投份数不可大于可投总份数 !");die;
		}		
		
		
        //保存当前数据对象
		if($m->borrow_status==2 || $m->borrow_status==1) $m->first_verify_time = time();
		else unset($m->first_verify_time);
		unset($m->borrow_uid);
		$bs = intval($_POST['borrow_status']);
        if ($result = $m->save()) { //保存成功
			if($bs==2 || $bs==1){
				$verify_info['borrow_id'] = intval($_POST['id']);
				$verify_info['deal_info'] = text($_POST['deal_info']);
				$verify_info['deal_user'] = $this->admin_id;
				$verify_info['deal_time'] = time();
				$verify_info['deal_status'] = $bs;
				if($vm['first_verify_time']>0) M('pro_borrow_verify')->save($verify_info);
				else  M('pro_borrow_verify')->add($verify_info);
			}			
            //成功提示
            $this->assign('jumpUrl', __URL__."/".session('listaction'));
            $this->success(L('修改成功'));
        } else {
            //失败提示
            $this->error(L('你未做任何修改或修改失败！'));
		}			
	}
    public function _listFilter($list){
		session('listaction',ACTION_NAME);
		$Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";
	 	$listType = $Bconfig['REPAYMENT_TYPE'];
	 	$BType = $Bconfig['BORROW_TYPE'];
		$row=array();
		$aUser = get_admin_name();

		$Productcategory = D('Productcategory')->getField('id,type_name');

		foreach($list as $key=>$v){
			$v['repayment_type_num'] = $v['repayment_type'];
			$v['repayment_type'] = $listType[$v['repayment_type']];
			$v['borrow_type'] = $Productcategory[$v['borrow_type']];			
			if($v['deadline']) $v['overdue'] = getLeftTime($v['deadline']) * (-1);
			if($v['borrow_status']==1 || $v['borrow_status']==3 || $v['borrow_status']==5){
				$v['deal_uname_2'] = $aUser[$v['deal_user_2']];
				$v['deal_uname'] = $aUser[$v['deal_user']];
			}
			
			$row[$key]=$v;
		}
		return $row;
	}
}