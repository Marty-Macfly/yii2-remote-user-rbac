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

Usage
------------

## Configuring to manage User Login in web interface

Configure **config/web.php** as follows

```php
  'modules' => [
            ................
      'user'  => [
       'class'       => 'macfly\user\Module',
       'rememberFor' => 1209600,													# Session life
       'url_base'    => 'https://auth.provider.url.com',	# Base url of the auth provide
       'url_login'   => 'user/security/login', 						# URL used to redirect if use of sso
       'url_user'    => 'api/users', 											# WS route to get user information
       'url_session' => 'api/session', 										# WS route to get session information
       'url_rbac'    => 'api/access/update',							# Ws route to get role list of the user
       'auth'        => 'sso',														# SSO you will be redirect on url_login for auth, login authentification while be done through form, user will need to log again
       'login'       => 'xxxx',														# Use if access to WS are made through login and password
       'password'    => 'xxxx',
or
       'key'         => 'xxxx',														# Auth to WS is made through API-KEY instead of login/password
      ],
            ................
  ],
```

## Configuring to use rbac

Configure **authManager** and **user** component as follows

```php
    'components' => [
            ................
        'user' => [
            'identityClass' => 'macfly\user\models\User',
            'enableAutoLogin' => true,
        ],
        'authManager' => [
            'class' => 'macfly\user\models\RbacManager',
        ],
            ................
  ],
```
