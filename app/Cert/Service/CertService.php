<?php

namespace PHPEMS\App\Cert\Service;

use PHPEMS\App\Cert\Service\Model\Cert;
use PHPEMS\App\Cert\Service\Model\CertMember;
use PHPEMS\Lib\DataBase\QueryBuilder;

class CertService
{
    public static function getCertQueryWithMember(): QueryBuilder
    {
        $memberTable = CertMember::getTableName();
        $certKey = Cert::getPrimaryKey();
        return Cert::getQuery()->join($memberTable, 'cemceid', '=', $certKey);
    }
}