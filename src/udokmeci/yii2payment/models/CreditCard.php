<?php

namespace udokmeci\yii2payment\models;
use Yii;
class CreditCard extends \yii\base\Model
{

    private $creditCardNumber="";
    private $expireMonth="";
    private $expireYear="";
    private $CCV2="";
    private $cardHolder="";
    private $bin=null;

    public static function ruleSet(){
         return [
            [['creditCardNumber','cardHolder','expireMonth','expireYear','CCV2'],'required'],
            [['cardHolder'], 'string'],
            [['expireMonth', 'expireYear','CCV2'], 'integer'],
            ["creditCardNumber","udokmeci\\yii2payment\\validators\\CreditCardValidator","message"=>Yii::t('app', 'The credit card number you have entered seem not to be valid.')],
            ["expireMonth","in","range"=>array_keys(self::getMonthOptions())],
            ["expireYear","in","range"=>array_keys(self::getYearOptions())],
            ];
    }
    public function rules()
    {
        return self::ruleSet();
            
        
    }

    public function setCCNO($creditCardNumber)
    {
        $this->creditCardNumber = preg_replace("/[^\d]/", "", $creditCardNumber);
        $this->bin = CreditCardBin::find()->where(
            ":ccno REGEXP concat('^',bin,'.*')",
            [":ccno"=>$this->creditCardNumber]
        )->one();
        return $this;
    }

    public function getBin()
    {
        return $this->bin;
    }

    public function setExpireMonth($expireMonth)
    {
        $this->expireMonth = str_pad($expireMonth, 2, '0', STR_PAD_LEFT);
        return $this;
    }

    public function setExpireYear($expireYear)
    {
        $this->expireYear = str_pad($expireYear, 2, '0', STR_PAD_LEFT);
        return $this;
    }

    public function setCCV2($CCV2)
    {
        $this->CCV2 = $CCV2;
        return $this;
    }

    public function setcardHolder($cardHolder)
    {
        $this->cardHolder = mb_strtoupper($cardHolder, "UTF-8");
        return $this;
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else if ($property=='expire') {
                return "$this->expireMonth/$this->expireYear";
        }
    }

    public static function loadFromModel($model)
    {
        try {

            return (new self)
            ->setCCNO($model->creditCardNumber)
            ->setcardHolder($model->cardHolder)
            ->setExpireMonth($model->expireMonth)
            ->setExpireYear($model->expireYear)
            ->setCCV2($model->CCV2);
        } catch (\Exception $e) {
            return (new self);
        }
    }

    public static function getMonthOptions(){
        $months=[];
        for ($k=1; $k<=12; $k++)
            $months[$k]=str_pad($k, 2, '0', STR_PAD_LEFT);

        return $months;
    }

    public static function getYearOptions(){
        $years=[];
        for ($k=date('Y'); $k<=date('Y')+15; $k++)
            $years[$k]=$k;
        return $years;
    }
}
