<?php

namespace app\modules\api;

use yii\web\Response;
use Yii;

class Api extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\api\controllers';

    public function init()
    {
        parent::init();

        // custom initialization code goes here
        Yii::$app->response->format = Response::FORMAT_JSON;
        // ...  other initialization code ...
        \Yii::$app->user->enableSession = false;
        \Yii::$app->user->loginUrl = null;
        \Yii::$app->user->identityClass = 'app\models\User';
        \Yii::$app->language='es';
    }
}
