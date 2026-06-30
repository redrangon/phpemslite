<?php

namespace PHPEMS\App\Cert\Service;

use Exception;
use PHPEMS\Lib\Config\Site\Site;
use PHPEMS\Lib\Utils\QrcodeProvider;

class CertMaker
{
    /**
     * @var string 字体文件所在的根路径
     */
    private $fontRootPath;
    private $backgroundPath;
    private $id;
    private $cacheDirPath;
    private $cacheFilePath;
    private $needRegenerate = null;

    public static function create(int $id): CertMaker
    {
        return new CertMaker($id);
    }

    /**
     * 构造函数
     * @param string $fontRootPath 字体文件所在的目录路径
     */
    public function __construct(int $id)
    {
        $config = new Site();
        $this->fontRootPath = $config->fontRootPath;
        $this->id = $id;
        $this->cacheDirPath = $config->certImageSavePath.str_pad(intval($this->id / 100) + 1, 8, '0', STR_PAD_LEFT).DIRECTORY_SEPARATOR;
        if (!is_dir($this->cacheDirPath)) {
            mkdir($this->cacheDirPath, 0777, true);
        }
        $this->cacheFilePath = $this->cacheDirPath . $this->id . '.png';
    }

    public function isCached($backgroundPath): bool
    {
        $this->needRegenerate = false;
        // 1. 判断是否需要重新生成
        if (!file_exists($this->cacheFilePath)) {
            // 情况1：缓存文件不存在，必须生成
            $this->needRegenerate = true;
        } else {
            $backgroundMtime = filemtime($this->backgroundPath = $backgroundPath);
            $cacheMtime = filemtime($this->cacheFilePath);

            // 情况2：背景图的最后修改时间晚于缓存图片，说明背景图更新了，需要重新合成
            if ($backgroundMtime > $cacheMtime) {
                $this->needRegenerate = true;
            }
        }
        return !$this->needRegenerate;
    }

    /**
     * 主入口：生成或输出证书
     *
     * @param string $backgroundPath
     * @return void
     * @throws Exception
     * @param array $settings 合成元素的设置数组
     *  $setting = [
     *  0 => 类型，分为txt,img和qrcode
     *  1 => x坐标
     *  2 => y坐标
     *  3 => txt内容，img地址，qrcode内容
     *  4 => txt文字大小，img和qrcode图片宽度
     *  5 => img和qrcode图片高度
     * ]
     */
    public function render(string $backgroundPath,array $settings): void
    {
        $this->backgroundPath = $backgroundPath;

        if($this->needRegenerate === null)$this->isCached($backgroundPath);
        // 2. 执行逻辑
        if ($this->needRegenerate) {
            // 重新生成并保存
            $this->createAndSave($settings, $this->cacheFilePath);
        }
    }


    /**
     * 创建证书图片并保存到磁盘
     */
    private function createAndSave(array $settings, string $savePath): void
    {
        // 复用之前的 createCertImg 逻辑
        $bgimg = $this->createCertImg($this->backgroundPath, $settings);

        if (!$bgimg) {
            throw new Exception("图片合成失败");
        }

        // 保存为 PNG
        if (!imagepng($bgimg, $savePath, 9)) {
            imagedestroy($bgimg);
            throw new Exception("图片保存失败: {$savePath}");
        }
        imagedestroy($bgimg);
    }

    /**
     * 直接从文件输出 HTTP 响应
     */
    public function outputFromFile(): void
    {
        $filePath = $this->cacheFilePath;
        if (!file_exists($filePath)) {
            throw new Exception("文件不存在: {$filePath}");
        }

        // 设置 HTTP 头
        header('Content-Type: image/png');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: public, max-age=31536000'); // 长期缓存（可选）

        // 直接输出文件流，避免大文件占用内存
        readfile($filePath);
        exit; // 结束脚本
    }

    public function getCertImage(): string
    {
        return str_replace(PEPATH,'',$this->cacheFilePath);
    }

