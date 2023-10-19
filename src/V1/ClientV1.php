<?php

namespace Oh86\UnicomCryptor\V1;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Oh86\UnicomCryptor\Exceptions\CryptorException;
use Oh86\UnicomCryptor\HttpClientUtil;
use Psr\Http\Message\ResponseInterface;

class ClientV1
{
    private string $host;
    private string $accessKey;
    private string $secretKey;

    /**
     * @param array{host: string, access_key: string, secret_key: string} $config
     */
    public function __construct(array $config)
    {
        $this->host = $config["host"];
        $this->accessKey = $config["access_key"];
        $this->secretKey = $config["secret_key"];
    }

    public function createHttpClient(): Client
    {
        return HttpClientUtil::createClientWithSignature($this->accessKey, $this->secretKey);
    }

    /**
     * 生成会话密钥
     * @return array{algID: int, keyID: string, encryptedSessionKey: string}
     * @throws CryptorException
     */
    public function generateKeyWithKek(string $keyIndex, int $algId = Constant::SGD_SM4_ECB): array
    {
        $url = sprintf("%s/api-console/key/v1/generate-key-with-kek", $this->host);
        $params = [
            "bits" => 128,
            "key" => $keyIndex,
            "algId" => $algId,
        ];
        $url = $url . "?" . http_build_query($params);

        $client = $this->createHttpClient();
        $response = $client->get($url);
        $jsonArr = $this->validateResponseAndJsonDecode($response);

        return $jsonArr["data"];
    }

    /**
     * 验证响应和返回解码后的json数组
     * @param ResponseInterface $response
     * @return array{requestId: string, code: int, message: string, data: array}
     * @throws CryptorException
     */
    protected function validateResponseAndJsonDecode(ResponseInterface $response): array
    {
        $httpCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();

        if($httpCode != 200){
            throw new CryptorException($httpCode, $body);
        }

        $jsonArr = json_decode($body, true);
        if($jsonArr["code"] != 20000){
            throw new CryptorException($httpCode, $body);
        }

        return $jsonArr;
    }

    /**
     * 对称加密
     * @param string $inData            输入数据，Base64 编码
     * @param array{algID: int, keyID: string, encryptedSessionKey: string} $sessionKeyContext 会话密钥
     * @param string|null $iv           初始向量，长度须为 16 字节，Base64 编码。CBC加密允许iv为null。
     * @param int $algId
     * @return string                   返回密文数据，Base64 编码
     * @throws CryptorException
     */
    public function encrypt(string $inData, array $sessionKeyContext, ?string $iv = null, int $algId = Constant::SGD_SM4_ECB): string
    {
        $url = sprintf("%s/api-console/crypt/v1/encrypt-with-padding", $this->host);
        $params = [
            "sessionKeyContext" => $sessionKeyContext,
            "algId" => $algId,
            "iv" => $iv,
            "inData" => $inData,
        ];

        $client = $this->createHttpClient();
        $response = $client->post($url, ["json" => $params]);
        $jsonArr = $this->validateResponseAndJsonDecode($response);
        return $jsonArr["data"];
    }

    /**
     * @param string $inData            Base64 编码
     * @param array $sessionKeyContext
     * @param string|null $iv           初始向量，长度须为 16 字节，Base64 编码。CBC加密允许iv为null。
     * @param int $algId
     * @return string                   返回明文数据，Base64 编码
     * @throws CryptorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function decrypt(string $inData, array $sessionKeyContext, ?string $iv = null, int $algId = Constant::SGD_SM4_ECB): string
    {
        $url = sprintf("%s/api-console/crypt/v1/decrypt-with-padding", $this->host);
        $params = [
            "sessionKeyContext" => $sessionKeyContext,
            "algId" => $algId,
            "iv" => $iv,
            "inData" => $inData,
        ];

        $client = $this->createHttpClient();
        $response = $client->post($url, ["json" => $params]);
        $jsonArr = $this->validateResponseAndJsonDecode($response);
        return $jsonArr["data"];
    }

    /**
     * 计算 HMAC
     * @param string $inData            Base64 编码
     * @param array $sessionKeyContext
     * @param int $algId
     * @return string                   Base64 编码
     * @throws CryptorException
     * @throws GuzzleException
     */
    public function hmac(string $inData, array $sessionKeyContext, int $algId = Constant::SGD_SM3): string
    {
        $url = sprintf("%s/api-console/crypt/v1/hmac", $this->host);
        $params = [
            "sessionKeyContext" => $sessionKeyContext,
            "algId" => $algId,
            "inData" => $inData,
        ];
        $client = $this->createHttpClient();
        $response = $client->post($url, ["json" => $params]);
        $jsonArr = $this->validateResponseAndJsonDecode($response);
        return $jsonArr["data"];
    }
}