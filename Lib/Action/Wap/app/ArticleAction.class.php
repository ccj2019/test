<?php
// 文章分类
class ArticleAction extends WCommonAction {
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
            // $menuList[$key]['sub']  = M('article_category')->where($map)->limit(10)->select(); 
            // foreach ($menuList[$key]['sub'] as $k => $v) {
            //     if($v['type_set'] == 2) $menuList[$key]['sub'][$key]['linkurl'] = $v['type_url'];
            //     else $menuList[$key]['sub'][$k]['linkurl'] = U('article/index',array('cid'=>$v['id']));
            // }
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
                // 获取文章
                
                $tpl_var['article'] = M('article')->find($id);
                $tpl_var['article']['name'] = $nowCategory['type_name'];
                
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
					// var_dump($tpl_var['list'] );die;
				    $this->display('news_list');    	die; 
                } else  if($cid == 55){
                  $parm = array(
                    'type_id'=> $cid,
                    'pagesize'=> 7
                    );  
                    $tpl_var['article']['name'] = $nowCategory['type_name'];
	                $tpl_var['list'] = getArticleListt($parm,"art_time DESC");
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
 
    
        $this->assign('cid',$cid);
        $this->assign('nowCategory',$nowCategory);    
        $this->assign($tpl_var);
        $this->display('cshow');
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
    public function safe(){
        $this->index(428);
        die;
    }
    public function help(){
        $this->index(425);
        die;
    }
    public function single(){
        $typeid = $this->_get('id');
        $single = D('Acategory')->find($typeid);
        $this->assign('single',$single);
        $this->display();
    }
    public function newsshow(){
        $id = $this->_get('id');
        $single = D('Article')->find($id);
        $this->assign('single',$single);
        $this->display();
    }

    public function agreement(){
        $typeid = $this->_get('cid');
        $article = D('Acategory')->find($typeid);
        $this->assign('article',$article);
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
        $this->display();
    }
    public function tnjrJdbg(){
        $this->display();
    }
    public function video(){
    //	die;
        $this->display();
    }
    public function report(){
        $this->display();
    }
    
}
