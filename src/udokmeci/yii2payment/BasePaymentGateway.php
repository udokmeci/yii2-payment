<?php
namespace app\components;

use \app\models\ExchangeRates;
use \app\models\Currency;
use \app\models\order\UserCreditcard;
use \app\models\order\TransactionCodesL10n;
use \app\models\order\TransactionCodes;

class BasePaymentGateway extends \yii\base\Component
{
    public $requestIp;
    public $email;
    public $oid;



    public $forceConversionTo=false;

    private $success=false;
    public $status=-1;
    public $errorCode=-1;
    public $errors=[];

    private $paymentMethod;


    private $amounts=[];

    private $total="0";
    private $currency;

    

    public function init()
    {
        parent::init();
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $this->requestIp=$_SERVER['REMOTE_ADDR'];
        } else {
            $this->requestIp="127.0.0.1";
        }
        return $this;
    }



    public function addAmount(Amount $amount)
    {
        if($this->forceConversionTo){
            $amount->convertTo($this->forceConversionTo);
        }
        $this->amounts[]=$amount;
        return $this;
    }
    
    public function setCreditCard(CreditCard $creditCard)
    {
        $this->creditCard = $creditCard;
        return $this;
    }


    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setOID($processID)
    {
        $this->processID = $processID;
        return $this;
    }

    public function getErrors()
    {
        $res=null;
        $orderBy = new \yii\db\Expression('FIELD (locale_code, "' . implode('"," ', [\Yii::$app->language,'en-gb']) . '")');
        if ($this->status_code) {
            $res= TransactionCodesL10n::find()
                ->select('description')
                ->where(["transaction_code" => $this->status_code])
                ->orderBy([$orderBy])
                ->column();
        }
        if ($res) {
            return $res;
        }

        if (sizeof($this->errors)==0) {
            return false;
        } else {
            return $this->errors;
        }
    }

    public function getType()
    {
        return $this->type;
    }

    public function getCreditCard()
    {
        return $this->creditCard;
    }

    public function getUserCreditCard()
    {
        return $this->userCreditCard;

    }

    

    public function getEmail()
    {
        return $this->email;
    }

    public function isSuccessfull()
    {
        if ($this->status_code) {
            $res = TransactionCodes::find()
                ->select('successfull')
                ->where(["code" => $this->status_code])
                ->column();
            if ($res) {
                return  $res[0] ;
            }
        }
        return $this->success;
    }

    private function getResponse()
    {
        return null;
    }

    public function getGrandTotal(){
        $amountsSubTotal=[];
        $amounts=[];
        if($this->amount)
            foreach ($this->amounts as $amount){
                $amountsSubTotal[$amount->currency->code]+=$amount->total;
            }
        if($amountsSubTotal)
            foreach ($amounts as $currency => $total){
                $amounts[]=new Amount([
                    'currency'=> Curency::findOne(['code'=>$currency]),
                    'total'=>$total,
                ]);
            }
        return $amounts;
    }

    
}
