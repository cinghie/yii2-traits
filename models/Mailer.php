<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-commerce
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-commerce
 * @version 0.0.1
 */

namespace cinghie\traits\models;

use Yii;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\validators\EmailValidator;

/**
 * Class Mailer
 */
class Mailer
{
	/**
	 * @var string
	 */
	public $emailFrom;

	/**
	 * @var string || array
	 */
	public $emailTo;

	/**
	 * @var string
	 */
	public $emailSubject;

	/**
	 * @var string
	 */
	public $emailBody;

	/**
	 * @var string
	 */
	public $emailAttach;


	/**
	 * @var string
	 */
	public $isHtml;

	/**
	 * @var string
	 */
	public $debug;

	/**
	 * Constructor
	 *
	 * @param string $from
	 * @param string $to
	 * @param string $subject
	 * @param string $body
	 * @param string $attachPath
	 * @param bool $isHtml
	 */
	public function __construct($from, $to, $subject, $body, $attachPath = '', $isHtml = true)
	{
		$this->emailFrom = $from;
		$this->emailTo = $to;
		$this->emailSubject = $subject;
		$this->emailBody = $body;
		$this->emailAttach = $attachPath;
		$this->isHtml = $isHtml;
		$this->debug = '';
	}

	/**
	 * Send Email
	 *
	 * @return array
	 */
	public function sendMail()
	{
		if(!$this->emailFromIsValid()) {

			$results = [
				'status' => 'error',
				'message' => $this->debug,
			];

		} elseif (!$this->emailFromIsValid()) {

			$results = [
				'status' => 'error',
				'message' => $this->debug,
			];

		} else {

			$mailer = Yii::$app->mailer;
			$message = $mailer->compose();

			// Set Email From
			$message->setFrom($this->emailFrom);

			// Set Email To
			$message->setTo($this->emailTo);

			// Set Email Subject
			$message->setSubject(HtmlPurifier::process($this->emailSubject));

			// Set Email Body
			if($this->isHtml) {
				$message->setHtmlBody($this->emailBody);
			} else {
				$message->setTextBody(HtmlPurifier::process($this->emailBody));
			}

			if($this->emailAttach) {
				$message->attach($this->emailAttach);
			}

			if ($send = $message->send())
			{
				$results = [
					'status' => 'success',
				    'message' => Yii::t( 'traits','Email correctly sent to {email}!', ['email' => $this->emailTo] )
				];

			} else {

				$results = [
					'status' => 'error',
					'message' => $send
				];
			}
		}

		return $results;
	}

	/**
	 * Check Email is valid
	 *
	 * @param $email
	 *
	 * @return bool
	 */
	public function checkEmailIsValid($email)
	{
		$validator = new EmailValidator();

		if($validator->validate($email, $error)) {
			$this->debug = Yii::t('traits','The email {email} is valid!', ['email' => $email]);
			return true;
		} else {
			$this->debug = Yii::t('traits','The email {email} is invalid!', ['email' => $email]);
			return false;
		}
	}

	/**
	 * @return bool
	 */
	public function emailFromIsValid()
	{
		return $this->checkEmailIsValid($this->emailFrom);
	}

	/**
	 * @return bool
	 */
	public function emailToIsValid()
	{
		return $this->checkEmailIsValid($this->emailTo);
	}
}
