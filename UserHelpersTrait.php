<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 1.1.1
 */

namespace cinghie\traits;

use Yii;
use dektrium\user\models\User;

/**
 * Trait UserHelperTrait
 */
trait UserHelpersTrait
{

	/**
	 * Get the User by user email
	 *
	 * @return User[] array
	 * @internal param string $email
	 */
	public function getUserByEmail()
	{
		$user = Yii::$app->user->identityClass::find()
			->select(['*'])
		    ->where(['email' => $this->email])
		    ->one();

		return $user;
	}

	/**
	 * Get current User Profile object or fied if on param
	 *
	 * @param string $field
	 *
	 * @return \dektrium\user\models\Profile || string || int
	 */
	public function getCurentUserProfile($field = '')
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
     * @internal param User $currentUser
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
     * @param integer $user_id
     * @param string $username
     *
     * @return array
     */
    public function getUsersSelect2($user_id = 0, $username = '')
    {
        if(!$user_id || !$username) {
            $user_id = Yii::$app->user->identity->id;
            $username = Yii::$app->user->identity->username;
        }

        $users = Yii::$app->user->identityClass::find()
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
