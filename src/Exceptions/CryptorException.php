<?php

namespace Oh86\UnicomCryptor\Exceptions;

class CryptorException extends \Exception
{
    protected int $httpCode;
    protected string $responseBody;

    /**
     * @param int $httpCode
     * @param string $responseBody
     */
    public function __construct(int $httpCode, string $responseBody)
    {
        $this->httpCode = $httpCode;
        $this->responseBody = $responseBody;

        parent::__construct("请求密码池出错");
    }

    /**
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    /**
     * @return string
     */
    public function getResponseBody(): string
    {
        return $this->responseBody;
    }
}