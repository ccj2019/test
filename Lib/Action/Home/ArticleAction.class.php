<?php
// 文章分类
class ArticleAction extends HCommonAction {
    public function index($topid = 427){
        $cid = empty($_REQUEST['cid']) ? '' : intval($_REQUEST['cid']);
		      
        $id = empty($_REQUEST['id']) ? '' : intval($_REQUEST['id']);        
        if(empty($cid)){
            $topCategorySub = M('article_category')->where(array('is_hiden'=>0,'parent_id'=>$topid))->order('sort_order desc,id desc')->find($topid);            
            $cid = empty($topCategorySub['id']) ? $topid : $topCategorySub['id'];
        }
        $nowCategory = M('article_category')->where(array('is_hiden'=>0))->find($cid);
        // 五大类
        $map = array(
            'parent_id'=>$topid,
            'is_hiden'=>0
            );
        $tpl_var['menuList_class']= array('mem_spec43','mem_spec44','mem_spec45','mem_spec46','mem_spec47',);
        $menuList = M('article_category')->where($map)->order("sort_order desc")->select(); 
        foreach ($menuList as $key => $value) {
            if($value['type_set'] == 2) $menuList[$key]['linkurl'] = $value['type_url'];
            else $menuList[$key]['linkurl'] = U('article/index',array('cid'=>$value['id']));
            $map['parent_id'] = $value['id'];
 
             if($menuList[$key]['id'] == $cid || $menuList[$key]['id'] == $nowCategory['parent_id']) $menuList[$key]['active'] = 'active';
        }
        $tpl_var['menuList'] = $menuList;
        //当前分类子类
        unset($map);
       // $map['parent_id'] = $cid;     
        //$map['id'] = array(array("eq"=>$cid),array(""));
        //$map['_logic'] = 'OR';
    
        if($nowCategory["parent_id"]!=0){
          $map['_string'] = ' parent_id='.$nowCategory["parent_id"].'  or parent_id='.$cid.' or id='.$cid;
          $nowMenuSub = M('article_category')->where($map)->select(); 
          foreach ($nowMenuSub as $key => $value) {
              if($value["parent_id"]!=425){
                  if($value['type_set'] == 2) $value['linkurl'] = $value['type_url'];
                  else $value['linkurl'] = U('article/index',array('cid'=>$value['id']));
                  if($value['id'] == $cid) $value['active'] = 'active';
                  $nowMenuSub2[$key]=$value;
              }
          }
        }

        //print_r($nowMenuSub2);
        $tpl_var['nowMenuSub'] = $nowMenuSub2;        
        // 获取当前分类信息
        
        if(empty($nowCategory)) $this->error('您访问的页面不存在！');
        $tpl_var['isArticle'] = 1;
         
        if($nowCategory['type_set']==0){
        	 
            if($id){//文章单页
            }else{//分类单页
                $tpl_var['article']['art_content'] = $nowCategory['type_content'];
                 $tpl_var['article']['name'] = $nowCategory['type_name'];
            }
        }else{
            // 获取列表
            if($id){
                $artlist = M('article')->where("type_id in (53,54,22)")->limit(1)->select();
                $this->assign('artlist',$artlist);
                // 获取文章
                $searchMaps['borrow_status']=array("in",'2');
                $parm['limit'] = 1;
                $parm['map']      = $searchMaps;
                $list_type3 = getBorrowList($parm); 
                $this->assign('list_type3',$list_type3);
                $tpl_var['article'] = M('article')->find($id);
                $tpl_var['article']['name'] = $nowCategory['type_name'];
                 //上一篇
                $pre = M('article')->where("type_id={$cid} and id>{$id}")->limit(1)->order("id desc")->find();
                //下一篇
                $next = M('article')->where("type_id={$cid} and id<{$id}")->limit(1)->order("id desc")->find();

                $this->assign('preid',$pre['id']);
                $this->assign('nextid',$next['id']);
            }else{
            	
                if($cid == 35){
                    //关于充值与体现
                    $parm = array(
                    'type_id'=> 36
                    ); 
                    $tpl_var['list'] = getArticleList($parm);
                    $czlist = getArticleList($parm);
                    //名词解释
                    $parm = array(
                    'type_id'=> 40
                    );         
                    $tpl_var['list'] = getArticleList($parm);
                    $mclist = getArticleList($parm);
                    $this->assign('czlist',$czlist);
                    $this->assign('mclist',$mclist);	 
                    // $this->display('list');die;
                }else  if($cid == 21||$cid == 22||$cid == 11||$cid == 53||$cid == 54){
                  $parm = array(
                    'type_id'=> $cid,
                    'pagesize'=> 7
                    );  
                    $tpl_var['article']['name'] = $nowCategory['type_name'];
	                $tpl_var['list'] = getArticleList($parm);
	                // var_dump($tpl_var['list']['list']);
	                $rightList = getArticleList($parm);
	                $tpl_var['rightList'] = $rightList['list']; 
	                $tpl_var['isArticle'] = 0;
	                $this->assign('cid',$cid);        
	                $this->assign($tpl_var); 
					//var_dump($tpl_var);die;
					//var_dump($tpl_var['list'] );die;
				    $this->display('news_list');    	die; 
                } else  if($cid == 55){
                  $parm = array(
                    'type_id'=> $cid,
                    'pagesize'=> 7
                    );  
                    $tpl_var['article']['name'] = $nowCategory['type_name'];
	                $tpl_var['list'] = getArticleList($parm);
	                // var_dump($tpl_var['list']['list']);
	                $rightList = getArticleList($parm);
	                $tpl_var['rightList'] = $rightList['list'];
	                $tpl_var['isArticle'] = 0;
	                $this->assign('cid',$cid);
				
	                $this->assign($tpl_var); 
					//var_dump($tpl_var);die;
					//var_dump($tpl_var['list'] );die;
				    $this->display('video');    	die; 
                }; 

				  $parm = array(
                    'type_id'=> 37
                    );      
 
                $tpl_var['article']['name'] = $nowCategory['type_name'];
                $tpl_var['list'] = getArticleList($parm);
                // var_dump($tpl_var['list']['list']);
                $rightList = getArticleList($parm);
                $tpl_var['rightList'] = $rightList['list']; 
               
                $tpl_var['isArticle'] = 0;
                $this->assign('cid',$cid);        
                $this->assign($tpl_var); 

				//var_dump($tpl_var['list'] );die;
				$this->display('index');    	die; 
                $this->display('list');die;
            }            
        }  
 
        if($cid==37){
            $this->assign("dh",'6');
        }
        $this->assign('cid',$cid);   
        $this->assign($tpl_var);
        $this->display('cshow');
    }
    public function news_list(){
    	 
	
	 
        $cid = empty($_REQUEST['cid']) ? '' : intval($_REQUEST['cid']);
		
        if(empty($cid)){
       		$parm = array(
                'type_id'=> [53,54,22],
                'pagesize'=> 7
            );          
        }else{
            $parm = array(
                'type_id'=> [$cid],
                'pagesize'=> 7
            ); 
        }
        $tpl_var['article']['name'] = $nowCategory['type_name'];
					
    	if(empty($parm['type_id'])) return;
    	$row=array();
    	$map['type_id'] = ["in",$parm['type_id'] ];
    	$map['is_fabu'] = 1;
    	$Osql="art_time DESC";
    	$field="id,title,art_set,art_info,art_img,art_time,art_url,art_content,type_id";
    	//查询条件 
    	if($parm['pagesize']){
    		//分页处理
    		import("ORG.Util.Page");
    		$count = M('article')->where($map)->count('id');
    		$p = new Page($count, $parm['pagesize']);
    		$row['_page'] = pageSet($p);
    		    		    	$page =  $p->show();
    		if($count/$parm['pagesize']<=1){
    		 	$page = '';   
    		} 

    	
    		$Lsql = "{$p->firstRow},{$p->listRows}";
    		//分页处理
    	}else{
    		$page="";
    		$Lsql="{$parm['limit']}";
    	}
		//var_dump($map);die;
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
        $tpl_var['list'] = $row;
        $rightList = $row;
        $tpl_var['rightList'] = $rightList['list']; 
        $tpl_var['isArticle'] = 0;
        $this->assign('cid',$cid);        
        $this->assign($tpl_var); 
        $searchMaps['borrow_status']=array("in",'2');
        $parm['limit'] = 1;
        $parm['map']      = $searchMaps;
        $list_type3 = getBorrowList($parm); 
        $this->assign('list_type3',$list_type3);
	    $this->display('news_list'); 
	}
    public function video(){
        $parm = array(
        'type_id'=> '55',
        'pagesize'=> 90
        );  
        $tpl_var['article']['name'] = $nowCategory['type_name'];
        $tpl_var['list'] = getArticleListt($parm,"art_time DESC");
        $rightList = getArticleList($parm,"ASC");
        $tpl_var['rightList'] = $rightList['list']; 
        $tpl_var['isArticle'] = 0;
        $this->assign('cid',$cid);    
    
        $this->assign($tpl_var); 

        $this->assign("dh",'5');
        $this->display(); 
    }
    public function videoshow(){
       $id = $this->_post('id');
        m('article')->where('id='.$id)->setInc('art_click');

        $single = m('article')->find($id);
        echo json_encode($single);
    }
    public function news(){
        $cid = empty($_REQUEST['cid']) ? '' : intval($_REQUEST['cid']);
        $nowCategory = M('article_category')->where(array('is_hiden'=>0))->find($cid);
        // 五大类

        $tpl_var['nowMenuSub'] = $nowMenuSub2;        
        // 获取当前分类信息
        
        if(empty($nowCategory)) $this->error('您访问的页面不存在！');
        $tpl_var['isArticle'] = 1;
        
        $parm = array(
        'type_id'=> $nowCategory['id'],
        'pagesize'=>20
        );         
        $tpl_var['article']['name'] = $nowCategory['type_name'];
        $tpl_var['list'] = getArticleList($parm);

        $rightList = getArticleList($parm);
        $tpl_var['rightList'] = $rightList['list']; 
        
        $tpl_var['isArticle'] = 0;
        $this->assign('cid',$cid);        
        $this->assign($tpl_var);        

        $this->display();
    }
    public function single(){
        $typeid = $this->_get('id');
        $single = D('Acategory')->find($typeid);
        $this->assign('single',$single);
        $this->display();
    }
    public function newsshow(){
        if(is_mobile()==1){
            $cid=$_GET["cid"];
            $id=$_GET["id"];
            echo "<script type='text/javascript'>";
            echo "window.location.href='".$url."/Wap/article/index/cid/$cid/id/$id.html';";
            echo "</script>";die;
        }
        $searchMaps['borrow_status']=array("in",'2');
        $parm['limit'] = 1;
        $parm['map']      = $searchMaps;
        $list_type3 = getBorrowList($parm); 
        $this->assign('list_type3',$list_type3);

        $artlist = M('article')->where("type_id in (53,54,22)")->limit(1)->select();
        $this->assign('artlist',$artlist);

        $id = $this->_get('id');
        m('article')->where('id='.$id)->setInc('art_click');
        $single = m('article')->find($id);
        $cid = $single['type_id'];
        $this->assign('single',$single);
        //上一篇
        $pre = M('article')->where("type_id={$cid} and id>{$id}")->limit(1)->order("id desc")->find();
        //下一篇
        $next = M('article')->where("type_id={$cid} and id<{$id}")->limit(1)->order("id desc")->find();
        $this->assign('preid',$pre['id']);
        $this->assign('nextid',$next['id']);

        $this->assign('cid',$cid);
        $this->display();
    }


