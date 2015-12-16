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
			'0001'=>Yii::t('vakifpos','BANKANIZI ARAYIN'),
			'0002'=>Yii::t('vakifpos','BANKANIZI ARAYIN'),
			'0003'=>Yii::t('vakifpos','ÜYE KODU HATALI/TANIMSIZ'),
			'0004'=>Yii::t('vakifpos','KARTA EL KOYUNUZ'),
			'0005'=>Yii::t('vakifpos','İŞLEM ONAYLANMADI.'),
			'0006'=>Yii::t('vakifpos','HATALI İŞLEM'),
			'0007'=>Yii::t('vakifpos','KARTA EL KOYUNUZ'),
			'0009'=>Yii::t('vakifpos','TEKRAR DENEYİNİZ'),
			'0010'=>Yii::t('vakifpos','TEKRAR DENEYİNİZ'),
			'0011'=>Yii::t('vakifpos','TEKRAR DENEYİNİZ'),
			'0012'=>Yii::t('vakifpos','Geçersiz İşlem'),
			'0013'=>Yii::t('vakifpos','Geçersiz İşlem Tutarı'),
			'0014'=>Yii::t('vakifpos','Geçersiz Kart Numarası'),
			'0015'=>Yii::t('vakifpos','MÜŞTERİ YOK/BIN HATALI'),
			'0021'=>Yii::t('vakifpos','İŞLEM ONAYLANMADI'),
			'0030'=>Yii::t('vakifpos','MESAJ FORMATI HATALI (ÜYE İŞYERİ)'),
			'0032'=>Yii::t('vakifpos','DOSYASINA ULAŞILAMADI'),
			'0033'=>Yii::t('vakifpos','SÜRESİ BİTMİŞ/İPTAL KART'),
			'0034'=>Yii::t('vakifpos','SAHTE KART'),
			'0036'=>Yii::t('vakifpos','İŞLEM ONAYLANMADI'),
			'0038'=>Yii::t('vakifpos','ŞİFRE AŞIMI/KARTA EL KOY'),
			'0041'=>Yii::t('vakifpos','KAYIP KART- KARTA EL KOY'),
			'0043'=>Yii::t('vakifpos','ÇALINTI KART-KARTA EL KOY'),
			'0051'=>Yii::t('vakifpos','LIMIT YETERSIZ'),
			'0052'=>Yii::t('vakifpos','HESAP NOYU KONTROL EDİN'),
			'0053'=>Yii::t('vakifpos','HESAP YOK'),
			'0054'=>Yii::t('vakifpos','VADE SONU GEÇMİŞ KART'),
			'0055'=>Yii::t('vakifpos','Hatalı Kart Şifresi'),
			'0056'=>Yii::t('vakifpos','Kart Tanımlı Değil.'),
			'0057'=>Yii::t('vakifpos','KARTIN İŞLEM İZNİ YOK'),
			'0058'=>Yii::t('vakifpos','POS İŞLEM TİPİNE KAPALI'),
			'0059'=>Yii::t('vakifpos','SAHTEKARLIK ŞÜPHESİ'),
			'0061'=>Yii::t('vakifpos','Para çekme tutar limiti aşıldı'),
			'0062'=>Yii::t('vakifpos','YASAKLANMIŞ KART'),
			'0063'=>Yii::t('vakifpos','Güvenlik ihlali'),
			'0065'=>Yii::t('vakifpos','GÜNLÜK İŞLEM ADEDİ LİMİTİ AŞILDI'),
			'0075'=>Yii::t('vakifpos','Şifre Deneme Sayısı Aşıldı'),
			'0077'=>Yii::t('vakifpos','ŞİFRE SCRIPT TALEBİ REDDEDİLDİ'),
			'0078'=>Yii::t('vakifpos','ŞİFRE GÜVENİLİR BULUNMADI'),
			'0089'=>Yii::t('vakifpos','İŞLEM ONAYLANMADI'),
			'0091'=>Yii::t('vakifpos','KARTI VEREN BANKA HİZMET DIŞI'),
			'0092'=>Yii::t('vakifpos','BANKASI BİLİNMİYOR'),
			'0093'=>Yii::t('vakifpos','İŞLEM ONAYLANMADI'),
			'0096'=>Yii::t('vakifpos','BANKASININ SİSTEMİ ARIZALI'),
			'0312'=>Yii::t('vakifpos','KARTIN CVV2 DEĞERİ HATALI'),
			'0315'=>Yii::t('vakifpos','TEKRAR DENEYİNİZ'),
			'0320'=>Yii::t('vakifpos','ÖNPROVİZYON KAPATILAMADI'),
			'0323'=>Yii::t('vakifpos','ÖNPROVİZYON KAPATILAMADI'),
			'0357'=>Yii::t('vakifpos','İŞLEM ONAYLANMADI'),
			'0358'=>Yii::t('vakifpos','Kart Kapalı'),
			'0381'=>Yii::t('vakifpos','RED KARTA EL KOY'),
			'0382'=>Yii::t('vakifpos','SAHTE KART-KARTA EL KOYUNUZ'),
			'0501'=>Yii::t('vakifpos','GEÇERSİZ TAKSİT/İŞLEM TUTARI'),
			'0503'=>Yii::t('vakifpos','KART NUMARASI HATALI'),
			'0504'=>Yii::t('vakifpos','İŞLEM ONAYLANMADI'),
			'0540'=>Yii::t('vakifpos','İade Edilecek İşlemin Orijinali Bulunamadı'),
			'0541'=>Yii::t('vakifpos','Orj. İşlemin tamamı iade edildi'),
			'0542'=>Yii::t('vakifpos','İADE İŞLEMİ GERÇEKLEŞTİRİLEMEZ'),
			'0550'=>Yii::t('vakifpos','İŞLEM YKB POS UNDAN YAPILMALI'),
			'0570'=>Yii::t('vakifpos','YURTDIŞI KART İŞLEM İZNİ YOK'),
			'0571'=>Yii::t('vakifpos','İşyeri Amex İşlem İzni Yok'),
			'0572'=>Yii::t('vakifpos','İşyeri Amex Tanımları Eksik'),
			'0574'=>Yii::t('vakifpos','ÜYE İŞYERİ İŞLEM İZNİ YOK'),
			'0575'=>Yii::t('vakifpos','İŞLEM ONAYLANMADI'),
			'0577'=>Yii::t('vakifpos','TAKSİTLİ İŞLEM İZNİ YOK'),
			'0580'=>Yii::t('vakifpos','HATALI 3D GÜVENLİK BİLGİSİ'),
			'0581'=>Yii::t('vakifpos','ECI veya CAVV bilgisi eksik'),
			'0582'=>Yii::t('vakifpos','HATALI 3D GÜVENLİK BİLGİSİ'),
			'0583'=>Yii::t('vakifpos','TEKRAR DENEYİNİZ'),
			'0961'=>Yii::t('vakifpos','İŞLEM TİPİ GEÇERSİZ'),
			'0962'=>Yii::t('vakifpos','TerminalID Tanımısız'),
			'0963'=>Yii::t('vakifpos','Üye İşyeri Tanımlı Değil'),
			'0966'=>Yii::t('vakifpos','İŞLEM ONAYLANMADI'),
			'0971'=>Yii::t('vakifpos','Eşleşmiş bir işlem iptal edilemez'),
			'0972'=>Yii::t('vakifpos','Para Kodu Geçersiz'),
			'0973'=>Yii::t('vakifpos','İŞLEM ONAYLANMADI'),
			'0974'=>Yii::t('vakifpos','İŞLEM ONAYLANMADI'),
			'0975'=>Yii::t('vakifpos','ÜYE İŞYERİ İŞLEM İZNİ YOK'),
			'0976'=>Yii::t('vakifpos','İŞLEM ONAYLANMADI'),
			'0978'=>Yii::t('vakifpos','İŞLEM ONAYLANMADI'),
			'0978'=>Yii::t('vakifpos','KARTIN TAKSİTLİ İŞLEME İZNİ YOK'),
			'0980'=>Yii::t('vakifpos','İŞLEM ONAYLANMADI'),
			'0981'=>Yii::t('vakifpos','EKSİK GÜVENLİK BİLGİSİ'),
			'0982'=>Yii::t('vakifpos','İŞLEM İPTAL DURUMDA. İADE EDİLEMEZ'),
			'0983'=>Yii::t('vakifpos','İade edilemez,iptal'),
			'0984'=>Yii::t('vakifpos','İADE TUTAR HATASI'),
			'0985'=>Yii::t('vakifpos','İŞLEM ONAYLANMADI.'),
			'0986'=>Yii::t('vakifpos','GIB Taksit Hata'),
			'0987'=>Yii::t('vakifpos','İŞLEM ONAYLANMADI.'),
			'8484'=>Yii::t('vakifpos','Birden fazla hata olması durumunda geri dönülür. ResultDetail alanından detayları alınabilir. 1001 Sistem hatası.'),
			'1006'=>Yii::t('vakifpos','Bu transactionId ile daha önce başarılı bir işlem gerçekleştirilmiş'),
			'1007'=>Yii::t('vakifpos','Referans transaction alınamadı'),
			'1046'=>Yii::t('vakifpos','İade işleminde tutar hatalı.'),
			'1047'=>Yii::t('vakifpos','İşlem tutarı geçersizdir.'),
			'1049'=>Yii::t('vakifpos','Geçersiz tutar.'),
			'1050'=>Yii::t('vakifpos','CVV hatalı.'),
			'1051'=>Yii::t('vakifpos','Kredi kartı numarası hatalıdır.'),
			'1052'=>Yii::t('vakifpos','Kredi kartı son kullanma tarihi hatalı.'),
			'1054'=>Yii::t('vakifpos','İşlem numarası hatalıdır.'),
			'1059'=>Yii::t('vakifpos','Yeniden iade denemesi.'),
			'1060'=>Yii::t('vakifpos','Hatalı taksit sayısı.'),
			'2200'=>Yii::t('vakifpos','İş yerinin işlem için gerekli hakkı yok.'),
			'2202'=>Yii::t('vakifpos','İşlem iptal edilemez. ( Batch Kapalı )'),
			'5001'=>Yii::t('vakifpos','İş yeri şifresi yanlış.'),
			'5002'=>Yii::t('vakifpos','İş yeri aktif değil.'),
			'1073'=>Yii::t('vakifpos','Terminal üzerinde aktif olarak bir batch bulunamadı'),
			'1074'=>Yii::t('vakifpos','İşlem henüz sonlanmamış yada referans işlem henüz tamamlanmamış.'),
			'1075'=>Yii::t('vakifpos','Sadakat puan tutarı hatalı'),
			'1076'=>Yii::t('vakifpos','Sadakat puan kodu hatalı'),
			'1077'=>Yii::t('vakifpos','Para kodu hatalı'),
			'1078'=>Yii::t('vakifpos','Geçersiz sipariş numarası'),
			'1079'=>Yii::t('vakifpos','Geçersiz sipariş açıklaması'),
			'1080'=>Yii::t('vakifpos','Sadakat tutarı ve para tutarı gönderilmemiş.'),
			'1061'=>Yii::t('vakifpos','Aynı sipariş numarasıyla daha önceden başarılı işlem yapılmış'),
			'1065'=>Yii::t('vakifpos','Ön provizyon daha önceden kapatılmış'),
			'1082'=>Yii::t('vakifpos','Geçersiz işlem tipi'),
			'1083'=>Yii::t('vakifpos','Referans işlem daha önceden iptal edilmiş.'),
			'1084'=>Yii::t('vakifpos','Geçersiz poaş kart numarası'),
			'7777'=>Yii::t('vakifpos','Banka tarafında gün sonu yapıldığından işlem gerçekleştirilemedi'),
			'1087'=>Yii::t('vakifpos','Yabancı para birimiyle taksitli provizyon kapama işlemi yapılamaz'),
			'1088'=>Yii::t('vakifpos','Önprovizyon iptal edilmiş'),
			'1089'=>Yii::t('vakifpos','Referans işlem yapılmak istenen işlem için uygun değil'),
			'1091'=>Yii::t('vakifpos','Recurring işlemin toplam taksit sayısı hatalı'),
			'1092'=>Yii::t('vakifpos','Recurring işlemin tekrarlama aralığı hatalı'),
			'1093'=>Yii::t('vakifpos','Sadece Satış (Sale) işlemi recurring olarak işaretlenebilir'),
			'1006'=>Yii::t('vakifpos','Bu transactionId ile daha önce başarılı bir işlem gerçekleştirilmiş'),
			'1095'=>Yii::t('vakifpos','Lütfen geçerli bir email adresi giriniz'),
			'1096'=>Yii::t('vakifpos','Lütfen geçerli bir IP adresi giriniz'),
			'1097'=>Yii::t('vakifpos','Lütfen geçerli bir CAVV değeri giriniz'),
			'1098'=>Yii::t('vakifpos','Lütfen geçerli bir ECI değeri giriniz.'),
			'1099'=>Yii::t('vakifpos','Lütfen geçerli bir Kart Sahibi ismi giriniz.'),
			'1100'=>Yii::t('vakifpos','Lütfen geçerli bir brand girişi yapın.'),
			'1105'=>Yii::t('vakifpos','Üye işyeri IP si sistemde tanımlı değil'),
			'1102'=>Yii::t('vakifpos','Recurring işlem aralık tipi hatalı bir değere sahip'),
			'1101'=>Yii::t('vakifpos','Referans transaction reverse edilmiş.'),
			'1111'=>Yii::t('vakifpos','Bu üye işyeri Non Secure işlem yapamaz'),
			'6000'=>Yii::t('vakifpos','Talep mesajı okunamadı. (Mesajda yer alan parametrelerinizin formatlarını kontrol ediniz)'),
		];
	}

}