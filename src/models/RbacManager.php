<?php

namespace macfly\user\models;

use yii\rbac\CheckAccessInterface;
use macfly\user\Module;

class RbacManager implements CheckAccessInterface
{
	public function checkAccess($userId, $permissionName, $params = [])
	{
		if(!\Yii::$app->hasModule('user'))
		{
			 throw new NotSupportedException('Module with id "user" not loaded');
		}

		$module	= \Yii::$app->getModule('user');
    $rs			= $module->createRequest()
								->setMethod('PUT')
								->setUrl(sprintf('%s?id=%d', $module->url_rbac, $userId))
								->setData(['permission' => $permissionName, 'params' => $params])
								->addHeaders([
									'Authorization' => 'Basic '. base64_encode(is_null($module->key) ?
										sprintf("%s:%s", $module->login, $module->password) : sprintf("%s:", $module->key))])
								->send();

		return $rs->data;
	}
}
