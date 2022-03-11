<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use app\models\Prize;


class MoneyController extends Controller
{
    public function actionBankTransfer($batch = 5)
    {
		$query = Prize::find()->where(['type' => Prize::TYPE_MONEY, 'refused' => 0]);
		if ($query->count() == 0) {
			Console::output("\n\tNo money to transfer.\n");
			return ExitCode::OK;
		}
		
		foreach ($query->batch($batch) as $key => $models) {
			Console::output("\n\tprocessing " . ++$key . "th batch [" . count($models) . "] users...\n");
			foreach ($models as $model) {
				if ($model->value == 0) continue;
				Prize::doMoneyTransfer($model->user_id, $model->value);
				Console::output("\tuser [{$model->user_id}] {$model->value} euro transfered.\n");
				$model->delete();
			}
        }

        return ExitCode::OK;
    }
}
