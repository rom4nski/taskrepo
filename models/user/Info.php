<?php

namespace app\models\user;

use Yii;
use app\models\User;
use yii\behaviors\BlameableBehavior;
use yii\helpers\ArrayHelper;



class Info extends \yii\db\ActiveRecord
{
	const KEY_LOYALTY_BONUS = 'loyalty_bonus';


	public static function tableName()
    {
        return 'user_info';
    }
	
	public static function doBonusTransfer($value)
    {
		$info = self::findOne(['user_id' => Yii::$app->user->id, 'key' => self::KEY_LOYALTY_BONUS]);
		if (!$info) {
			$info = new Info();
			$info->key = self::KEY_LOYALTY_BONUS;
			$info->value = $value;
		} else {
			$newValue = $info->value + $value;
			$info->value = $newValue;
		}
		$info->save();
    }
	
	public function behaviors()
	{
		return [
			[
				'class' => BlameableBehavior::class,
				'createdByAttribute' => 'user_id',
				'updatedByAttribute' => false,
			],
		];
	}

    public function rules()
    {
        return [
            ['user_id', 'integer'],
			['key', 'string'],
			['value', 'safe'],
        ];
    }
	
	public static function getBonus()
    {
		$model = self::findOne(['user_id' => Yii::$app->user->id, 'key' => self::KEY_LOYALTY_BONUS]);
		if (!$model) {
			return 0;
		}
		
		return $model->value;
    }
}
