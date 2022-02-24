<?php

namespace app\models;

use Yii;
use \app\models\base\Modulo as BaseModulo;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "modulo".
 */
class Modulo extends BaseModulo
{

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                # custom validation rules
            ]
        );
    }
}
