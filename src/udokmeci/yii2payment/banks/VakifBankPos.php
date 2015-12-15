<?php
namespace udokmeci\yii2payment\banks;

use Yii;

class VakifBankPos extends PosBase
{
	public $merchantId;
	public $password;
	public $terminalNo;
	public $transactionType='Sale';

	public function prepareRequest()
	{

		return [
			'prmstr'=>'<?xml version="1.0" encoding="utf-8"?>
<VposRequest>
  <MerchantId>'.$this->merchantId.'</MerchantId>
  <Password>'.$this->password.'</Password>
  <TerminalNo>'.$this->terminalNo.'</TerminalNo>
  <TransactionType>'.$this->transactionType.'</TransactionType>
  <TransactionId>'.$this->uid.'</TransactionId>
  <CurrencyAmount>'.$this->_amount->total.'</CurrencyAmount>
  <CurrencyCode>'.$this->_amount->currency->no.'</CurrencyCode>
  <Pan>'.$this->_creditCard->getCCNO().'</Pan>
  <Cvv>'.$this->_creditCard->getCCVNO().'</Cvv>
  <ClientIp>'.$this->requestIp.'</ClientIp>
  <Expiry>'.$this->_creditCard->expireYear . str_pad($this->_creditCard->expireMonth,2,'0', STR_PAD_LEFT).'</Expiry>
  <TransactionDeviceSource>0</TransactionDeviceSource>
</VposRequest>'
		];
	}
	
	public function afterRequest()
	{
		try{
			$xml = simplexml_load_string($this->_response);
			$json = json_encode($xml);
			$response = json_decode($json);
			var_dump($response);die;


					
			$this->bankStatusCode=$reponse->ResultCode;
			$this->bankMessage=$reponse->ResultDetail;
			if(isset($reponse->AuthCode))
				$this->authCode=$reponse->AuthCode;
			
		}
		catch (\Exception $e)
		{
			throw $e;
			
		}
	}

	public function isSuccessfull(){
		return $this->bankStatusCode=='0000';
	}

}