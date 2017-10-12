<?php

namespace macfly\user\client\filters\auth;

use Yii;

class HttpBasicAuth extends \yii\filters\auth\HttpBasicAuth
{
    use AuthTrait;

    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        $username = $request->getAuthUser();

        if ($this->auth) {
            return parent::authenticate($user, $request, $response);
        } elseif ($username !== null) {
            return $this->check($username, $user, $request, $response);
        }

        return null;
    }
}
