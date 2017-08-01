<?php

namespace macfly\user\client\filters\auth;

use macfly\user\client\Module;

use Yii;

class HttpBearerAuth extends \yii\filters\auth\HttpBearerAuth
{
	/**
	 * @inheritdoc
	 */
	public function authenticate($user, $request, $response)
	{
		$authHeader = $request->getHeaders()->get('Authorization');
		if ($authHeader !== null && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches))
		{
			$module     = Module::getInstance();
			$module->setToken($matches[1]);
			$identity   = $user->loginByAccessToken($matches[1], get_class($this));

			if ($identity === null)
			{
				$this->handleFailure($response);
			}
			return $identity;
		}
	}
}
