<?php
class CelebrityAction extends ACommonAction
{    
    public function project(){
    	$pre = C('DB_PREFIX');
        import("ORG.Util.Page");
        $count = M('celebrity_borrow t1')->join($pre.'borrow_info t2 on t1.borrow_id = t2.id')->join($pre.'pro_category t3 on t2.pid = t3.id')->count('t1.id');
        $p = new Page($count, 10);
        $page = $p->show();
        $Lsql = "{$p->firstRow},{$p->listRows}";

        $celeList = M('celebrity_borrow t1')->field('t1.*,t2.borrow_name,t2.add_time,t3.type_name,t2.borrow_info')->join($pre.'borrow_info t2 on t1.borrow_id = t2.id')->join($pre.'pro_category t3 on t2.pid = t3.id')->limit($Lsql)->select();                
        $celeList = $this->_listFilter($celeList);  
        // var_dump($celeList);
        $this->assign('celeList',$celeList);
        $this->assign('page',$page);
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

            $haochu = explode(',', $value['haochu']); 
            $haochu_cn = array();
            foreach ($haochu as $v) {
                $haochu_cn[] = $Bconfig['HAOCHU'][$v];
            }            
            $value['haochu'] = !empty($haochu_cn) ? implode(',', $haochu_cn) : '';

            $list[$key] = $value;
        }
        return $list;
    }
}
