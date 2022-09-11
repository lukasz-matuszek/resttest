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

    public function setBasicAuthMethod($params = [])
    {
        $params = $params ? $params : [
            'user' =>$this->config->get('resttest.apiUser'),
            'password' =>$this->config->get('resttest.apiPassword'),
        ];

        $this->authMethod = [ 'name' => 'basic' , 'params' => $params ];
    }

    public function setJwtAuthMethod( $params = [])
    {
        //get Token
        $params = $params
            ? $params
            : [
                'user' => $this->config->get('resttest.apiUser'),
                'password' => $this->config->get('resttest.apiPassword'),
                'registerUrl' => $this->config->get('resttest.registerUrl')
            ];

        $response = $this->curl->curlRequest(
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
        $response = $this->curl->setAuthMethod($this->authMethod)->curlRequest("get", $data, $this->baseUrl . "/" . $apiUrl);

        return $response;
    }

    public function post(array $data, string $apiUrl = ''): array|null
    {
        $response = $this->curl->setAuthMethod($this->authMethod)->curlRequest("post", $data, $this->baseUrl . "/" . $apiUrl);

        //if( !in_array($response['headers']['status'], [ 200,201,204 ] ));

        return $response;
    }

    public function put(array $data, string $apiUrl = ''): array|null
    {
        $response = $this->curl->setAuthMethod($this->authMethod)->curlRequest("put", $data, $this->baseUrl . "/" . $apiUrl);

        return $response;
    }

    public function patch(array $data, string $apiUrl = ''): array|null
    {
        $response = $this->curl->setAuthMethod($this->authMethod)->curlRequest("patch", $data, $this->baseUrl . "/" . $apiUrl);

        return $response;
    }

    public function delete(array $data, string $apiUrl = ''): array|null
    {
        $response = $this->curl->setAuthMethod($this->authMethod)->curlRequest("delete", $data, $this->baseUrl . "/" . $apiUrl);

        return $response;
    }

}