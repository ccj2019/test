<?php 
function outJson($data){
    header('content-type:application/json;charset=utf-8');
    $data['status'] = isset($data['status']) ? strval($data['status']) : "0";
    exit(json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT));  
}
function outWeb($content,$title = ''){
    header("Content-Type:text/html;charset=utf-8"); 
    $outStr = '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>'.$title.'</title>
            <meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0"/>
            <meta name="apple-mobile-web-app-capable" content="yes"/>
            <meta name="apple-mobile-web-app-status-bar-style" content="black"> 
            <style type="text/css">*{max-width: 100%;}</style>
        </head>
        <body>'.$content.'</body></html>';
    exit($outStr);
}
function out_Web($title,$add_time,$content){
    header("Content-Type:text/html;charset=utf-8"); 
    $outStr = '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>'.$title.'</title>
            <meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0"/>
            <meta name="apple-mobile-web-app-capable" content="yes"/>
            <meta name="apple-mobile-web-app-status-bar-style" content="black"> 
            <style type="text/css">*{max-width: 100%;font-family:微软雅黑;font-size:18px;line-height:2;}</style>
        </head>
        <body><span style="line-height:30px;">'.$title.'</span><br/><span style="line-height:30px;">'.$add_time.'</span><hr>'.$content.'</body></html>';
    exit($outStr);
}


