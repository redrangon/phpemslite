<?php

namespace PHPEMS\Lib\Auth\Drivers;

interface DriverInterface
{
    public function forget(string $sessionId, int $userId);
    public function bind(string $sessionId, int $userId, string $authToken);
    public function getAuthToken(string $sessionId): string;
    public function enforceRotation();
}