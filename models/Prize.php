<?php

namespace app\models;

use Yii;
use app\models\User;
use yii\behaviors\BlameableBehavior;
use yii\helpers\ArrayHelper;



class Prize extends \yii\db\ActiveRecord
{
	const TYPE_MONEY = 'money';
	const TYPE_BONUS = 'bonus';
	const TYPE_THING = 'thing';
	
	const LIMIT_MONEY = 300;
	const LIMIT_THING = 3;
	
	const LOYALTY_COEFF = 0.01;

	public static $types = [
		1 => self::TYPE_MONEY,
		2 => self::TYPE_BONUS,
		3 => self::TYPE_THING,
	];
	
	public static $limits = [
		self::TYPE_MONEY => self::LIMIT_MONEY,
		self::TYPE_THING => self::LIMIT_THING,
	];
	
	public static $things = [
		"Neo",
		"Morpheus",
		"Trinity",
		"Cypher",
		"Tank"
	];


	public static function tableName()
    {
        return 'prize';
    }
	
	public static function doMoneyTransfer($value)
    {
		
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
            [['user_id', 'refused'], 'integer'],
			['type', 'string'],
			['value', 'safe'],
        ];
    }

	public function getAll()
	{
		$condition = ['user_id' => Yii::$app->user->id, 'type' => $this->type, 'refused' => 0];
		return self::findAll($condition);
	}

	protected function getLeftValue()
    {
		if ($this->type == self::TYPE_BONUS) {
			return;
		}
		
		$all = $this->getAll();
		if (!$all) {
			return self::$limits[$this->type];
		}
		
		if ($this->type == self::TYPE_MONEY) {
			$used = array_sum(ArrayHelper::getColumn($all, 'value'));
			return $used < self::LIMIT_MONEY ? self::LIMIT_MONEY - $used : 0;
		}
		
		return count($all) <= self::LIMIT_THING ? 1 : 0;
    }
	
	public function generateType()
    {
		$key = rand(1, 3);
		return self::$types[$key];
    }
	
	public function generateValue()
    {
		if ($this->type == self::TYPE_MONEY) {
			$value = rand(0, $this->getLeftValue());
		} elseif ($this->type == self::TYPE_BONUS) {
			$value = rand(0, 1000);
		} else {
			$key = array_rand(self::$things);
			$value = $this->getLeftValue() > 0 ? self::$things[$key] : "\xE2\x9D\x8C";
		}
		
		return $value;
    }
}
