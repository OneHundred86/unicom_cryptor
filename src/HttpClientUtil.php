<?php

namespace Oh86\UnicomCryptor;

use \GuzzleHttp\Client;

class HttpClientUtil
{
    public static function createClientWithSignature(string $accessKey, string $secretKey): Client
    {
        $nonce = rand(100000, 999999);
        $timestamp = Util::getCurMSTimestamp();
        $signature = base64_encode(hash_hmac("sha256", $accessKey.$nonce.$timestamp, $secretKey, true));

        return new Client([
            "headers" => [
                "X-TD-AppKey" => $accessKey,
                "X-TD-Nonce" => $nonce,
                "X-TD-Timestamp" => $timestamp,
                "X-TD-Signature" => $signature,
            ],
        ]);
    }
}