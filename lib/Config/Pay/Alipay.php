<?php

namespace PHPEMS\Lib\Config\Pay;

use PHPEMS\Lib\Config\Config;
use PHPEMS\Lib\Config\Site\Site;
use Yansongda\Pay\Pay;

class Alipay extends Config
{
    public array $config = [
        'default' => [
            'enable' => true,
            'account' => [
                // 必填-支付宝分配的 app_id
                'app_id' => '9021000164635968',
                // 必填-应用私钥 字符串或路径
                'app_secret_cert' => 'MIIEogIBAAKCAQEAj8HUXagEnZjmNWSdO7tnikeBhVrM/cuuwNlkXqRUFbK41XB6P7VrDnPgRWKIxdXideLPjfL9I8Y3uWZZGTJ2DmLlfgVPWkU1d4JdrD3gRHmSW3wUVJNF0KPM1HtHoTcH42LGybH7tqLuHrTaufAlq0ir/6EfUGgMmY/1VT+GkBTfL+xlePXBTTMLZZFnIG8XC3NywqhYOyxZSkOKvJo+t4UbvsAPqiDiERalkNKmqBEusupFg9dwxsDbK1X6g0jQimf651qMCAf6IXpzlbRvxKbZOXidSI0meorJ6W7dNMhoRR9iL49MWyTVpGiRx0dhheSZKfdA6UeOyPLCSwU+GQIDAQABAoIBAAVg5yUp6CJNWi+dOBTICjOuQTZQE2Nbf4pPl+XCmDLHoQq7TpebngnIZBe0fBGK0xI+Di3tbXMtM2CIZ8T9klUvdg6k/DcQZ9O02PRczEtDkSpDFi+j4vTPbBZ3FlL90Bm91Fovgo9uwSH8+kICriYebfD+gkFr4yzLqg4qdkplrtF0GcC0fapw9T4Az2RcaoWVckkVNWL4m3JTHDWRbfx1wVd9CyAaXQ2BfVM2RFlqYH8YBjK3TlHLQ4RY48ZS28dgdnCoI+Yj56w+iryETb/N8HTNz6B0oNWhaYLswLZWy5bQCsSwaZurN4J2tkBDQZAe61FlrShBlnTrIi+VaBECgYEA7pIh2B415WmaLsMuiB1s7hNsaDflKk8cQqIR4FZAEsaJ+K5ivF8E1H2vBjelOaL+R8Czfnk+01eaSmk1a9FUQYHDIjjFW0vXiMt8JLoXlMU9+evRR5QMwSOLnBGi/jCI77RZ0Z5svaRaBor2l3dgDDzgyYgxmrsJi0ZVJeGVQ/cCgYEAmkJyGZcR4djh+mTINOs5Zvw0/irDVtR30rJeFhMTORua5YtQGzZ9p7DKTWwDSs7qnHTfxyxEJeFpqkYNVZBFW27vaQ+eUegPQvb2es5ZYeCJ1j83ySYRLLm83grtUYnv7BPxP+Por7KHqIDIWaLaMkU0uyK2WSMY+uVs19j76m8CgYA27XxPVv1PuZWfKJ8hBa1bzysJf70KlbZK/SeigBk2eXGwyO3AsxvYlKtkghFPiOCEwrBQ8Tduz1+dvEVpcoO0pVy5F1sRHxAb1cXaauIdBaC0VwATO9oH6dgt8b2WSrRshBid85zTuPBlSz9lNj3t82JJ5EqPdnrHD0VxeFO2lwKBgBy06M7iUi2ZUtRqFOfkLlZ/8Myr4JY0C+hl0SSCgD1MadL1zf6CcXfXao5l32b4gqiDB3HlRvlVrXhGeQzHQGD3kA4ZHflYGh74Hn5UEEYqpvv738HLF78fAYrAtdFKvh9MxsSBAThRHPW6oY4sxDY+ssVwfEayRV/Leing82B5AoGATLCaSYOP05yXSQZMNoyLT3llPU9ZSwLVL0P57LLLmVmagiJP/4TvS8WVeQIIaK3k8aSUxpzXCB3T/mQF+UFBWjNlp7XzjEZ2o/vcZ2G3lSuuO6FhiO8vb1yEqWbPExrIRcKsUG+V1lHagKkRxIrXGxPylPNjJaH4rF4BEMVnkbk=',
                // 必填-应用公钥证书 路径
                'app_public_cert_path' => PEPATH.'/secret/pay/alipay/appPublicCert.crt',
                // 必填-支付宝公钥证书 路径
                'alipay_public_cert_path' => PEPATH.'/secret/pay/alipay/alipayPublicCert.crt',
                // 必填-支付宝根证书 路径
                'alipay_root_cert_path' => PEPATH.'/secret/pay/alipay/alipayRootCert.crt',
                'return_url' => 'https://yansongda.cn/alipay/return',
                'notify_url' => 'https://yansongda.cn/alipay/notify',
                // 选填-第三方应用授权token
                'app_auth_token' => '',
                // 选填-服务商模式下的服务商 id，当 mode 为 Pay::MODE_SERVICE 时使用该参数
                'service_provider_id' => '',
                // 选填-默认为正常模式。可选为： MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
                'mode' => Pay::MODE_SANDBOX,
            ],
            'logger' => [
                'enable' => false,
                'file' => PEPATH . '/storage/logs/alipay.log',
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