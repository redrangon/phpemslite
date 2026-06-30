<?php

namespace PHPEMS\App\User\Service\Model;

use PHPEMS\Lib\Rules\Model;

class User extends Model
{
    protected static $dbTable = 'user'; // 表名
    protected static $primaryKeys = ['userid']; // 主键名

    /**
     * 默认值（不含主键字段）
     * 字符串类型默认值为空字符串 ""
     * 整数类型默认值为 0
     * 可空字段默认值为 null
     */
    protected static $defaultValues = [
        'useropenid' => "",
        'userunionid' => "",
        'username' => "",
        'userpassport' => "",
        'useremail' => "",
        'userpassword' => "",
        'userphoto' => "",
        'usercoin' => 0,
        'userregip' => "",
        'userregtime' => 0,
        'userlogtime' => 0,
        'userverifytime' => 0,
        'usergroupid' => 0,
        'manager_apps' => "",
        'usertruename' => "",
        'teacher_subjects' => "",
        'userprofile' => "",
        'usergender' => "",
        'userphone' => "",
        'useraddress' => "",
        'userstatus' => 0,
    ];

    /**
     * 字段校验规则（不含主键字段）
     * required: 必填（NOT NULL 且无默认值的字段）
     * int: 整数类型
     * float: 浮点数类型
     * string: 字符串类型
     * date: 日期类型
     */
    protected static $rules = [
        'useropenid' => 'string',
        'userunionid' => 'string',
        'username' => 'required|string',
        'userpassport' => 'string',
        'useremail' => 'string',
        'userphone' => 'string',
        'userpassword' => 'string',
        'userphoto' => 'string',
        'usercoin' => 'integer',
        'userregip' => 'string',
        'userregtime' => 'integer',
        'userlogtime' => 'integer',
        'userverifytime' => 'integer',
        'usergroupid' => 'integer',
        'usertruename' => 'string',
        'userprofile' => 'string',
        'usergender' => 'string',
        'useraddress' => 'string',
        'userstatus' => 'integer',
    ];

    /**
     * 字段校验规则失败后的提示信息
     */
    protected static $ruleMessages = [
        'username' => '请填写用户名',
    ];

    public static function findByUserName(string $userName): ?static
    {
        $result = static::getQuery()->where('username',$userName)->first();
        return new static($result??[]);
    }

    public static function findByUserEmail(string $email): ?static
    {
        $result = static::getQuery()->where('useremail',$email)->first();
        return new static($result??[]);
    }

    public function validatePassword(string $password): bool
    {
        return password_verify($password,$this->userpassword);
    }

    public function isAdmin(): int
    {
        return $this->usergroupid == 1?1:0;
    }

    public function isTeacher(): int
    {
        return $this->usergroupid == 2;
    }

    public function isVerify(): bool
    {
        return $this->userstatus == 3;
    }

    public static function saltPassword($password):string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function generateUniqueId(): string {
        $millisecond = (int)(hrtime(true) / 1000000);
        $time = str_pad(dechex($millisecond), 10, '0', STR_PAD_LEFT);
        $random = bin2hex(random_bytes(7));
        return $time . $random;
    }
}
