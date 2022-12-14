<?php

/**
 * 获取最新安装包
 * Class UpdateAction
 */
class UpdateAction extends Action {

    public function __construct()
    {
        $allow_origin = array(
            'houtai.rzmwzc.com',
            'rzmwzc.com',
        );
        //跨域访问的时候才会存在此字段
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        if (in_array($origin, $allow_origin)) {

            header('Access-Control-Allow-Origin:' . $origin);
        }

        header('Access-Control-Allow-Origin:*');//允许跨域,不限域名

        header("Access-Control-Allow-Methods:POST,GET,OPTIONS");

        header('Access-Control-Allow-Headers: Content-Type'); // 设置允许自定义请求头的字段

        header('Access-Control-Allow-Credentials:true');
    }

    public function pack()
    {
        $id=$_REQUEST["id"];
        if(empty($id)){
            $id=1;
        }
        $data = M('update_pack')->where("id=".$id)->find();
        echo json_encode($data);
    }
    public function hide(){
        $data = 2; //1隐藏 2显示
        echo $data;
    }
}