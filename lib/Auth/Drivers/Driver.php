<?php

namespace PHPEMS\Lib\Auth\Drivers;

use PHPEMS\Lib\Session\SessionProvider;

abstract class Driver
{
    protected const SESSION_ROTATE_INTERVAL = 30; // 30分钟
    protected const SESSION_LIFETIME = 60; //2小时
    protected const GRACE_PERIOD = 60; // 宽限期：旧 token 旋转后仍有效的秒数

    public function enforceRotation(): ?string
    {
        SessionProvider::start();

        $lastRotated = SessionProvider::get('_session_rotated_at', 0);
        $now = time();
        if ($now - $lastRotated > self::SESSION_ROTATE_INTERVAL) {
            //SessionProvider::regenerateId(); // 保留数据并删除旧Session
            SessionProvider::set('_session_rotated_at', $now);

            // 同步更新数据库中绑定（关键！）
            $userId = SessionProvider::get('auth_user_id');
            if ($userId) {
                $newSessionId = session_id();
                $newAuthToken = bin2hex(random_bytes(32));

                // 旋转前保存旧 token 到宽限 key（GRACE_PERIOD 秒内旧 token 仍有效）
                $oldAuthToken = $this->getAuthToken($newSessionId);
                if ($oldAuthToken && $oldAuthToken !== $newAuthToken) {
                    $this->saveGraceToken($newSessionId, $oldAuthToken);
                }

                // 重新绑定新 Session ID + 新 authToken
                $this->bind($newSessionId, $userId, $newAuthToken);

                return $newAuthToken; // 返回新 Token
            }
        }
        return null;
    }

    /**
     * 获取宽限期内旧 AuthToken（公开入口）
     */
    public function getGraceAuthToken(string $sessionId): string
    {
        return $this->fetchGraceToken($sessionId);
    }

    /**
     * 保存旧 token 到宽限存储（由各驱动实现）
     */
    abstract protected function saveGraceToken(string $sessionId, string $oldToken): void;

    /**
     * 从宽限存储读取旧 token（由各驱动实现）
     */
    abstract protected function fetchGraceToken(string $sessionId): string;
}