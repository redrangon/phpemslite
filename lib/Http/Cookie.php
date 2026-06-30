<?php

namespace PHPEMS\Lib\Http;

class Cookie
{
    /**
     * 默认配置
     *
     * @var array
     */
    protected static $defaultOptions = [
        'expire' => 0,
        'path' => '/',
        'domain' => null,
        'secure' => null, // null 表示自动检测 HTTPS
        'httponly' => true,
        'samesite' => 'Lax', // 可选: Lax, Strict, None
    ];

    /**
     * 待发送的 Cookie 队列
     *
     * @var array
     */
    protected static $queue = [];

    /**
     * 设置全局默认选项
     *
     * @param array $options
     */
    public static function setDefaultOptions(array $options)
    {
        static::$defaultOptions = array_merge(static::$defaultOptions, $options);
    }

    /**
     * 添加一个 Cookie 到队列
     *
     * @param string $name
     * @param string|null $value
     * @param int|array $minutesOrOptions 过期时间（分钟）或完整选项数组
     * @return void
     */
    public static function queue($name, $value = null, $minutesOrOptions = 0)
    {
        if (is_array($minutesOrOptions)) {
            $options = $minutesOrOptions;
        } else {
            $minutes = is_numeric($minutesOrOptions) ? (int)$minutesOrOptions : 0;
            $options = ['expire' => $minutes > 0 ? time() + ($minutes * 60) : 0];
        }

        static::$queue[$name] = [
            'name' => $name,
            'value' => $value,
            'options' => $options,
        ];
    }

    /**
     * 删除 Cookie（设为空值 + 过期）
     *
     * @param string $name
     * @param array $options
     * @return void
     */
    public static function forget($name, array $options = [])
    {
        static::queue($name, '', array_merge($options, ['expire' => time() - 3600]));
    }

    /**
     * 获取当前队列中的所有 Cookie
     *
     * @return array
     */
    public static function getQueue()
    {
        return static::$queue;
    }

    /**
     * 清空队列（通常在响应发送后调用）
     */
    public static function flush()
    {
        static::$queue = [];
    }

    /**
     * 发送所有排队的 Cookie
     * 通常由 Response 对象在输出前调用
     */
    public static function sendQueuedCookies()
    {
        foreach (static::$queue as $cookie) {
            static::send(
                $cookie['name'],
                $cookie['value'],
                $cookie['options']
            );
        }
        static::flush();
    }

    /**
     * 实际发送单个 Cookie（兼容 PHP 7.1+）
     *
     * @param string $name
     * @param string|null $value
     * @param array $options
     * @return void
     */
    protected static function send($name, $value, array $options = [])
    {
        $config = array_merge(static::$defaultOptions, $options);

        // 自动检测 secure
        if ($config['secure'] === null) {
            $config['secure'] = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        }

        // 构建 path 参数（兼容 SameSite for PHP < 7.3）
        $path = $config['path'];
//        if (!empty($config['samesite'])) {
//            $path .= '; samesite=' . $config['samesite'];
//        }

        // 注意：PHP 7.2 及以下不支持 options 数组，必须用参数列表
        setcookie(
            $name,
            $value,
            (int)$config['expire'],
            $path,
            $config['domain'] ?? '',
            (bool)$config['secure'],
            (bool)$config['httponly']
        );
    }
}