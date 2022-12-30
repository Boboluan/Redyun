<?php
//应用公共文件
use Illuminate\Support\Facades\Redirect;

/**
 * @param string $msg 提示信息
 * @param int $code 状态码
 * @param $data
 * @return array
 * 返回数据
 */
function DataReturn(string $msg,int $code,$data = [])
{
    $result = ['msg'=>$msg,'code'=>$code,'data'=>$data];

    if ($result['code'] != 0 && empty($result['msg'])) {
        $result['msg'] = '操作失败';
    }
    return $result;
}

/**
 * @param string $msg 提示信息
 * @param int $code 状态码
 * @param $data
 * @return array
 * 返回数据
 */
function DataReturnToken(string $msg,int $code,$data = [],$token = null)
{
    $result = ['msg'=>$msg,'code'=>$code,'data'=>$data,'token'=>$token];

    if ($result['code'] != 0 && empty($result['msg'])) {
        $result['msg'] = '操作失败';
    }
    return $result;
}

/**
 * @param $result
 * @return \Illuminate\Http\JsonResponse
 * JSON格式数据返回
 */
function ApiReturn($result)
{
    return response()->json($result);
}

/**
 * @return array|string|null
 * post数据
 */
function PostData()
{
    $Params = request()->post();
    return $Params;
}

/**
 * @return array|string|null
 * get数据
 */
function GetData()
{
    $Params = request()->post();
    return $Params;
}


/**
 * @return string
 * 判断当前域名http或https,组装域名
 */
function http_type(){
    $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
    return  $http_type . $_SERVER['HTTP_HOST'];
}


/**
// * @return \Illuminate\Http\RedirectResponse
 * 登陆检测
 */
function loginCheck()
{
    $result = request()->session()->has('user');
    if($result===false){
        return false;
    }else{
        return true;
    }
}



/**
 * 对象转数组
 * @param $array
 * @return array
 */
function object_array($array) {
    if(is_object($array)) {
        $array = (array)$array;
    } if(is_array($array)) {
        foreach($array as $key=>$value) {
            $array[$key] = object_array($value);
        }
    }
    return $array;
}


/**
 * @param string $url
 * @param string $param
 * @return bool|string
 * curl 请求post
 */
function request_post($url = '', $param = '')
{
    if (empty($url) || empty($param)) {
        return false;
    }
    $postUrl = $url;
    $curlPost = $param;
    // 初始化curl
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $postUrl);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    // 要求结果为字符串且输出到屏幕上
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    // post提交方式
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
    // 运行curl
    $data = curl_exec($curl);
    curl_close($curl);
    return $data;
}


/**
 * curl 请求
 * @param $url
 * @param string $data
 * @param string $method
 * @param string $header
 * @return mixed
 */
function request_curl($url, $data = '', $method = 'GET', $header = '')
{
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    if ($header) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    } else {
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    }
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    if ($method == 'POST') {
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        if ($data != '') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        }
    }
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl); // 执行操作
    curl_close($curl); // 关闭CURL会话
    return $tmpInfo; // 返回数据
}

/**
 * @return string|null
 * 获取头部token
 */
function getToken()
{
    return request()->header('token');
}


