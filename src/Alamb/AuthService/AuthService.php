<?php


namespace App\Services;

use Carbon\Carbon;
use \GuzzleHttp\Client as Client;

class AuthService
{

      static private $_instance = null;

      protected $config;
      protected $api_list;
      protected $service_tickets=null;


    /**
     * 私有化构造函数
     * AuthService constructor.
     */
      private function __construct()
      {
          $this->config=\config('service.service_config');
          $this->api_list=\config('service.api_list');
      }

    /**
     * 单例模型，自己实例化自己
     * @return AuthService|null
     */
    static function getInstance()
    {
        if(!self::$_instance instanceof  AuthService){
            self::$_instance=new AuthService();
        }
        return self::$_instance;
    }



    /**
     * 创建RSA加密签名
     * @param string $content
     * @param string $privateKey
     * @return bool|string
     */
    public  function createSign($content, $privateKey)
    {
        $privateKey =$this->convertPrivateKey($privateKey);
        $openssl_private_key = openssl_get_privatekey($privateKey);

        openssl_sign($content, $signature, $openssl_private_key, OPENSSL_ALGO_MD5);
       // @openssl_private_encrypt($content, $signature, $openssl_private_key, OPENSSL_PKCS1_PADDING);
        @openssl_free_key($openssl_private_key);
        $sign = base64_encode($signature);

        return empty($sign) ? false : $sign;
    }

    /**
     * 转换PrivateKey
     * @param string $priKey
     * @return string
     */
    public  function convertPrivateKey($priKey)
    {
        //判断是否传入私钥内容
        $private_key_string = !empty($priKey) ? $priKey : '';
        //64位换行私钥内容
        $private_key_string = chunk_split($private_key_string, 64, "\r\n");
        //私钥头部
        $private_key_header = "-----BEGIN RSA PRIVATE KEY-----\r\n";
        //私钥尾部
        $private_key_footer = "-----END RSA PRIVATE KEY-----";
        //完整私钥拼接
        $private_key_string = $private_key_header . $private_key_string . $private_key_footer;
        return $private_key_string;
    }

    /**
     * RSA解密(验签)
     * @param string $sign
     * @param string $publickey
     * @return bool
     */
    public function checkSign($content,$sign, $publicKey)
    {
        //转换成PublicKey格式
        $publicKey = $this->convertPublicKey($publicKey);
        //获取公钥钥内容
        $openssl_public_key = @openssl_get_publickey($publicKey);

        //对数据进行解密
        $result = @openssl_verify($content,base64_decode($sign), $openssl_public_key,OPENSSL_ALGO_MD5);


      // $result = openssl_public_decrypt(base64_decode($sign), $decrypted, $openssl_public_key, OPENSSL_PKCS1_PADDING);
        //释放资源
        @openssl_free_key($openssl_public_key);
        return $result;
    }

    /**
     * 转换PublicKey
     * @param string $publicKey
     * @return string
     */
    public  function convertPublicKey($publicKey)
    {
        //判断是否传入公钥内容
        $public_key_string = !empty($publicKey) ? $publicKey : '';
        //64位换行公钥钥内容
        $public_key_string = chunk_split($public_key_string, 64, "\r\n");
        //公钥头部
        $public_key_header = "-----BEGIN PUBLIC KEY-----\r\n";
        //公钥尾部
        $public_key_footer = "-----END PUBLIC KEY-----";
        //完整公钥拼接
        $public_key_string = $public_key_header . $public_key_string . $public_key_footer;
        return $public_key_string;
    }



    /**
     * @param $method 请求类型
     * @param $url 请求地址
     * @param array $params GET 为query  其他为Json 的内容
     * @param array $headers 信息头
     * @return bool|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getResponse($method,$url,$params=[],$headers=[])
    {
        $client = new Client();

        //获取微服务token
        $app_token=$this->checkTickets();

        //头信息合并
        $headers = array_merge($headers, ['Authorization' => "Bearer " . $app_token]);


        switch ($method) {
            case "GET":
                $array = [
                    'headers' => $headers,
                    'query' => $params,
                ];
                break;
            case "POST":
            case "PUT":
            case "DELETE":
                $array=[
                    'headers'=>$headers,
                    'json'=>$params,
                ];
                break;
            default:
                $array=false;
                break;
        }

        if(!$array){
            return false;
        }

        $response = $client->request($method, $url, $array);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTickets()
    {
        $timestamp = Carbon::now()->timestamp;
        $content = $this->config['app_key'] . $timestamp;

        $data = [
            'service_id' => $this->config['service_id'],
            'sign' => $this->createSign($content, $this->config['private_key']),
            'timestamp' => $timestamp,
        ];

        $ticket_data = collect($this->api_list['ticket']);
        $url = $this->config['url'] . $ticket_data->last();


        $client = new Client();
        $response = $client->request($ticket_data->first(), $url, ['json' => $data]);

        $contents = json_decode($response->getBody()->getContents());

        $this->cacheTickets($contents->data);
    }

    /**
     * 检查子服务token有效性
     * @return string|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkTickets()
    {

        $client =new \Predis\Client();

        if(!$client->exists('app_token')){
            return $this->getTickets();
        }else{
            if (!$client->exists('app_expire')) {
                $this->refreshTicket($client->get('app_refresh_token'));
            }
        }

        return $client->get('app_token');
    }



    /**
     * 调用API刷新通信证
     * @param $refresh_token
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function refreshTicket($refresh_token)
    {
        $refresh= collect($this->api_list['refreshTicket']);
        $url = $this->config['url'] . $refresh->last();

        $data=['refresh_token'=>$refresh_token];
        $client = new Client();
        $response = $client->request($refresh->first(), $url, ['query' => $data]);

        $contents = json_decode($response->getBody()->getContents());

        $this->refreshCacheTickets($contents->data);

    }


    /**
     * 缓存票据
     * @param array $service_tickets
     */
    public function cacheTickets($service_tickets=[])
    {
        if(!empty($service_tickets)) {
            $client = new \Predis\Client();

            $client->set("app_token", $service_tickets->token);
            $client->expire("app_token", 100);

            $client->set("app_refresh_token", $service_tickets->refresh_token);
            $client->expire("app_refresh_token", 100);

            $client->set("app_expire", $service_tickets->expire);
            $client->expire("app_expire", 10);
        }
    }


    /**
     * 刷新缓存的票据
     * @param array $refresh_tickets
     */
    public function refreshCacheTickets($refresh_tickets=[])
    {
        if(!empty($refresh_tickets)){
            $client =new \Predis\Client();
            $client->expire("app_token", 100);

            $client->set("app_refresh_token", $refresh_tickets->refresh_token);
            $client->expire("app_refresh_token", 100);

            $client->set("app_expire", $refresh_tickets->expire);
            $client->expire("app_expire", 10);
        }
    }

}