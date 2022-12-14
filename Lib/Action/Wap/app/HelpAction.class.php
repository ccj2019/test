<?php
// 本类由系统自动生成，仅供测试用途
class HelpAction extends HCommonAction {
	/**
	 * 新手指引
	 */
	public function novice(){
		$this->display();
	}
	public function vc(){
		$glo = array('web_title'=>'龙采集团战略入股');
    	$this->assign($glo);	
		$this->display();
	}
    public function index(){
    	$cid = $tpl_var['cid'] = empty($_REQUEST['cid']) ? 398 : $_REQUEST['cid'];
        $id = $tpl_var['id'] =empty($_REQUEST['id']) ? '' : $_REQUEST['id'];
        $tpl_var['nowCategory'] = $nowCategory = M('article_category')->where(array('is_hiden'=>0))->find($cid);        
       
        // 五大类
        $map = array(
            'parent_id'=>398,
            'is_hiden'=>0
            );
        $tpl_var['menuList_class']= array('mem_spec89','mem_spec90','mem_spec91','mem_spec92');
        $menuList = M('article_category')->where($map)->limit(5)->select(); 
        foreach ($menuList as $key => $value) {
            if($value['type_set'] == 2) $menuList[$key]['linkurl'] = $value['type_url'];
            else $menuList[$key]['linkurl'] = U('help/index',array('cid'=>$value['id']));
            $map['parent_id'] = $value['id'];
			$menuList[$key]['sub']  = M('article_category')->where($map)->limit(5)->select(); 
			foreach ($menuList[$key]['sub'] as $k => $v) {
				if($v['type_set'] == 2) $menuList[$key]['sub'][$key]['linkurl'] = $v['type_url'];
            	else $menuList[$key]['sub'][$k]['linkurl'] = U('help/index',array('cid'=>$v['id']));
			}
			if($menuList[$key]['id'] == $cid || $menuList[$key]['id'] == $nowCategory['parent_id']) {
                $menuList[$key]['active'] = 'active';                
                $tpl_var['nowCategory_par'] = $value;
            }
        }
        // dump($menuList);
        $tpl_var['menuList'] = $menuList;
        // 获取当前分类信息
        $nowCategory = M('article_category')->where(array('is_hiden'=>0))->find($cid);
        if(empty($nowCategory)) $this->error('您访问的页面不存在！');
        $tpl_var['isArticle'] = 1;
        if($nowCategory['type_set']==0){ 
            if($id){//文章单页
            }else{//分类单页
                $tpl_var['article']['art_content'] = $nowCategory['type_content'];
                $tpl_var['article']['title'] = $nowCategory['type_name'];
            }
            
        }else{
            // 获取列表
            if($id){
                // 获取文章
                $tpl_var['article'] = M('article')->find($id);
            }else{
                $parm = array(
                'type_id'=> $nowCategory['id'],
                'pagesize'=>20
                );                                    
                $tpl_var['article']['title'] = $nowCategory['type_name'];
                $tpl_var['list'] = getArticleList($parm);           
                $tpl_var['isArticle'] = 0;
            }            
        } 
        $this->assign($tpl_var);
    	$this->display();    
    }
	
	public function view(){
		$id = intval($_GET['id']);
		if($_GET['type']=="subsite") $vo = M('article_area')->find($id);
		else $vo = M('article')->find($id);
		$this->assign("vo",$vo);
		//left
		$typeid = $vo['type_id'];
		$listparm['type_id']=$typeid;
		$listparm['limit']=20;
		if($_GET['type']=="subsite"){
			$listparm['area_id'] = $this->siteInfo['id'];
			$leftlist = getAreaTypeList($listparm);
		}else	$leftlist = getTypeList($listparm);
		
		$this->assign("leftlist",$leftlist);
		$this->assign("cid",$typeid);
		
		if($_GET['type']=="subsite"){
			$vop = D('Aacategory')->field('type_name,parent_id')->find($typeid);
			if($vop['parent_id']<>0) $this->assign('cname',D('Aacategory')->getFieldById($vop['parent_id'],'type_name'));
			else $this->assign('cname',$vop['type_name']);
		}else{
			$vop = D('Acategory')->field('type_name,parent_id')->find($typeid);
			if($vop['parent_id']<>0) $this->assign('cname',D('Acategory')->getFieldById($vop['parent_id'],'type_name'));
			else $this->assign('cname',$vop['type_name']);
		}
		$this->display();
	}
	
