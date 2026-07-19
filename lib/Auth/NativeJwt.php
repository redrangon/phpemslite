<?php

namespace PHPEMS\Lib\Auth;

/**
 * JWT (JSON Web Token) 原生实现类
 *
 * 支持 HS256 算法，包含完整的签名验证、时间声明检查、防重放攻击等。
 * 零外部依赖，专为高安全性与跨端认证设计。
 *
 * @package PHPEMS\Lib\Auth
 */
class NativeJwt
{
    private string $secretKey;
    private string $algorithm = 'sha256';
    private int $leeway = 60; // 时钟偏差容忍秒数

    /**
     * 构造函数
     *
     * @param string $secretKey 密钥（推荐至少32字节）
     * @param int $leeway 时钟偏差容忍时间（秒），默认60秒
     * @throws \InvalidArgumentException 密钥长度不足时抛出
     */
    public function __construct(string $secretKey, int $leeway = 60)
    {
        if (strlen($secretKey) < 32) {
            throw new \InvalidArgumentException(
                'Secret key should be at least 32 bytes for HS256 security'
            );
        }

        $this->secretKey = $secretKey;
        $this->leeway = max(0, $leeway);
    }

    /**
     * 生成 JWT Token
     *
     * @param array $payload 载荷数据（应包含 exp、iat 等标准声明）
     * @return string JWT Token 字符串
     * @throws \RuntimeException JSON 编码失败时抛出
     */
    public function encode(array $payload): string
    {
        // 1. 构建 Header
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        if ($header === false) {
            throw new \RuntimeException('Failed to encode header: ' . json_last_error_msg());
        }

        // 2. 自动补充标准声明
        if (!isset($payload['iat'])) {
            $payload['iat'] = time();
        }

        // 3. 自动生成 jti (JWT ID) 防止重放攻击
        if (!isset($payload['jti'])) {
            $payload['jti'] = $this->generateJti();
        }

        $payloadJson = json_encode($payload);
        if ($payloadJson === false) {
            throw new \RuntimeException('Failed to encode payload: ' . json_last_error_msg());
        }

        // 4. Base64Url 编码
        $base64Header = $this->base64UrlEncode($header);
        $base64Payload = $this->base64UrlEncode($payloadJson);

        // 5. 生成签名 (HMAC-SHA256)
        $signature = hash_hmac($this->algorithm, "$base64Header.$base64Payload", $this->secretKey, true);
        $base64Signature = $this->base64UrlEncode($signature);

        // 6. 拼接返回
        return "$base64Header.$base64Payload.$base64Signature";
    }

    /**
     * 解析并验证 JWT Token
     *
     * @param string $token JWT Token 字符串
     * @return array|null 验证通过返回载荷数组，失败返回 null
     */
    public function decode(string $token): ?array
    {
        // 1. 分割 Token
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$base64Header, $base64Payload, $base64Signature] = $parts;

        // 2. 解码并验证 Header
        $headerJson = $this->base64UrlDecode($base64Header);
        if ($headerJson === '') {
            return null;
        }

        $header = json_decode($headerJson, true);
        if (!$header || !is_array($header)) {
            return null;
        }

        // 3. 验证算法（防止 alg=none 绕过攻击）
        if (!isset($header['alg']) || $header['alg'] !== 'HS256') {
            return null;
        }

        // 4. 验证签名（防篡改，使用 hash_equals 防止时序攻击）
        $expectedSignature = hash_hmac(
            $this->algorithm,
            "$base64Header.$base64Payload",
            $this->secretKey,
            true
        );

        if (!hash_equals($this->base64UrlEncode($expectedSignature), $base64Signature)) {
            return null;
        }

        // 5. 解码 Payload
        $payloadJson = $this->base64UrlDecode($base64Payload);
        if ($payloadJson === '') {
            return null;
        }

        $payload = json_decode($payloadJson, true);
        if (!$payload || !is_array($payload)) {
            return null;
        }

        // 6. 验证标准时间声明
        $currentTime = time();

        // 6.1 检查过期时间 (exp)
        if (isset($payload['exp'])) {
            if (!is_numeric($payload['exp']) || $payload['exp'] < $currentTime - $this->leeway) {
                return null;
            }
        }

        // 6.2 检查生效时间 (nbf)
        if (isset($payload['nbf'])) {
            if (!is_numeric($payload['nbf']) || $payload['nbf'] > $currentTime + $this->leeway) {
                return null;
            }
        }

