<?php

namespace PHPEMS\Lib\Config\Pay;

use PHPEMS\Lib\Config\Config;
use Yansongda\Pay\Pay;

class Wechat extends Config
{
    public array $config = [
        'default' => [
            'enable' => true,
            'account' => [
                // 必填-商户号
                'mch_id' => '1414206302',
                // 选填-v2商户私钥
                'mch_secret_key_v2' => '72653616204d16975931a46f9296092e',
                // 必填-v3商户秘钥
                'mch_secret_key' => '72653616204d16975931a46f9296092e',
                // 必填-商户私钥 字符串或路径
                'mch_secret_cert' => '72653616204d16975931a46f9296092e',
                // 必填-商户公钥证书路径
                'mch_public_cert_path' => PEPATH.'/secret/pay/wechat/wechatPublicCert.crt',
                // 必填
                'notify_url' => 'https://yansongda.cn/wechat/notify',
                // 选填-公众号 的 app_id
                'mp_app_id' => 'wx6967d8319bfeea19',
                // 选填-小程序 的 app_id
                'mini_app_id' => '',
                // 选填-app 的 app_id
                'app_id' => '',
                // 选填-服务商模式下，子公众号 的 app_id
                'sub_mp_app_id' => '',
                // 选填-服务商模式下，子 app 的 app_id
                'sub_app_id' => '',
                // 选填-服务商模式下，子小程序 的 app_id
                'sub_mini_app_id' => '',
                // 选填-服务商模式下，子商户id
                'sub_mch_id' => '',
                // 选填-微信平台公钥证书路径, optional，强烈建议 php-fpm 模式下配置此参数
                'wechat_public_cert_path' => [
                    '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatpay_45F***D57.pem',
                ],
                // 选填-默认为正常模式。可选为： MODE_NORMAL, MODE_SERVICE
                'mode' => Pay::MODE_NORMAL,
            ],
            'logger' => [
                'enable' => false,
                'file' => PEPATH . '/storage/logs/wechatpay.log',
                'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
                'type' => 'single', // optional, 可选 daily.
                'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
            ],
            'http' => [
                'timeout' => 5.0,
                'connect_timeout' => 5.0,
            ]
        ]
    ];
}