<?php

namespace Lib;

class CurlService
{
    /** @var array */
    protected $authMethod;

    /** @var array */
    private $defaultOptions;

    public function __construct()
    {
        $this->setDefaultOptions();
    }

    public function setDefaultOptions()
    {
        $this->defaultOptions = [
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_HEADER' => true,
        ];
    }

    public function curlRequest(string $method, array $data, string $requestUrl = ''): array|null
    {
        $this->requestUrl = $requestUrl;

        if (! $requestUrl) {
            return null;
        }

        $setCurlOptions = 'set' . ucfirst($method) . "Options";
        if (! method_exists($this, $setCurlOptions)) {
            return null;
        }
        $curlOptions = array_merge($this->defaultOptions, $this->$setCurlOptions($data));

        return $this->execCurl($curlOptions, $requestUrl);
    }

    protected function setGetOptions($data)
    {
        return [
            'CURLOPT_URL' => sprintf("%s?%s", $this->requestUrl, http_build_query($data)),
        ];
    }

    protected function setPostOptions($data)
    {
        return [
            'CURLOPT_POST' => 1,
            'CURLOPT_POSTFIELDS' => $data,
        ];
    }

    protected function setPutOptions($data)
    {
        return [
            'CURLOPT_CUSTOMREQUEST' => 'PUT',
            'CURLOPT_POSTFIELDS' => $data,
        ];
    }

    protected function setPatchOptions($data)
    {
        return [
            'CURLOPT_CUSTOMREQUEST' => 'PATCH',
            'CURLOPT_POSTFIELDS' => $data,
        ];
    }

    protected function setDeleteOptions($data)
    {
        return [
            'CURLOPT_CUSTOMREQUEST' => 'DELETE',
        ];
    }

    public function setAuthMethod($authMethod)
    {
        $this->authMethod = $authMethod;

        return $this;
    }

    protected function execCurl(array $curlOptions, string $requestUrl): array|null
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $requestUrl);

        // set auth curl options
        if ($this->authMethod && isset($this->authMethod['name']) && $this->authMethod['name']) {
            $authMethod = "set" . ucfirst($this->authMethod['name']) . "Auth";
            if (! method_exists($this, $authMethod)) {
                return ['error' => 'Auth error', 'message' => 'Authorization method do not exist'];
            }

            $curlAuthOptions = $this->$authMethod($this->authMethod['params']);
            $curlOptions = array_merge($curlOptions, $curlAuthOptions,);
        }
        // set curl options
        foreach ($curlOptions as $curlOption => $optionValue) {
            if (! defined($curlOption)) {
                continue;
            }
            curl_setopt($curl, constant($curlOption), $optionValue);
        }

        // execute
        try {
            $result = curl_exec($curl);
        } catch (\Exception $e) {
            $error = curl_error($curl);
            curl_close($curl);

            return ['error' => $error, 'message' => $e->getMessage()];
        }

        $headers = $this->getHeaders($curl, $result);
        $response = $this->getData($curl, $result);
        curl_close($curl);


        switch ($headers['content-type']) {
            case 'application/json' :
                $data = json_decode($response);
                break;
            default:
                $data = $response;
                break;
        }

        return ['headers' => $headers, 'data' => $data];
    }

    protected function getHeaders($curl, $result)
    {
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headerData = substr($result, 0, $headerSize);

        $headers = preg_split("/\r\n|\n|\r/", $headerData);

        if ('HTTP' === substr($headers[0], 0, 4)) {
            [, $parsedHeaders['status'], $parsedHeaders['status_text']] = explode(' ', $headers[0]);
            unset($headers[0]);
        }

        foreach ($headers as $header) {

            if (! preg_match('/^([^:]+):(.*)$/', $header, $output)) {
                continue;
            }

            $parsedHeaders[$output[1]] = $output[2];
        }

        return $parsedHeaders;
    }

    protected function getData($curl, $result)
    {
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $data = substr($result, $headerSize);

        return $data;
    }

    // AUTH methods : setting curl options
    protected function setBasicAuth($params)
    {
        return [
            'CURLOPT_HTTPAUTH' => CURLAUTH_BASIC,
            'CURLOPT_USERPWD' => "{$params['user']}:{$params['password']}",
        ];
    }

    protected function setJwtAuth($params)
    {
        return [
            'CURLOPT_HTTPHEADER' => [
                'Content-Type: application/json',
                "Authorization: {$params['token']}",
            ],
        ];
    }

}