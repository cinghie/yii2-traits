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
use dektrium\user\models\Profile;
use dektrium\user\models\User;
use yii\web\IdentityInterface;

/**
 * Trait UserHelperTrait
 */
trait UserHelpersTrait
{

	/**
	 * Get the User by user email
	 *
	 * @return User[] array
	 */
	public function getUserByEmail()
	{
		$user = User::find()
			->select(['*'])
		    ->where(['email' => $this->email])
		    ->one();

		return $user;
	}

	/**
	 * Get current User or Current User field
	 *
	 * @param string $field
	 *
	 * @return IdentityInterface | string | int
	 */
	public function getCurrentUser($field = '')
	{
		if($field) {
			return Yii::$app->user->identity->$field;
		}

		return Yii::$app->user->identity;
	}

	/**
	 * Get current User Profile object or fied if on param
	 *
	 * @param string $field
	 *
	 * @return Profile | string | int
	 */
	public function getCurrentUserProfile($field = '')
	{
		if($field) {
			return Yii::$app->user->identity->profile->$field;
		}

		return Yii::$app->user->identity->profile;
	}

    /**
     * Return an array with current User
     *
     * @return array
     */
    public function getCurrentUserSelect2()
    {
        /** @var User $currentUser */
        $currentUser = Yii::$app->user->identity;

        return [$currentUser->id => $currentUser->username];
    }

    /**
     * Return an array with the User's Roles adding "Public" on first position
     *
     * @return array
     */
    public function getRolesSelect2()
    {
    	$array = [];
        $roles = Yii::$app->authManager->getRoles();

        foreach($roles as $role) {
            $role_name = $role->name;
            $array[$role_name] = $role_name;
        }

        if($this->isNewRecord) {
	        $array = array_merge(array('public'=>$array['public']), $array);
        }

        return $array;
    }

    /**
     * Return array with all Users (not blocked or not unconfirmed), adding current User on first position [ 'user_id' => 'username' ]
     *
     * @param int $user_id
     * @param string $username
     *
     * @return array
     */
    public function getUsersSelect2($user_id = 0, $username = '')
    {
        if(!$user_id | !$username) {
            $user_id = Yii::$app->user->identity->id;
            $username = Yii::$app->user->identity->username;
        }

        $users = User::find()
            ->select(['id','username'])
            ->where(['blocked_at' => null, 'unconfirmed_email' => null])
            ->andWhere(['!=', 'id', $user_id])
            ->all();

        $array[$user_id] = ucwords($username);

        foreach($users as $user) {
            $array[$user['id']] = ucwords($user['username']);
        }

        return $array;
    }

}
