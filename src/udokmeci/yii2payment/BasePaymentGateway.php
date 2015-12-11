<?php
namespace app\components;

use \app\models\ExchangeRates;
use \app\models\Currency;
use \app\models\order\UserCreditcard;
use \app\models\order\TransactionCodesL10n;
use \app\models\order\TransactionCodes;

class BasePaymentGateway extends \yii\base\Component
{
    private $translations=[];

    const TEST_MODE = "testMode";
    const REAL_MODE = "realMode";
    public $mode;

    const AUTH_TYPE = "Auth";
    const PREAUTH_TYPE = "PreAuth";
    private $type;



    public $requestIp;
    public $email;
    public $oid;

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
        $this->type = $this->translate($type);
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setOID($oid)
    {
        $this->oid = $oid;
        return $this;
    }

    public function setMode($mode)
    {
        $this->mode = $this->translate($mode);
        return $this;
    }

    public function getErrors()
    {
        $res=null;
        $orderBy = new \yii\db\Expression('FIELD (locale_code, "' . implode('"," ', [\Yii::$app->language,'en-gb']) . '")');
        if ($this->return_code) {
            $res= TransactionCodesL10n::find()
                ->select('description')
                ->where(["transaction_code" => $this->return_code])
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
        if ($this->return_code) {
            $res = TransactionCodes::find()
                ->select('successfull')
                ->where(["code" => $this->return_code])
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

    
}
