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
        list($username, $password) = $request->getAuthCredentials();

        if ($username !== null) {
            return $this->check($username, $request, $response);
        }

        return null;
    }
}
