<?php

namespace PHPEMS\Lib\Auth\Drivers;

use PHPEMS\Lib\DataBase\QueryBuilder;
use PHPEMS\Lib\Utils\Env;

class MySQLDriver extends Driver implements DriverInterface
{
    protected $dbName = 'mysql.default';
    protected $dbTable = 'session';
    private static $instance;

    private function __construct(?string $dbName = null)
    {
        if($dbName??false){
            $this->dbName = $dbName;
        }
    }

    private function getQuery(): QueryBuilder
    {
        return new QueryBuilder(DI($this->dbName), $this->dbTable);
    }

    public static function getInstance(?string $dbName = null): self
    {
        if (!self::$instance) {
            self::$instance = new self($dbName);
        }
        return self::$instance;
    }

    public function bind(string $sessionId, int $userId, string $authToken)
    {
        $session = $this->getQuery()->where('sessionuserid', $userId)->first();
        if($session['serialid']??false)
        {
            $this->getQuery()->where('serialid',$session['serialid'])
                ->update([
                    'sessionid' => $sessionId,
                    'sessionauthtoken' => $authToken,
                    'sessionsalt' => $session['sessionsalt']?:bin2hex(random_bytes(32)),
                ]);
        }
        else
        {
            $this->getQuery()->insert([
                'sessionid' => $sessionId,
                'sessionuserid' => $userId,
                'sessionauthtoken' => $authToken,
                'sessionip' => Env::getClientIp(),
                'sessionsalt' => bin2hex(random_bytes(32)),
                'sessioncurrent' => '',
                'sessionlogintime' => TIME,
                'sessionlasttime' => TIME
            ]);
        }
    }

    public function forget(string $sessionId, int $userId)
    {
        $this->getQuery()->where('sessionid', $sessionId)
            ->where('sessionuserid', $userId)
            ->delete();
    }

    public function getAuthToken(string $sessionId): string
    {
        $result = $this->getQuery()->where('sessionid', $sessionId)->first();
        return $result['sessionauthtoken'] ?? '';
    }

    public function getAuthSalt(string $sessionId): string
    {
        $result = $this->getQuery()->where('sessionid', $sessionId)->first();
        return $result['sessionsalt'] ?? '';
    }
}