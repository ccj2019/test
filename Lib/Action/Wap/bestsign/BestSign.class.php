<?php
namespace BestSignSDK;

require(__DIR__.'/util/HttpUtils.php');

class BestSign
{
    private $_developerId = '1641455700011578258';
    private $_pem = 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCGt2j32yagVtGvzIeGb3xPgrgLYQkKvOWvzglWkpNriNEBh8WbED5cCOMu2J7ILKCVAjtN+/b0JEUzMWdKzmx7yn3jOjWt7RRTSYPkcT/3Ty442MUTQot0ToH488/ok6Nlr2YngBuEyQMpln8BIn+UxaD26ZLkjfzY6A7ZR2RpzQ8Ojt+cxnnMZWVjaKN3I2D0Cb9cds54C+Yy+GPitIRpUsZckC3Igrpnzsns+B6J5wS1gDjb4K+GN3Cn4hTS2WwCsYfGgjEsC+TnzvOmvmOKTdoUpoL3L7xDubrW48uYg85/1ZW1YGKINTFy5nU0N1sxMJZTJbAK47CgTXS+q5GXAgMBAAECggEAZpLIDdCDfJoeVmi42BeS40d4stFTfSWPDWknmw9HyB0IZs3mG8cmdBUgRc8SNwAj1NE6Loqm4gWr2urG5yJ9X1QyvgzYSnG1hCT5k7rXJYrYetgyVhPY8tnEBLY/m6quQwJmGbVlPpl/Gu9IDKkj0zcW9GmsOvIRj2ZByBaf0h39K7NmDt2MnYIElA/T1No9tn1VjeNuItS0++Kg5J8a69HaKsFyLAjQolRYcbf5AwBbPI8AuSLvHCiAaFK0wWmyf/Us7Q8HmjVe08nzbP+1LlVtg/BlnsXVMKX+dV7o6WmliSSaaSewOHobnXOFWZGvHlEyUTqo0TK4CyCoiZZPYQKBgQDdTnLTMWKR+eHemNYF2P5IKsXbe6Bh7T4QyYE3bFI5/3/PIfUZVNDwucKhUelxD62h62klXExpROi6dUeFHG8afpRkKSg2uXRzaK4Uwl4g+S1/Uk4vesWsxFsR4LdUF5XWTivTzvXSAKQLFnPC7WkzaMiQJ5XTh+tD8ao5eUhU1QKBgQCb1eZlwamoxBgw1qhT11OQVol0zUanSEffi9xb4/IgAHxQ0hgW3aZaqllDMLxVTcTA2mnUuUk/jKRnmQyh34GhemN1z3wPdG74dSFzBLN7cNVZ1CXBF9fuLd1Hh+153KU/kogv8gU5f7yNJz3Mnrju4MNhai/qPaxYvyyKHzQyuwKBgDJpmWyUlmCsbJB5fl+Mo7gqzjGGQu2rznSigmXurfh6RFIZ6SxhDsuXdUesUiIoMSRubOBO2zUrtlwrNSSUapa9eO4sFfYJXwafNPCTqj+Wo4+aXycfr4IApMI4z2o/iSltWzx/q62v6a6A6dPgoxNL7kwwSEgMcUJj/aPkBd8VAoGAE+tGdPFJN2pBUVTO1VCrvMJi9YwboNxLW5asBw7e7xpAya/hJmMUlXg6yqzbFehH4wLIDknUxAu9JATbKYHBNxvGxNNNn0gZuyZ1rRcHvhqRGtdUMBdVIXu5UiBcyXvbn4GiaMZ9xni+aCiA7LvJFbejIbuJ/l39vqZJEiqk6GsCgYEAn2rKIYQPoHvphiX0wnK8aky1vSjjvOia1OaxyqPlzNsWxTtlRMEUFQw1HtkM7LTyHoSWe15QDxLBv1ZzllII/rIMk2YiJCe2wwfkR9QCAq7cOODtU3jQub5xY8iVv2VXBN3ysAvi+aXYErBeSYdCOYfBsnErWWsTlLtIuDNDTCQ=';
    private $_host = 'https://openapi.bestsign.cn/openapi/v2';
    private $_http_utils = null;

