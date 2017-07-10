<?php

namespace app\components;

use Yii;
use yii\helpers\ArrayHelper;

class User extends \yii\web\User
{
    public function getIdentity($autoRenew = true)
    {
        $identity = parent::getIdentity($autoRenew);
        if ($identity !== null
            && ($tz = ArrayHelper::getValue($identity, 'profile.timezone')) !== null
            && $tz != Yii::$app->timeZone)
        {
            Yii::info(sprintf("Timezone set to %s", $tz));
            Yii::$app->timeZone = $tz;
            if (($datecontrol = Yii::$app->getModule('datecontrol')) !== null)
            {
                $datecontrol->displayTimezone		= $tz;
            }
        }
        return $identity;
    }
}
