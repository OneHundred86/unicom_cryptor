<?php

namespace Oh86\UnicomCryptor;

class Util
{
    /**
     * 获取当前的毫秒级时间戳
     * @return int
     */
    public static function getCurMSTimestamp(): int
    {
        $ts = microtime(true);
        return (int)($ts * 1000);
    }
}