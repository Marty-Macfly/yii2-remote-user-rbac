<?php

namespace macfly\user;

use Yii;
use yii\httpclient\Client;

class Module extends \yii\base\Module
{
	public $url_base		= 'http://127.0.0.1:8888/index.php?r=';
	public $url_login		= 'user/security/login';
	public $url_user		= 'api/users';
	public $url_session	= 'api/sessions';
	public $url_rbac		= 'api/access/update';

	public $login				= null;
	public $password		= null;
	public $key					= null;

	public $auth				= null;

	private $client			= null;

	/** @var int The time you want the user will be remembered without asking for credentials. */
	public $rememberFor = 1209600; // two week

  public function init()
  {
    parent::init();
  }

	public function getHttpClient()
	{
		if(is_null($this->client))
		{
			$this->client	= new Client([
					'baseUrl'	=> $this->url_base,
					'requestConfig' => [
							'format' => Client::FORMAT_JSON
					],
				]);
		}

		return $this->client;
	}

	public function createRequest()
	{
		return $this->getHttpClient()
						->createRequest()
						->setOptions([
							'sslVerify_peer_name'	=> false,
    				]);
	}
}

