<?php

namespace PHPEMS\Lib\Utils\FileService;

use RuntimeException;

class FileUploader
{
    private mixed $size;
    private mixed $error;
    private mixed $clientFilename;
    private mixed $clientMediaType;
    private bool $moved = false;
    private mixed $tmpName;

    public function __construct(array $file = [])
    {
        if(empty($file))throw new RuntimeException('No file uploaded');
        $this->tmpName = $file['tmp_name'];
        $this->clientFilename = $file['name']??null;
        $this->clientMediaType = $file['type']??null;
        $this->size = $file['size'];
        $this->error = $file['error']??UPLOAD_ERR_OK;
    }

    public function moveTo($targetPath): void
    {
        if ($this->moved) {
            throw new RuntimeException('File already moved');
        }
        if ($this->error !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Cannot move file with upload error');
        }

        $dir = dirname($targetPath);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                throw new RuntimeException("Unable to create directory: $dir");
            }
        }

        // 关键：使用 move_uploaded_file() 保证安全
        if (!move_uploaded_file($this->tmpName, $targetPath)) {
            throw new RuntimeException("Failed to move uploaded file to: $targetPath");
        }

        $this->moved = true;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function getClientFilename(): ?string
    {
        return $this->clientFilename;
    }

    public function getClientMediaType(): ?string
    {
        return $this->clientMediaType;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function getTempFilePath(): string
    {
        return $this->tmpName;
    }
}