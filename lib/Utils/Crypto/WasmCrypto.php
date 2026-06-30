<?php

namespace PHPEMS\Lib\Utils\Crypto;

use Exception;
use PHPEMS\Lib\Config\Site\Site;

class WasmCrypto
{
    // 密钥文件路径（请根据实际部署位置修改）
    private array $keys;
    private string $cipher = 'AES-256-CBC';

    public function __construct(string $keyFilePath = null)
    {
        if(!$keyFilePath)$keyFilePath = (new Site())->wasmKeyFile;
        if (!file_exists($keyFilePath)) {
            throw new Exception("密钥文件不存在: {$keyFilePath}");
        }
        $this->keys = include $keyFilePath;

        if (!is_array($this->keys)) {
            throw new Exception("密钥文件格式错误，必须返回一个数组");
        }
    }

    /**
     * 解密前端 WASM 加密的数据
     * @param string $base64Payload Base64 编码的密文（包含 IV + 密文）
     * @param int $keyId 密钥 ID (0-99)
     * @return string 解密后的明文
     */
    public function decrypt(string $base64Payload, int $keyId): string
    {
        // 1. 校验 key_id 是否存在
        if (!isset($this->keys[$keyId])) {
            throw new Exception("无效的密钥 ID: {$keyId}");
        }

        // 2. Base64 解码，获取原始二进制数据
        $rawData = base64_decode($base64Payload, true); // 第二个参数 true 表示严格模式
        if ($rawData === false) {
            throw new Exception("Base64 解码失败，数据可能已损坏");
        }

        // 3. 提取 IV 和密文 (AES-256-CBC 的 IV 长度固定为 16 字节)
        $ivLength = openssl_cipher_iv_length($this->cipher);
        if (strlen($rawData) < $ivLength) {
            throw new Exception("密文数据长度不足，缺少 IV");
        }

        $iv = substr($rawData, 0, $ivLength);
        $ciphertext = substr($rawData, $ivLength);

        // 4. 获取对应的真实密钥（Base64 解码回 32 字节二进制）
        $key = base64_decode($this->keys[$keyId], true);

        // 5. 执行 OpenSSL 解密
        $decrypted = openssl_decrypt(
            $ciphertext,
            $this->cipher,
            $key,
            OPENSSL_RAW_DATA, // 必须使用此标志，表示输入是原始二进制数据
            $iv
        );

        if ($decrypted === false) {
            throw new Exception("解密失败，请检查密钥、IV 或密文完整性");
        }

        return $decrypted;
    }

    /**
     * 加密数据（通常用于 PHP 端测试，或加密后返回给前端让 WASM 解密）
     * @param string $plaintext 明文数据
     * @param int $keyId 密钥 ID (0-99)
     * @return string Base64 编码的密文（包含 IV + 密文）
     */
    public function encrypt(string $plaintext, int $keyId): string
    {
        // 1. 校验 key_id
        if (!isset($this->keys[$keyId])) {
            throw new Exception("无效的密钥 ID: {$keyId}");
        }

        // 2. 生成随机的 16 字节 IV
        $ivLength = openssl_cipher_iv_length($this->cipher);
        $iv = openssl_random_pseudo_bytes($ivLength);

        // 3. 获取真实密钥
        $key = base64_decode($this->keys[$keyId], true);

        // 4. 执行 OpenSSL 加密
        $ciphertext = openssl_encrypt(
            $plaintext,
            $this->cipher,
            $key,
            OPENSSL_RAW_DATA, // 输出原始二进制数据
            $iv
        );

        if ($ciphertext === false) {
            throw new Exception("加密失败");
        }

        // 5. 将 IV 和密文拼接后，进行 Base64 编码以便网络传输
        return base64_encode($iv . $ciphertext);
    }
}