<?php

namespace macfly\user\client;

use Yii;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;
use yii\authclient\OAuthToken;

class Module extends \yii\base\Module
{
    public $authclient				= null;
    public $identityUrl				= null;
    public $rbacUrl						= null;
		public $userComponent			= null;
    public $clientCollection	= 'authClientCollection';
    public $remoteModelMap		= [
        'app\models\User'	=> 'User',
    ];

    /** @var array Model map */
    public $modelMap			= [];

    /** @var int The time you want the user will be remembered without asking for credentials. */
    public $rememberFor		= 1209600; // two week

    /** @var int|false The time you want api call to be cache (O = infinite, integer = nb of seconds, false no cache). */
    public $cacheDuration	= false; // Disabled

    protected $client			= null;

    public function identity($method, $args, $cache = false)
    {
        if(is_null($this->identityUrl))
        {
            throw new NotSupportedException("Property 'identityUrl' not defined");
        }

        return $this->request($method, $this->identityUrl, $args, $cache);
    }

    public function rbac($method, $args, $cache = false)
    {
        if(is_null($this->identityUrl))
        {
            throw new NotSupportedException("Property 'rbacUrl' not defined");
        }

        return $this->request($method, $this->rbacUrl, $args, $cache);
    }

    protected function request($method, $url, $args = [], $cache = false)
    {
        $id	= hash('sha256', json_encode([$url, $method, $args]));

        if(($arr = Yii::$app->cache->get($id)) === false || $cache !== false)
        {
            $client	= $this->getClient();

            if(Yii::$app instanceof \yii\console\Application && empty($client->getAccessToken()))
            {
                $client->authenticateClient();
            }

            $rq     = $client->createApiRequest()
                ->setMethod('PUT')
                ->setUrl(sprintf("%s/%s", $url, $method))
                ->setData($args);
            $rs			= $rq->send();

            if(!$rs->isOk)
            {
                throw new NotSupportedException(sprintf("Error requesting method '%s' : %s", $method, $rs->content));
            }

            $arr		= $rs->data;

            if($this->cacheDuration !== false)
            {
                Yii::$app->cache->set($id, $arr, $this->cacheDuration);
            }
        }

        if(is_array($arr) && array_key_exists('class', $arr))
        {
            if(array_key_exists($arr['class'], $this->remoteModelMap))
            {
                $arr['class']	= $this->modelMap[$this->remoteModelMap[$arr['class']]];
            }
            return Yii::createObject($arr);
        }

        return $arr;
    }

    public function getClient()
    {
        if(is_null($this->client))
        {
            $this->client = Yii::$app->get($this->clientCollection)->getClient($this->authclient);
        }

        return $this->client;
    }

    public function setToken($token)
    {
        $client	= $this->getClient();
        $otoken	= new OAuthToken();
        $otoken->setToken($token);
        return $client->setAccessToken($otoken);
    }
}
