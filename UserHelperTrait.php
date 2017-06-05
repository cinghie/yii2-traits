<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 0.1.0
 */

namespace cinghie\traits;

use dektrium\user\models\User;

trait UserHelperTrait
{

    /**
     * Get the user_id By user email
     *
     * @param $email
     * @return integer
     */
    public function getUserIdByEmail($email)
    {
        $user = User::find()
            ->select(['*'])
            ->where(['email' => $email])
            ->one();

        return $user['id'];
    }

    /**
     * Return User's array with current User on first position [ 'user_id' => 'username' ]
     *
     * @param $user_id
     * @param $username
     * @return array
     */
    public function getUsersSelect2($user_id,$username)
    {
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

    /**
     * Return an array with the User's Roles
     *
     * @return array
     */
    public function getRoles()
    {
        $roles = \Yii::$app->authManager->getRoles();
        $array = ['public' => 'Public'];

        foreach($roles as $role) {
            $array[ucwords($role->name)] = ucwords($role->name);
        }

        return $array;
    }

}
