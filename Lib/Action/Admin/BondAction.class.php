<?php
    class BondAction extends ACommonAction
    {
        private $Bond;
        function _initialize()
        {
            parent::_initialize();
            D("BondBehavior");
            $this->Bond = new BondBehavior();
        }
        public function index()
        {
            $get_status = $this->_get("status");
            
            $status = '1';
            $get_status && $status =  " d.status = ".$get_status;
            stripos($get_status, ',') && $status = " d.status in ({$get_status})";

            $list = $this->Bond->adminList($status);
            $this->assign('list', $list);
            
            $template = '';
            $get_status == 3 && $template='list3';
            
            $this->display($template);
        }
        
        public function audit()
        {
            if($_POST['dosubmit']){
                $status = intval($this->_post('status', 'strip_tags','99'));
                $Bond_id = intval($this->_post('bond_id', 'strip_tags', 0));
                $remark = '管理员：'.$this->_post('remark', 'htmlspecialchars');  
                $data = array(
                    'status'=>$status,
                    'remark'=>$remark,
                );
                if($status == 2){
                    $data['valid'] = time()+60*60*24*7;
                    if(!$result = M("invest_bond")->where("id={$Bond_id}")->save($data)){
                        $this->error("审核失败", U("Bond/index"));
                    }
                }elseif($status == 3){
                    $Bond_info = M("invest_bond")->field("invest_id")->where("id={$Bond_id}")->find();
                    M("invest_bond")->where("id={$Bond_id}")->save($data);
                    M("borrow_investor")->where("id={$Bond_info['invest_id']}")->save(array('Bond_status'=>0));
                }
                $this->success("审核成功！", U("Bond/index")); 
            }else{
                $Bond_id = $this->_get('bond_id','strip_tags');
                $this->assign("Bond_id", $Bond_id);
                $this->display();    
            }
            
        }
    }
?>
