<?php

namespace udokmeci\yii2payment\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;


class ExchangeController extends Controller
{
	public $openExchangeAppID;
	public function actions(){
		return [
			'update' => [
				'class'=>'udokmeci\yii2payment\actions\UpdateExchangeRatesAction',
				'update'=>true,
				'openExchangeAppID'=>$this->openExchangeAppID,
			],
			'list' => [
				'class'=>'udokmeci\yii2payment\actions\UpdateExchangeRatesAction',
			]
		];
	}
}