<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace tests\unit\models;

use app\models\Prize;
use app\models\user\Info;

/**
 * Description of MoneyConvertTest
 *
 * @author Roman
 */
class MoneyConvertTest extends \Codeception\Test\Unit
{
	public function testConvertMoneyToBonus()
    {
		expect_that($prize = Prize::findOne(['type' => Prize::TYPE_MONEY, 'refused' => 0]));
		expect($prize->value)->greaterThan(0);
		
		$prize->type = Prize::TYPE_BONUS;
		$prize->value = $prize->value / Prize::LOYALTY_COEFF;
		expect($prize->save())->true();
    }
}
