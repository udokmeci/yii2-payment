<?php
namespace udokmeci\yii2payment;
use Exception;
use udokmeci\yii2payment\models\ExchangeRates;
use udokmeci\yii2payment\models\Currency;
class Amount extends \yii\base\Object
{        
	public $total;
	public $currency;

	public function init(){
		parent::init();

	}

	public function setTotal($total){
		$this->total = $total;
        return $this;
	}

    public function getTotal()
    {
        return $this->total;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function convertTo($to)
    {
    	$currencyClass=$this->currency->className();
    	
        $res=ExchangeRates::convert($this->getTotal(), $this->currency->code, $to);
        if (!$res) {
            return false;
        }
        
        $this->setTotal($res);
        $this->setCurrency($currencyClass::findOne($to));
        
        return $this;

    }

    
    public function setCurrency(Currency $currency)
    {
        $this->currency = $currency;

        $this->total = number_format($this->total, $this->currency->E, ".", "");
        return $this;
    }

}

		