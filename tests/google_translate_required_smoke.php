<?php

/**
 * GoogleTranslateTrait requires the Google Cloud Translate SDK at runtime.
 */
$packageRoot = dirname(__DIR__);
$autoload = $packageRoot . '/vendor/autoload.php';

if (!is_file($autoload)) {
	$autoload = dirname(__DIR__, 3) . '/autoload.php';
}

require $autoload;

use cinghie\traits\GoogleTranslateTrait;
use Google\Cloud\Translate\TranslateClient;

if (!class_exists(TranslateClient::class, true)) {
	fwrite(STDERR, "FAIL: required Google Cloud Translate SDK is not autoloadable\n");
	exit(1);
}

$host = new class() {
	use GoogleTranslateTrait;
};

if (!method_exists($host, 'getGoogleCloudTranslation')) {
	fwrite(STDERR, "FAIL: GoogleTranslateTrait API is unavailable\n");
	exit(1);
}

echo "OK required Google Cloud Translate runtime dependency\n";
