<?php

namespace macfly\user\client\filters\auth;

use Yii;
use macfly\user\client\Module;

trait AuthTrait
{
    /**
     * @inheritdoc
     */
    protected function check($token, $user, $request, $response)
    {
        $module     = Module::getInstance();
        $module->setToken($token);
        $identity   = $user->loginByAccessToken($token, get_class($this));

        if ($identity === null) {
            $this->handleFailure($response);
        }
        return $identity;
    }
}
