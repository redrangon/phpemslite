<?php

namespace PHPEMS\Lib\Utils\Converter;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use PHPEMS\Lib\Config\Site\Site;

/**
 * 文档转换核心类 (基于 Pandoc)
 * 特性：安全防护、资源自动清理、并发图片下载、企业级特性支持、媒体持久化与格式兼容
 */
class PanDoc
{
    protected ?Client $client = null; // 懒加载 HTTP 客户端
    protected string $tempDir;
    protected static ?bool $pandocExists = null;

    // 允许的转换格式白名单
    protected const ALLOWED_EXTENSIONS = ['docx', 'pdf', 'md', 'html', 'htm', 'epub', 'txt'];
    // 浏览器不支持的图片格式黑名单
    protected const UNSUPPORTED_IMAGE_EXTS = ['wmf', 'emf', 'eps'];

    public function __construct()
    {
        $this->checkPandocExists();

        // 创建临时目录存放图片和中间文件
        $this->tempDir = DI(Site::class)->tmpDir . '/doc_converter_' . uniqid();
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }

        // 注册析构清理，确保即使发生异常也能清理临时文件
        register_shutdown_function([$this, 'cleanup']);
    }

    /**
     * 懒加载获取 Guzzle Client
     * 只有在真正需要发起 HTTP 请求时才会实例化
     */
    protected function getClient(): Client
    {
        if ($this->client === null) {
            $this->client = new Client([
                'timeout'  => 30.0,
                'headers'  => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36'
                ]
            ]);
        }
        return $this->client;
    }

    /**
     * 将内容转换为字符串并返回（不落盘）
     * 注意：此方法仅适用于目标文件不需要外部媒体资源的情况（如 PDF 或纯文本）
     */
    public function convertToString(string $input, string $format, array $options = []): string
    {
        $format = strtolower($format);
        if (!in_array($format, self::ALLOWED_EXTENSIONS, true)) {
            throw new Exception("不支持的输出格式: {$format}");
        }

        $tempOutputPath = $this->tempDir . '/' . uniqid() . '.' . $format;
        $this->convert($input, $tempOutputPath, $options);

        $content = file_get_contents($tempOutputPath);
        if ($content === false) {
            throw new Exception("读取转换后的文件内容失败: {$tempOutputPath}");
        }

        //@unlink($tempOutputPath);
        return $content;
    }

    /**
     * 通用转换方法（落盘）
     */
    public function convert(string $input, string $outputPath, array $options = []): bool
    {
        $this->validateOutputPath($outputPath);
        $inputPath = $this->prepareInput($input, $options['base_url'] ?? '');
        $params = $this->buildCommandParams($inputPath, $outputPath, $options);
        $this->executePandoc($params);

        // 【关键】：如果是导出为 HTML/MD 等文本格式，需要持久化提取的媒体文件
        $ext = strtolower(pathinfo($outputPath, PATHINFO_EXTENSION));
        if (in_array($ext, ['html', 'htm', 'md', 'markdown'], true)) {
            $this->persistMedia($outputPath);
        }

        return true;
    }

    // ==================== 核心内部方法 ====================

    /**
     * 检查 Pandoc 命令是否存在（带静态缓存，使用 proc_open 绕过 Shell 解析）
     */
    protected function checkPandocExists(): void
    {
        if (self::$pandocExists !== null) return;

        $command = stripos(PHP_OS, 'WIN') === 0 ? ['where', 'pandoc'] : ['which', 'pandoc'];
        $descriptors = [1 => ['pipe', 'w'], 2 => ['pipe', 'w']];

        $process = proc_open($command, $descriptors, $pipes);
        if (!is_resource($process)) throw new Exception("Pandoc 进程启动失败。");

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]); fclose($pipes[2]);
        $returnCode = proc_close($process);

        if ($returnCode !== 0 || empty(trim($stdout))) {
            throw new Exception("系统中未检测到 Pandoc 命令，请确保已安装并配置到环境变量中。");
        }
        self::$pandocExists = true;
    }

    protected function validateOutputPath(string $outputPath): void
    {
        $ext = strtolower(pathinfo($outputPath, PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
            throw new Exception("不支持的输出格式: {$ext}");
        }
        $dir = dirname($outputPath);
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            throw new Exception("无法创建输出目录: {$dir}");
        }
    }

    protected function prepareInput(string $input, string $baseUrl): string
    {
        if (filter_var($input, FILTER_VALIDATE_URL)) {
            $html = $this->fetchHtml($input);
            $processedHtml = $this->processImages($html, $input);
        } elseif (stripos($input, '<') !== false && stripos($input, '>') !== false) {
            $processedHtml = $this->processImages($input, $baseUrl);
        } else {
            if (!file_exists($input)) throw new Exception("输入文件不存在: {$input}");
            return $input;
        }

        $inputPath = $this->tempDir . '/content.html';
        file_put_contents($inputPath, $processedHtml);
        return $inputPath;
    }

    protected function buildCommandParams(string $inputPath, string $outputPath, array $options): string
    {
        $params = ['pandoc', '-s', escapeshellarg($inputPath), '--mathml', '--extract-media=' . escapeshellarg($this->tempDir), '--metadata title="'.($options['title']??'').'"','-V CJKmainfont="SimSun"'];

        if (!empty($options['reference_doc']) && file_exists($options['reference_doc'])) {
            $params[] = '--reference-doc=' . escapeshellarg($options['reference_doc']);
        }
        if (!empty($options['pdf_engine'])) {
            $params[] = "--pdf-engine={$options['pdf_engine']}";
        }
        if (!empty($options['toc'])) {
            $params[] = '--toc';
        }

        $params[] = '-o ' . escapeshellarg($outputPath);
        return implode(' ', $params);
    }

    protected function executePandoc(string $command): void
    {
        $descriptors = [1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        // Linux 下加上 timeout 限制，防止 Pandoc 遇到损坏文件时死锁
        if (stripos(PHP_OS, 'WIN') !== 0) $command = "timeout 60 {$command}";

        $process = proc_open($command, $descriptors, $pipes);
        if (!is_resource($process)) throw new Exception("Pandoc 进程启动失败。");

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]); fclose($pipes[2]);
        $returnCode = proc_close($process);

        if ($returnCode !== 0) {
            throw new Exception("Pandoc 转换失败 (Code: {$returnCode}): " . trim($stderr).$command);
        }
    }

    // ==================== 网络与媒体处理 ====================

    protected function fetchHtml(string $url): string
    {
        try {
            return $this->getClient()->get($url)->getBody()->getContents();
        } catch (GuzzleException $e) {
            throw new Exception("抓取 URL 内容失败: " . $e->getMessage());
        }
    }

    /**
     * 处理 HTML 中的图片（并发下载 + MIME 校验）
     */
    protected function processImages(string $html, string $baseUrl): string
    {
        $pattern = '/<img[^>]+src=["\']([^"\']+)["\']/i';
        preg_match_all($pattern, $html, $matches);
        if (empty($matches[1])) return $html;

        $promises = [];
        $client = $this->getClient();
        foreach ($matches[1] as $imgSrc) {
            if (str_starts_with($imgSrc, 'data:')) continue;
            $absoluteUrl = (string) UriResolver::resolve(new Uri($baseUrl), new Uri($imgSrc));
            $promises[$imgSrc] = $client->getAsync($absoluteUrl);
        }
        $responses = Utils::unwrap($promises);

        return preg_replace_callback($pattern, function ($matches) use ($responses) {
            $imgSrc = $matches[1];
            if (!isset($responses[$imgSrc])) return $matches[0];
            try {
                $imgContent = $responses[$imgSrc]->getBody()->getContents();
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $extMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
                $ext = $extMap[$finfo->buffer($imgContent)] ?? 'jpg';

                $localPath = $this->tempDir . '/' . md5($imgSrc) . '.' . $ext;
                file_put_contents($localPath, $imgContent);
                return str_replace($imgSrc, $localPath, $matches[0]);
            } catch (\Exception $e) { return $matches[0]; }
        }, $html);
    }

    /**
     * 持久化 Word 转 HTML/MD 时提取的图片
     */
    protected function persistMedia(string $outputPath): void
    {
        $outputDir = dirname($outputPath);
        $mediaDir = $outputDir . '/' . pathinfo($outputPath, PATHINFO_FILENAME) ;
        // 【修复点】：Pandoc 默认将图片提取到 --extract-media 指定的目录下的 media 子文件夹中
        $pandocMediaDir = $this->tempDir . '/media';

        // 如果 Pandoc 没有提取出任何图片，直接返回
        if (!is_dir($pandocMediaDir)) return;

        $mediaFiles = glob($pandocMediaDir . '/*');

        if (empty($mediaFiles)) return;
        if (!is_dir($mediaDir)) mkdir($mediaDir, 0755, true);

        $pathMap = [];
        foreach ($mediaFiles as $file) {
            if (is_dir($file)) continue;
            $fileName = basename($file);
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            // 检测并转换浏览器不支持的图片格式（如 WMF, EMF）
            if (in_array($ext, self::UNSUPPORTED_IMAGE_EXTS, true)) {
                $convertedFile = $this->convertUnsupportedImage($file);
                if ($convertedFile) {
                    $file = $convertedFile;
                    $fileName = pathinfo($convertedFile, PATHINFO_BASENAME);
                }
            }

            $newPath = $mediaDir . '/' . $fileName;
            if (@rename($file, $newPath)) {
                $pathMap[$this->tempDir . '/media/' . basename($file)] = str_ireplace(PEPATH,'.',pathinfo($outputPath, PATHINFO_DIRNAME) . '/'.pathinfo($outputPath, PATHINFO_FILENAME) . '/' . $fileName);
            }
        }
        // 替换输出文件中的图片路径
        if (!empty($pathMap) && file_exists($outputPath)) {
            $content = file_get_contents($outputPath);
            $content = str_replace(array_keys($pathMap), array_values($pathMap), $content);
            file_put_contents($outputPath, $content);
        }
    }

    /**
     * 将不支持的图片格式（如 WMF, EMF）转换为 PNG
     * 依赖系统安装的 ImageMagick (convert 命令)
     */
    protected function convertUnsupportedImage(string $filePath): ?string
    {
        $outputPath = $this->tempDir . '/' . pathinfo($filePath, PATHINFO_FILENAME) . '.png';

        // 使用 ImageMagick 进行转换，同样使用数组传参绕过 Shell
        $command = ['convert', $filePath, $outputPath];
        $descriptors = [2 => ['pipe', 'w']];

        $process = proc_open($command, $descriptors, $pipes);
        if (!is_resource($process)) return null;

        fclose($pipes[2]);
        $returnCode = proc_close($process);

        return ($returnCode === 0 && file_exists($outputPath)) ? $outputPath : null;
    }

    /**
     * 清理临时目录
     */
    public function cleanup(): void
    {
        if (is_dir($this->tempDir)) {
            $files = glob($this->tempDir . '/*');
            foreach ($files as $file) if (is_file($file)) @unlink($file);
            @rmdir($this->tempDir);
        }
    }
}