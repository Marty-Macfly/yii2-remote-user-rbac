# yii2-remote-user-rbac

Yii2 User and Rbac provider from another Yii2 instance for sso or cenralized way to manage user and role.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist "macfly/yii2-remote-user-rbac" "*"
```

or add

```
"macfly/yii2-remote-user-rbac": "*"
```

to the require section of your `composer.json` file.

Configure
------------

> **NOTE:** Make sure that you don't have `user` component configuration in your config files.

Configure **config/web.php** as follows

```php
  'components' => [
    ................
    'authClientCollection' => [
      'class'   => \yii\authclient\Collection::className(),
      'clients' => [
        'oauth2' => [
          'class'           => 'macfly\authclient\OAuth2',
          'authUrl'         => 'http://127.0.0.1:8888/oauth2/authorize',
          'tokenUrl'        => 'http://127.0.0.1:8888/oauth2/token',
          'apiBaseUrl'      => 'http://127.0.0.1:8888/oauth2',
          'clientId'        => 'testclient',
          'clientSecret'    => 'testpass',
          'requestOptions'  => [
            'sslVerifyPeer' => false,
            'sslVerifyPeerName' => false,
          ],
        ],
      ],
    ],
  ................
  'modules' => [
      ................
      'user'  => [
       'class'       => 'macfly\user\client\Module',
			 'cacheDuration' => 3600,
       'authclient'  => 'oauth2',
       'rememberFor' => 1209600, # Session life (default: 1209600)
       'identityUrl' => 'http://127.0.0.1:8888/user/api/identity', # (optional)
       'rbacUrl'     => 'http://127.0.0.1:8888/user/api/rbac',     # (optional)
#			 'userComponent' => '',
#      'modelMap'    => [],
#      'remoteModelMap' = [
#         'app\models\User' => 'User',
#       ],
      ],
      ................
  ],
```

Usage
------------

Authentication with HTTP Bearer token
------------

HttpBearerAuth is an action filter that supports the authentication method based on HTTP Bearer token.

You may use HttpBearerAuth by attaching it as a behavior to a controller or module, like the following:

```php
public function behaviors()
{
    return [
        'authenticator' => [
            'class' => \macfly\user\client\filters\auth\HttpBearerAuth::className(),
        ],
    ];
}
```
