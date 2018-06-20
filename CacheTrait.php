<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 1.2.0
 */

namespace cinghie\traits;

use Yii;
use yii\caching\Cache;
use yii\caching\TagDependency;
use yii\data\ArrayDataProvider;
use yii\web\HttpException;

/**
 * Trait CacheTrait
 */
trait CacheTrait
{

    /**
     * @return mixed
     * @throws \yii\base\ViewNotFoundException
     * @throws \yii\base\InvalidCallException
     */
    public function actionCache()
    {
        /** @var $this yii\web\View */
        $dataProvider = new ArrayDataProvider(['allModels'=>$this->findCaches()]);
        return $this->render('cache', ['dataProvider'=>$dataProvider]);
    }

    /**
     * @param $id
     *
     * @return \yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\HttpException
     */
    public function actionFlushCache($id)
    {
        /** @var $this yii\web\Response */
        if ($this->getCache($id)->flush()) {
            Yii::$app->session->setFlash('success', Yii::t('traits', 'Cache has been successfully flushed'));
        }
        return $this->redirect(['cache']);
    }

    /**
     * @param $id
     * @param $key
     *
     * @return \yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\HttpException
     */
    public function actionFlushCacheKey($id, $key)
    {
        /** @var $this yii\web\Response */
        if ($this->getCache($id)->delete($key)) {
            Yii::$app->session->setFlash('success', Yii::t('traits', 'Cache entry has been successfully deleted'));
        }
        return $this->redirect(['cache']);
    }

    /**
     * @param $id
     * @param $tag
     *
     * @return \yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\HttpException
     */
    public function actionFlushCacheTag($id, $tag)
    {
        /** @var $this yii\web\Response */
        TagDependency::invalidate($this->getCache($id), $tag);
        Yii::$app->session->setFlash('success', Yii::t('traits', 'TagDependency was invalidated'));
        return $this->redirect(['cache']);
    }

    /**
     * @param $id
     *
     * @return \yii\caching\Cache|null
     * @throws \yii\base\InvalidConfigException
     * @throws HttpException
     */
    protected function getCache($id)
    {
        if (!array_key_exists($id, $this->findCaches())) {
            throw new HttpException(400, 'Given cache name is not a name of cache component');
        }
        return Yii::$app->get($id);
    }

    /**
     * Returns array of caches in the system, keys are cache components names, values are class names.
     *
     * @param array $cachesNames caches to be found
     *
     * @return array
     */
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
                $caches[$name] = ['name'=>$name, 'class'=>get_class($component)];
            } elseif (is_array($component) && isset($component['class']) && $this->isCacheClass($component['class'])) {
                $caches[$name] = ['name'=>$name, 'class'=>$component['class']];
            } elseif (is_string($component) && $this->isCacheClass($component)) {
                $caches[$name] = ['name'=>$name, 'class'=>$component];
            }
        }
        return $caches;
    }

    /**
     * Checks if given class is a Cache class.
     *
     * @param string $className class name.
     *
     * @return boolean
     */
    private function isCacheClass($className)
    {
        return is_subclass_of($className, Cache::class);
    }

}
