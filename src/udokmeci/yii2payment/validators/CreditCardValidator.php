<?php 
namespace udokmeci\yii2payment\validators;

use yii\validators\Validator;


class CreditCardValidator extends Validator
{
    public function init()
    {
        parent::init();
        $this->message = \Yii::t('app','The credit card number you have entered seem not to be valid.');
    }

    public function validateAttribute($model, $attribute)
    {
        if ($model->paymentType!="CreditCard") return true;
        
        $value = $model->$attribute;
        if (!$this->checkLuhns($value)) {
            $model->addError($attribute, $this->message);
        }
    }

    public function checkLuhns($number) {
        


      settype($number, 'string');
      $sumTable = array(
        array(0,1,2,3,4,5,6,7,8,9),
        array(0,2,4,6,8,1,3,5,7,9));
      $sum = 0;
      $flip = 0;
      for ($i = strlen($number) - 1; $i >= 0; $i--) {
        $sum += $sumTable[$flip++ & 0x1][intval($number[$i])];
      }

      return $sum % 10 === 0;


    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        
        $message = json_encode($this->message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return <<<JS
                value=value.replace(/ /g,'');
                
                var len = value.length,
                mul = 0,
                prodArr = [[0, 1, 2, 3, 4, 5, 6, 7, 8, 9], [0, 2, 4, 6, 8, 1, 3, 5, 7, 9]],
                sum = 0;
         
            while (len--) {
                sum += prodArr[mul][parseInt(value.charAt(len), 10)];
                mul ^= 1;
            }
             
                 
            if (!( sum % 10 === 0 && sum > 0) && (value!="")) {
                messages.push($message);
            }
JS;
    }
}