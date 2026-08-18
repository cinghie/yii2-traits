<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 1.2.3
 */

namespace cinghie\traits\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\mail\BaseMailer;

class Mailer extends Component
{
    /**
     * @var string|null Yii mailer component id. Null uses Yii::$app->mailer.
     */
    public $mailerComponent;

    /** @var string */
    protected $fromName = '';

    /** @var string */
    protected $fromEmail = '';

    /** @var string */
    protected $toName = '';

    /** @var string */
    protected $toEmail = '';

    /** @var string */
    protected $subject = '';

    /** @var string */
    protected $body = '';

    public function setFromName($value)
    {
        $this->fromName = (string)$value;
    }

    public function setFromEmail($value)
    {
        $this->fromEmail = (string)$value;
    }

    public function setToName($value)
    {
        $this->toName = (string)$value;
    }

    public function setToEmail($value)
    {
        $this->toEmail = (string)$value;
    }

    public function setSubject($value)
    {
        $this->subject = (string)$value;
    }

    public function setBody($value)
    {
        $this->body = (string)$value;
    }

    /**
     * Send the configured email.
     *
     * @return bool
     * @throws InvalidConfigException
     */
    protected function sendEmail()
    {
        if ($this->fromEmail === '' || $this->toEmail === '') {
            throw new InvalidConfigException('Both fromEmail and toEmail must be configured before sending mail.');
        }

        /** @var BaseMailer $mailer */
        $mailer = $this->mailerComponent === null
            ? Yii::$app->mailer
            : Yii::$app->get($this->mailerComponent);

        $from = $this->fromName !== '' ? [$this->fromEmail => $this->fromName] : $this->fromEmail;
        $to = $this->toName !== '' ? [$this->toEmail => $this->toName] : $this->toEmail;

        return $mailer->compose()
            ->setTo($to)
            ->setFrom($from)
            ->setSubject($this->subject)
            ->setTextBody($this->body)
            ->send();
    }

    /**
     * Hook for subclasses that need to derive body variables.
     *
     * @return string
     */
    protected function setBodyVariables()
    {
        return '';
    }
}
