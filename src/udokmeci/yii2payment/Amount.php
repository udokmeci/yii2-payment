<?php
namespace udokmeci\yii2payment;
use Exception;
class Amount extends \yii\base\Object
{        
	public $total;
	public $currency;

	public function setTotal($total){
		
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
        $res=ExchangeRates::convert($this->getTotal(), $this->currency->code, $to);
        if (!$res) {
            return false;
        }

        $this->setCurrency(Currency::findOne($to));
        $this->setTotal($res);
        
        return $this;

    }

    
    public function setCurrency(models\Currency $currency)
    {
        $this->currency = $currency;
        $this->total = number_format($this->total, $this->currency->E, ".", "");
        return $this;
    }

}

		