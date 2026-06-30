<?php
// Core/Session/SessionDriverInterface.php

namespace PHPEMS\Lib\Session;

interface SessionDriverInterface
{
    public function start(): void;
    public function get(string $key, $default = null);
    public function set(string $key, $value): void;
    public function delete(string $key): void;
    public function clear(): void;
    public function regenerateId(): void;
    public function destroy(): void;
}