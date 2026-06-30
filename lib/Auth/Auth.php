<?php

namespace PHPEMS\Lib\Auth;

use PHPEMS\Lib\Session\SessionProvider;

class Auth
{
    /**
     * 安全登录：生成 AuthToken + 绑定 Redis + 踢旧会话
     */
    public static function login(array $user): string
    {
        // 1. 再生 Session ID（防固定）
        SessionProvider::regenerateId();
        $sessionId = session_id();

        // 2. 生成高强度 AuthToken
        $authToken = bin2hex(random_bytes(32));

        $userId = $user['userid'];

        // 3. 写入 Session（用于内部校验）
        SessionProvider::set('auth_user_id', $userId);
        SessionProvider::set('_session_rotated_at', time());

        DataProvider::Create()->forget($sessionId, $userId);
        DataProvider::Create()->bind($sessionId, $userId, $authToken);

        return $authToken; // 返回给前端
    }

    public static function check(): bool
    {
        return SessionProvider::has('auth_user_id');
    }

    public static function id(): ?int
    {
        return self::check() ? SessionProvider::get('auth_user_id') : null;
    }

    public static function logout(): void
    {
        $userId = self::id();
        $sessionId = session_id();

        if ($userId) {
            DataProvider::Create()->forget($sessionId, $userId);
        }

        SessionProvider::delete('auth_user_id');
        SessionProvider::delete('_session_rotated_at');
    }

    /**
     * 验证请求中的 AuthToken 是否合法
     */
    public static function validateRequestToken(string $providedToken): bool
    {
        if (!self::check()) return false;

        $sessionId = session_id();
        file_put_contents(PEPATH.'/storage/logs/lsession.txt', "sessionId:".$sessionId.";token:{$providedToken};time:".date('Y-m-d H:i:s')."\n", FILE_APPEND);
        $expectedToken = DataProvider::Create()->getAuthToken($sessionId);

        return hash_equals($expectedToken ?: '', $providedToken);
    }

    public static function enforceRotation()
    {
        return DataProvider::Create()->enforceRotation();
    }

    public static function getSessionSalt(): string
    {
        $sessionId = session_id();
        return DataProvider::Create()->getAuthSalt($sessionId);
    }
}