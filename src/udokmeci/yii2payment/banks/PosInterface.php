<?php
namespace udokmeci\yii2payment\banks;

use Yii;

interface PosInterface
{
	public function setCreditCard(\udokmeci\yii2payment\models\CreditCard $creditCard);
	public function setAmount(\udokmeci\yii2payment\Amount $amount);
	public function prepareRequest();
	public function reset();
	public function makeRequest();
	public function process();
	public function isSuccessful();
}