<?php
namespace udokmeci\yii2payment\models;

use Yii;
/**
 *
 * @property string $code
 * @property integer $no
 * @property string $symbol
 * @property integer $E
 *
 */
class CurrencyActiveRecord extends \yii\db\ActiveRecord
{
    use Curreny;

    public static function tableName()
    {
        return 'currency';
    }
}
