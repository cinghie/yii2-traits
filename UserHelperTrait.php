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

}
