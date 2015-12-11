<?php
namespace udokmeci\yii2payment\actions;

use Yii;
use yii\base\Action;
use yii\web\Response;
use yii\helpers\Url;
use Exception;

/**
* 
*/
class UpdateExchangeRatesAction extends Action
{
    public $exchangeRatesClass='';
    public $file='latest.json';
    public $openExchangeAppID;
    public $update=false;


    public function run()
    {
        $exchangeRatesClass=$this->exchangeRatesClass;

        if($this->update)
            $this->updateRates();
        return $exchangeRatesClass::find()->asArray()->all();
    }

    public function updateRates(){

        $exchangeRatesClass=$this->exchangeRatesClass;
        $file = $this->file;

        if(!$this->openExchangeAppID){
            throw new Exception("Needs an OpenExchange AppID to update. Set openExchangeAppID", 1);           
        }

        // Open CURL session:
        $ch = curl_init("http://openexchangerates.org/api/{$file}?app_id=".$this->openExchangeAppID);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // Get the data:
        $json = curl_exec($ch);
        curl_close($ch);

        // Decode JSON response:
        $exchangeRates = json_decode($json);

        if(!isset($exchangeRates->rates)){
            throw new Exception("No rates returned server response: $json", 1);           
        }

        foreach($exchangeRates->rates as $currency => $rate){
            $exchangeRate = $exchangeRatesClass::findOne($currency);
            if (!$exchangeRate)
                $exchangeRate= new $exchangeRatesClass([
                        "currency_code" => $currency
                    ]);
            $exchangeRate->rate=(float)$rate;
            $exchangeRate->save();
        }

    }
}