	public function kf(){
		$kflist = M("ausers")->where("is_kf=1")->select();
		$this->assign("kflist",$kflist);
		//left
		$listparm['type_id']=0;
		$listparm['limit']=20;
		if($_GET['type']=="subsite"){
			$listparm['area_id'] = $this->siteInfo['id'];
			$leftlist = getAreaTypeList($listparm);
		}else	$leftlist = getTypeList($listparm);
		
		$this->assign("leftlist",$leftlist);
		$this->assign("cid",$typeid);
		
		if($_GET['type']=="subsite"){
			$vop = D('Aacategory')->field('type_name,parent_id')->find($typeid);
			if($vop['parent_id']<>0) $this->assign('cname',D('Aacategory')->getFieldById($vop['parent_id'],'type_name'));
			else $this->assign('cname',$vop['type_name']);
		}else{
			$vop = D('Acategory')->field('type_name,parent_id')->find($typeid);
			if($vop['parent_id']<>0) $this->assign('cname',D('Acategory')->getFieldById($vop['parent_id'],'type_name'));
			else $this->assign('cname',$vop['type_name']);
		}
		$this->display();
	}
	
	public function tuiguang(){
		$_P_fee=get_global_setting();
		$this->assign("reward",$_P_fee);	
		$field = " m.id,m.user_name,sum(ml.affect_money) jiangli ";
		$list = M("members m")->field($field)->join(" lzh_member_moneylog ml ON m.id = ml.target_uid ")->where("ml.type=13")->group("ml.uid")->order('jiangli desc')->limit(10)->select();
		$this->assign("list",$list);	
		
		$this->display();
	}
	
	public function newbie(){
		$is_subsite=false;
		$typeinfo = get_type_infos();
		if(intval($typeinfo['typeid'])<1){
			$typeinfo = get_area_type_infos($this->siteInfo['id']);
			$is_subsite=true;
		}
		//print_r($typeinfo);
		$typeid = $typeinfo['typeid'];
		$typeset = $typeinfo['typeset'];
		//left
		$listparm['type_id']=$typeid;
		$listparm['limit']=20;
		if($is_subsite===false) $leftlist = getTypeList($listparm);
		else{
			$listparm['area_id'] = $this->siteInfo['id'];
			$leftlist = getAreaTypeList($listparm);
		}
		$this->assign("leftlist",$leftlist);
		$this->assign("cid",$typeid);
		
		if($typeset==1){
			$parm['pagesize']=15;
			$parm['type_id']=$typeid;
			if($is_subsite===false){
				$list = getArticleList($parm);
				$vo = D('Acategory')->find($typeid);
				if($vo['parent_id']<>0) $this->assign('cname',D('Acategory')->getFieldById($vo['parent_id'],'type_name'));
				else $this->assign('cname',$vo['type_name']);
			}
			else{
				$vo = D('Aacategory')->find($typeid);
				if($vo['parent_id']<>0) $this->assign('cname',D('Aacategory')->getFieldById($vo['parent_id'],'type_name'));
				else $this->assign('cname',$vo['type_name']);
				$parm['area_id']= $this->siteInfo['id'];
				$list = getAreaArticleList($parm);
			}
			$this->assign("vo",$vo);
			$this->assign("list",$list['list']);
			$this->assign("pagebar",$list['page']);
		}else{
			if($is_subsite===false){
				$vo = D('Acategory')->find($typeid);
				if($vo['parent_id']<>0) $this->assign('cname',D('Acategory')->getFieldById($vo['parent_id'],'type_name'));
				else $this->assign('cname',$vo['type_name']);
			}else{
				$vo = D('Aacategory')->find($typeid);
				if($vo['parent_id']<>0) $this->assign('cname',D('Aacategory')->getFieldById($vo['parent_id'],'type_name'));
				else $this->assign('cname',$vo['type_name']);
			}
			$this->assign("vo",$vo);
		}
		
		$this->display();
	}
	
	public function ruhejieru(){
		$_P_fee=get_global_setting();
		
		$this->assign("vox",$vox);	
		$this->display();
	}
	
	public function ruhejiechu(){
		$_P_fee=get_global_setting();
		$this->assign("reward",$_P_fee);	
		$field = " m.id,m.user_name,sum(ml.affect_money) jiangli ";
		$list = M("members m")->field($field)->join(" lzh_member_moneylog ml ON m.id = ml.target_uid ")->where("ml.type=13")->group("ml.uid")->order('jiangli desc')->limit(10)->select();
		$this->assign("list",$list);	
		
		$this->display();
	}
	
}