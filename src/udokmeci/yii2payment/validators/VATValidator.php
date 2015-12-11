<?php
namespace udokmeci\yii2payment\validators;
use Yii;
use yii\validators\Validator;
class VATValidator extends Validator
{
    const TYPE_PERSONAL="Personal";
    const TYPE_COMMERCIAL="Commercial";

    public $typeAttribute;
    public $countryAttribute;

    public $messageRequiredVATNo;
    public $messageRequiredVATDepartment;
    public $messageNotValid;
    public $messageTRPersonalNotValid;



    public function init()
    {
        parent::init();
        $this->messageRequiredVATNo = Yii::t('app','The VAT Number is required for your country.');
        $this->messageRequiredVATDepartment = Yii::t('app','The VAT Department is required for your country.');
        $this->messageNotValid = Yii::t('app','VAT number does not seem to be valid for your country');
        $this->messageTRPersonalNotValid = Yii::t('app','Please enter a valid Turkish Republic Identity Number(TCKN).');
    }

    public function validateAttribute($model, $attribute)
    {
        $value=$model->$attribute;
        $countryAttribute=$this->countryAttribute;
        $typeAttribute=$this->typeAttribute;
        if(!$countryAttribute || !$typeAttribute )
            return;
        $countryCode=$model->$countryAttribute;
        $type=$model->$typeAttribute;
        switch ($type) {
            // Personal ID Check
            case self::TYPE_PERSONAL:
                switch ($countryCode) {
                    case 'TR':
                        if(!$this->validateTRPersonal($value))
                                    $model->addError($attribute, $this->messageTRPersonalNotValid);
                        break;

                    default:
                        # code...
                        break;
                }
                break;
            //Other    
            default:
                case 'TR':
                    if(!$this->regex_test('/^\d{10}$/',$value))
                        $model->addError($attribute, $this->messageNotValid);
                    break;
                break;
            }
    }

    public function regex_test($pattern,$value){
        $res=preg_grep($pattern, [$value]);
        if(!isset($res[0]))
        {
            return false;
        }
        return true;
    }


    public function validateTRPersonal($number){
        $impossible = array(
            '11111111110',
            '22222222220',
            '33333333330',
            '44444444440',
            '55555555550',
            '66666666660',
            '77777777770',
            '88888888880',
            '99999999990'
        );

        if ( $number[0]==0 || !ctype_digit($number) || strlen($number)!=11 || in_array($number, $impossible)) {
            return false;
        } else {
            $all=$last=$first=0;

            for ( $a=0;$a<9;$a=$a+2)
                $first=$first+$number[$a];
            for ( $a=1;$a<9;$a=$a+2)
                $last=$last+$number[$a];
            for ( $a=0;$a<10;$a=$a+1)
                $all=$all+$number[$a];
            if ( ( $first*7-$last )%10!=$number[9] || $all%10!=$number[10]) {
                return false;
            } else {
                return true;
            }
        }
    }


}
