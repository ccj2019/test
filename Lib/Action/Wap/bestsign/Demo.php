<?php
namespace BestSignSDK;

require('BestSignDemo.php');

$pem = "";
$developerId = "";
$server_host = "http://192.168.30.190:8085/openapi/v2"; //这个地方不要末尾的 /

$bestSign_demo = new BestSignDemo($developerId, $pem, $server_host, "");
regUser();

echo "\nFinish!\n";

//****************************************************************************************************
// demo functions
//****************************************************************************************************
//注册用户
function regUser()
{
    global $bestSign_demo;

    var_dump("Test regUser...");

    $mail = time() . rand(1000, 9999)."@test.com";
    $account = $mail;
    $mobile = "13995656375";
    $name = "test_name";
    $user_type = "1";
    $response = $bestSign_demo->regUser($account, $mail, $mobile, $name, $user_type);

    var_dump("Test regUser result:");
    var_dump($response);
}

function regUserWithCredential()
{
    global $bestSign_demo;

    var_dump("Test regUser with credential...");

    $mail = time() . rand(1000, 9999)."@test.com";
    $account = $mail;
    $mobile = "13995656375";
    $name = "test_name";
    $user_type = "2";

    $credential['regCode'] = '211224198407052098';
    $credential['orgCode'] = '73593362-9';
    $credential['taxCode'] = '113304017359336298';
    $credential['legalPerson'] = '张三';
    $credential['legalPersonIdentity'] = '372833199508332759';
    $credential['legalPersonIdentityType'] = '0';
    $credential['legalPersonMobile'] = '16382746283';
    $credential['contactMobile'] = '16274638485';
    $credential['contactMail'] = '123456@qq.com';
    $credential['province']= '浙江省';
    $credential['city'] = '杭州市';
    $credential['address'] = '万塘路317号';

    $applyCert = '1';

    $response = $bestSign_demo->regUser($account, $mail, $mobile, $name, $user_type, $credential, $applyCert);

    var_dump("Test regUser result:");
    var_dump($response);
}

function downloadSignatureImage()
{
    global $bestSign_demo;

    var_dump("Test downloadSignatureImage:");

    $account = "335075644@test.com";
    $image_name = "test";
    $response = $bestSign_demo->downloadSignatureImage($account, $image_name);

    //response即签名图片二进制文件流，请按照自己的业务需求处理，以下代码仅示例写到文件中，请更换自己的文件路径
    $out_file_path = "D:/work/test/download.png";
    $out_file = fopen($out_file_path, "w") or die("Unable to open file!");
    fwrite($out_file, $response);

    var_dump("Test downloadSignatureImage result:");
    var_dump("Signature picture has been written to:".$out_file_path);
}

