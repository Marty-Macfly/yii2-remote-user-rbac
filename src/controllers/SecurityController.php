<?php

namespace macfly\user\client\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\authclient\AuthAction;

use macfly\user\client\models\User;

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
		$attributes						=	$client->getUserAttributes();
		$attributes['class']	= $this->module->modelMap['User'];
		$user									= Yii::createObject($attributes);
		return Yii::$app->user->login($user, $this->module->rememberFor);
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