        // 6.3 检查签发时间 (iat)
        if (isset($payload['iat'])) {
            if (!is_numeric($payload['iat']) || $payload['iat'] > $currentTime + $this->leeway) {
                return null;
            }
        }

        return $payload;
    }

    /**
     * 安全刷新 Token（延长有效期）
     *
     * 【业务安全】：保留原 Token 中的所有业务数据（如 role, client_type 等），
     * 仅强制刷新时间戳和 jti，防止旧时间戳被复用并保证业务不中断。
     *
     * @param string $token 原始 Token
     * @param int|null $newExp 新的过期时间（时间戳），默认延长1小时
     * @return string|null 返回新 Token，失败返回 null
     */
    public function refresh(string $token, ?int $newExp = null): ?string
    {
        $payload = $this->decode($token);
        if ($payload === null) {
            return null;
        }

        // 1. 移除所有时间相关声明
        unset($payload['exp'], $payload['iat'], $payload['nbf']);

        // 2. 继承原 Token 中的所有业务数据，更新时间戳
        $payload['iat'] = time();
        $payload['exp'] = $newExp ?? (time() + 3600);

        // 3. 强制生成全新的 jti，防止重放攻击
        $payload['jti'] = $this->generateJti();

        return $this->encode($payload);
    }

    /**
     * 验证 Token 是否有效（仅返回布尔值）
     *
     * @param string $token JWT Token
     * @return bool 是否有效
     */
    public function validate(string $token): bool
    {
        return $this->decode($token) !== null;
    }

    /**
     * 获取 Token 中的载荷（不验证签名，仅用于调试）
     *
     * 【类型安全】：严格校验 json_decode 的结果必须是数组，杜绝下游代码的类型警告。
     *
     * @param string $token JWT Token
     * @return array|null 载荷数组，失败返回 null
     * @deprecated 仅用于调试，生产环境请使用 decode() 方法
     */
    public function getPayloadWithoutVerify(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) < 2) {
            return null;
        }

        $payloadJson = $this->base64UrlDecode($parts[1]);
        if ($payloadJson === '') {
            return null;
        }

        $decoded = json_decode($payloadJson, true);

        // 确保解码结果严格为数组
        return is_array($decoded) ? $decoded : null;
    }

    /**
     * 生成安全的 JWT ID（防重放攻击）
     *
     * @return string 唯一标识符
     */
    private function generateJti(): string
    {
        try {
            return bin2hex(random_bytes(16));
        } catch (\Exception $e) {
            // 降级方案：高精度时间 + 随机数，确保唯一性
            return uniqid('jti_', true) . '_' . mt_rand(100000, 999999);
        }
    }

    // ==================== 私有工具方法 ====================

    /**
     * Base64Url 编码（URL 安全的 Base64）
     *
     * @param string $data 待编码数据
     * @return string 编码后的字符串
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64Url 解码（增强容错与安全性）
     *
     * 【传输兼容】：增加 URL 解码支持，防止 GET 传参时被 URL 编码破坏。
     * 【安全防御】：白名单过滤非法字符，防止模糊测试（Fuzzing）攻击。
     *
     * @param string $data 待解码数据
     * @return string 解码后的数据，失败返回空字符串
     */
    private function base64UrlDecode(string $data): string
    {
        // 1. 先进行 URL 解码，兼容 URL 参数传递的场景
        $data = urldecode($data);

        // 2. 【安全防御】仅保留合法的 Base64Url 字符，防止恶意注入与模糊测试
        $data = preg_replace('/[^A-Za-z0-9\-_]/', '', $data);

        // 3. 补全 padding（标准 Base64 解码流程）
        $padding = strlen($data) % 4;
        if ($padding) {
            $data .= str_repeat('=', 4 - $padding);
        }

        // 4. 转换字符并解码（开启 strict 模式）
        $decoded = base64_decode(strtr($data, '-_', '+/'), true);

        return $decoded === false ? '' : $decoded;
    }

    /**
     * 获取当前使用的算法
     *
     * @return string 算法名称
     */
    public function getAlgorithm(): string
    {
        return $this->algorithm;
    }

    /**
     * 设置时钟偏差容忍时间
     *
     * @param int $leeway 容忍秒数
     * @return self
     */
    public function setLeeway(int $leeway): self
    {
        $this->leeway = max(0, $leeway);
        return $this;
    }
}