<?php

namespace PHPEMS\Lib\Face;

use PHPEMS\Lib\Config\Site\Site;
use PHPEMS\Lib\Face\Drivers\TestFace;
use Exception;
class FaceProvider
{
    protected static $instance = null;
    protected ?FaceInterface $faceDriver = null;

    private function __construct()
    {
        $this->faceDriver = new TestFace();
    }

    public function compare(string $imageA, string $imageB):bool
    {
        return $this->faceDriver->FaceComparison($imageA, $imageB);
    }
    public static function FaceComparison(string $imageA, string $imageB):bool
    {
        if(self::$instance == null)
        {
            self::$instance = new static();
        }
        return self::$instance->compare($imageA, $imageB);
    }

    /**
     * 防止作弊
     * @throws Exception
     */
    public static function PreventCheatingAndSave(string $imageData,array $existingHashes = [],?string $subDir = null):bool|string
    {
        $imageData = (new FaceVerify($existingHashes))->validate($imageData);
        $name = bin2hex(random_bytes(16)).'.png';
        $targetDir = (new Site())->faceImageSavePath."/".$subDir?:date('Ymd');
        if (!is_dir($targetDir)) {
            // mkdir 错误检查
            if (!mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                throw new Exception("人脸信息保存失败");
            }
        }
        file_put_contents($targetDir."/".$name,$imageData);
        $relativePath = ltrim(str_replace(PEPATH, '', $targetDir."/".$name), DIRECTORY_SEPARATOR);
        return str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
    }
}