    public function search(){
        $key = $this->_get('key');
        $map['title'] = array('like','%'.$key.'%'); 
        $map['type_id'] = array('in', '36,40');
        $artlist = D('Article')->where($map)->select();
        $this->assign('artlist',$artlist);
        $this->display();
    }
    public function active(){
        $parm = array(
        'type_id'=> '37',
        'pagesize'=> 6
        );  
        $tpl_var['article']['name'] = $nowCategory['type_name'];
        $tpl_var['list'] = getArticleList($parm);
        $rightList = getArticleList($parm);
        $tpl_var['rightList'] = $rightList['list']; 
        $tpl_var['isArticle'] = 0;
        $this->assign('cid',$cid);    
    
        $this->assign($tpl_var); 
        $this->assign("dh",'6');
        $this->display(); 
    }
    public function about(){
        $about = M('article_category')->where(array('is_hiden'=>0))->find(520);
        $contact = M('article_category')->where(array('is_hiden'=>0))->find(518);
        $this->assign('about',$about);
        $this->assign('contact',$contact);
        $this->assign("dh",'7');
        $this->display(); 
    }
    public function cateshow(){
        $cid = empty($_REQUEST['cid']) ? '' : intval($_REQUEST['cid']);
        $about = M('article_category')->where(array('is_hiden'=>0))->find($cid);
        $this->assign('about',$about);
        $this->display(); 
    }
    
}
