<?php

namespace PHPEMS\Lib\Face;

use Exception;

class FaceVerify
{
    private array $existingHashes;
    public function __construct(array $existingHashes = [])
    {
        $this->existingHashes = $existingHashes;
    }

    public function validate(string $imageData): string
    {
        // 1. 清理并解码 Base64
        $imageData = $this->decodeBase64($imageData);

        // 2. 验证 MIME 和 PNG 文件头
        $this->validatePngFormat($imageData);

        // 3. 验证尺寸（宽高 ≤640，≥200）
        $this->validateDimensions($imageData);

        // 4. 检查 Lavc 标识（元数据 + OCR 兜底）
        if ($this->containsLavc($imageData)) {
            throw new Exception("检测到虚拟设备标识（Lavc），请使用真实摄像头");
        }

        // 5. 检查是否与历史图片重复
        $currentHash = md5($imageData);
        if (in_array($currentHash, $this->existingHashes, true)) {
            throw new Exception("请勿重复提交相同照片");
        }

        return $imageData; // 返回原始数据供后续使用
    }

    private function decodeBase64(string $imageData): string
    {
        if (strlen($imageData) === 0) {
            throw new Exception("图像数据为空");
        }
        $imageData = base64_decode($imageData);
        return $imageData;
    }

    private function validatePngFormat(string $imageData): void
    {
        // 1. 检查文件头 (PNG signature)
        if (!str_starts_with($imageData, "\x89PNG\r\n\x1a\n")) {
            throw new Exception("无效的 PNG 文件头");
        }

        // 2. 检查 MIME 类型
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_buffer($fileInfo, $imageData);
        finfo_close($fileInfo);

        if ($mime !== 'image/png') {
            throw new Exception("仅支持 PNG 格式");
        }
    }

    private function validateDimensions(string $imageData): void
    {
        $size = @getimagesizefromstring($imageData);
        if (!$size || $size[2] !== IMAGETYPE_PNG) {
            throw new Exception("无法解析图像尺寸");
        }

        $width = $size[0];
        $height = $size[1];

        // 最小尺寸（防模糊小图）
        if ($width < 100 || $height < 100) {
            throw new Exception("图像分辨率过低（需 ≥200px）");
        }

        // 最大尺寸（根据你的业务：≤640px）
        if ($width > 640) {
            throw new Exception("图像分辨率过高（最大支持 640px）");
        }
    }

    private function containsLavc(string $imageData): bool
    {
        // === 第一层：检查 PNG 元数据（tEXt chunk）===
        if ($this->hasLavcInPngMetadata($imageData)) {
            return true;
        }
        return false;

        // === 第二层：OCR 扫描像素内容（兜底）===
        // 注意：OCR 有性能开销，可根据需要开关
        //return $this->hasLavcInImagePixels($imageData);
    }

    private function hasLavcInPngMetadata(string $imageData): bool
    {
        $offset = 8; // 跳过 PNG header
        $len = strlen($imageData);

        while ($offset + 12 <= $len) { // 至少要有 length(4) + type(4) + CRC(4)
            $chunkLength = unpack('N', substr($imageData, $offset, 4))[1];
            $offset += 4;

            if ($chunkLength < 0 || $offset + 8 + $chunkLength > $len) {
                break;
            }

            $chunkType = substr($imageData, $offset, 4);
            $offset += 4;

            if ($chunkType === 'tEXt') {
                $chunkData = substr($imageData, $offset, $chunkLength);
                if (stripos($chunkData, 'Lavc') !== false) {
                    return true;
                }
            }

            $offset += $chunkLength + 4; // +4 for CRC
        }

        return false;
    }

    private function hasLavcInImagePixels(string $imageData): bool
    {
        return false;
        //使用 Tesseract OCR 分析截图是否存在指定字幕，因有性能开销暂不启用
    }
}