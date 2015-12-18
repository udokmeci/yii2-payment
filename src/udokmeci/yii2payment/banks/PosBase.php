<?php
namespace udokmeci\yii2payment\banks;

use Yii;

class PosBase extends \yii\base\Component implements PosInterface
{

	public $endUrl;
	public $bankStatusCode=-1;
	public $requestIp;
	public $authCode;
	public $bankMessage;
	public $timeout=3;
	public $errors=[];
	public $uid;

	public $_amount;
	public $_request;
	public $_response;
	public $_creditCard;
	public $_ch;

	public function init()
	{
		parent::init();
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $this->requestIp=$_SERVER['REMOTE_ADDR'];
        } else {
            $this->requestIp="127.0.0.1";
        }

		$this->_ch = curl_init();
	}
	public function setUid($uid)
	{
		$this->uid=$uid;
		return $this;
	}
	public function setCreditCard(\udokmeci\yii2payment\models\CreditCard $creditCard)
	{
		$this->_creditCard=$creditCard;
		return $this;
	}

	public function setAmount(\udokmeci\yii2payment\Amount $amount)
	{
		$this->_amount=$amount;
		return $this;
	}


	public function reset()
	{
		$this->bankStatusCode=null;
		$this->authCode=null;
		$this->bankMessage=null;
		$this->_amount=null;
		$this->_request=null;
		$this->_response=null;
		$this->_creditCard=null;
	}

	public function makeRequest()
	{
		curl_setopt($this->_ch, CURLOPT_URL,$this->endUrl);
		curl_setopt($this->_ch, CURLOPT_POST, true);
		curl_setopt($this->_ch, CURLOPT_POSTFIELDS,http_build_query($this->_request));

		curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->_ch, CURLOPT_TIMEOUT, $this->timeout);
		if(($this->_response=curl_exec($this->_ch) )=== false)
			Yii::error(curl_error($this->_ch));
		curl_close($this->_ch);
	}


	public function process()
	{

		$this->_request=$this->prepareRequest();
		$this->makeRequest();
		$this->afterRequest();
		$result= $this->isSuccessful();

		return $result;
	}

	public function prepareRequest(){}
	public function isSuccessful(){}
	public function afterRequest(){}



}