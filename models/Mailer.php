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
use yii\helpers\HtmlPurifier;
use yii\validators\EmailValidator;

/**
 * Class Mailer
 */
class Mailer
{
	/** @var string */
	public $emailFrom;

	/** @var string|array */
	public $emailTo;

	/** @var string */
	public $emailSubject;

	/** @var string */
	public $emailBody;

	/** @var string */
	public $emailAttach;

	/** @var bool */
	public $isHtml;

	/** @var string */
	public $debug;

	/**
	 * @param string $from
	 * @param string|array $to
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
		if (!$this->emailFromIsValid()) {
			$results = [
				'status' => 'error',
				'message' => $this->debug,
			];
		} elseif (!$this->emailToIsValid()) {
			$results = [
				'status' => 'error',
				'message' => $this->debug,
			];
		} else {
			$mailer = Yii::$app->mailer;
			$message = $mailer->compose();

			$message->setFrom($this->emailFrom);
			$message->setTo($this->emailTo);
			$message->setSubject(HtmlPurifier::process($this->emailSubject));

			if ($this->isHtml) {
				$message->setHtmlBody($this->emailBody);
			} else {
				$message->setTextBody(HtmlPurifier::process($this->emailBody));
			}

			if ($this->emailAttach) {
				$message->attach($this->emailAttach);
			}

			if ($send = $message->send()) {
				$results = [
					'status' => 'success',
					'message' => Yii::t('traits', 'Email correctly sent to {email}!', [
						'email' => $this->formatRecipientsForMessage(),
					]),
				];
			} else {
				$results = [
					'status' => 'error',
					'message' => $send,
				];
			}
		}

		return $results;
	}

	/**
	 * @param mixed $email
	 * @return bool
	 */
	public function checkEmailIsValid($email)
	{
		if (!is_string($email) || $email === '') {
			$this->debug = Yii::t('traits', 'The email {email} is invalid!', ['email' => (string)$email]);
			return false;
		}

		$validator = new EmailValidator();

		if ($validator->validate($email, $error)) {
			$this->debug = Yii::t('traits', 'The email {email} is valid!', ['email' => $email]);
			return true;
		}

		$this->debug = Yii::t('traits', 'The email {email} is invalid!', ['email' => $email]);
		return false;
	}

	/** @return bool */
	public function emailFromIsValid()
	{
		return $this->checkEmailIsValid($this->emailFrom);
	}

	/**
	 * Validate either a single Yii mail address or every address in a Yii
	 * recipient map (`email@example.com => Display Name`).
	 *
	 * @return bool
	 */
	public function emailToIsValid()
	{
		if (!is_array($this->emailTo)) {
			return $this->checkEmailIsValid($this->emailTo);
		}

		if ($this->emailTo === []) {
			$this->debug = Yii::t('traits', 'The email recipient list is empty.');
			return false;
		}

		foreach ($this->emailTo as $key => $value) {
			$email = is_int($key) ? $value : $key;
			if (!$this->checkEmailIsValid($email)) {
				return false;
			}
		}

		$this->debug = Yii::t('traits', 'The email {email} is valid!', [
			'email' => $this->formatRecipientsForMessage(),
		]);
		return true;
	}

	/** @return string */
	private function formatRecipientsForMessage()
	{
		if (!is_array($this->emailTo)) {
			return (string)$this->emailTo;
		}

		$addresses = [];
		foreach ($this->emailTo as $key => $value) {
			$addresses[] = is_int($key) ? (string)$value : (string)$key;
		}

		return implode(', ', $addresses);
	}
}
