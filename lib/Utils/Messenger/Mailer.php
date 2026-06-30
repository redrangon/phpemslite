<?php

namespace PHPEMS\Lib\Utils\Messenger;

use PHPEMS\Lib\Config\Site\Mail;
use PHPEMS\Lib\Rules\Error;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer implements MessengerInterface
{
    static public ?self $instance = null;
    private ?PHPMailer $mailer = null;

    public function __construct()
    {
        //
    }

    private function initMailer(): void
    {
        if(!$this->mailer)
        {
            $mail = new Mail();
            $this->mailer = new PHPMailer(true);
            $this->mailer->isSMTP();
            $this->mailer->Host = $mail->host;
            $this->mailer->SMTPAuth   = true;                             // 开启 SMTP 认证
            $this->mailer->Username   = $mail->username;              // 你的发件邮箱账号
            $this->mailer->Password   = $mail->password;                   // 授权码（非登录密码！）
            $this->mailer->SMTPSecure = $mail->encryption;   // 加密方式 (TLS)
            $this->mailer->Port       = $mail->port;                              // TCP 端口号 (TLS通常为587，SSL通常为465)
            $this->mailer->CharSet    = $mail->charset;
            $this->mailer->isHTML(true);
            $this->mailer->setFrom($mail->username, '系统管理员');
        }
    }

    public function send(string $to, array $message):bool | Error
    {
        try{
            $this->initMailer();
            $this->mailer->addAddress($to, '张三');
            $this->mailer->Subject = $message['subject'];
            $this->mailer->Body = $message['content'];
            $this->mailer->AltBody = $message['content'];
            $this->mailer->send();
            return true;
        }catch (Exception $e){
            return error(['error' => $e->getMessage()]);
        }
    }

    static public function getInstance():static
    {
        if(self::$instance == null)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    static public function sendMessage(string $to, array $message): bool | Error
    {
        return self::getInstance()->send($to, $message);
    }
}