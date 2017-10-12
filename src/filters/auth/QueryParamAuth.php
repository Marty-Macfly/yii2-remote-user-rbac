<?php

namespace macfly\user\client\filters\auth;

use Yii;

class QueryParamAuth extends \yii\filters\auth\QueryParamAuth
{
    use AuthTrait;

    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        $accessToken = $request->get($this->tokenParam);
        if (is_string($accessToken)) {
            return $this->check($accessToken, $user, $request, $response);
        }
    }
}
