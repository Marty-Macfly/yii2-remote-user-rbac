<?php

namespace macfly\user;

use Yii;
use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
	/** @var array Model's map */
	private $_modelMap = [
		'User'				=> 'macfly\user\models\User',
		'AuthManager'	=> 'macfly\user\models\RbacManager',
	];

	/** @inheritdoc */
	public function bootstrap($app)
	{
		/** @var Module $module */
		/** @var \yii\db\ActiveRecord $modelName */
		if($app->hasModule('user') && ($module = $app->getModule('user')) instanceof Module)
		{
			$this->_modelMap = array_merge($this->_modelMap, $module->modelMap);
			foreach ($this->_modelMap as $name => $definition)
			{
				$class										= "macfly\\user\\models\\" . $name;

				Yii::$container->set($class, $definition);

				$modelName								= is_array($definition) ? $definition['class'] : $definition;
				$module->modelMap[$name]	= $modelName;
			}			

			if ($app instanceof ConsoleApplication) {
				$module->controllerNamespace = 'macfly\user\commands';
			} else
			{
				$user	= [
						'loginUrl'	=> ['/user/security/auth', 'authclient' => $module->authclient],
					];

				if(!is_null($module->identityUrl))
				{
					$user['identityClass']	= $module->modelMap['User'];
				}

				Yii::$container->set('yii\web\User', $user);

				if(!is_null($module->rbacUrl))
				{
					Yii::$container->set('authManager', $module->modelMap['AuthManager']);
				}

				if (!$app->has('authClientCollection'))
				{
					$app->set('authClientCollection', [
						'class'	=> Collection::className(),
					]);
				}
			}
		}
	}
}
