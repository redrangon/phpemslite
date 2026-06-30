<?php

namespace PHPEMS\Lib\Utils;

use PHPEMS\Lib\Config\Site\Site;
use PHPEMS\Lib\Session\SessionProvider;

class Captcha
{
    private $id;
    private $answer;
    private $width = 120;
    private $height = 42;
    private $length = 4;
    private $mathMode = false;

    /**
     * 构造函数
     *
     * @param array $config 配置项
     * @param string $config['id'] 验证码 ID
     * @param string $config['answer'] 验证码答案
     * @param int $config['width'] 验证码宽度
     * @param int $config['height'] 验证码高度
     * @param int $config['length'] 验证码长度
     * @param bool $config['mathMode'] 是否启用数学模式
     */
    public function __construct(array $config = [])
    {
        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        $this->id = bin2hex(random_bytes(16));
    }

    /**
     * 生成验证码图片（返回 base64）
     */
    public function create(): string
    {
        $code = $this->generateCode();
        $key = "captcha_{$this->id}";
        $data = SessionProvider::set($key,[
            'value' => strtolower($this->answer),
            'expire_at' => time() + 300
        ]);
        return 'data:image/png;base64,' . base64_encode($this->buildImage($code));
    }

    /**
     * 验证用户输入（验证后立即销毁）
     */
    public static function verify(string $input, string $id): bool
    {
        if (empty($input) || empty($id)) {
            return false;
        }

        $key = "captcha_{$id}";
        $data = SessionProvider::get($key);
        if (!$data) {
            return false;
        }

        SessionProvider::set($key,null); // 一次性使用

        // 检查是否过期
        if ($data['expire_at'] < time()) {
            return false;
        }

        // 安全比较（防时序攻击）
        return hash_equals($data['value'], strtolower(trim($input)));
    }

    public function getId(): string
    {
        return $this->id;
    }

    // ----------- 内部方法 -----------

    private function generateCode(): string
    {
        if ($this->mathMode) {
            $a = random_int(1, 20);
            $b = random_int(1, 20);
            $this->answer = (string)($a + $b);
            return "$a + $b = ?";
        }

        // 排除 0/O, 1/l/i 等易混淆字符
        $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $this->answer = substr(str_shuffle($chars), 0, $this->length);
        return $this->answer;
    }

    private function buildImage(string $text): string
    {
        $ttfPath = DI(Site::class,'default')->randCodeTTFPath;
        $image = imagecreatetruecolor($this->width, $this->height);
        imagealphablending($image, false);
        imagesavealpha($image, true);

        // 白色背景
        $bg = imagecolorallocatealpha($image, 255, 255, 255, 127);
        imagefill($image, 0, 0, $bg);

        // 添加干扰点
        for ($i = 0; $i < 150; $i++) {
            $color = imagecolorallocate($image, mt_rand(180, 230), mt_rand(180, 230), mt_rand(180, 230));
            imagesetpixel($image, mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
        }

        // 添加干扰线
        for ($i = 0; $i < 2; $i++) {
            $color = imagecolorallocate($image, mt_rand(150, 200), mt_rand(150, 200), mt_rand(150, 200));
            imageline($image, mt_rand(0, $this->width), mt_rand(0, $this->height),mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
        }

        // 文字颜色
        $textColor = imagecolorallocate($image, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));

        // 计算文字位置（居中）
        $fontSize = round($this->height * 0.6);
        $bbox = imagettfbbox($fontSize, 0, $ttfPath, $text);
        $textWidth = $bbox[2] - $bbox[0];
        $x = round(($this->width - $textWidth) / 2);
        $y = round(($this->height + $fontSize) / 2);

        // 写入文字（需提供字体文件）
        imagettftext($image, $fontSize, mt_rand(-8, 8), $x, $y, $textColor,$ttfPath, $text);

        ob_start();
        imagepng($image);
        $data = ob_get_clean();
        imagedestroy($image);

        return $data;
    }
}