<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\user\Info;

class MoneyController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'bank-transfer' => ['post'],
                ],
            ],
        ];
    }
	
	public function beforeAction($action)
    {
		$this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

	public function actionBankTransfer()
    {
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$post = Yii::$app->request->post();
		
		$info = Info::findOne(['user_id' => intval($post['user']), 'key' => Info::KEY_BANK_ACCOUNT]);
		if (!$info) {
			$info = new Info();
			$info->user_id = intval($post['user']);
			$info->key = Info::KEY_BANK_ACCOUNT;
			$info->value = $post['amount'];
		} else {
			$newValue = $info->value + $post['amount'];
			$info->value = $newValue;
		}
		return $info->save();
    }
}
