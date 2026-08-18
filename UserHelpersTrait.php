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
use cinghie\userextended\models\User;
use yii\helpers\Url;

/**
 * Trait UserHelperTrait
 */
trait UserHelpersTrait
{
    public function getUserAdminUrl($user_id)
    {
        return Url::to(['/user/admin/update', 'id' => $user_id]);
    }

    public function getUserProfileUrl($user_id)
    {
        return Url::to(['/user/profile/show', 'id' => $user_id]);
    }

    public function getUserByEmail()
    {
        return User::find()
            ->select(['*'])
            ->where(['email' => $this->email])
            ->one();
    }

    public function getCurrentUser($field = '')
    {
        $identity = Yii::$app->user->identity;
        if ($identity === null) {
            return null;
        }

        return $field ? $identity->$field : $identity;
    }

    public function getCurrentUserProfile($field = '')
    {
        $identity = Yii::$app->user->identity;
        if ($identity === null || $identity->profile === null) {
            return null;
        }

        return $field ? $identity->profile->$field : $identity->profile;
    }

    public function getCurrentUserSelect2()
    {
        /** @var User|null $currentUser */
        $currentUser = Yii::$app->user->identity;
        if ($currentUser === null) {
            return [];
        }

        return [$currentUser->id => $currentUser->username];
    }

    public function getRolesSelect2()
    {
        $array = ['public' => 'public'];
        $roles = Yii::$app->authManager->getRoles();

        foreach ($roles as $role) {
            $roleName = $role->name;
            $array[$roleName] = $roleName;
        }

        if (isset($array['public']) && $this->isNewRecord) {
            $array = array_merge(['public' => $array['public']], $array);
        }

        return $array;
    }

    public function getUsersSelect2($user_id = 0, $username = '')
    {
        $identity = Yii::$app->user->identity;
        if ((!$user_id || !$username) && $identity !== null) {
            $user_id = $identity->id;
            $username = $identity->username;
        }

        $query = User::find()
            ->select(['id', 'username'])
            ->where(['blocked_at' => null, 'unconfirmed_email' => null]);

        if ($user_id) {
            $query->andWhere(['!=', 'id', $user_id]);
        }

        $users = $query->orderBy('username ASC')->all();
        $array = [];

        if ($user_id && $username !== '') {
            $array[$user_id] = ucwords($username);
        }

        foreach ($users as $user) {
            $array[$user['id']] = ucwords($user['username']);
        }

        return $array;
    }
}
