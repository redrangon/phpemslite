<?php

namespace PHPEMS\Lib\Utils;

use Exception;
use finfo;
use PHPEMS\Lib\Config\Site\Attach;
use PHPEMS\Lib\Utils\FileService\FileStream;
use PHPEMS\Lib\Utils\FileService\FileUploader;
use RuntimeException;

class FileProvider
{
    private array $allowedMimes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'application/pdf',
        'text/plain',
        'application/zip',
        'application/vnd.rar',
        'application/json',
        'application/x-wine-extension-ini',
        'video/mp4',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    /**
     * MIME 到扩展名的严格映射（用于安全生成文件扩展名）
     * 原因：防止扩展名与内容不一致（如 .jpg.php）
     */
    private array $mimeToExtension = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'application/pdf' => 'pdf',
        'application/json' => 'json',
        'application/x-wine-extension-ini' => 'json',
        'text/plain' => 'txt',
        'application/zip' => 'zip',
        'application/vnd.rar' => 'rar',
        'video/mp4' => 'mp4',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
    ];

    /**
     * MIME 扩展名别名到真实扩展名映射（用于生成文件扩展名）
     * 某些 MIME 类型可能与标准扩展名不匹配
     */
    private array $aliasMap = ['jpeg' => 'jpg'];

    /**
     * 最大文件大小（字节），默认 5MB
     */
    private int $maxSize = 524288000;

    private string $storagePath;

    public function __construct($isPrivate = false)
    {
        $config = DI(Attach::class,'default');
        $storagePath = $isPrivate ? $config->privateSavePath : $config->publicSavePath;
        $this->storagePath = rtrim(realpath($storagePath), DIRECTORY_SEPARATOR);
    }

    /**
     * 设置允许的 MIME 类型
     * 同时更新 mimeToExtension 映射（可选，此处简化为仅设白名单）
     */
    public function setAllowedMimes(array $mimes): self
    {
        $this->allowedMimes = $mimes;
        return $this;
    }

    /**
     * 设置最大文件大小（字节）
     */
    public function setMaxSize(int $bytes): self
    {
        $this->maxSize = $bytes;
        return $this;
    }

    /**
     * 上传文件
     *
     * 检查顺序优化（性能优先）：
     * 1. 上传错误检查 - 零成本
     * 2. 文件大小检查 - 零成本（PHP 已在内存中）
     * 3. MIME 类型检测 - 读取文件头部（16KB，内存友好）
     * 4. 扩展名验证 - 简单字符串比较
     * 5. 移动文件 - I/O 操作
     *
     * 优化说明：
     * - 在读取文件内容之前就完成所有基本验证
     * - 避免处理不符合要求的文件
     * - MIME 检测只读取文件头部，避免大文件内存问题
     *
     * @param FileUploader $file 上传的文件
     * @param string|null $subDir 子目录
     * @return array 上传结果
     */
    public function upload(FileUploader $file,string $subDir = null): array
    {
        // 第一步：检查上传错误（零成本）
        if ($file->getError() !== UPLOAD_ERR_OK) {
            return $this->handleUploadError($file->getError());
        }

        // 第二步：检查文件大小（零成本，避免处理超大文件）
        if ($file->getSize() > $this->maxSize) {
            return ['success' => false, 'error' => 'File too large'];
        }

        // 第三步：获取文件基本信息（零成本）
        $originalName = $file->getClientFilename();
        $originalExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        // 第四步：检测 MIME 类型（只读取文件头部 16KB）
        // 优化：避免将整个文件加载到内存，对于 100MB 文件只需 16KB 内存
        $mime = $this->detectMimeType(DI(FileStream::class, $file->getTempFilePath()),$originalName);

        //  检查 MIME 是否在白名单中
        if (!in_array($mime, $this->allowedMimes, true)) {
            return ['success' => false, 'error' => 'Invalid file type'.$mime];
        }

        //  从 MIME 映射出安全扩展名
        if (!isset($this->mimeToExtension[$mime])) {
            return ['success' => false, 'error' => 'Unsupported MIME type'.$mime];
        }
        $extension = $this->mimeToExtension[$mime];
        $normalizedOriginalExt = $this->aliasMap[$originalExt] ?? $originalExt;
        if ($originalExt !== '' && $normalizedOriginalExt !== $extension) {
            return ['success' => false, 'error' => 'File extension does not match content type'.$mime];
        }

        //  子目录只允许字母、数字、下划线、连字符（禁止 / \）
        $subDir = $subDir ?: date('Ymd');
        $subDir = preg_replace('/[^a-zA-Z0-9_-]/', '', $subDir);
        if ($subDir === '') {
            $subDir = 'default';
        }
        $targetDir = $this->storagePath . DIRECTORY_SEPARATOR . $subDir;
        if (!is_dir($targetDir)) {
            // mkdir 错误检查
            if (!mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                throw new RuntimeException("Failed to create directory: {$targetDir}");
            }
        }

        $safeFilename = $this->generateSafeFilename($extension);
        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $safeFilename;

        // 第五步：移动文件（I/O 操作，使用 PHP 内置的安全函数）
        // 优化：move_uploaded_file 直接移动临时文件，不读取内容到内存
        $file->moveTo($targetPath);

        $relativePath = ltrim(str_replace(PEPATH, '', $targetPath), DIRECTORY_SEPARATOR);
        $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);

        return ['success' => true, 'path' => $relativePath, 'targetPath' => $targetPath];
    }

    private function handleUploadError(int $errorCode): array
    {
        $messages = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension',
        ];
        $msg = $messages[$errorCode] ?? 'Unknown upload error';
        return ['success' => false, 'error' => $msg];
    }

    /**
     * 检测文件的 MIME 类型
     * 
     * 优化说明：只读取文件头部（16KB）而不是整个文件
     * 原因：
     * 1. 文件头部信息足够识别大多数文件类型
     * 2. 避免大文件上传时将整个文件加载到内存
     * 3. 对于 100MB 文件，只需消耗 16KB 内存而非 100MB+
     * 
     * @param FileStream $stream 文件流
     * @param int $sampleSize 读取的样本大小（字节），默认 16KB
     * @return string MIME 类型
     */
    private function detectMimeType(FileStream $stream, ?string $originalName = null, int $sampleSize = 16384): string
    {
        $stream->rewind();
        $header = $stream->read($sampleSize);
        $fInfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $fInfo->buffer($header);

        // 对 JSON 进行特殊处理
        if ($originalName !== null && strtolower(pathinfo($originalName, PATHINFO_EXTENSION)) === 'json') {
            // 如果 finfo 返回文本类 MIME，进一步验证内容是否为 JSON
            if (in_array($mime, ['text/plain', 'text/x-json', 'application/json', 'application/octet-stream'], true)) {
                $trimmed = ltrim($header);
                if (str_starts_with($trimmed, '{') || str_starts_with($trimmed, '[')) {
                    return 'application/json';
                }
            }
        }
        return $mime ?: 'application/octet-stream';
    }

    /**
     * 生成安全文件名（避免冲突和特殊字符）
     */
    private function generateSafeFilename(string $extension): string
    {
        $name = bin2hex(random_bytes(16));
        return $name . '.' . $extension;
    }

    public function transfer(string $originalName):string
    {
        $originalExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $mime = $this->detectMimeType(DI(FileStream::class, $originalName));
        if (!in_array($mime, $this->allowedMimes, true)) {
            throw new Exception('文件格式错误');
        }

        //  从 MIME 映射出安全扩展名
        if (!isset($this->mimeToExtension[$mime])) {
            throw new Exception('文件格式错误');
        }
        $extension = $this->mimeToExtension[$mime];
        $normalizedOriginalExt = $this->aliasMap[$originalExt] ?? $originalExt;
        if ($originalExt !== '' && $normalizedOriginalExt !== $extension) {
            throw new Exception('文件格式错误');
        }
        $subDir = date('Ymd');
        $targetDir = $this->storagePath . DIRECTORY_SEPARATOR . $subDir;
        if (!is_dir($targetDir)) {
            // mkdir 错误检查
            if (!mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                throw new Exception("目标文件夹创建失败");
            }
        }

        $safeFilename = $this->generateSafeFilename($extension);
        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $safeFilename;

        // 第五步：移动文件（I/O 操作，使用 PHP 内置的安全函数）
        // 优化：move_uploaded_file 直接移动临时文件，不读取内容到内存
        rename($originalName,$targetPath);

        $relativePath = ltrim(str_replace(PEPATH, '', $targetPath), DIRECTORY_SEPARATOR);
        $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);

        return $relativePath;
    }
}