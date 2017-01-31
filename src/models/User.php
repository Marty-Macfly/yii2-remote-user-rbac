<?php

namespace macfly\user\models;

use Yii;
use macfly\user\Module;

class User extends \yii\base\Object implements \yii\web\IdentityInterface
{
	public $id;
	public $username;
	public $email;
	public $timezone;

	public $accessToken;

  public function __construct($data)
  { 
		foreach(['id', 'username', 'email', 'timezone'] as $var) {
			if(array_key_exists($var, $data)) {
				$this->$var	= $data[$var];
			}
		}

		if(!is_null($this->timezone))
		{
			\Yii::$app->timeZone	=	$this->timezone;
		}
  }

	/**
	 * @inheritdoc
	 */
	public static function findIdentity($id)
	{
		if(($data	= \Yii::$app->cache->get($id)) === false)
		{
			if(!\Yii::$app->hasModule('user'))
			{
				 throw new NotSupportedException('Module with id "user" not loaded');
			}

			$module = \Yii::$app->getModule('user');
			$rs     = $module->createRequest()
									->setMethod('GET')
									->setUrl(sprintf('%s/%d', $module->url_user, $id))
									->addHeaders([
										'Authorization' => 'Basic '. base64_encode(is_null($module->key) ?
											sprintf("%s:%s", $module->login, $module->password) : sprintf("%s:", $module->key))])
									->send();

			if($rs->isOk) {
				$data	=	$rs->data;
				\Yii::$app->cache->set($data['id'], $data, $module->rememberFor);
			} else
			{
				return null;
			}
		}
	
		return new self($data);
	}

	/**
	 * @inheritdoc
	 */
	public static function findIdentityByAccessToken($token, $type = null)
	{
		return self::findByUsername($token);
	}

	/**
	 * Finds user by username
	 *
	 * @param string $username
	 * @return static|null
	 */
	public static function findByUsername($username, $password = null)
	{
    if(!\Yii::$app->hasModule('user'))
    {
       throw new NotSupportedException('Module with id "user" not loaded');
    }

    $module = \Yii::$app->getModule('user');
		$rs			= $module->createRequest()
								->setMethod('GET')
								->setUrl($module->url_user)
								->addHEaders(['Authorization' => 'Basic '.base64_encode(sprintf("%s:%s", $username, $password))])
								->send();
	
		if($rs->isOk) {
			\Yii::$app->cache->set($rs->data['id'], $rs->data, $module->rememberFor);
			return new self($rs->data);
		}

	  return null;
	}

	/**
	* @inheritdoc
	*/
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @inheritdoc
	 */
	public function getAuthKey()
	{
		return sha1($this->id . 'Eipaih3V');
	}
	
	/**
	 * @inheritdoc
	 */
	public function validateAuthKey($authKey)
	{
		return $this->getAuthKey() === $authKey;
	}
}
