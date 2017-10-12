<?php

namespace macfly\user\client\filters\auth;

use Yii;

class HttpBearerAuth extends \yii\filters\auth\HttpBearerAuth
{
    use AuthTrait;

    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        $authHeader = $request->getHeaders()->get('Authorization');
        if ($authHeader !== null && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
            return $this->check($matches[1], $request, $response);
        }
    }
}
