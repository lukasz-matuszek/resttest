<?php

namespace Lib;

use Lib\CurlService as Curl;

class RestClient implements RestInterface
{
    /** @var string */
    protected $config;

    /** @var string */
    protected $baseUrl;

    /** @var array */
    protected $authMethod;

    /** @var  CurlService */
    protected $curl;

    public function __construct(string $baseUrl = '')
    {
        $this->config = new Config();

        $defaultBaseUrl = $this->config->get('resttest.baseUrl');

        $baseUrl = $baseUrl ? $baseUrl : $defaultBaseUrl;
        $this->baseUrl = $baseUrl;
        $this->curl = new Curl();
    }

    public function setBaseUrl(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function setBasicAuthMethod( string $user='', string $password='')
    {
        $params =  [
            'user' => $user ? $user : $this->config->get('resttest.apiUser'),
            'password' => $password ? $password : $this->config->get('resttest.apiPassword'),
        ];

        $this->authMethod = [ 'name' => 'basic' , 'params' => $params ];
    }

    public function setJwtAuthMethod(  string $user='', string $password='', string $registerUrl='')
    {
        //get Token
        $params = [
                'user' => $user ? $user : $this->config->get('resttest.apiUser'),
                'password' => $password ? $password : $this->config->get('resttest.apiPassword'),
                'registerUrl' => $registerUrl ? $registerUrl : $this->config->get('resttest.registerUrl')
            ];

        $response = $this->curl->request(
            'post',[
                'user' => $params['user'],
                'password' => $params['password']
            ],
            $params['registerUrl']
        );

        if (key_exists('data', $response)) {
            $token = 'sdkjghkjsghkjshgkds';// $result['data']['token'];
            if($token) {
                $this->authMethod = ['name' => 'jwt', 'params' => ['token' => $token]];
            }
        }
    }
// ----- REST interface
    public function get(array $data, string $apiUrl = ''): array|null
    {
        $this->curl->setAuthMethod($this->authMethod);
        $response = $this->curl->request("get", $data, $this->baseUrl . "/" . $apiUrl);

        return $response;
    }

    public function post(array $data, string $apiUrl = ''): array|null
    {
        $this->curl->setAuthMethod($this->authMethod);
        $response = $this->curl->request("post", $data, $this->baseUrl . "/" . $apiUrl);

        if( ! in_array($response['headers']['status'], [ 200,201,204 ] )){

            return  [
                'error' => 1,
                'message' => "Request returned error status: {$response['headers']['status']}",
                'data' => $response['data']
            ];
        }
        return [
            'success' => 1,
            'message' => "Request successful status: {$response['headers']['status']}",
            'data' => $response['data']
        ];
    }

    public function put(array $data, string $apiUrl = ''): array|null
    {
        $this->curl->setAuthMethod($this->authMethod);
        $response = $this->curl->request("put", $data, $this->baseUrl . "/" . $apiUrl);

        return $response;
    }

    public function patch(array $data, string $apiUrl = ''): array|null
    {
        $this->curl->setAuthMethod($this->authMethod);
        $response = $this->curl->request("patch", $data, $this->baseUrl . "/" . $apiUrl);

        return $response;
    }

    public function delete(array $data, string $apiUrl = ''): array|null
    {
        $this->curl->setAuthMethod($this->authMethod);
        $response = $this->curl->request("delete", $data, $this->baseUrl . "/" . $apiUrl);

        return $response;
    }

}