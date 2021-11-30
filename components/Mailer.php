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
	 * @var BaseMailer Default: `Yii::$app->mailer`
	 */
    public $mailerComponent;

	/**
	 * @var string
	 */
    protected $fromName;

	/**
	 * @var string
	 */
    protected $fromEmail;

	/**
	 * @var string
	 */
    protected $toName;

	/**
	 * @var string
	 */
    protected $toEmail;

	/**
	 * @var string
	 */
    protected $subject;

	/**
	 * @var string
	 */
    protected $body;

	/**
	 * Send Email
	 *
	 * @return bool
	 * @throws InvalidConfigException
	 */
    protected function sendEmail()
    {
        $mailer = $this->mailerComponent === null ? Yii::$app->mailer : Yii::$app->get($this->mailerComponent);

        return $mailer->compose()
            ->setTo('giando.olini@gogodigital.it')
            ->setFrom('info@gogodigital.it')
            ->setSubject('Test email')
            ->setTextBody('Lorem ipsum')
            ->send();
    }

    /**
     * Set body variables
     *
     * @return string
     */
    protected function setBodyVariables()
    {
        return '';
    }
}