    /**
     * 创建证书图片
     *
     * @param string $backgroundPath 背景图片的绝对路径
     * @param array $settings 合成元素的设置数组
     * @return resource|false 返回 GD 图像资源，失败返回 false
     * @throws Exception 如果文件不存在或GD库不可用
     */
    public function createCertImg(string $backgroundPath, array $settings)
    {
        // 1. 基础校验
        if (!file_exists($backgroundPath)) {
            throw new Exception("背景图片不存在: {$backgroundPath}");
        }

        if (!function_exists('imagecreatefromstring')) {
            throw new Exception('GD库未安装或未启用');
        }

        // 2. 加载背景图
        $bgInfo = getimagesize($backgroundPath);
        if (!$bgInfo) {
            throw new Exception("无法读取背景图片信息: {$backgroundPath}");
        }

        $ext = strtolower(pathinfo($backgroundPath, PATHINFO_EXTENSION));
        $bgimg = $this->createImageResource($backgroundPath, $ext);

        if (!$bgimg) {
            throw new Exception("无法创建背景图像资源: {$backgroundPath}");
        }

        $width = $bgInfo[0];
        $height = $bgInfo[1];
        // 3. 遍历设置项并绘制
        foreach ($settings as $setting) {
            // 统一处理坐标，防止越界
            $x = isset($setting[1]) ? max(0, min($setting[1], $width)) : 0;
            $y = isset($setting[2]) ? max(0, min($setting[2], $height)) : 0;
            switch ($setting['type'] ?? 'txt') {
                case 'txt':
                    $this->drawText($bgimg, ['content' => $setting[3],'fontSize' => $setting[4]], $x, $y);
                    break;
                case 'img':
                    $this->drawImage($bgimg, ['src' => $setting[3],'width' => $setting[4],'height' => $setting[5]], $x, $y);
                    break;
                case 'qrcode':
                    $this->drawQrCode($bgimg, ['content' => $setting[3],'width' => $setting[4],'height' => $setting[5]], $x, $y);
                    break;
            }
        }

        return $bgimg;
    }

    /**
     * 将资源输出为 PNG 并销毁
     * @param resource $resource
     * @return void
     */
    public function outputAndDestroy($resource): void
    {
        if (is_resource($resource)) {
            header('Content-Type: image/png');
            imagepng($resource, null, 9);
            imagedestroy($resource);
        }
    }

    // --- 私有辅助方法 ---

    /**
     * 根据扩展名创建图像资源
     */
    private function createImageResource(string $source, string $ext)
    {
        $ext = strtolower($ext);
        return match ($ext) {
            'jpg', 'jpeg' => function_exists('imagecreatefromjpeg') ? imagecreatefromjpeg($source) : false,
            'gif' => function_exists('imagecreatefromgif') ? imagecreatefromgif($source) : false,
            'png' => function_exists('imagecreatefrompng') ? imagecreatefrompng($source) : false,
            default => false,
        };
    }

    /**
     * 绘制文字
     */
    private function drawText($bgimg, $setting, $x, $y): void
    {
        $text = $setting['content'] ?? '';
        $fontSize = $setting['fontSize'] ?? 24;
        $fontFile = $this->fontRootPath . DIRECTORY_SEPARATOR . ($setting['font'] ?? 'simfang.ttf');

        // 确保字体文件存在
        if (!file_exists($fontFile)) {
            // 这里可以设置一个默认字体作为后备
            $fontFile = $this->fontRootPath . DIRECTORY_SEPARATOR . 'simfang.ttf';
        }

        // 设置颜色 (默认黑色)
        $color = $this->allocateColor($bgimg, $setting['color'] ?? [0, 0, 0]);
        // 绘制文字
        // 注意：原代码中 y 坐标可能需要调整，因为 imagettftext 的 y 是基线
        imagettftext($bgimg, $fontSize, 0, $x, $y + $fontSize, $color, $fontFile, $text);
    }

    /**
     * 绘制图片
     */
    private function drawImage($bgimg, $setting, $x, $y): void
    {
        $imgPath = $setting['src'] ?? '';
        if (!file_exists($imgPath)) return;

        $size = getimagesize($imgPath);
        $resource = $this->createImageResource($imgPath, pathinfo($imgPath, PATHINFO_EXTENSION));
        if (!$resource) return;

        $targetW = $setting['width'] ?? imagesx($resource);
        $targetH = $setting['height'] ?? imagesy($resource);

        imagecopyresampled($bgimg, $resource, $x, $y, 0, 0, $targetW, $targetH, imagesx($resource), imagesy($resource));
        imagedestroy($resource);
    }

    /**
     * 绘制二维码 (需要引入第三方库)
     * 这里仅作示意，实际生产环境建议使用 endroid/qr-code
     */
    private function drawQrCode($bgimg, $setting, $x, $y): void
    {
        $text = $setting['content'] ?? '';
        if (empty($text)) return;

        $resource = imagecreatefromstring(QrcodeProvider::outSimpleQrcodeString($text));
        if (!$resource) return;
        $targetW = $setting['width'] ?? imagesx($resource);
        $targetH = $setting['height'] ?? imagesy($resource);
        imagecopyresampled($bgimg, $resource, $x, $y, 0, 0, $targetW, $targetH, imagesx($resource), imagesy($resource));
        imagedestroy($resource);
    }

    /**
     * 分配颜色
     */
    private function allocateColor($image, $color): bool|int
    {
        if (is_array($color) && count($color) === 3) {
            return imagecolorallocate($image, $color[0], $color[1], $color[2]);
        }
        return imagecolorallocate($image, 0, 0, 0); // 默认黑色
    }
}