function arrayFilterValByKey($arr, $filterKey, $filter = true){
    foreach ($arr as $key => $value) {
        if($filter && !in_array($key, $filterKey)) unset($arr[$key]);
        if(!$filter && in_array($key, $filterKey)) unset($arr[$key]);
    }
    return $arr;
}
/**
*判断是否是通过手机访问
*/
if(!function_exists('is_mobile'))
{
    function is_mobile()
    {
    
        //如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if(isset($_SERVER['HTTP_X_WAP_PROFILE']))  return TRUE;
    
        //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if(isset($_SERVER['HTTP_VIA']))
        {
            //找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
    
        //判断手机发送的客户端标志,兼容性有待提高
        if(isset($_SERVER['HTTP_USER_AGENT']))
        {
    
            $clientkeywords = array('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile');
    
            //从HTTP_USER_AGENT中查找手机浏览器的关键字
            if(preg_match('/('.implode('|', $clientkeywords).')/i', strtolower($_SERVER['HTTP_USER_AGENT'])))
            {
                return TRUE;
            }
        }
    
        //协议法，因为有可能不准确，放到最后判断
        if(isset($_SERVER['HTTP_ACCEPT']))
        {
            //如果只支持wml并且不支持html那一定是移动设备
            //如果支持wml和html但是wml在html之前则是移动设备
            if((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) &&
               (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false ||
               (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
            {
                    return TRUE;
            }
        }
    
        return FALSE;
    }
}
//自定义方法
/**
 * 友好输出数组
 */
if(!function_exists('dump'))
{
    function dump($var, $echo=true, $label=null, $strict=true) {
        $label = ($label === null) ? '' : rtrim($label) . ' ';
        if (!$strict) {
            if (ini_get('html_errors')) {
                $output = print_r($var, true);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            } else {
                $output = $label . print_r($var, true);
            }
        } else {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (!extension_loaded('xdebug')) {
                $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            }
        }
        if ($echo) {
            echo($output);
            return null;
        }else
        return $output;
    }
}
function time_tran($the_time) {  
    $now_time = date("Y-m-d H:i:s", time());  
    //echo $now_time;  
    $now_time = strtotime($now_time);  
    $show_time = strtotime($the_time);  
    $show_time = $the_time;  
    $dur = $now_time - $show_time;  
    if ($dur < 0) {  
        return date("Y-m-d",strtotime($the_time));  
    } else {  
        if ($dur < 60) {  
            return $dur . '秒前';  
        } else {  
            if ($dur < 3600) {  
                return floor($dur / 60) . '分钟前';  
            } else {  
                if ($dur < 86400) {  
                    return floor($dur / 3600) . '小时前';  
                } else {  
                    //if ($dur < 259200) {//3天内  
                        return floor($dur / 86400) . '天前';  
                    /*} else {  
                        return date("Y-m-d",strtotime($the_time));  
                    }  */
                }  
            }  
        }  
    }  
} 
function url_request($data,$url = '',$method = 'post',$port='80') {
        $url or $url = self::$basePostUrl;
        //writeLog($data);
        $form_data = "";
        foreach($data as $key => $value) {
            if ($form_data == "") {
                $form_data = $key . "=" . rawurlencode($value);
            } else {
                $t = "&" . $key . '=' . rawurlencode($value);
                $form_data = $form_data . $t;
            }
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_PORT, $port);        
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, false);//设定是否输出页面内容  
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);// 对认证证书来源的检查
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);// 从证书中检查SSL加密算法是否存在  
        
        if(strtolower($method) == 'post'){
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $form_data);
        }else{
            $url .= strstr($url, '?') ? '&'.$form_data : "?".$form_data;
        }
        // echo $url;die;
        curl_setopt($ch, CURLOPT_URL, $url);        
        // curl_setopt_array($ch, $option);
        $result = curl_exec($ch);
        return $result;
}
function url_request_token($data,$url = '',$method = 'post',$port='80') {
    $headers = array("Content-type: application/json;charset='utf-8'","Accept: application/json","Cache-Control: no-cache","Pragma: no-cache");
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6000); //设置超时
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);// 对认证证书来源的检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);// 从证书中检查SSL加密算法是否存在 
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
    $rtn = curl_exec($ch);//CURLOPT_RETURNTRANSFER 不设置  curl_exec返回TRUE 设置  curl_exec返回json(此处) 失败都返回FALSE
    curl_close($ch);
    $token1 = cut('token: ','Set-Cookie: Secure',$rtn);
    $token = trim($token1); 
    $matches = strstr($rtn,'{');
    $rs = json_decode($matches,true);
    $rs['token']=$token;
    return $rs;
}
function url_request_json($data,$url = '',$token='',$method = 'post',$port='80') {
    $headers = array("Content-type: application/json;charset='utf-8'","Accept: application/json","Cache-Control: no-cache","Pragma: no-cache","token: ".$token);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6000); //设置超时
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);// 对认证证书来源的检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);// 从证书中检查SSL加密算法是否存在 
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data,JSON_UNESCAPED_UNICODE)); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
    $rtn = curl_exec($ch);//CURLOPT_RETURNTRANSFER 不设置  curl_exec返回TRUE 设置  curl_exec返回json(此处) 失败都返回FALSE
    curl_close($ch);
    return $rtn;
}
function curl_get_https($url,$token=''){
    $headers = array("Content-type: application/json;charset='utf-8'","Accept: application/json","Cache-Control: no-cache","Pragma: no-cache","token: ".$token);
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);// 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);// 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); 
    $tmpInfo = curl_exec($curl);     //返回api的json对象
    //关闭URL请求
    curl_close($curl);
    return $tmpInfo;    //返回json对象
}
function curl_post_https($url,$token=''){
    $headers = array("Content-type: application/json;charset='utf-8'","Accept: application/json","Cache-Control: no-cache","Pragma: no-cache","token: ".$token);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6000); //设置超时
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);// 对认证证书来源的检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);// 从证书中检查SSL加密算法是否存在 
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
    $rtn = curl_exec($ch);//CURLOPT_RETURNTRANSFER 不设置  curl_exec返回TRUE 设置  curl_exec返回json(此处) 失败都返回FALSE
    curl_close($ch);
    return $rtn;
}
//科学计数法转换
function numberchange($str){
    return number_format($str,0,'','');
}
function cut($begin,$end,$str){
    $b = mb_strpos($str,$begin) + mb_strlen($begin);
    $e = mb_strpos($str,$end) - $b;
    return mb_substr($str,$b,$e);
}
 function xmlToArray($strXml){
        $strXml = stripcslashes($strXml);
        $strXml = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $strXml);
        return json_decode(json_encode((array) simplexml_load_string($strXml)), true);
    }
function writeLog($str)
{
    $logTit = '==  ' . date('Y-m-d H:i:s') . '=====================' . PHP_EOL;
    $str    = $logTit . var_export($str, true) . PHP_EOL;
    $open   = fopen(dirname(__FILE__) . "/Log/" . date('Y_m_d') . ".txt", "a");
    fwrite($open, $str . PHP_EOL);
    fclose($open);
}
// 获取中奖概率 $arr = array('a'=>5,'b'=>15,'c'=>30,'d'=>50); 参数可以是礼品id和礼品数量 的键值对。数字代表概率
function bingo_rand($arr)
{
    $pro_sum=array_sum($arr);
    $rand_num=mt_rand(1,$pro_sum);
    $tmp_num=0;
    foreach($arr as $k=>$val)
    {
        if($rand_num<=$val+$tmp_num)
        {
            $n=$k;
            break;
        }else
        {
            $tmp_num+=$val;
        }
    }
    return $n;
}