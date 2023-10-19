<?php

namespace Oh86\UnicomCryptor\V1;

class Constant
{
    const SGD_SM4_ECB = 0x401;      // 1025
    const SGD_SM4_CBC = 0x402;      // 1026
    const SGD_SM4_MAC = 0x410;      // 1040

    const SGD_SM3 = 0x1;

    // 默认/null的向量
    const DEFAULT_IV = "VD9gUjJQvhS6pV+4RxrnSg==";
}