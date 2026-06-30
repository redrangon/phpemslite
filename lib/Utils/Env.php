<?php

namespace PHPEMS\Lib\Utils;

class Env
{
    static public function getClientIp(): string
    {
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP',    // Cloudflare
            'HTTP_X_REAL_IP',           // Nginx
            'HTTP_X_FORWARDED_FOR',     // 代理/负载均衡
            'HTTP_CLIENT_IP',           // 少数代理
            'REMOTE_ADDR',              // 直连
        ];

        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                // X-Forwarded-For 可能包含多个 IP，取第一个
                if ($key === 'HTTP_X_FORWARDED_FOR') {
                    $ips = explode(',', $_SERVER[$key]);
                    $ip = trim($ips[0]);
                } else {
                    $ip = $_SERVER[$key];
                }

                // 验证 IP 格式
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return '0.0.0.0';
    }

    static public function getPaymentEnv(): string
    {
        // 1. 安全获取 User-Agent 并转为小写，防止大小写不一致导致漏判
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (empty($ua)) {
            return 'unknown';
        }
        $ua = strtolower($ua);

        // 2. 优先判断特殊环境（如微信小程序）
        // 微信小程序的 UA 特征包含 micromessenger 和 miniprogram
        if (str_contains($ua, 'micromessenger') && str_contains($ua, 'miniprogram')) {
            return 'wechat_mini';
        }

        // 3. 判断主流 App 内置浏览器
        if (str_contains($ua, 'micromessenger')) {
            return 'wechat';      // 微信内置浏览器
        }

        if (str_contains($ua, 'alipayclient')) {
            return 'alipay';      // 支付宝内置容器
        }

        if (preg_match('/qq\/|mqqbrowser/', $ua)) {
            return 'qq';          // QQ 或 QQ 浏览器
        }

        // 4. 判断是否为移动端（手机）
        // 核心逻辑：包含 'mobile' 关键字，但排除 iPad（iPad 通常归为平板/PC端处理）
        if (str_contains($ua, 'mobile') && !str_contains($ua, 'ipad')) {
            return 'mobile';
        }
        // 补充判断 iOS 设备
        if (str_contains($ua, 'iphone') || str_contains($ua, 'ipod')) {
            return 'mobile';
        }

        // 5. 默认返回 PC 端
        return 'pc';
    }

    static public function getClientEnv(): string
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        // 1. 检测设备类型
        $device = '电脑'; // 默认为电脑
        // 检测平板 (平板通常包含 Mobile 关键词，所以要先检测平板)
        if (preg_match('/(iPad|Android(?!.*Mobile)|Silk|Kindle|PlayBook|Tablet|Xoom)/i', $userAgent)) {
            $device = '平板';
        }
        // 检测手机
        elseif (preg_match('/(iPhone|Android.*Mobile|Mobile|SymbianOS|Windows Phone|BlackBerry)/i', $userAgent)) {
            $device = '手机';
        }
        // 2. 检测浏览器名称
        $browser = '未知浏览器';
        if (str_contains($userAgent, 'Edg')) {
            $browser = 'Edge';
        } elseif (str_contains($userAgent, 'Chrome')) {
            $browser = 'Chrome';
        } elseif (str_contains($userAgent, 'Firefox')) {
            $browser = 'Firefox';
        } elseif (str_contains($userAgent, 'Safari')) {
            // 排除 Chrome 和 Edge，因为它们内核也是 WebKit
            if (!str_contains($userAgent, 'Chrome') && !str_contains($userAgent, 'Edg')) {
                $browser = 'Safari';
            }
        } elseif (str_contains($userAgent, 'Opera') || str_contains($userAgent, 'OPR')) {
            $browser = 'Opera';
        } elseif (str_contains($userAgent, 'MSIE') || str_contains($userAgent, 'Trident')) {
            $browser = 'IE';
        }

        return "{$device}:{$browser}";
    }
}