<?PHP
    class HeepayOpenssl{

    /**
     * 初始化.
     *
     * @param array $sslconfig
     */
    public function __construct($sslconfig)
    {
        $this->sslconfig = $sslconfig;
    }
    /**
     * 获取密钥分段加密长度.
     *
     * @param Closure $keyClosure
     *
     * @return int
     *
     * @throws Exception
     */
    protected function getEncryptBlockLen($keyClosure)
    {
        $key_info = openssl_pkey_get_details($keyClosure);
        if (!$key_info)
        {
            throw new Exception('获取密钥信息失败' . openssl_error_string());
        }
        // bits数除以8 减去padding长度，OPENSSL_PKCS1_PADDING 长度是11
        // php openssl 默认填充方式是 OPENSSL_PKCS1_PADDING
        return $key_info['bits'] / 8 - 11;
    }
    /**
     * 获取密钥分段解密长度.
     *
     * @param Closure $keyClosure
     *
     * @return int
     *
     * @throws Exception
     */
    protected function getDecryptBlockLen($keyClosure)
    {
        $key_info = openssl_pkey_get_details($keyClosure);
        if (!$key_info)
        {
            throw new Exception('获取密钥信息失败' . openssl_error_string());
        }
        // bits数除以8得到字符长度
        return $key_info['bits'] / 8;
    }
    /**
     * 数据加密.
     *
     * @param string $text 需要加密的文本
     * @param int $type 加密方式:1.公钥加密 2.私钥加密
     *
     * @return string
     *
     * @throws Exception
     */
    public function encrypt($text, $type)
    {
        //获取密钥资源
        $keyClosure = 1 == $type ? openssl_pkey_get_public($this->sslconfig['publicKey']) : openssl_pkey_get_private($this->sslconfig['privateKey']);
        if (!$keyClosure)
        {
            throw new Exception('获取密钥失败,请检查密钥是否合法');
        }
        //RSA进行加密
        $encrypt = '';
        $plainData = str_split($text, $this->getEncryptBlockLen($keyClosure));
        foreach ($plainData as $key => $encrypt_item)
        {
            $isEncrypted = (1 == $type) ? openssl_public_encrypt($encrypt_item, $encrypted, $keyClosure) : openssl_private_encrypt($encrypt_item, $encrypted, $keyClosure);
            if (!$isEncrypted)
            {
                throw new Exception('加密数据失败,请检查密钥是否合法,' . openssl_error_string());
            }
            $encrypt .= $encrypted;
        }
        $encrypt = base64_encode($encrypt);
        //返回
        return $encrypt;
    }
    /**
     * 数据解密.
     *
     * @param string $text 需要加密的文本
     * @param int $type 加密方式:1.公钥加密 2.私钥加密
     *
     * @return string
     *
     * @throws Exception
     * @throws
     */
    public function decrypt($text, $type)
    {
        //获取密钥资源
        $keyClosure = 1 == $type ? openssl_pkey_get_public($this->sslconfig['publicKey']) : openssl_pkey_get_private($this->sslconfig['privateKey']);
        if (!$keyClosure)
        {
            throw new Exception('获取密钥失败,请检查密钥是否合法');
        }
        //RSA进行解密
        $data = base64_decode($text);
        $data = str_split($data, $this->getDecryptBlockLen($keyClosure));
        $decrypt = '';
        foreach ($data as $key => $chunk)
        {
            $isDecrypted = (1 == $type) ? openssl_public_decrypt($chunk, $encrypted, $keyClosure) : openssl_private_decrypt($chunk, $encrypted, $keyClosure);
            if (!$isDecrypted)
            {
                throw new Exception('解密数据失败,请检查密钥是否合法,' . openssl_error_string());
            }
            $decrypt .= $encrypted;
        }
        //返回
        return $decrypt;
    }
}
/*
//01.配置公钥私钥
require dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'config.php';
$respri = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($config['merchant_private_key'], 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";
$respub = "-----BEGIN PUBLIC KEY-----\n" .
                      wordwrap($config['merchant_public_key'], 64, "\n", true) .
                      "\n-----END PUBLIC KEY-----";

$configKey = ['publickey'=>$respub,'privateKey'=>$respri];

//02.初始化
$openssl = new HeepayOpenssl($configKey);
//03.私钥加密->公钥解密,加密5000长度的字符串A
//$waitChar = str_repeat('A', 5000);
//$privateEnData = $openssl->encrypt($waitChar, 2);  //私钥加密
//$publicDeData = $openssl->decrypt($privateEnData, 1); //公钥解密
//04.公钥加密->私钥解密,加密10000长度的字符串B
$waitChar = "hfyLu/Ya+L1Knj3N1FVPtiYjNQdGpZ3wzKZamJDclRP4/1Uwm816uZvB0IevetutmzYF3onxWDx+H9xGU2UpqSADVXulSGy0+TXS6kGeUrPOwLRrZH9w3NDak9sJpMeKJI3eZcmDU8aA2NumoOPlI0+VV3oja8zst9/jvWam8VNi3vTK7XLuk97oqw+Anaql18pXWWJyfo6LSb0xYy5EKrCsuXDjB13dxj1Uw7KlMGcRTI2S9UHqm3ebl7Kp+yGj6IFUTit0KUq8ynxm92VO23lTzEgPfPuP6plpDKIRAGq4LzpMKtmqMFH4+C0758ILsJfEcP/vZSwEgwHLW5jmBSkVMiBSjZcbkOPDX4eq4cjcqR/6BPk/AUToQOnbLr3UDsBKPbKstWdWEsHMsAOZC3lpzfKQDWw0lNzr3CNL84n3g4SI5bIAmH0A3vB5eKPOc6tyRlPbq9JH1gtO7q23RxBvNUr/bPX0TX2+y8LRzHxjAViSNEzdAR/TuoWoP9AH7gC5BXkCihR1INDb3VPMwcKpEAmacfApis/1sRCO9IDd2+9uZA9ZXGAr3uck8U9h0anxLhA4cr9b+3jWP8gMNUITpXQw8cu3IcNpTwAdJ67xifrBPGTQqNoZlzgdc4jYEdOnZ26zPgiM/BuW/hAWXxoU28dC5DASWTZp8+8r9cA=";//str_repeat('B', 10000);
//echo "原始数据=",$waitChar;
//$publicEnData = $openssl->encrypt($waitChar, 1); //公钥加密
echo "公钥加密的数据=",$waitChar;
$privateDeData = $openssl->decrypt($waitChar, 2); //私钥解密
echo "私钥解密的数据=",$privateDeData;
*/
?>