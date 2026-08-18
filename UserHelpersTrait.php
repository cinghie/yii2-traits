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
use cinghie\userextended\models\Profile;
use cinghie\userextended\models\User;
use yii\helpers\Url;
use yii\web\IdentityInterface;

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
		if($field) {
			return Yii::$app->user->identity->$field;
		}

		return Yii::$app->user->identity;
	}

	public function getCurrentUserProfile($field = '')
	{
		if($field) {
			return Yii::$app->user->identity->profile->$field;
		}

		return Yii::$app->user->identity->profile;
	}

    public function getCurrentUserSelect2()
    {
        /** @var User $currentUser */
        $currentUser = Yii::$app->user->identity;

        return [$currentUser->id => $currentUser->username];
    }

    public function getRolesSelect2()
    {
    	$array = ['public' => 'public'];
        $roles = Yii::$app->authManager->getRoles();

        foreach($roles as $role) {
            $role_name = $role->name;
            $array[$role_name] = $role_name;
        }

        if(isset($array['public']) && $this->isNewRecord) {
	        $array = array_merge(array('public' => $array['public']), $array);
        }

        return $array;
    }

    public function getUsersSelect2($user_id = 0, $username = '')
    {
        if(!$user_id || !$username) {
            $user_id = Yii::$app->user->identity->id;
            $username = Yii::$app->user->identity->username;
        }

        $users = User::find()
            ->select(['id','username'])
            ->where(['blocked_at' => null, 'unconfirmed_email' => null])
            ->andWhere(['!=', 'id', $user_id])
            ->orderBy('username ASC')
            ->all();

        $array = [$user_id => ucwords($username)];

        foreach($users as $user) {
            $array[$user['id']] = ucwords($user['username']);
        }

        return $array;
    }
}
