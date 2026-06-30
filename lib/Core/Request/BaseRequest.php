<?php

namespace PHPEMS\Lib\Core\Request;

use PHPEMS\Lib\Utils\FileService\FileUploader;

abstract class BaseRequest
{
    private string $type = 'json';
    protected array $request = [
        'route' => [],
        'data' => [],
        'files' => [],
        'user' => [],
        'store' => []
    ];

    protected function parseFiles($rawFiles): array
    {
        $files = [];
        foreach($rawFiles as $key => $file)
        {
            if(is_array($file['name']))
            {
                foreach($file['name'] as $i => $name)
                {
                    $files[$key][$i] = new FileUploader([
                        'name' => $name,
                        'type' => $file['type'][$i],
                        'tmp_name' => $file['tmp_name'][$i],
                        'error' => $file['error'][$i],
                        'size' => $file['size'][$i],
                    ]);
                }
            }
            else
            {
                $files[$key] = new FileUploader([
                    'name' => $file['name'],
                    'type' => $file['type'],
                    'tmp_name' => $file['tmp_name'],
                    'error' => $file['error'],
                    'size' => $file['size'],
                ]);
            }
        }
        return $files;
    }

    public function getRoute($key = null)
    {
        if($this->request['route'][$key]??false)return $this->request['route'][$key];
        return match ($key) {
            'controller' => $this->request['route'][0] === 'plugin' ? $this->request['route'][3] : $this->request['route'][2],
            'action' => $this->request['route'][0] === 'plugin' ? $this->request['route'][4] : $this->request['route'][3],
            'endpoint' => $this->request['route'][1],
            'app' => $this->request['route'][0],
            default => $this->request['route'],
        };
    }

    public function getRaw()
    {
        return $this->request['data'];
    }

    public function getUser()
    {
        return $this->request['user'];
    }

    public function setUser($user)
    {
        return $this->request['user'] = $user;
    }

    public function get($key = null)
    {
        return $this->request['data'][$key] ?? null;
    }

    public function getFile($key)
    {
        return $this->request['files'][$key] ?? null;
    }

    public function getFiles()
    {
        return $this->request['files'];
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function __set($key, $value)
    {
        $this->request['data'][$key] = $value;
    }

    public function getStore(string $key)
    {
        return $this->request['store'][$key]??null;
    }

    public function setStore(string $key, $value = null): void
    {
        $this->request['store'][$key] = $value;
    }

    public function getType(): string
    {
        return $this->type;
    }
}