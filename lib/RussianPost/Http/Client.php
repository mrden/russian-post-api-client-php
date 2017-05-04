<?php

namespace RussianPost\Http;

use RussianPost\Exception\CurlException;
use RussianPost\Response\ApiResponse;

class Client
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    protected $url = 'https://otpravka-api.pochta.ru/1.0/';
    protected $accessToken;
    protected $login;
    protected $password;

    public function __construct($accessToken, $login, $password)
    {
        $this->accessToken = $accessToken;
        $this->login = $login;
        $this->password = $password;
    }

    public function makeRequest($path, $method = 'GET', $parameters = array(), $headers = array(), $timeout = 30) {
        $url = $this->url . $path;

        if ($method == self::METHOD_GET) {
            $url .= '?' . http_build_query($parameters);
        }

        if (in_array($method, array(self::METHOD_POST, self::METHOD_PUT, self::METHOD_DELETE))) {
            $headers[] = 'Content-Type: application/json';
        }

        $headers[] = sprintf('Authorization: AccessToken %s', $this->accessToken);
        $headers[] = sprintf('X-User-Authorization: Basic %s', base64_encode(sprintf('%s:%s', $this->login, $this->password)));

        $curlHandler = curl_init();
        curl_setopt($curlHandler, CURLOPT_USERAGENT, 'RussianPost-API-client/1.0');
        curl_setopt($curlHandler, CURLOPT_URL, $url);
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandler, CURLOPT_FAILONERROR, false);
        curl_setopt($curlHandler, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandler, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curlHandler, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curlHandler, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($curlHandler, CURLOPT_HEADER, false);
        curl_setopt($curlHandler, CURLOPT_POST, false);
        curl_setopt($curlHandler, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curlHandler, CURLOPT_POSTFIELDS, array());
        curl_setopt($curlHandler, CURLOPT_HTTPHEADER, $headers);

        if ($method == self::METHOD_POST) {
            curl_setopt($curlHandler, CURLOPT_POST, true);
        } elseif (in_array($method, array(self::METHOD_PUT, self::METHOD_DELETE))) {
            curl_setopt($curlHandler, CURLOPT_CUSTOMREQUEST, $method);
        }

        if (count($parameters) > 0 && in_array($method, array(self::METHOD_POST, self::METHOD_PUT, self::METHOD_DELETE))) {
            curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $parameters);
        }

        $responseBody = curl_exec($curlHandler);
        $statusCode = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($curlHandler, CURLINFO_CONTENT_TYPE);

        $errno = curl_errno($curlHandler);
        $error = curl_error($curlHandler);

        curl_close($curlHandler);

        if ($errno) {
            throw new CurlException($error, $errno);
        }

        if ($statusCode >= 400) {
            $error = $responseBody;

            $response = @json_decode($responseBody, true);
            if (isset($response['message'])) {
                $error = $response['message'];
            } elseif (isset($response['desc'])) {
                $error = $response['desc'];
            }

            throw new CurlException($error, $statusCode);
        }

        if (stripos($contentType, 'pdf') !== false) {
            return $responseBody;
        }


        return new ApiResponse($statusCode, $responseBody);
    }
}
