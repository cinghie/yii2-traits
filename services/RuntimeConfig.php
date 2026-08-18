<?php

namespace cinghie\traits\services;

use Yii;

/**
 * Resolve optional runtime configuration without coupling model traits directly
 * to the active controller/module.
 *
 * Resolution order:
 * 1. host getTraitsConfig() array
 * 2. Yii::$app->params['yii2Traits'] array
 * 3. legacy current-module property (backwards compatibility)
 * 4. supplied default
 */
final class RuntimeConfig
{
    public static function get($host, $key, $default = null, $legacyModuleKey = null)
    {
        if (is_object($host) && method_exists($host, 'getTraitsConfig')) {
            $config = $host->getTraitsConfig();
            if (is_array($config) && array_key_exists($key, $config)) {
                return $config[$key];
            }
        }

        if (Yii::$app !== null && isset(Yii::$app->params['yii2Traits']) && is_array(Yii::$app->params['yii2Traits'])) {
            $config = Yii::$app->params['yii2Traits'];
            if (array_key_exists($key, $config)) {
                return $config[$key];
            }
        }

        $legacyModuleKey = $legacyModuleKey ?: $key;
        if (Yii::$app !== null && isset(Yii::$app->controller) && Yii::$app->controller !== null) {
            $controller = Yii::$app->controller;
            if (isset($controller->module) && $controller->module !== null && isset($controller->module->$legacyModuleKey)) {
                return $controller->module->$legacyModuleKey;
            }
        }

        return $default;
    }
}
