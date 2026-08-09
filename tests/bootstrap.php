<?php

$packageRoot = dirname(__DIR__);
$autoload = $packageRoot . '/vendor/autoload.php';
$yii = $packageRoot . '/vendor/yiisoft/yii2/Yii.php';

if (!is_file($autoload)) {
	$autoload = dirname(__DIR__, 3) . '/autoload.php';
	$yii = dirname(__DIR__, 3) . '/yiisoft/yii2/Yii.php';
}

require_once $autoload;
require_once $yii;
