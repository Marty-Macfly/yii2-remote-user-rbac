<?php

namespace macfly\user\controllers;

use Yii;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

use macfly\user\models\LoginForm;
use macfly\user\models\User;


class ActionController extends Controller
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

  /**
   * Login action.
   *
   * @return string
   */
  public function actionLogin()
  {
    if (!Yii::$app->user->isGuest)
		{
			return $this->goHome();
    }

		$module = \Yii::$app->controller->module;
		if(\Yii::$app->controller->module->auth == 'sso')
		{
			if(is_null(($sid = Yii::$app->request->get('id'))))
			{
				return $this->redirect(sprintf("%s/%s?redirect=%s", $module->url_base, $module->url_login, Url::current([], true)));
			} else
			{ 
				$rs = $module->createRequest()
						->setMethod('POST')
						->setUrl($module->url_session)
						->setData(['sid'  => $sid])
						->send();

				if($rs->isOk) {
					// keep the data in cache for at most 45 seconds
					\Yii::$app->cache->set($rs->data['id'], $rs->data, \Yii::$app->controller->module->rememberFor);
					if(Yii::$app->user->login(new User($rs->data), 0))
					{
						return $this->goBack();
					}
				}
			}
			throw new yii\web\ForbiddenHttpException();
		}

		$model = new LoginForm();
		if ($model->load(Yii::$app->request->post()) && $model->login())
		{
			return $this->goBack();
		}

		return $this->render('login', [
				'model' => $model,
		]);
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