    public function __construct($_developerId, $pem, $host, $pem_type)
    {
        $this->_pem = $this->_formatPem($this->_pem, $pem_type);

//        $this->_developerId = $_developerId;
//        $this->_host = $host;
        $this->_http_utils = new HttpUtils();
    }
    function strToUtf8($str){
        $encode = mb_detect_encoding($str, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5'));
        if($encode == 'UTF-8'){
            return $str;
        }else{
            return mb_convert_encoding($str, 'UTF-8', $encode);
        }
    }
    //********************************************************************************
    // 注册接口
    //********************************************************************************
    public function regUser($account, $mail, $mobile, $name, $userType, $credential=null, $applyCert='1')
    {
        $path = "/user/reg/";
        $post_data['account'] = $account;
        $post_data['name'] = $name;
        $post_data['userType'] = $userType;
        $post_data['email'] = $mail;
        $post_data['mobile'] = $mobile;
        $post_data['credential'] =$credential;
        $post_data['applyCert'] = $applyCert;

        $post_data = json_encode($post_data);
        $rtick = time().rand(1000, 9999);
        //sign data
        $sign_data = $this->_genSignData($path, null, $rtick, md5($post_data));
        //sign
        $sign = $this->getRsaSign($sign_data);
        $params['developerId'] = $this -> _developerId;
        $params['rtick'] = $rtick;
        $params['signType'] = 'rsa';
        $params['sign'] =$sign;
        //url
        $url = $this->_getRequestUrl($path, $params, $sign, $rtick);
        //header data
        $header_data = array();
        //content
        $response = $this->execute('POST', $url, $post_data, $header_data, true);
        return $response;
    }
    //********************************************************************************
    // 查询接口
    //********************************************************************************
    public function getUser($account,$type=1)
    {
        if($type==1){
            $path = "/user/getPersonalCredential/";
        }
        if($type==2){
            $path = "/user/getEnterpriseCredential/";
        }
        $post_data['account'] = $account;
        $post_data = json_encode($post_data);
        //rtick
        $rtick = time().rand(1000, 9999);
        //sign data
        $sign_data = $this->_genSignData($path, null, $rtick, md5($post_data));
        //sign
        $sign = $this->getRsaSign($sign_data);

        $params['developerId'] = $this -> _developerId;
        $params['rtick'] = $rtick;
        $params['signType'] = 'rsa';
        $params['sign'] =$sign;
        //var_dump($params);exit;
        //url
        $url = $this->_getRequestUrl($path, null, $sign, $rtick);
        $header_data = array();

        //content
        $response = $this->execute('POST', $url, $post_data, $header_data, true);
        // var_dump($response);exit;
        return $response;
    }
    //********************************************************************************
    // 查询证书
    //********************************************************************************
    public function getCert($account)
    {

        $path = "/user/getCert/";
        $post_data['account'] = $account;
        $post_data = json_encode($post_data);
        //rtick
        $rtick = time().rand(1000, 9999);
        //sign data
        $sign_data = $this->_genSignData($path, null, $rtick, md5($post_data));
        //sign
        $sign = $this->getRsaSign($sign_data);
        $params['developerId'] = $this -> _developerId;
        $params['rtick'] = $rtick;
        $params['signType'] = 'rsa';
        $params['sign'] =$sign;
        //var_dump($params);exit;
        //url
        $url = $this->_getRequestUrl($path, null, $sign, $rtick);
        $header_data = array();
        //content
        $response = $this->execute('POST', $url, $post_data, $header_data, true);
        //var_dump($response);exit;
        return $response;
    }
    //********************************************************************************
    // 查询证书详情
    //********************************************************************************
    public function getCertinfo($account,$certid)
    {

        $path = "/user/cert/info/";
        $post_data['account'] = $account;
        $post_data['certId']=$certid;
        $post_data = json_encode($post_data);
        //rtick
        $rtick = time().rand(1000, 9999);
        //sign data
        $sign_data = $this->_genSignData($path, null, $rtick, md5($post_data));
        //sign
        $sign = $this->getRsaSign($sign_data);
        $params['developerId'] = $this -> _developerId;
        $params['rtick'] = $rtick;
        $params['signType'] = 'rsa';
        $params['sign'] =$sign;
        //var_dump($params);exit;
        //url
        $url = $this->_getRequestUrl($path, null, $sign, $rtick);
        $header_data = array();
        //content
        $response = $this->execute('POST', $url, $post_data, $header_data, true);
        // var_dump($response);exit;
        return $response;
    }
    //********************************************************************************
    // 生成印章
    //********************************************************************************
    public function signatureImage($account)
    {
        $path = "/signatureImage/user/create/";
        $post_data['account'] = $account;
//        $post_data['fontName']='SimHei';
        $post_data['fontSize']='40';
        $post_data['fontColor']='red';
        $post_data = json_encode($post_data);
        //rtick
        $rtick = time().rand(1000, 9999);
        //sign data
        $sign_data = $this->_genSignData($path, null, $rtick, md5($post_data));
        //sign
        $sign = $this->getRsaSign($sign_data);
        $params['developerId'] = $this -> _developerId;
        $params['rtick'] = $rtick;
        $params['signType'] = 'rsa';
        $params['sign'] =$sign;
        //var_dump($params);exit;
        //url
        $url = $this->_getRequestUrl($path, null, $sign, $rtick);
        $header_data = array();
        //content
        $response = $this->execute('POST', $url, $post_data, $header_data, true);
        // var_dump($response);exit;
        return $response;
    }
    //********************************************************************************
    // 生成印章企业
    //********************************************************************************
    public function qysignatureImage($account,$text)
    {

        $path = "/dist/signatureImage/ent/create/";
        $post_data['account'] = $account;
        $post_data['text']=$text;
//        $post_data['footText']="横向测试可配参数";
//        $post_data['footText2']= "横向测试2可配参数";
        $post_data = json_encode($post_data);
        //rtick
        $rtick = time().rand(1000, 9999);
        //sign data
        $sign_data = $this->_genSignData($path, null, $rtick, md5($post_data));
        //sign
        $sign = $this->getRsaSign($sign_data);
        $params['developerId'] = $this -> _developerId;
        $params['rtick'] = $rtick;
        $params['signType'] = 'rsa';
        $params['sign'] =$sign;
        //var_dump($params);exit;
        //url
        $url = $this->_getRequestUrl($path, null, $sign, $rtick);
        $header_data = array();
        //content
        $response = $this->execute('POST', $url, $post_data, $header_data, true);
        // var_dump($response);exit;
        return $response;
    }
    //********************************************************************************
    // 通过模版生成合同文件
    //********************************************************************************
    public function createContractPdf($account,$tid,$templateValues)
    {

        $path = "/template/createContractPdf/";
        $post_data['account'] = $account;
        $post_data['tid']=$tid;
        $post_data['templateValues']=$templateValues;
        $post_data = json_encode($post_data);

        //rtick
        $rtick = time().rand(1000, 9999);
        //sign data
        $sign_data = $this->_genSignData($path, null, $rtick, md5($post_data));
        //sign
        $sign = $this->getRsaSign($sign_data);
        $params['developerId'] = $this -> _developerId;
        $params['rtick'] = $rtick;
        $params['signType'] = 'rsa';
        $params['sign'] =$sign;
        //var_dump($params);exit;
        //url
        $url = $this->_getRequestUrl($path, null, $sign, $rtick);
        $header_data = array();
        //content
        $response = $this->execute('POST', $url, $post_data, $header_data, true);
        // var_dump($response);exit;
        return $response;
    }
    //********************************************************************************
    // 通过模版创建合同
    //********************************************************************************
    public function createByTemplate($post_data)
    {

        $path = "/contract/createByTemplate/";
//        $post_data['account'] = $account;
//        $post_data['tid']=$tid;
//
//        $post_data['templateValues']=$templateValues;
        $post_data = json_encode($post_data);

        //rtick
        $rtick = time().rand(1000, 9999);
        //sign data
        $sign_data = $this->_genSignData($path, null, $rtick, md5($post_data));
        //sign
        $sign = $this->getRsaSign($sign_data);
        $params['developerId'] = $this -> _developerId;
        $params['rtick'] = $rtick;
        $params['signType'] = 'rsa';
        $params['sign'] =$sign;
        //var_dump($params);exit;
        //url
        $url = $this->_getRequestUrl($path, null, $sign, $rtick);
        $header_data = array();
        //content
        $response = $this->execute('POST', $url, $post_data, $header_data, true);
        // var_dump($response);exit;
        return $response;
    }
    //********************************************************************************
    // 用模版变量签署合同
    //********************************************************************************
    public function qstemplate($post_data)
    {

        $path = "/contract/sign/template/";
//        $post_data['account'] = $account;
//        $post_data['tid']=$tid;
//
//        $post_data['templateValues']=$templateValues;
        $post_data = json_encode($post_data);
       //var_dump($post_data);//exit;
        //rtick
        $rtick = time().rand(1000, 9999);
        //sign data
        $sign_data = $this->_genSignData($path, null, $rtick, md5($post_data));
        //sign
        $sign = $this->getRsaSign($sign_data);
        $params['developerId'] = $this -> _developerId;
        $params['rtick'] = $rtick;
        $params['signType'] = 'rsa';
        $params['sign'] =$sign;
        //var_dump($params);exit;
        //url
        $url = $this->_getRequestUrl($path, null, $sign, $rtick);
        $header_data = array();
        //content
        $response = $this->execute('POST', $url, $post_data, $header_data, true);

        return $response;
    }



    //********************************************************************************
    // 创建合同
    //********************************************************************************
    public function qsContract($contractId,$tid,$vars)
    {
        $path = "/dist/signatureImage/ent/create/";
        $post_data['contractId'] = $contractId;
        $post_data['tid']=$tid;
        $post_data['vars']=$vars;
        $post_data = json_encode($post_data);
        //rtick
        $rtick = time().rand(1000, 9999);
        //sign data
        $sign_data = $this->_genSignData($path, null, $rtick, md5($post_data));
        //sign
        $sign = $this->getRsaSign($sign_data);
        $params['developerId'] = $this -> _developerId;
        $params['rtick'] = $rtick;
        $params['signType'] = 'rsa';
        $params['sign'] =$sign;
        //var_dump($params);exit;
        //url
        $url = $this->_getRequestUrl($path, null, $sign, $rtick);
        $header_data = array();
        //content
        $response = $this->execute('POST', $url, $post_data, $header_data, true);
        // var_dump($response);exit;
        return $response;
    }

    //********************************************************************************
    // 创建合同
    //********************************************************************************
    public function getTemplate($tid)
    {

        $path = "/template/getTemplate/";
//        $post_data['contractId'] = $contractId;
        $post_data['tid']=$tid;
//        $post_data['vars']=$vars;
        $post_data = json_encode($post_data);
        //rtick
        $rtick = time().rand(1000, 9999);
        //sign data
        $sign_data = $this->_genSignData($path, null, $rtick, md5($post_data));
        //sign
        $sign = $this->getRsaSign($sign_data);
        $params['developerId'] = $this -> _developerId;
        $params['rtick'] = $rtick;
        $params['signType'] = 'rsa';
        $params['sign'] =$sign;
        //var_dump($params);exit;
        //url
        $url = $this->_getRequestUrl($path, null, $sign, $rtick);
        $header_data = array();
        //content
        $response = $this->execute('POST', $url, $post_data, $header_data, true);
        // var_dump($response);exit;
        return $response;
    }
//********************************************************************************
    // 创建合同
    //********************************************************************************
    public function sendByTemplate($tid)
    {

        $path = "/contract/sendByTemplate/";
//        $post_data['contractId'] = $contractId;
        $post_data['tid']=$tid;
//        $post_data['vars']=$vars;
        $post_data = json_encode($post_data);
        //rtick
        $rtick = time().rand(1000, 9999);
        //sign data
        $sign_data = $this->_genSignData($path, null, $rtick, md5($post_data));
        //sign
        $sign = $this->getRsaSign($sign_data);
        $params['developerId'] = $this -> _developerId;
        $params['rtick'] = $rtick;
        $params['signType'] = 'rsa';
        $params['sign'] =$sign;
        //var_dump($params);exit;
        //url
        $url = $this->_getRequestUrl($path, null, $sign, $rtick);
        $header_data = array();
        //content
        $response = $this->execute('POST', $url, $post_data, $header_data, true);
        // var_dump($response);exit;
        return $response;
    }

    public function downloadSignatureImage($account, $image_name)
    {
        $path = "/signatureImage/user/download/";

        $url_params['account'] = $account;
        $url_params['imageName'] = $image_name;

        //rtick
        $rtick = time() . rand(1000, 9999);

        //sign
        $sign_data = $this->_genSignData($path, $url_params, $rtick, null);
        $sign = $this->getRsaSign($sign_data);

        $url = $this->_getRequestUrl($path, $url_params, $sign, $rtick);
        //var_dump("url: ".$url);

        //header data
        $header_data = array();

        //content
        $response = $this->execute('GET', $url, null, $header_data, true);
        return $response;
    }

    public function downloadContract($contractId)
    {
        $path = "/storage/contract/download/";

        $url_params['contractId'] = $contractId;

        //rtick
        $rtick = time() . rand(1000, 9999);

        //sign
        $sign_data = $this->_genSignData($path, $url_params, $rtick, null);
        $sign = $this->getRsaSign($sign_data);

        $url = $this->_getRequestUrl($path, $url_params, $sign, $rtick);
       // var_dump("url: ".$url);

        //header data
        $header_data = array();

        //content
        $response = $this->execute('GET', $url, null, $header_data, true);

        return $response;
    }
    public function lockContract($contractId)
    {
        $path = "/storage/contract/lock/";

       $post_data['contractId'] = $contractId;
//        $post_data['tid']=$tid;
//        $post_data['vars']=$vars;
        $post_data = json_encode($post_data);
        //rtick
        $rtick = time().rand(1000, 9999);
        //sign data
        $sign_data = $this->_genSignData($path, null, $rtick, md5($post_data));
        //sign
        $sign = $this->getRsaSign($sign_data);
        $params['developerId'] = $this -> _developerId;
        $params['rtick'] = $rtick;
        $params['signType'] = 'rsa';
        $params['sign'] =$sign;
        //var_dump($params);exit;
        //url
        $url = $this->_getRequestUrl($path, null, $sign, $rtick);
        $header_data = array();
        //content
        $response = $this->execute('POST', $url, $post_data, $header_data, true);
         //var_dump($response);//exit;
        return $response;
    }
    /**
     * @param $path：接口名
     * @param $url_params: get请求需要放进参数中的参数
     * @param $rtick：随机生成，标识当前请求
     * @param $post_md5：post请求时，body的md5值
     * @return string
     */
    private function _genSignData($path, $url_params, $rtick, $post_md5)
    {
        $request_path = parse_url($this->_host . $path)['path'];

        $url_params['developerId'] = $this -> _developerId;
        $url_params['rtick'] = $rtick;
        $url_params['signType'] = 'rsa';

        ksort($url_params);

        $sign_data = '';
        foreach ($url_params as $key => $value)
        {
            $sign_data = $sign_data . $key . '=' . $value;
        }
        $sign_data = $sign_data . $request_path;

        if (null != $post_md5)
        {
            $sign_data = $sign_data . $post_md5;
        }
        return $sign_data;
    }

    private function _getRequestUrl($path, $url_params, $sign, $rtick)
    {
        $url = $this->_host .$path . '?';

        //url
        $url_params['sign'] = $sign;
        $url_params['developerId'] = $this -> _developerId;
        $url_params['rtick'] = $rtick;
        $url_params['signType'] = 'rsa';

        foreach ($url_params as $key => $value)
        {
            $value = urlencode($value);
            $url = $url . $key . '=' . $value . '&';
        }

        $url = substr($url, 0, -1);
        return $url;
    }

    private function _formatPem($rsa_pem, $pem_type = '')
    {
        //如果是文件, 返回内容
        if (is_file($rsa_pem))
        {
            return file_get_contents($rsa_pem);
        }

        //如果是完整的证书文件内容, 直接返回
        $rsa_pem = trim($rsa_pem);
        $lines = explode("\n", $rsa_pem);
        if (count($lines) > 1)
        {
            return $rsa_pem;
        }

        //只有证书内容, 需要格式化成证书格式
        $pem = '';
        for ($i = 0; $i < strlen($rsa_pem); $i++)
        {
            $ch = substr($rsa_pem, $i, 1);
            $pem .= $ch;
            if (($i + 1) % 64 == 0)
            {
                $pem .= "\n";
            }
        }
        $pem = trim($pem);
        if (0 == strcasecmp('RSA', $pem_type))
        {
            $pem = "-----BEGIN RSA PRIVATE KEY-----\n{$pem}\n-----END RSA PRIVATE KEY-----\n";
        }
        else
        {
            $pem = "-----BEGIN PRIVATE KEY-----\n{$pem}\n-----END PRIVATE KEY-----\n";
        }
        return $pem;
    }

    /**
     * 获取签名串
     * @param $args
     * @return
     */
    public function getRsaSign()
    {
        $pkeyid = openssl_pkey_get_private($this->_pem);
        if (!$pkeyid)
        {
            throw new \Exception("openssl_pkey_get_private wrong!", -1);
        }

        if (func_num_args() == 0) {
            throw new \Exception('no args');
        }
        $sign_data = func_get_args();
        //var_dump($sign_data);exit;
        $sign_data = trim(implode("\n", $sign_data));
        openssl_sign($sign_data, $sign, $this->_pem);
        openssl_free_key($pkeyid);
        return base64_encode($sign);
    }

    //执行请求
    public function execute($method, $url, $request_body = null, array $header_data = array(), $auto_redirect = true, $cookie_file = null)
    {
        $response = $this->request($method, $url, $request_body, $header_data, $auto_redirect, $cookie_file);

        $http_code = $response['http_code'];
        if ($http_code != 200)
        {
            throw new \Exception("Request err, code: " . $http_code . "\nmsg: " . $response['response'] );
        }

        return $response['response'];
    }

    public function request($method, $url, $post_data = null, array $header_data = array(), $auto_redirect = true, $cookie_file = null)
    {
        $headers = array();
        $headers[] = 'Content-Type: application/json; charset=UTF-8';
        $headers[] = 'Cache-Control: no-cache';
        $headers[] = 'Pragma: no-cache';
        $headers[] = 'Connection: keep-alive';

        foreach ($header_data as $name => $value)
        {
            $line = $name . ': ' . rawurlencode($value);
            $headers[] = $line;
        }

        if (strcasecmp('POST', $method) == 0)
        {
            $ret = $this->_http_utils->post($url, $post_data, null, $headers, $auto_redirect, $cookie_file);
        }
        else
        {
            $ret = $this->_http_utils->get($url, $headers, $auto_redirect, $cookie_file);
        }
        return $ret;
    }
}