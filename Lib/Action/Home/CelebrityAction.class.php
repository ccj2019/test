<?php
class CelebrityAction extends HCommonAction
{    
    public function index(){
    	$pre = C('DB_PREFIX');
    	import("ORG.Util.Page");
		$count = M('article')->where('type_id=506')->count('id');
		$p = new Page($count, 10);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
    	$articleList = M('article t1')->where('type_id=506 and is_fabu = 1')->limit($Lsql)->select();    			       
    	$this->assign('articleList',$articleList);
    	$this->assign('page',$page);
    	$this->display();
    }
    public function detail(){
    	$pre = C('DB_PREFIX');
    	$id = intval($_GET['id']);    	
        $celebrity = M('article t1')->where("is_fabu = 1")->find($id);
    	$this->assign('celebrity',$celebrity);
    	$this->display();
    }
    public function edit(){
    	if(!empty($_POST) && !empty($_POST['borrow_id'])){
    		$data['borrow_id'] = intval($_POST['borrow_id']);
    		$yuanyin = implode(',', $_POST['yuanyin']);
    		$data['yuanyin'] = $yuanyin;

    		$haochu = implode(',', $_POST['haochu']);
    		$data['haochu'] = $haochu;
    		$data['shipin'] = text($_POST['shipin']);
    		if(empty($_POST['id'])){
	    		$data['add_time'] = time();
	    		$rs = M('celebrity_borrow')->add($data);    			
    		}else{
    			$data['id'] = intval($_POST['id']);
    			$rs = M('celebrity_borrow')->save($data);    			
    		}
    		if($rs){
    			$this->success('修改成功！');
    		}else{
    			$this->error('您没做修改或者修改失败！');
    		}
    		die;
        }
        //
        $Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";
        $this->assign('yuanyinArr',$Bconfig['YUANYIN']);
        $this->assign('haochuArr',$Bconfig['HAOCHU']);     

        //
        $cele = M('celebrity_borrow')->getField('id,borrow_id');
        $celeIdArr = array_values($cele);
        if(!empty($_GET['id'])){
            $celeNow = M('celebrity_borrow')->find($_GET['id']);   
            $celeNow['yuanyin'] = explode(',', $celeNow['yuanyin']);
            $celeNow['haochu'] = explode(',', $celeNow['haochu']);            
            $this->assign('celeNow',$celeNow);            
            $map['id'] = $celeNow['borrow_id'];
        }else{
            $celeIdArr and $map['id'] = array('not in',$celeIdArr);
        }
    	$map['borrow_status'] = array('in',array(6,7));
    	$borrowList = M('borrow_info')->field('id,borrow_name')->where($map)->select();    	
    	$this->assign('borrowList',$borrowList);
    	$this->display();
    }
    public function _listFilter($list){
        session('listaction',ACTION_NAME);
        $Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";
        foreach ($list as $key => $value) {
            $yuanyin = explode(',', $value['yuanyin']); 
            $yuanyin_cn = array();
            foreach ($yuanyin as $v) {
                $yuanyin_cn[] = $Bconfig['YUANYIN'][$v];
            }            
            $value['yuanyin'] = !empty($yuanyin_cn) ? implode(',', $yuanyin_cn) : '';
            $value['yuanyin_cn'] = $yuanyin_cn;

            $haochu = explode(',', $value['haochu']); 
            $haochu_cn = array();
            foreach ($haochu as $v) {
                $haochu_cn[] = $Bconfig['HAOCHU'][$v];
            }            
            $value['haochu_cn'] = $haochu_cn;
            $value['haochu'] = !empty($haochu_cn) ? implode(',', $haochu_cn) : '';

            $list[$key] = $value;
        }
        return $list;
    }
}
