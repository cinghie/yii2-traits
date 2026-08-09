<?php

/**
 * GoogleTranslateTrait must fail clearly when its optional SDK is absent.
 */
$packageRoot = dirname(__DIR__);
$autoload = $packageRoot . '/vendor/autoload.php';
$yii = $packageRoot . '/vendor/yiisoft/yii2/Yii.php';

if (!is_file($autoload)) {
	$autoload = dirname(__DIR__, 3) . '/autoload.php';
	$yii = dirname(__DIR__, 3) . '/yiisoft/yii2/Yii.php';
}

require $autoload;
require $yii;

use cinghie\traits\GoogleTranslateTrait;
use Google\Cloud\Translate\TranslateClient;

if (class_exists(TranslateClient::class)) {
	echo "SKIP Google Cloud Translate is installed\n";
	exit(0);
}

$host = new class() {
	use GoogleTranslateTrait;
};

try {
	$host->getGoogleCloudTranslation('', 'it', 'Hello');
} catch (\RuntimeException $exception) {
	echo "OK optional Google Cloud Translate guard\n";
	exit(0);
}

fwrite(STDERR, "FAIL: missing Google Cloud Translate did not raise RuntimeException\n");
exit(1);
