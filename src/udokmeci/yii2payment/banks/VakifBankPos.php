<?php
namespace udokmeci\yii2payment\banks;

use Yii;

class VakifBankPos extends PosBase
{
	public $merchantId;
	public $password;
	public $terminalNo;
	public $transactionType='Sale';
	public $rawResponse;

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
			$this->rawResponse = json_decode($json);
			

					
			$this->bankStatusCode=substr($this->rawResponse->ResultCode,-2);
			if(!$this->isSuccessful()){
				$this->errors[]=$this->errorCodes[$this->rawResponse->ResultCode];
			}
			$this->bankMessage=$this->rawResponse->ResultDetail;
			if(isset($this->rawResponse->AuthCode))
				$this->authCode=$this->rawResponse->AuthCode;
			
		}
		catch (\Exception $e)
		{
			$this->errors[]=Yii::t('vakifpos','Beklenmedik Hata! Lütfen tekrar Deneyiniz.');
			throw $e;
			
		}
	}

	public function isSuccessful(){

		try {
			return $this->rawResponse->ResultCode=='0000';
		} catch (Exception $e) {
			return false;			
		}
	}

	public function getErrorCodes(){
		return [
			'0000'=>Yii::t('vakifpos','Başarılı'),
			'0001'=>Yii::t('vakifpos','Bankanizi Arayin'),
			'0002'=>Yii::t('vakifpos','Bankanizi Arayin'),
			'0003'=>Yii::t('vakifpos','Üye Kodu Hatali/tanimsiz'),
			'0004'=>Yii::t('vakifpos','Karta El Koyunuz'),
			'0005'=>Yii::t('vakifpos','İşlem Onaylanmadi.'),
			'0006'=>Yii::t('vakifpos','Hatali İşlem'),
			'0007'=>Yii::t('vakifpos','Karta El Koyunuz'),
			'0009'=>Yii::t('vakifpos','Tekrar Deneyiniz'),
			'0010'=>Yii::t('vakifpos','Tekrar Deneyiniz'),
			'0011'=>Yii::t('vakifpos','Tekrar Deneyiniz'),
			'0012'=>Yii::t('vakifpos','Geçersiz İşlem'),
			'0013'=>Yii::t('vakifpos','Geçersiz İşlem Tutarı'),
			'0014'=>Yii::t('vakifpos','Geçersiz Kart Numarası'),
			'0015'=>Yii::t('vakifpos','Müşteri Yok/bin Hatali'),
			'0021'=>Yii::t('vakifpos','İşlem Onaylanmadi'),
			'0030'=>Yii::t('vakifpos','Mesaj Formati Hatali (üye İşyeri)'),
			'0032'=>Yii::t('vakifpos','Dosyasina Ulaşilamadi'),
			'0033'=>Yii::t('vakifpos','Süresi Bitmiş/iptal Kart'),
			'0034'=>Yii::t('vakifpos','Sahte Kart'),
			'0036'=>Yii::t('vakifpos','İşlem Onaylanmadi'),
			'0038'=>Yii::t('vakifpos','Şifre Aşimi/karta El Koy'),
			'0041'=>Yii::t('vakifpos','Kayip Kart- Karta El Koy'),
			'0043'=>Yii::t('vakifpos','Çalinti Kart-karta El Koy'),
			'0051'=>Yii::t('vakifpos','Limit Yetersiz'),
			'0052'=>Yii::t('vakifpos','Hesap Noyu Kontrol Edin'),
			'0053'=>Yii::t('vakifpos','Hesap Yok'),
			'0054'=>Yii::t('vakifpos','Vade Sonu Geçmiş Kart'),
			'0055'=>Yii::t('vakifpos','Hatalı Kart Şifresi'),
			'0056'=>Yii::t('vakifpos','Kart Tanımlı Değil.'),
			'0057'=>Yii::t('vakifpos','Kartin İşlem İzni Yok'),
			'0058'=>Yii::t('vakifpos','Pos İşlem Tipine Kapali'),
			'0059'=>Yii::t('vakifpos','Sahtekarlik Şüphesi'),
			'0061'=>Yii::t('vakifpos','Para Çekme Tutar Limiti Aşıldı'),
			'0062'=>Yii::t('vakifpos','Yasaklanmiş Kart'),
			'0063'=>Yii::t('vakifpos','Güvenlik Ihlali'),
			'0065'=>Yii::t('vakifpos','Günlük İşlem Adedi Limiti Aşildi'),
			'0075'=>Yii::t('vakifpos','Şifre Deneme Sayısı Aşıldı'),
			'0077'=>Yii::t('vakifpos','Şifre Script Talebi Reddedildi'),
			'0078'=>Yii::t('vakifpos','Şifre Güvenilir Bulunmadi'),
			'0089'=>Yii::t('vakifpos','İşlem Onaylanmadi'),
			'0091'=>Yii::t('vakifpos','Karti Veren Banka Hizmet Dişi'),
			'0092'=>Yii::t('vakifpos','Bankasi Bilinmiyor'),
			'0093'=>Yii::t('vakifpos','İşlem Onaylanmadi'),
			'0096'=>Yii::t('vakifpos','Bankasinin Sistemi Arizali'),
			'0312'=>Yii::t('vakifpos','Kartin Cvv2 Değeri Hatali'),
			'0315'=>Yii::t('vakifpos','Tekrar Deneyiniz'),
			'0320'=>Yii::t('vakifpos','Önprovizyon Kapatilamadi'),
			'0323'=>Yii::t('vakifpos','Önprovizyon Kapatilamadi'),
			'0357'=>Yii::t('vakifpos','İşlem Onaylanmadi'),
			'0358'=>Yii::t('vakifpos','Kart Kapalı'),
			'0381'=>Yii::t('vakifpos','Red Karta El Koy'),
			'0382'=>Yii::t('vakifpos','Sahte Kart-karta El Koyunuz'),
			'0501'=>Yii::t('vakifpos','Geçersiz Taksit/işlem Tutari'),
			'0503'=>Yii::t('vakifpos','Kart Numarasi Hatali'),
			'0504'=>Yii::t('vakifpos','İşlem Onaylanmadi'),
			'0540'=>Yii::t('vakifpos','İade Edilecek İşlemin Orijinali Bulunamadı'),
			'0541'=>Yii::t('vakifpos','Orj. İşlemin Tamamı Iade Edildi'),
			'0542'=>Yii::t('vakifpos','İade İşlemi Gerçekleştirilemez'),
			'0550'=>Yii::t('vakifpos','İşlem Ykb Pos Undan Yapilmali'),
			'0570'=>Yii::t('vakifpos','Yurtdişi Kart İşlem İzni Yok'),
			'0571'=>Yii::t('vakifpos','İşyeri Amex İşlem İzni Yok'),
			'0572'=>Yii::t('vakifpos','İşyeri Amex Tanımları Eksik'),
			'0574'=>Yii::t('vakifpos','Üye İşyeri İşlem İzni Yok'),
			'0575'=>Yii::t('vakifpos','İşlem Onaylanmadi'),
			'0577'=>Yii::t('vakifpos','Taksitli İşlem İzni Yok'),
			'0580'=>Yii::t('vakifpos','Hatali 3d Güvenlik Bilgisi'),
			'0581'=>Yii::t('vakifpos','Eci Veya Cavv Bilgisi Eksik'),
			'0582'=>Yii::t('vakifpos','Hatali 3d Güvenlik Bilgisi'),
			'0583'=>Yii::t('vakifpos','Tekrar Deneyiniz'),
			'0961'=>Yii::t('vakifpos','İşlem Tipi Geçersiz'),
			'0962'=>Yii::t('vakifpos','Terminalid Tanımısız'),
			'0963'=>Yii::t('vakifpos','Üye İşyeri Tanımlı Değil'),
			'0966'=>Yii::t('vakifpos','İşlem Onaylanmadi'),
			'0971'=>Yii::t('vakifpos','Eşleşmiş Bir Işlem Iptal Edilemez'),
			'0972'=>Yii::t('vakifpos','Para Kodu Geçersiz'),
			'0973'=>Yii::t('vakifpos','İşlem Onaylanmadi'),
			'0974'=>Yii::t('vakifpos','İşlem Onaylanmadi'),
			'0975'=>Yii::t('vakifpos','Üye İşyeri İşlem İzni Yok'),
			'0976'=>Yii::t('vakifpos','İşlem Onaylanmadi'),
			'0978'=>Yii::t('vakifpos','İşlem Onaylanmadi'),
			'0978'=>Yii::t('vakifpos','Kartin Taksitli İşleme İzni Yok'),
			'0980'=>Yii::t('vakifpos','İşlem Onaylanmadi'),
			'0981'=>Yii::t('vakifpos','Eksik Güvenlik Bilgisi'),
			'0982'=>Yii::t('vakifpos','İşlem İptal Durumda. İade Edilemez'),
			'0983'=>Yii::t('vakifpos','İade Edilemez,iptal'),
			'0984'=>Yii::t('vakifpos','İade Tutar Hatasi'),
			'0985'=>Yii::t('vakifpos','İşlem Onaylanmadi.'),
			'0986'=>Yii::t('vakifpos','Gib Taksit Hata'),
			'0987'=>Yii::t('vakifpos','İşlem Onaylanmadi.'),
			'8484'=>Yii::t('vakifpos','Birden Fazla Hata Olması Durumunda Geri Dönülür. Resultdetail Alanından Detayları Alınabilir. 1001 Sistem Hatası.'),
			'1006'=>Yii::t('vakifpos','Bu Transactionid Ile Daha Önce Başarılı Bir Işlem Gerçekleştirilmiş'),
			'1007'=>Yii::t('vakifpos','Referans Transaction Alınamadı'),
			'1046'=>Yii::t('vakifpos','İade Işleminde Tutar Hatalı.'),
			'1047'=>Yii::t('vakifpos','İşlem Tutarı Geçersizdir.'),
			'1049'=>Yii::t('vakifpos','Geçersiz Tutar.'),
			'1050'=>Yii::t('vakifpos','Cvv Hatalı.'),
			'1051'=>Yii::t('vakifpos','Kredi Kartı Numarası Hatalıdır.'),
			'1052'=>Yii::t('vakifpos','Kredi Kartı Son Kullanma Tarihi Hatalı.'),
			'1054'=>Yii::t('vakifpos','İşlem Numarası Hatalıdır.'),
			'1059'=>Yii::t('vakifpos','Yeniden Iade Denemesi.'),
			'1060'=>Yii::t('vakifpos','Hatalı Taksit Sayısı.'),
			'2200'=>Yii::t('vakifpos','İş Yerinin Işlem Için Gerekli Hakkı Yok.'),
			'2202'=>Yii::t('vakifpos','İşlem Iptal Edilemez. ( Batch Kapalı )'),
			'5001'=>Yii::t('vakifpos','İş Yeri Şifresi Yanlış.'),
			'5002'=>Yii::t('vakifpos','İş Yeri Aktif Değil.'),
			'1073'=>Yii::t('vakifpos','Terminal Üzerinde Aktif Olarak Bir Batch Bulunamadı'),
			'1074'=>Yii::t('vakifpos','İşlem Henüz Sonlanmamış Yada Referans Işlem Henüz Tamamlanmamış.'),
			'1075'=>Yii::t('vakifpos','Sadakat Puan Tutarı Hatalı'),
			'1076'=>Yii::t('vakifpos','Sadakat Puan Kodu Hatalı'),
			'1077'=>Yii::t('vakifpos','Para Kodu Hatalı'),
			'1078'=>Yii::t('vakifpos','Geçersiz Sipariş Numarası'),
			'1079'=>Yii::t('vakifpos','Geçersiz Sipariş Açıklaması'),
			'1080'=>Yii::t('vakifpos','Sadakat Tutarı Ve Para Tutarı Gönderilmemiş.'),
			'1061'=>Yii::t('vakifpos','Aynı Sipariş Numarasıyla Daha Önceden Başarılı Işlem Yapılmış'),
			'1065'=>Yii::t('vakifpos','Ön Provizyon Daha Önceden Kapatılmış'),
			'1082'=>Yii::t('vakifpos','Geçersiz Işlem Tipi'),
			'1083'=>Yii::t('vakifpos','Referans Işlem Daha Önceden Iptal Edilmiş.'),
			'1084'=>Yii::t('vakifpos','Geçersiz Poaş Kart Numarası'),
			'7777'=>Yii::t('vakifpos','Banka Tarafında Gün Sonu Yapıldığından Işlem Gerçekleştirilemedi'),
			'1087'=>Yii::t('vakifpos','Yabancı Para Birimiyle Taksitli Provizyon Kapama Işlemi Yapılamaz'),
			'1088'=>Yii::t('vakifpos','Önprovizyon Iptal Edilmiş'),
			'1089'=>Yii::t('vakifpos','Referans Işlem Yapılmak Istenen Işlem Için Uygun Değil'),
			'1091'=>Yii::t('vakifpos','Recurring Işlemin Toplam Taksit Sayısı Hatalı'),
			'1092'=>Yii::t('vakifpos','Recurring Işlemin Tekrarlama Aralığı Hatalı'),
			'1093'=>Yii::t('vakifpos','Sadece Satış (sale) Işlemi Recurring Olarak Işaretlenebilir'),
			'1006'=>Yii::t('vakifpos','Bu Transactionid Ile Daha Önce Başarılı Bir Işlem Gerçekleştirilmiş'),
			'1095'=>Yii::t('vakifpos','Lütfen Geçerli Bir Email Adresi Giriniz'),
			'1096'=>Yii::t('vakifpos','Lütfen Geçerli Bir Ip Adresi Giriniz'),
			'1097'=>Yii::t('vakifpos','Lütfen Geçerli Bir Cavv Değeri Giriniz'),
			'1098'=>Yii::t('vakifpos','Lütfen Geçerli Bir Eci Değeri Giriniz.'),
			'1099'=>Yii::t('vakifpos','Lütfen Geçerli Bir Kart Sahibi Ismi Giriniz.'),
			'1100'=>Yii::t('vakifpos','Lütfen Geçerli Bir Brand Girişi Yapın.'),
			'1105'=>Yii::t('vakifpos','Üye Işyeri Ip Si Sistemde Tanımlı Değil'),
			'1102'=>Yii::t('vakifpos','Recurring Işlem Aralık Tipi Hatalı Bir Değere Sahip'),
			'1101'=>Yii::t('vakifpos','Referans Transaction Reverse Edilmiş.'),
			'1111'=>Yii::t('vakifpos','Bu Üye Işyeri Non Secure Işlem Yapamaz'),
			'6000'=>Yii::t('vakifpos','Talep Mesajı Okunamadı. (mesajda Yer Alan Parametrelerinizin Formatlarını Kontrol Ediniz)'),
		];
	}

}