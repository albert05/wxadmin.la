<?php
namespace App\Common;

use \Curl\Curl;

class Upload
{
    const URL = "http://api.albert.pub/v1/file/upload";

    protected $apiKey;
    protected $apiSecret;

    protected $curl;

    public function __construct()
    {
        $this->curl = new Curl();
        $this->curl->setOpt(CURLOPT_HTTPHEADER, ['Content-Type:multipart/form-data;charset=utf-8']);
        $this->apiKey = env("api_key");
        $this->apiSecret = env("api_secret");
    }

    public function upload($filename) {
        $params = [
            "api_key" => $this->apiKey,
            "api_secret" => $this->apiSecret,
            "image" => "@$filename",
        ];

        $this->curl->post(self::URL, $params);

        return [
            "state" => json_encode($this->curl->response),//$this->curl->response->code == 200 ? "SUCCESS" :
            "url" => $this->curl->response->data->url,
        ];
    }

}