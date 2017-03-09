<?php

namespace macfly\user\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\authclient\AuthAction;

use macfly\user\models\User;

class SecurityController extends Controller
{
  /**
   * @inheritdoc
   */
  public function behaviors()
  {
    return [
      'access' => [
        'class' => AccessControl::className(),
        'only' => ['logout'],
        'rules' => [
          [
            'actions' => ['logout'],
            'allow' => true,
            'roles' => ['@'],
          ],
        ],
      ],
      'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
					'logout' => ['post'],
        ],
      ],
    ];
  }

  public function actions()
  {
      return [
        'auth' => [
						'class' => AuthAction::className(),
            'successCallback' => [$this, 'login'],
        ],
      ];
  }

  public function login($client)
  {
    $attributes = $client->getUserAttributes();

		Yii::$app->cache->set($attributes['id'], $attributes, $this->module->rememberFor);

		return Yii::$app->user->login(new User($attributes), 0);
  }

  /**
   * Logout action.
   *
   * @return string
   */
  public function actionLogout()
  {
		Yii::$app->user->logout();

		return $this->goHome();
  }
}
