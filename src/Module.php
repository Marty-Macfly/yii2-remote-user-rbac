<?php

namespace macfly\user\client;

use Yii;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;
use yii\authclient\OAuthToken;

class Module extends \yii\base\Module
{
    public $authclient            = null;
    public $identityUrl            = null;
    public $rbacUrl                = null;
    public $userComponent        = null;
    public $clientCollection    = 'authClientCollection';
    public $remoteModelMap        = [
        'app\models\User'    => 'User',
    ];

    /** @var array Model map */
    public $modelMap        = [];

    /** @var int The time you want the user will be remembered without asking for credentials. */
    public $rememberFor        = 1209600; // two week

    /** @var int|false The time you want api call to be cache (O = infinite, integer = nb of seconds, false no cache). */
    public $cacheDuration    = false; // Disabled

    protected $client        = null;

    public function identity($method, $args)
    {
        if (is_null($this->identityUrl)) {
            throw new NotSupportedException("Property 'identityUrl' not defined");
        }

        return $this->request($method, $this->identityUrl, $args, true, false);
    }

    public function rbac($method, $args, $cache = false, $rw = false)
    {
        if (is_null($this->identityUrl)) {
            throw new NotSupportedException("Property 'rbacUrl' not defined");
        }

        return $this->request($method, $this->rbacUrl, $args, $cache, $rw);
    }

    protected function request($method, $url, $args = [], $cache = false, $rw = false)
    {
        $client = $this->getClient();
        $token  = $client->getAccessToken();
        $id     = hash('sha256', json_encode([$token, $url, $method, $args]));

        if (($arr = Yii::$app->cache->get($id)) === false || $cache === false) {
            Yii::info(sprintf("Cache id: %s => miss", $id));
            if (Yii::$app instanceof \yii\console\Application && empty($token)) {
                $client->authenticateClient();
                // Disable cache when use in CLI
                $cache = false;
            }

            $rq = $client->createApiRequest()
                ->setMethod('PUT')
                ->setUrl(sprintf("%s/%s/%s", $url, $rw ? 'write' : 'read', $method))
                ->setData($args);
            $rs = $rq->send();

            if (!$rs->isOk) {
                if (ArrayHelper::keyExists('name', $rs->data)
                    && ArrayHelper::keyExists('message', $rs->data)
                    && ArrayHelper::keyExists('code', $rs->data)
                    && ArrayHelper::keyExists('status', $rs->data)
                    && ArrayHelper::keyExists('type', $rs->data)) {
                    throw Yii::createObject([
                                        'class'      => $rs->data['type'],
                                        'statusCode' => $rs->data['status']
                                    ], [$rs->data['message'], $rs->data['code']]);
                } else {
                    throw new NotSupportedException(sprintf("Error requesting method '%s' : %s", $method, $rs->content));
                }
            }

            $arr = $rs->data;

            if ($cache === true && $this->cacheDuration !== false) {
                Yii::$app->cache->set($id, $arr, $this->cacheDuration);
            }
        } else {
            Yii::info(sprintf("Cache id: %s => hit for %s/%s/%s", $id, $url, $rw ? 'write' : 'read', $method));
        }

        if (is_array($arr) && array_key_exists('class', $arr)) {
            if (array_key_exists($arr['class'], $this->remoteModelMap)) {
                $arr['class']    = $this->modelMap[$this->remoteModelMap[$arr['class']]];
            }
            return Yii::createObject($arr);
        }

        return $arr;
    }

    public function getClient()
    {
        if (is_null($this->client)) {
            $this->client = Yii::$app->get($this->clientCollection)->getClient($this->authclient);
        }

        return $this->client;
    }

    public function setToken($token)
    {
        $client    = $this->getClient();
        $otoken    = new OAuthToken();
        $otoken->setToken($token);
        return $client->setAccessToken($otoken);
    }
}
