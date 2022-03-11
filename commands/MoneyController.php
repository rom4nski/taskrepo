<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use app\models\Prize;


class MoneyController extends Controller
{
    public function actionSend($batch = 5)
    {
		$query = Prize::find()->where(['type' => Prize::TYPE_MONEY, 'refused' => 0]);
		if ($query->count() == 0) {
			Console::output("\n\tNo money to send.\n");
			return ExitCode::OK;
		}
		
		$i = 1;
		foreach ($query->batch($batch) as $models) {
			Console::output("\n\tprocessing {$i}th batch [" . count($models) . "] users...\n");
			foreach ($models as $model) {
				Console::output("\tuser [{$model->user_id}] " . '$' . "{$model->value} transfered.\n");
			}
			$i++;
        }

        return ExitCode::OK;
    }
}
