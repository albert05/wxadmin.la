<?php
namespace App\Common;

use \Curl\Curl;

class Upload
{
    const url = "http://api.albert.pub/v1/file/upload";

    protected $apiKey;
    protected $apiSecret;

    protected $curl;

    public function __construct()
    {
        $this->curl = new Curl();
        $this->apiKey = env("api_key");
        $this->apiSecret = env("api_secret");
    }

    public function upload($filename) {
        $params = [
            "api_key" => $this->apiKey,
            "api_secret" => $this->apiSecret,
            "image" => $filename,
        ];

        $this->curl->post($this->url, $params);

        return [
            "state" => $this->curl->response->code == 200 ? "SUCCESS" : $this->curl->response->message,
            "url" => $this->curl->response->data->url,
        ];
    }

}