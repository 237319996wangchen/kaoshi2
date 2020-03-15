<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\web\IdentityInterface;



class UserScore extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_score}}';
    }
}
