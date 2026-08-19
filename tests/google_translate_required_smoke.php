<?php

/**
 * GoogleTranslateTrait requires the Google Cloud Translate V3 SDK at runtime.
 */
$packageRoot = dirname(__DIR__);
$autoload = $packageRoot . '/vendor/autoload.php';

if (!is_file($autoload)) {
	$autoload = dirname(__DIR__, 3) . '/autoload.php';
}

require $autoload;

use cinghie\traits\GoogleTranslateTrait;
use Google\Cloud\Translate\V3\Client\TranslationServiceClient;
use Google\Cloud\Translate\V3\TranslateTextRequest;

if (!class_exists(TranslationServiceClient::class, true) || !class_exists(TranslateTextRequest::class, true)) {
	fwrite(STDERR, "FAIL: required Google Cloud Translate V3 SDK is not autoloadable\n");
	exit(1);
}

$host = new class() {
	use GoogleTranslateTrait;
};

if (!method_exists($host, 'getGoogleCloudTranslation')) {
	fwrite(STDERR, "FAIL: GoogleTranslateTrait API is unavailable\n");
	exit(1);
}

echo "OK required Google Cloud Translate V3 runtime dependency\n";
