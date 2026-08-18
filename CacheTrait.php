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

namespace cinghie\traits;

use Yii;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\base\ViewNotFoundException;
use yii\caching\Cache;
use yii\caching\TagDependency;
use yii\data\ArrayDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\Request;
use yii\web\Response;

/**
 * Trait CacheTrait
 */
trait CacheTrait
{
    public function actionCache()
    {
        $dataProvider = new ArrayDataProvider(['allModels' => $this->findCaches()]);
        return $this->render('cache', ['dataProvider' => $dataProvider]);
    }

    /**
     * RBAC permission required by destructive cache actions.
     * Override this method in the host controller when a project uses a
     * different permission name.
     *
     * @return string
     */
    protected function getCacheManagementPermission()
    {
        return 'cache-manage';
    }

    /**
     * Require POST, explicit CSRF validation and RBAC authorization before
     * mutating cache. CSRF is validated directly on the web request so these
     * actions remain protected even if the host controller disables its own
     * automatic CSRF check.
     *
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws MethodNotAllowedHttpException
     */
    protected function ensureCacheMutationAllowed()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) {
            throw new MethodNotAllowedHttpException('Cache mutations require a POST request.');
        }

        if ($request instanceof Request) {
            if (!$request->enableCsrfValidation || !$request->validateCsrfToken()) {
                throw new BadRequestHttpException('Unable to verify your data submission.');
            }
        }

        $user = Yii::$app->user;
        $permission = $this->getCacheManagementPermission();
        if ($user->isGuest || !$permission || !$user->can($permission)) {
            throw new ForbiddenHttpException('You are not allowed to modify application cache.');
        }
    }

    public function actionFlushCache($id)
    {
        $this->ensureCacheMutationAllowed();

        if ($this->getCache($id)->flush()) {
            Yii::$app->session->setFlash('success', Yii::t('traits', 'Cache has been successfully flushed'));
        }
        return $this->redirect(['cache']);
    }

    public function actionFlushCacheKey($id, $key)
    {
        $this->ensureCacheMutationAllowed();

        if ($this->getCache($id)->delete($key)) {
            Yii::$app->session->setFlash('success', Yii::t('traits', 'Cache entry has been successfully deleted'));
        }
        return $this->redirect(['cache']);
    }

    public function actionFlushCacheTag($id, $tag)
    {
        $this->ensureCacheMutationAllowed();

        TagDependency::invalidate($this->getCache($id), $tag);
        Yii::$app->session->setFlash('success', Yii::t('traits', 'TagDependency was invalidated'));
        return $this->redirect(['cache']);
    }

    protected function getCache($id)
    {
        if (!array_key_exists($id, $this->findCaches())) {
            throw new HttpException(400, 'Given cache name is not a name of cache component');
        }
        return Yii::$app->get($id);
    }

    private function findCaches(array $cachesNames = [])
    {
        $caches = [];
        $components = Yii::$app->getComponents();
        $findAll = ($cachesNames === []);
        foreach ($components as $name => $component) {
            if (!$findAll && !in_array($name, $cachesNames)) {
                continue;
            }
            if ($component instanceof Cache) {
                $caches[$name] = ['name' => $name, 'class' => get_class($component)];
            } elseif (is_array($component) && isset($component['class']) && $this->isCacheClass($component['class'])) {
                $caches[$name] = ['name' => $name, 'class' => $component['class']];
            } elseif (is_string($component) && $this->isCacheClass($component)) {
                $caches[$name] = ['name' => $name, 'class' => $component];
            }
        }
        return $caches;
    }

    private function isCacheClass($className)
    {
        return is_subclass_of($className, Cache::class);
    }
}
