<?php

namespace PHPEMS\Lib\Auth\Drivers;

use PHPEMS\Lib\Session\SessionProvider;

abstract class Driver
{
    protected const SESSION_ROTATE_INTERVAL = 1800; // 30分钟
    protected const SESSION_LIFETIME = 7200; //2小时

    public function enforceRotation(): ?string
    {
        SessionProvider::start();

        $lastRotated = SessionProvider::get('_session_rotated_at', 0);
        $now = time();
        if ($now - $lastRotated > self::SESSION_ROTATE_INTERVAL) {
            SessionProvider::regenerateId(); // 保留数据并删除旧Session
            SessionProvider::set('_session_rotated_at', $now);

            // 同步更新数据库中绑定（关键！）
            $userId = SessionProvider::get('auth_user_id');
            if ($userId) {
                $newSessionId = session_id();
                $newAuthToken = bin2hex(random_bytes(32));

                // 重新绑定新 Session ID + 新 authToken
                $this->bind($newSessionId, $userId, $newAuthToken);

                return $newAuthToken; // 返回新 Token
            }
        }
        return null;
    }
}