<?php

namespace PHPEMS\Lib\Face\Drivers;

use PHPEMS\Lib\Config\Site\Face;
use PHPEMS\Lib\Face\FaceInterface;

class BaiDuFace implements FaceInterface
{
    private string $accessId;
    private string $accessKey;
    private ?string $accessToken = null;

    public function __construct()
    {
        $config = new Face();
        $this->accessId = $config->accessId;
        $this->accessKey = $config->accessKey;
    }

    private function getAccessToken(){
        if($this->accessToken === null){
            $curl = curl_init();
            $postData = array(
                'grant_type' => 'client_credentials',
                'client_id' => $this->accessId,
                'client_secret' => $this->accessKey
            );
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://aip.baidubce.com/oauth/2.0/token',
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_SSL_VERIFYPEER  => false,
                CURLOPT_SSL_VERIFYHOST  => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => http_build_query($postData)
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $rtn = json_decode($response);
            return $rtn->access_token;
        }
        else return $this->accessToken;
    }

    public function FaceComparison(string $imageA, string $imageB):bool
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://aip.baidubce.com/rest/2.0/face/v3/match?access_token={$this->getAccessToken()}",
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_SSL_VERIFYHOST  => false,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>json_encode(array(
                array("image" => $imageA,'image_type' => "BASE64"),
                array("image" => $imageB,'image_type' => "BASE64")
            ),256),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            )
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $tokenJson = json_decode($response,true);
        return $tokenJson['result']['score'] > 0;
    }
}